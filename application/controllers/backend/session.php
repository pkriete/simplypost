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
class Session extends Backend_Controller {

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function Session()
	{
		parent::Backend_Controller();
		$this->load->library('authentication');
	}

	// --------------------------------------------------------------------

	/**
	 * Backend Login Page
	 *
	 * @access	public
	 */
	function login()
	{
		$this->load->library('form_validation');
		$this->load->helper('form');
		
		if ( $this->permission->logged_in() )
		{
			if (current_user('session'))
			{
				redirect( backend_url('') );
			}
		}
		
		if ($this->form_validation->run('backend/login') == FALSE)
		{
			$this->layout->set_title('Login');
		   	$this->layout->render('session/login');
		}
		else
		{
			redirect( backend_url('') );
		}		
	}

	// --------------------------------------------------------------------

	/**
	 * Backend Logout Page
	 *
	 * @access	public
	 */
	function logout()
	{
		$this->authentication->logout();
		redirect( backend_url('') );
	}
	
	// --------------------------------------------------------------------
	

}

// END Session class

/* End of file session.php */
/* Location: ./application/controllers/backend/session.php */