<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SP_Parser extends CI_Parser {

	var $CI;
	var $globals = array();

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function SP_Parser()
	{
		$this->CI =& get_instance();
	}
	
	// --------------------------------------------------------------------

	/**
	 * Start the parsing
	 *
	 * @access	public
	 */
	function parse($text, $nest_vars = array())
	{
		// Start out by eval'ing normal php to allow for
		// complex logic that the parser cannot handle
		$text = $this->_parse_php($text);
		
		// Global variable replacements
		$text = $this->_simple_tags($text, $nest_vars);
		
		// @TODO: Think of a plugin trigger
		
		// Conditionals that use the globals
		$text = $this->_conditionals($text);

		// The dynamic content needs an id			
		$id = end($this->CI->uri->segment_array());
		$id = is_numeric($id) ? $id : FALSE;
		
		// Parse the db content - lots of work get's done here
		$text = $this->_db_content($text, $id);
		
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
	 * @access	public
	 */
	function _simple_tags($text, $nest_vars)
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
		
		// Add them to the global array so other parsers can use them
		$this->_set_global($simple_tags);
		unset($simple_tags);
		
		return $text;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Parse the dynamic [read:automagic from url] tags
	 *
	 * @access	public
	 */
	function _db_content($text, $id = FALSE)
	{
		$db_content = array(
			'category',
			'forum',
			'thread',
			'post',
			'member'
		);

		// Only want to look for dynamic stuff once
		$dynamic_parsed = FALSE;

		foreach($db_content as $node_type)
		{
			// Regular expression patterns
			$optional = '(?: (.+?))?';
			$dynamic = "#{" . $node_type . $optional . "}(.+?){" . '/'.$node_type . "}#is";
			$static = "#{" . $node_type . ':(\d+)' . $optional . "}(.+?){" . '/'.$node_type . "}#is";
			
			// Parsing function for this node type
			$pf = '_parse_'.$node_type;
			
			// If there is no id or we've found a dynamic one - skip it
			if (is_numeric($id) && ! $dynamic_parsed)
			{
				// Dynamic content
				if ( preg_match_all($dynamic, $text, $matches))
				{
					// First and last dynamic one
					$dynamic_parsed = TRUE;
					
					foreach($matches[0] as $key => $full_match)
					{
						$optional	= $matches[1][$key];
						$inner		= $matches[2][$key];

						$optional = $this->_split_optional($optional);
						
						$replace = $this->$pf($inner, $optional, $id);
						$text = str_replace($full_match, $replace, $text);
					}
				}
			}
			
			// Static content
			if ( preg_match_all($static, $text, $matches))
			{
				foreach($matches[0] as $key => $full_match)
				{
					$id			= $matches[1][$key];
					$optional	= $matches[2][$key];
					$inner		= $matches[3][$key];
					
					$optional = $this->_split_optional($optional);
					
					$replace = $this->$pf($inner, $optional, $id);
					$text = str_replace($full_match, $replace, $text);
				}
			}
		}
		
		return $text;
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
			$text = $this->_parse_pair($var, $data, $text);
		}

		// Cleanup child tags
		$pattern = "#{(forums|threads)}(.+?){" . '/'. '\\1' . "}#is";
		if ( preg_match_all($pattern, $text, $matches))
		{
			// Found tags, by key :)
			$found = $matches[1];
			$full_matches = $matches[0];
			
			// Clean up
			foreach($matches[0] as $key => $full_match)
			{
				// Do we have an empty alternative?
				if (preg_match('#{' .$found[$key]. ':empty}(.+?){/empty}#is', $text, $match))
				{
					$text = str_replace($match[0], $match[1], $text);
				
				}
				$text = str_replace($full_match, '', $text);
			}
		}
		
		return $text;
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
	
	// --------------------------------------------------------------------
	
	/**
	 * Add parsed tags to the globals array
	 *
	 * @access	private
	 */
	function _set_global($key, $val = FALSE)
	{
		if (is_array($key))
		{
			$this->globals = array_merge($this->globals, $key);
			return;
		}
		$this->globals[$key] = $value;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the value of the parsed tag
	 *
	 * @access	private
	 */
	function _get_global($key)
	{
		if (isset($this->globals[$key]))
		{
			return $this->globals[$key];
		}
		return FALSE;
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
		// Thanks to the input lib for the regex idea ;-)
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
}

// END Parser class


/* End of file Parser.php */
/* Location: ./application/libraries/parser/Parser.php */