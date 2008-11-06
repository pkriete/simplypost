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
 * Member Controller
 *
 * @package		SimplyPost
 * @subpackage	Controller
 * @category	Member Management
 * @author		Pascal Kriete
 */
class Member extends AppController {

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function Member()
	{
		parent::AppController();	
	}
	
	// --------------------------------------------------------------------

	/**
	 * Member Home Page
	 *
	 * @access	public
	 */
	function index()
	{
		echo 'members';
	}

	// --------------------------------------------------------------------

	/**
	 * Member Home Page
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
	 * Member Home Page
	 *
	 * @access	public
	 */
	function logout()
	{
		echo 'logout';
	}

	// --------------------------------------------------------------------

	

}

// END Member class

/* End of file member.php */
/* Location: ./application/controllers/member/member.php */