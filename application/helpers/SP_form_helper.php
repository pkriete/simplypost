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
 * Form Declaration - CSRF Safe
 *
 * Creates the opening portion of the form as well as a hidden field with a unique id.
 *
 * @access	public
 * @param	string	the URI segments of the form destination
 * @param	array	a key/value pair of attributes
 * @param	array	a key/value pair hidden data
 * @return	string
 */
function form_open_safe($action = '', $attributes = '', $hidden = array())
{
	$CI =& get_instance();
	
	$sig = $CI->input->get_csrf_token();
	$hidden = array_merge($hidden, array('act_s' => $sig));
	
	return form_open($action, $attributes, $hidden);
}

/* End of file MY_form_helper.php */
/* Location: ./application/helpers/MY_form_helper.php */