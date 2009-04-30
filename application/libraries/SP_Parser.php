<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SP_Parser extends CI_Parser {

	var $CI;
	var $id;						// id as extracted from the url

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function SP_Parser()
	{
		$this->CI =& get_instance();
		
		$this->CI->load->library('parsers/singles');
		$this->CI->load->library('parsers/pairs');

		// $this->delim is just too much typing
		// ditch them, but still use them
		define(T_OPEN, $this->l_delim);
		define(T_CLOSE, $this->r_delim);
		
		$this->dynamic_tags = array(
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
		
		// {php} tags
		$text = $this->_php($text);
		
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
						'base_url'			=> site_url(),
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
			'root',
			'category',
			'forum',
			'thread',
			'post',
			'member'
		);
		
		$tag_name = '('.implode('|', $pairs).')';
		$parameter = '(?:[:](\d+))?';
		$optional = '(?: (.+?))?';

		$regex = "#" . T_OPEN . $tag_name . $parameter . $optional . T_CLOSE . "(.+?)" . T_OPEN . '/\\1' . T_CLOSE. "#is";

		return preg_replace_callback($regex, array($this->CI->pairs, 'dispatch'), $text);
	}

	// --------------------------------------------------------------------

	/**
	 * Evaluate conditionals
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
		if (preg_match_all('#\s*([a-z_-]+)=(\042|\047)([^\\2]*?)\\2#i', $optional, $matches))
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
					$key = str_replace(':', '', $key);  // colons confuse the hell out of the singles parser
					$random = uniqid(rand().$key);

					$this->CI->template->_db_store[$random] = $val;
					$inner[$key] = $random;
				}
				
				$data[$num] = $inner;
			}
			
			$text = $this->_parse_pair($tag, $data, $text);
		}
		else
		{
			$random = uniqid(rand().$tag);
			$this->CI->template->_db_store[$random] = $data;

			$text = $this->_parse_single($var, $random, $text);
		}

		return $text;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Replace php tags
	 *
	 * @access	private
	 */
	function _php($text)
	{
		// @TODO implementation: {php}bla bla bla{/php}
		
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