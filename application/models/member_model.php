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
 * Member Model
 *
 * @package		SimplyPost
 * @subpackage	Models
 * @category	Members
 * @author		Pascal Kriete
 */

class Member_model extends Model {
	
	var $table				= 'members';
	var $groups_table		= 'groups';
	
	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function Member_model()
	{
		parent::Model();	
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Get all users
	 *
	 * @access	private
	 * @return	bool
	 */
	function get_all()
	{
		$this->db->select($this->table.'.*');
		$this->db->select($this->db->dbprefix($this->groups_table).'.title AS user_group', FALSE);
		$this->db->join($this->groups_table, $this->groups_table.'.group_id = '.$this->table.'.group_id');

		$query = $this->db->get($this->table);
		return ($query->num_rows() > 0) ? $query->result() : FALSE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Insert New Password
	 *
	 * @access	private
	 * @param	string	password, username, salt
	 * @param	bool	force user password change
	 * @return	bool
	 */
	function update_password($password, $email, $change = FALSE)
	{
		$hash = sha1(microtime());
		$salt = $this->config->item('auth');
		$salt = $salt['salt'];

		$password = sha1($salt.$hash.$password);

		$change = $change ? '1' : '0';
		$data = array(
				'change_password'		=> $change,
				'password'				=> $password,
				'hash' 					=> $hash
        );

		$this->db->where('email', $email);
		$this->db->update($this->table, $data);
		
		return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Update Banning Information
	 *
	 * @access	private
	 * @param	user/tracker id | ban type | 0 = un_ban, 1 = ban
	 * @return	bool
	 */
	function update_ban($id, $direction, $type = '')
	{
		if($type == 'ip')
		{
			$this->db->set('banned', $direction);
			$this->db->where('tracker_id', $id);
			$this->db->update($this->tracker_table);
		}
		else
		{
			$this->db->set('active', intval(1 - $direction));
			$this->db->where('user_id', $id);
			$this->db->update($this->table);
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Update User Information
	 *
	 * @access	private
	 * @return	bool
	 */
	function update($data)
	{
		$allowed	= array('group_id', 'username', 'email', 'password', 'first_name', 'last_name', 'interface');
		$data		= $this->_filter_input($allowed, $data);
		
		$this->db->where('user_id', current_user('id'));
		$this->db->update($this->table, $data);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Delete a user
	 *
	 * @access	public
	 */
	function delete($id)
	{
		$this->db->delete($this->table, array('user_id' => $id));
	}

}
// END Member_model class


/* End of file member_model.php */
/* Location: ./application/models/member_model.php */