<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

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
 * Tracker Model
 *
 * @package		SimplyPost
 * @subpackage	Models
 * @category	Permissions
 * @author		Pascal Kriete
 */
class Tracker_model extends Model {

	var $table	= 'tracker';

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function Tracker_model()
	{
		parent::Model();
	}

	// --------------------------------------------------------------------

	/**
	 * Get IP Info
	 *
	 * Gets information on an ip address
	 *
	 * @access	private
	 * @return	mixed
	 */
	function get_ip_info()
	{
		$this->db->where('ip_address', $this->input->ip_address());
		$i = $this->db->get($this->table, 1, 0);

		return $var = ($i->num_rows() > 0) ? $i->row() : FALSE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Add IP
	 *
	 * Add an ip address to the tracker
	 *
	 * @access	private
	 * @return	mixed	new row
	 */
	function add_ip()
	{
		$this->db->set('ip_address', $this->input->ip_address());
		$this->db->insert($this->table);
		
		$this->db->where('ip_address', $this->input->ip_address());
		$query = $this->db->get($this->table);
		return $query->row();
	}
	
	// --------------------------------------------------------------------

	/**
	 * Increment failure count and set last login time
	 *
	 * @access	public
	 * @return	object	user object
	 */
	function increment_failures($failed_so_far)
	{
		$now = time();
		$this->db->where('ip_address', $this->input->ip_address());
		
		if($failed_so_far < 4) // Not relevant beyond this point
		{
			$this->db->set('attempts', 'attempts + 1', FALSE);
		}
		
		$this->db->set('last_attempt', $now);
		$this->db->update($this->table);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Resets login failure count
	 *
	 * @access	public
	 * @return	object	user object
	 */
	function reset_failures()
	{		
		$this->db->where('ip_address', $this->input->ip_address());
		$this->db->set('attempts', 0);
		
		$this->db->update($this->table);
	}

}
// END Tracker_model class

/* End of file tracker_model.php */
/* Location: ./application/models/tracker_model.php */