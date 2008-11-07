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

function backend_url($segments)
{
	$CI =& get_instance();
	$base = $CI->config->item('backend_base');
	
	return $base . '/' . trim($segments, '/');
}


/* End of file SP_url_helper.php */
/* Location: ./application/helpers/SP_url_helper.php */