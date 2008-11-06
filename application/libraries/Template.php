<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * SimplyPost
 *
 * @author		Pascal Kriete
 * @package		SimplyPost
 * @copyright	Copyright (c) 2008, Pascal Kriete
 * @license 	http://www.opensource.org/licenses/mit-license.php
 */

// ------------------------------------------------------------------------

/**
 * Template Library
 *
 * @package		SimplyPost
 * @subpackage	Libraries
 * @category	Template Engine
 * @author		Pascal Kriete
 */
class Template {

	var $CI;
	var $paths;
	var $slugs;
	var $tmp_folder;
	var $_processed = array();

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function Template()
	{
		$this->CI =& get_instance();
		log_message('debug', "Layout Class Initialized");
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Checks the template folder and keeps track of the paths
	 *
	 * @access	public
	 * @param	new title
	 */
	function initialize($template_name)
	{
		$this->tmp_folder	= BASEPATH.'/../templates/'.$template_name.'/';
		$paths_file			= $this->tmp_folder.'_paths'.EXT;
		
		if ( ! is_dir($this->tmp_folder))
		{
			show_error( lang('error_no_template_group') );
		}
		
		if ( ! file_exists($paths_file))
		{
			show_error( lang('error_no_paths_file') );
		}
		
		include($paths_file);
		
		// Named Paths
		if ( ! isset($paths) OR ! is_array($paths) OR ! isset($paths['login']))
		{
			show_error( lang('error_paths_incomplete') );
		}

		// Forced Slugs
		if (isset($force_slug) && is_array($force_slug))
		{
			$this->slugs = $force_slug;
			
			if ( ! $this->_check_segments())
			{
				die('missing segment');
			}
		}
		
		$this->paths = $paths;
		
		// Looks good - load the parser
		$this->CI->load->library('parser');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Start the parsing phase
	 *
	 * Makes sure all required templates are available
	 * and builds an array of files to parse.
	 *
	 * @TODO: Template permissions
	 *
	 * @access	public
	 * @param	Root template path
	 * @param	Nesting variables
	 */
	function render($path, $nest_vars = array())
	{
		$path = trim($path, '/');

		// Prevent infinite recursion and duplicious parsing
		if ( isset($this->_processed[$path]) )
		{
			return $this->_processed[$path];
		}
		
		if ( ! file_exists($this->tmp_folder.$path.EXT))
		{
			die('cannot find template');
		}
				
		// Iterate through the includes and build a parsing map
		$text = file_get_contents($this->tmp_folder.$path.EXT);

		// Parse the current template
		$text = $this->CI->parser->parse($text, $nest_vars);
		
		$this->_processed[$path] = $text;
		
		// Find the nesting calls
		$optional = '(?: (.+?))?';
		$pattern = "#{nest:(.+?)" . $optional . "}#is";

		preg_match_all($pattern, $text, $matches);
		
		foreach($matches[0] as $key => $match)
		{
			// Grab the path and the optional extras
			$nest_path = $matches[1][$key];
			$variables = $matches[2][$key];

			if ($variables)
			{
				$variables = $this->CI->parser->_split_optional($variables);
			}			
			
			// Recurse into each file
			$inner = $this->render($nest_path, $variables);
						
			// Replace on the way out
			$inner = $this->_processed[$nest_path];
			$text = str_replace($match, $inner, $text);
		}
		
		$this->_processed[$path] = $text;
		return $text;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Validates the requested path
	 *
	 * @access	public
	 * @param	new title
	 */
	function _check_segments()
	{
		$requested = $this->CI->uri->uri_string();
		$requested = trim($requested, '/');
		
		foreach($this->slugs as $section => $slug)
		{
			if (preg_match('#^('.$section.')(.*)$#i', $requested))
			{
				// Section is right - how about that slug
				if (preg_match('#^('.$section.')/'.$slug.'$#i', $requested))
				{
					return TRUE;
				}
				return FALSE;
			}
		}
		
		// Not in there? move along.
		return TRUE;
	}

}
// END Template class

/* End of file template.php */
/* Location: ./application/libraries/template.php */