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
 * Backend Session Controller
 *
 * @package		SimplyPost
 * @subpackage	Backend - Sessions
 * @category	Controller
 * @author		Pascal Kriete
 */
class Secure extends Controller {

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function Secure()
	{
		parent::Controller();	
	}
	
	// --------------------------------------------------------------------

	/**
	 * Secure Home Page
	 *
	 * @access	public
	 */
	function index()
	{
		echo 'secure home';
	}
	
	// --------------------------------------------------------------------

	/**
	 * Backend Login Page
	 *
	 * @access	public
	 */
	function login()
	{
		$this->load->library('authentication');
		
		if ( $this->authentication->logged_in() )
		{
			if ( ! BOARD_LOCKED OR current_user('group') == 1)
			{
				redirect('');
			}
		}
		else
		{
			echo 'login form';
		}

		echo 'login';
	}

	// --------------------------------------------------------------------

	/**
	 * Backend Logout Page
	 *
	 * @access	public
	 */
	function logout()
	{
		echo 'logout';
	}
	
	// --------------------------------------------------------------------
	

}

// END Secure class


/* End of file secure.php */
/* Location: ./application/controllers/backend/secure.php */