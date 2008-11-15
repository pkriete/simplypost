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

function reorder_tree($tree)
{
	if ( ! $tree)
	{
		return FALSE;
	}
	
	$CI =& get_instance();
	$CI->load->library('tree_iterator');
	
	$CI->tree_iterator->initialize($tree);
	
	$tree = $CI->tree_iterator->get_tree();
	return $tree;
}


/* End of file tree_helper.php */
/* Location: ./application/helpers/tree_helper.php */