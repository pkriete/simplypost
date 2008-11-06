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
 * Simplify session operations
 * 
 * @access	public
 * @return	string	session item to get
 * 
 */
function current_user($item = '')
{
	$CI =& get_instance();
	return $CI->session->userdata($item);
}

/* End of file session_helper.php */
/* Location: ./application/helpers/session_helper.php */