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
 * Single Tag Parser
 *
 * @package		SimplyPost
 * @subpackage	Parsers - Single Tags
 * @category	Libraries
 * @author		Pascal Kriete
 */
class Singles {

	var $CI;
	var $early = array();
	var $late = array('url');

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function Singles()
	{
		$this->CI =& get_instance();	
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Cleans up the matched tag and calls the appropriate function
	 *
	 * @access	public
	 */
	function dispatch($match)
	{
		$method = '_'.$match[1];
		$optional = $this->CI->parser->_split_optional($match[3]);

		if (method_exists($this, $method))
		{
			return $this->$method($match[1], $optional);
		}
		return $match[3];
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * {url} parser
	 *
	 * @access	public
	 */
	function _url($param, $optional)
	{
		$segments = ($param) ? ltrim($param, '/') : '';
		
		if ($optional['path'])
		{
			$segments = $this->CI->template->get_path($optional['path']).'/'.$segments;
		}
		return site_url($segments);
	}
}

// END Singles class


/* End of file singles.php */
/* Location: ./application/libraries/parsers/singles.php */