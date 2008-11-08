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
 * Backend Layout Library
 *
 * @package		SimplyPost
 * @subpackage	Libraries
 * @category	Backend Layout
 * @author		Pascal Kriete
 */
class Layout {

	var $CI;
	var $template	= 'template';
	var $title		= 'SimplyPost Backend';

	var $section	= '';
	var $breadcrumb	= '';
	
	var $root_url;
	var $root_text	= 'Home';
	
	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function Layout()
	{
		$this->CI =& get_instance();
		log_message('debug', "Layout Class Initialized");
		
		$this->CI->load->helper('html');
		$this->root_url = site_url('');
	}
	
	// --------------------------------------------------------------------

	/**
	 * Set the title
	 *
	 * @access	public
	 * @param	new title
	 */
	function set_title($title)
	{
		$this->title = $this->title.' - '.$title;
	}

	// --------------------------------------------------------------------

	/**
	 * Set the section to determine
	 * the sidebar setting
	 *
	 * @access	public
	 * @param	new title
	 */
	function set_section($section)
	{
		$this->section = $section;
	}

	// --------------------------------------------------------------------

	/**
	 * Generate the breadcrumb
	 *
	 * @access	public
	 * @param	new title
	 */
	function gen_crumb($crumbs = array())
	{
		$this->breadcrumb = anchor($this->root_url, $this->root_text);
		
		if (count($crumbs) > 1)
		{
			$front = array_slice($crumbs, 0, -1);
			$end = array_slice($crumbs, -1);

			foreach($front as $url => $title)
			{
				$this->breadcrumb .= '&nbsp; &rsaquo; &nbsp;'.anchor($url, $title);
			}
						
			$this->breadcrumb .= '&nbsp; &rsaquo; &nbsp;'.current($end);
		}
		else
		{
			if (count($crumbs) > 0)
			{
				$this->breadcrumb .= '&nbsp; &rsaquo; &nbsp;'.current($crumbs);
			}
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Custom breadcrumb text
	 *
	 * @access	public
	 * @param	text
	 */
	function custom_crumb($crumb_text = '')
	{
		$this->breadcrumb = $crumb_text;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Change breadcrumb root
	 *
	 * @access	public
	 * @param	text
	 */
	function crumb_root($root_text, $root_url = '/')
	{
		$this->root_text = $root_text;
		$this->root_url = $root_url;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Get flashdata
	 *
	 * @access	public
	 * @param	main content, data
	 */
	function _get_flashdata()
	{
		// @TODO: Should really be a loop (might want to display one of each type)
		
		$text	= '';
		$level	= '';
		
		if ($text = $this->CI->session->flashdata('error'))
		{
			$level = 'error';
		}
		else if ($text = $this->CI->session->flashdata('note'))
		{
			$level = 'note';
		}
		else if ($text = $this->CI->session->flashdata('success'))
		{
			$level = 'success';
		}
		
		$this->CI->load->vars(array(
									'flash_txt'		=> $text,
									'flash_lvl'		=> $level
									));
	}

	// --------------------------------------------------------------------

	/**
	 * Render Final Layout
	 *
	 * @access	public
	 * @param	main content, data
	 */
	function render($view, $data = array())
	{
		$data = array_merge(array(
					'title'			=> $this->title,
					'breadcrumb'	=> $this->breadcrumb,
					'section'		=> $this->section,
					'content'		=> $view
		), $data);
		
		$this->_get_flashdata();
		$this->_headers();
		
		$this->CI->load->view($this->template, $data);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Send no-cache headers
	 *
	 * @access	protected
	 */
	function _headers()
	{
		// Go 'Way Overboard(TM)' on strict non-caching headers
		$this->CI->output->set_header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
		$this->CI->output->set_header('Last-Modified: '.gmdate("D, d M Y H:i:s").' GMT');
		$this->CI->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
		$this->CI->output->set_header("Cache-Control: post-check=0, pre-check=0", false);
		$this->CI->output->set_header('Pragma: no-cache');
	}
}
// END Layout class

/* End of file Layout.php */
/* Location: ./application/libraries/Layout.php */