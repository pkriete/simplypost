<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SP_Parser extends CI_Parser {

	var $CI;
	var $id;						// id as extracted from the url

	var $dynamic_parsed = FALSE;	// boolean to limit dynamic parsing
	var $dynamic = array();			// array of dynamic tags
	
	var $db_store = array();		// store db data until post parse

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function SP_Parser()
	{
		$this->CI =& get_instance();
		
		$this->CI->load->library('parsers/singles');

		// $this->delim is just too much typing
		// ditch them, but still use them
		define(T_OPEN, $this->l_delim);
		define(T_CLOSE, $this->r_delim);
		
		$this->dynamic = array(
								'category',
								'forum',
								'thread',
								'post',
								'member'
								);

		// The dynamic content needs an id			
		$id = end($this->CI->uri->segment_array());
		$this->id = is_numeric($id) ? $id : FALSE;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Start the parsing
	 *
	 * @access	public
	 */
	function parse($text, $nest_vars = array())
	{
		// New one for each template
		$this->db_store = array();
		
		// Start out by eval'ing normal php to allow for
		// complex logic that the parser cannot handle
		$text = $this->_parse_php($text);
		
		// Global variable replacements
		$text = $this->_globals($text, $nest_vars);

		// @TODO: Think of a plugin trigger
		
		// Conditionals that use the globals
		$text = $this->_conditionals($text);
		
		// Start parsing the pairs
		$text = $this->_find_pairs($text);
		
		// Parse simple tags that might rely on the data above
		$text = $this->_parse_late_singles($text, TRUE);

		// Last conditional run
		$text = $this->_conditionals($text);
		
		// Nested optionals?
		return $text;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Replace Global Variables
	 *
	 * Does not deal with globals inside other tags - those are done
	 * separately by the handlers for those tags (@TODO make this true!)
	 *
	 * @access	private
	 */
	function _globals($text, $nest_vars)
	{
		// Seperated for readability - no duplicate keys!
		$board = array(
						'board_title'		=> $this->CI->preferences->get('title'),
						'template_group'	=> $this->CI->preferences->get('template')
		);
		
		$urls = array(
						'backend_base_url'	=> $this->CI->preferences->get('backend_base_url'),
						'backend_login'		=> $this->CI->preferences->get('backend_login'),
						'frontend_login'	=> $this->CI->template->get_path('login')
		);
		
		$user = array(
						'logged_in'			=> $this->CI->permission->logged_in(),
						'user_id'			=> current_user('id'),
						'group_id'			=> current_user('group'),
						'username'			=> current_user('username'),
						'email'				=> current_user('email'),
						'join_date'			=> current_user('join_date')
		);
		
		$simple_tags = array_merge($board, $urls, $user);
		unset($board, $urls, $user);
		
		// Add the nested vars with an n: prefix
		if (is_array($nest_vars))
		{
			foreach($nest_vars as $key => $val)
			{
				$simple_tags['n:'.$key] = $val;
			}
		}
		
		// Parse them using CI's native _parse_single
		foreach ($simple_tags as $key => $val)
		{
			$text = $this->_parse_single($key, (string)$val, $text);
		}
		
		// Free up memory
		unset($simple_tags);
		
		return $text;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Replace Single Variables
	 *
	 * These are done at the end of the parsing sequence,
	 * so they should have access to most of the data that
	 * is generated
	 *
	 * @access	private
	 */
	function _parse_late_singles($text)
	{
		$tag_name = '('.implode('|', $this->CI->singles->late).')';
		$parameter = '(?:[:](['.preg_quote('a-z0-9/._-', '#').']+))?';
		$optional = '(?: (.+?))?';
		
		$regex = "#" . T_OPEN . $tag_name . $parameter . $optional . T_CLOSE . "#is";

		return preg_replace_callback($regex, array(&$this->CI->singles, 'dispatch'), $text);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Find pairs and parse them accordingly
	 *
	 * @access	public
	 */
	function _find_pairs($text)
	{
		$pairs = array(
			'category',
			'forum',
			'forums',
			'thread',
			'threads',
			'post',
			'member'
		);
		
		$tag_name = '('.implode('|', $pairs).')';
		$optional = '(?: (.+?))?';
		$parameter = '(?:[:](\d+))?';

		$regex = "#" . T_OPEN . $tag_name . $parameter . $optional . T_CLOSE . "(.+?)" . T_OPEN . '/\\1' . T_CLOSE. "#is";

		return preg_replace_callback($regex, array($this, '_handle_pairs'), $text);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Handle the inner parsing for each pair
	 *
	 * @access	public
	 */
	function _handle_pairs($matches)
	{
		// For readability
		$text = $matches[0];
		$tag = $matches[1];
		$param = $matches[2];
		$optional = $matches[3];
		$inner = $matches[4];
		
		// The function that will do the heavy lifting
		$pf = '_parse_'.$tag;
		
		if (! method_exists($this, $pf))
		{
			// Unknown tag - do nothing
			return $text;
		}
		
		// If it's in the dynamic list, it needs an id of some sort.
		// The first time we use the function parameter (url id)
		// after that it must have a :id to be parsed
		if (in_array($tag, $this->dynamic))
		{
			// No parameter, check dynamic
			if ( ! $param)
			{
				
				// Dynamic gone or we have no id? Skip
				if ($this->dynamic_parsed || ! $this->id)
				{
					return $text;
				}
				else
				{
					// This will be a the dynamic one
					$this->dynamic_parsed = TRUE;
					$id = $this->id;
				}
			}
			else
			{
				$id = $param;
			}
		}
		
		// Make an optionals array
		$optional = $this->_split_optional($optional);
		
		return $this->$pf($inner, $optional, $id);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Parse the category tags
	 *
	 * Categories are essentially forums.  They have a different
	 * name to make the homepage listing more efficient.
	 *
	 * One exception: by default no threads are listed :: manual override
	 * required.
	 *
	 * @access	public
	 * @param	text to parse
	 * @param	optional parameters
	 * @param	node id
	 */
	function _parse_category($text, $optional, $id)
	{
		// Category Identifier
		$optional['is_category'] = TRUE;
		
		// Parse like a forum
		return $this->_parse_forum($text, $optional, $id);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Parse the forum tags
	 *
	 * @access	public
	 * @param	text to parse
	 * @param	optional parameters
	 * @param	node id
	 */
	function _parse_forum($text, $optional, $id)
	{
		// Required Model
		$this->CI->load->model('tree_model', 'tree');
		
		// Category calls this
		$node_type	= isset($optional['is_category']) ? 'category' : 'forum';

		// Get the current node and direct descendants
		$subtree = $this->CI->tree->get_subtree($id, 1, TRUE);
		$current = array_shift($subtree);

		$restrict_child = $current->restrict_child_type;
		
		$text = $this->_parse_node($current, $text, $node_type);
		$text = $this->_parse_children($text, $optional, $subtree, $restrict_child);
		
		return $text;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Parse the thread tags
	 *
	 * @access	public
	 * @param	text to parse
	 * @param	optional parameters
	 * @param	node id
	 */
	function _parse_thread($text, $optional, $id)
	{
		// Required Model
		$this->CI->load->model('tree_model', 'tree');
		
		// Get the current node
		$node = $this->CI->tree->get_node($id);
		
		if ($node)
		{
			// @TODO: Check for pagination
			$this->CI->load->model('post_model', 'posts');
			$children = $this->posts->get_from_parent($id);
		}

		$restrict_child = $current->restrict_child_type;
		
		$text = $this->_parse_node($current, $text, $node_type);
		$text = $this->_parse_children($text, $optional, $subtree, $restrict_child);
		
		return $text;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Parse a tree node
	 *
	 * @access	public
	 * @param	node data
	 * @param	text to parse
	 * @param	node type
	 */
	function _parse_node($current, $text, $node_type, $prefix = '')
	{
		// The tag controls the node type
		if ($current->node_type != $node_type)
		{
			die('node type missmatch');
		}

		// What meta tags do we have to parse?
		$node_meta = array('title', 'description');
		
		// Meta Data for the parent
		foreach($node_meta as $meta_key)
		{
			$tmp_key = $prefix.$meta_key;
			$tmp_val = $current->$meta_key;
			
			$text = $this->_parse_single($tmp_key, (string)$tmp_val, $text);
		}
		return $text;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Parse the child tree
	 *
	 * @access	public
	 * @param	the text to parse
	 * @param	tag parameters
	 * @param	child tree
	 * @param	restrict child type
	 */
	function _parse_children($text, $optional, $tree, $restrict_type = FALSE)
	{
		$node_meta = array('title', 'description');
		
		// An array of data based on child type
		$variable = array();
		
		foreach($tree as $kid)
		{
			$prefix = substr($kid->node_type, 0, 1).':';
			
			$tmp = array();
			foreach($node_meta as $meta_key)
			{
				$tmp[$prefix.$meta_key] = $kid->$meta_key;
			}
			
			$variable[$kid->node_type.'s'][] = $tmp;
		}
		
		foreach($variable as $var => $data)
		{
			$text = $this->_add_db($var, $data, $text);
		}

		// Cleanup child tags
		$pattern = "#{(forums|threads)}(.+?){" . '/'. '\\1' . "}#is";
		
		return preg_replace_callback($pattern, array($this, '_clean_children'), $text);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Cleans up the child tags
	 *
	 * @access	private
	 */
	function _clean_children($matches)
	{
		$prefix = substr($matches[1], 0 , 1);

		if (preg_match('#' . T_OPEN . $prefix . ':empty' . T_CLOSE . '(.+?)' . T_OPEN .'/empty' . T_CLOSE . '#is', $matches[2], $match))
		{
			return $match[1];
		}

		return '';
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Evaluates the final php
	 *
	 * @access	private
	 */
	function _conditionals($text)
	{
		$conditionals = array('if:(.+)', 'elseif:(.+)', 'else', 'endif');

		// @TODO: Implementation

		return $text;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Checks for an optional part and splits it into a nice array
	 *
	 * @access	private
	 */
	function _split_optional($optional)
	{
		if (! $optional)
		{
			return FALSE;
		}
		
		$opt_vars = array();
				
		// Quoted text is parsed as one to allow for spaces
		if (preg_match_all('#\s*([a-z\-]+)=(\042|\047)([^\\2]*?)\\2#i', $optional, $matches))
		{
			foreach ($matches[0] as $key => $match)
			{
				// String => Boolean Conversion
				$tmp[1] = ($matches[3][$key] == 'true') ? TRUE : $matches[3][$key];
				$tmp[1] = ($matches[3][$key] == 'false') ? FALSE : $matches[3][$key];
				
				$opt_vars[$matches[1][$key]] = $matches[3][$key];
				$optional = str_replace($match, '', $optional);
			}
		}
		
		// Deal with the unquoted ones
		$pairs = explode(' ', $optional);
		
		foreach($pairs as $pair)
		{
			$tmp = explode('=', $pair);
			
			// Weed out corrupt parameters
			if (count($tmp) == 2)
			{
				// String => Boolean Conversion
				$tmp[1] = ($tmp[1] == 'true') ? TRUE : $tmp[1];
				$tmp[1] = ($tmp[1] == 'false') ? FALSE : $tmp[1];
				
				$opt_vars[$tmp[0]] = $tmp[1];
			}
		}
		
		return (count($opt_vars) > 0) ? $opt_vars : FALSE;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Converts the db tag data to a unique replacement
	 * This is done to prevent parsing tags inside db
	 * data.
	 *
	 * @TODO: Clean up!
	 *
	 * @access	private
	 */
	function _add_db($tag, $data, $text)
	{
		if (is_array($data))
		{
			foreach($data as $num => $inner)
			{
				foreach($inner as $key => $val)
				{
					$random = uniqid(rand().$key);
					$this->db_store[$random] = $val;
					$inner[$key] = $random;
				}
				
				$data[$num] = $inner;
			}
			
			$text = $this->_parse_pair($tag, $data, $text);
		}
		else
		{
			echo 'yes: '.$tag.'<br />';
			$random = uniqid(rand().$tag);
			$this->db_store[$random] = $data;

			$text = $this->_parse_single($var, $random, $text);
		}

		return $text;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Evaluates the final php
	 *
	 * @access	private
	 */
	function _parse_php($text)
	{
		ob_start();
		echo eval('?>'.$text.'<?php ');
		$text = ob_get_contents();
		@ob_end_clean();

		return $text;
	}
}

// END Parser class


/* End of file Parser.php */
/* Location: ./application/libraries/parser/Parser.php */