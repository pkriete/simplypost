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
 * Authentication Model
 *
 * @package		SimplyPost
 * @subpackage	Models
 * @category	Permissions
 * @author		Pascal Kriete
 */

class Authentication_model extends Model {

	var $table				= 'members';
	var $groups_table		= 'groups';
	var $tracker_table		= 'tracker';

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function Authentication_model()
	{
		parent::Model();	
	}
	
	// --------------------------------------------------------------------

	/**
	 * Get relevant user information by id
	 *
	 * @access	public
	 * @param	integer	User ID
	 * @param	mixed	remember_me unique token
	 */
	function get_user($column, $identifier, $rem_data = FALSE)
	{
		$this->db->select($this->table.'.*');
		$this->db->select($this->db->dbprefix($this->groups_table).'.title AS user_group', FALSE);
		$this->db->join($this->groups_table, $this->groups_table.'.group_id = '.$this->table.'.group_id');

		$this->db->where($column, $identifier);

		// If the remember me data is set, we'll include that
		if($rem_data !== FALSE)
		{
			$this->db->where('rem_data', $rem_data);
		}

		$this->db->limit(1);

		$query = $this->db->get($this->table);
		return ($query->num_rows() > 0) ? $query->row() : FALSE;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Register User
	 *
	 * Add user to the db
	 *
	 * @access	private
	 * @param	string	table, where
	 */
	function register_user($userdata)
	{
		/* Filter array keys */
		$allowed = array('group_id', 'username', 'email', 'password', 'join_date');
		$userdata = filter_input_data($allowed, $userdata);
		
		/* Hash the password */
		$userdata['password'] = $this->hash_password($userdata['password']);
		
		$this->db->set($userdata);
		$this->db->insert($this->table);
		
		return $this->db->insert_id();
	}

	// --------------------------------------------------------------------

	/**
	 * Update Remember Me Information
	 *
	 * @access	private
	 * @return	bool
	 */
	function update_remember($data)
	{
		$this->db->where('user_id', current_user('id'));
		$this->db->update($this->table, array('rem_data' => $data));
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Salt and hash a password
	 *
	 * @access	private
	 * @param	string	password
	 * @param	string	stored password to extract salt
	 * @return	string	hashed password
	 */
	function hash_password($password, $stored = FALSE)
	{
		if ($stored)
		{
			$salt = substr($stored, 0, 20);
		}
		else
		{
			$salt = substr(sha1(uniqid(rand(), TRUE)), 0, 20);
		}
		
		return $salt . sha1($salt . $password);
	}
	
	// --------------------------------------------------------------------
	
}
// END Authentication_model class

/* End of file authentication_model.php */
/* Location: ./application/models/authentication_model.php */