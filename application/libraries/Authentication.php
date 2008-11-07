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
 * Authentication Class
 *
 * @package		SimplyPost
 * @subpackage	Libraries
 * @category	Permissions
 * @author		Pascal Kriete
 */
class Authentication {
	
	var $CI;
	var $tracker;
	
	var $default_group	= 3;
	
	/**
	 * Constructor
	 *
	 * Loads the configuration options and assigns them to
	 * class variables available throughout the library.
	 *
	 * @access	public
	 * @param	void
	 * @return	void
	 */
	function Authentication()
	{
		$this->CI =& get_instance();

		$this->CI->load->model('authentication_model');
		$this->CI->load->model('tracker_model');

		// Track all visitors
		$this->_start_tracking();
	}
	
	// --------------------------------------------------------------------

	/**
	 * Register
	 *
	 * Registers a user
	 *
	 * @access	public
	 * @param	mixed userdata
	 * @return	mixed
	 */
	function register($userdata)
	{
		/* Assign to default group */
		$userdata['group_id'] = $this->default_group;

		/* Add to database */
		$this->CI->authentication_model->register_user($userdata);
		
		return TRUE;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Login
	 *
	 * Verifies a user based on username and password
	 *
	 * @access	public
	 * @param	string username, password
	 * @return	bool
	 */
	function login($username, $password)
	{
		/* Throttle ip address */
		if($this->tracker->attempts > 3)
		{
			$now = time();
			$wait = $now - 20;

			if($this->tracker->last_attempt > $wait)
			{
				return TIMEOUT;
			}
		}

		$result = $this->CI->authentication_model->get_user('username', $username);

		if ($result) // Result Found
		{
			// User banned?
			if ($result->banned == BANNED)
			{
				return BANNED;
			}

			// Hash input password
			$password = $this->CI->authentication_model->hash_password($password, $result->password);

			// Passwords match?
			if ($password === $result->password)
			{
				$result->secure = TRUE;
				
				// Start session, reset login count
				$this->_fill_session($result);
				$this->CI->tracker_model->reset_failures();

				// Remember me?
				if($this->CI->input->post('remember'))
				{
					$this->_set_remember($result->user_id);
				}

				return TRUE;
			}
		}
		
		$this->CI->tracker_model->increment_failures($this->tracker->attempts);
		return FALSE;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Logout
	 *
	 * Destroys the user's session
	 *
	 * @access	public
	 * @return	void
	 */
	function logout()
	{
		$this->CI->load->helper('cookie');
		delete_cookie('remember');
		
		$this->CI->session->unset_userdata('group');
		$this->CI->session->sess_destroy();
	}
	
	// --------------------------------------------------------------------

	/**
	 * Remember Me check
	 *
	 * Checks to see if a visitor has any login credentials
	 * DO NOT CALL DIRECTLY (use permission->logged_in())
	 *
	 * @access	public
	 * @param	void
	 * @return	bool
	 */
	function _check_remember()
	{
		if ($this->_get_remember()) // Remember?
		{
			return TRUE;
		}
		
		$this->_create_guest();
		return FALSE;		
	}
	
	// --------------------------------------------------------------------

	/**
	 * Track the user logins
	 *
	 * Logs the ip and checks for ip bans
	 *
	 * @access	public
	 */
	function _start_tracking()
	{
		$this->tracker = $this->CI->tracker_model->get_ip_info();
		
		if( ! $this->tracker)
		{
			$this->tracker = $this->CI->tracker_model->add_ip();
		}
		else if($this->tracker->banned == BANNED)
		{
			exit('This IP has been banned.');
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Set Remember-Me cookie
	 *
	 * Updates the remember me cookie and database information
	 *
	 * @access	public
	 * @param	string unique identifier
	 * @return	void
	 */
	function _set_remember()
	{
		// New token and timeout
		$token = md5(uniqid(rand(), TRUE));
		$timeout = 60 * 60 * 24 * 7;
		
		$user_id = current_user('id');
		$rem_key = $this->CI->config->item('rem_salt');
		
		// Encrypt the unique data
		$this->CI->load->library('encrypt');
		$value = $this->CI->encrypt->encode($user_id.':'.$token.':'.(time() + $timeout), $rem_key);
		
		// Set the cookie and database
		$this->CI->load->helper('cookie');
		$cookie = array(
						'name'		=> 'remember',
						'value'		=> $value,
						'expire'	=> $timeout
						);

		set_cookie($cookie);
		$this->CI->authentication_model->update_remember($value);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Get data from remember me cookie
	 *
	 * @access	public
	 * @param	string unique identifier
	 * @return	void
	 */
	function _get_remember()
	{
		// Get the cookie
		if($persistent = $this->CI->input->cookie('remember'))
		{
			$rem_key = $this->CI->config->item('rem_salt');
			
			$this->CI->load->library('encrypt');
			$data = $this->CI->encrypt->decode($persistent, $rem_key);
			
			list($user_id, $token, $timeout) = explode(':', $data);
			
			// Cookie should've expired - cheating bastards
			if ($timeout < time())
			{
				return FALSE;
			}
	
			// Grab the user, returns false if he/she doesn't exist or
			// the cookie was tampered with
			if ($data = $this->CI->authentication_model->get_user('member_id', $user_id, $persistent))
			{
				$data['secure'] = FALSE;
				
				// Fill the session and renew the remember me cookie
				$this->_fill_session($data);
				$this->_set_remember();
				
				return TRUE;
			}
			
			// You cheat, cookie monster get's cookie
			$this->CI->load->helper('cookie');
			delete_cookie('remember');
		}
		return FALSE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Fill the session with the default guest values
	 *
	 * @access	public
	 */
	function _create_guest()
	{
		$guest_data = array(
							'user_id'	=> 0,
							'group_id'	=> 0,
							'username'	=> 'Guest',
							'email'		=> '',
							'join_date'	=> 0,
							'secure'	=> FALSE
		);
		
		// _fill_session expects an object
		$guest_data = (object) $guest_data;
		
		$this->_fill_session($guest_data);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Drop the userdata into the session object
	 *
	 * @access	public
	 * @return	object	user object
	 */
	function _fill_session($data)
	{
		$userdata = array();
		
		// Key -> field_name relationships
		$member_keys = array(
						'id'			=> 'user_id',
						'group'			=> 'group_id',
						'username'		=> 'username',
						'email'			=> 'email',
						'join_date'		=> 'join_date'
						);

		// Ensure that all the fields we set are actually there
		foreach($member_keys as $key => $db)
		{
			$userdata[$key] = (isset($data->$db)) ? $data->$db : '';
		}
				
		// Plop it all down in the session
		$this->CI->session->set_userdata($userdata);
	}
}

/* End of file Authentication.php */
/* Location: ./application/libraries/Authentication.php */