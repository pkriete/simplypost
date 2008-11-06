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
 * Filters allowed post keys
 *
 * @access	private
 */
function filter_input_data($allowed, $data)
{
	$allowed = array_flip($allowed);
	return array_intersect_key($data, $allowed);
}

// --------------------------------------------------------------------

/**
 * Checks if $value for $field is already used
 *
 * @access	private
 * @param	string	email
 * @return	bool
 */
function check_unique($table, $field, $value)
{
	$this->db->select($field);
	$this->db->where($field, $value);
	$this->db->limit(1);

	return ($this->db->count_all_results($this->table) > 0) ? TRUE : FALSE;
}

/* End of file input_helper.php */
/* Location: ./application/helpers/input_helper.php */