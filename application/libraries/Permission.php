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
 * Permission Class
 *
 * @package		SimplyPost
 * @subpackage	Libraries
 * @category	Permissions
 * @author		Pascal Kriete
 */
class Permission {
	
	var $CI;
	var $tracker;
	
	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	void
	 * @return	void
	 */
	function Permission()
	{
		$this->CI =& get_instance();
	}

	// --------------------------------------------------------------------

	/**
	 * Logged In
	 *
	 * Checks to see if a visitor is logged into the site
	 * Based on the current session information.  If there
	 * is no session - it asks the authentication library.
	 *
	 * @access	public
	 * @param	void
	 * @return	bool
	 */
	function logged_in()
	{
		$group = current_user('group');
		
		// If the group is set, we have done this check
		if ($group !== FALSE)
		{
			// Group 0 is guests
			return ($group == 0) ? FALSE : TRUE;
		}

		// Just came to the page, no session
		// Authentication checks for remember me cookie
		$this->CI->load->library('authentication');
		return $this->CI->authentication->_check_remember();	
	}

	// --------------------------------------------------------------------

	/**
	 * Protect a backend controller / function
	 *
	 * Default use is to bar non-members
	 * A user group can be specified to limit to that user group
	 *
	 * @access	public
	 * @param	string	user group
	 */
	function secure_restrict()
	{
		if ( ! current_user('secure') )
		{
			if ( ! $this->logged_in() )
			{
				$this->CI->session->set_flashdata('error', 'You must log in to see this content.');
			}
			else
			{
				$this->CI->session->set_flashdata('msg', 'You must re-enter your password to see this content.');
			}
			redirect( backend_url('/session/login') );
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Denies access to locked board unless you're a superadmin
	 *
	 * @access	private
	 * @return	bool
	 */
	function _check_locked()
	{
		if($this->CI->preferences->get('locked') && current_user('group') != 1)
		{
			return TRUE;
		}
		return FALSE;
	}
}

/* End of file Permission.php */
/* Location: ./application/libraries/Permission.php */