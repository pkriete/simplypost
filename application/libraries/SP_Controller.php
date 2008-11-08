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
 * AppController
 *
 * @package		SimplyPost
 * @subpackage	Libraries
 * @category	Core Extensions
 * @author		Pascal Kriete
 */
class Backend_Controller extends Controller {
	
	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function Backend_Controller()
	{
		parent::Controller();
		
		$this->input->require_csrf_token();
		
		$this->lang->load('member');
		
		// I use these so much - may move them later
		$this->load->library('layout');
		$this->load->helper(array('html','form'));
	}

	// --------------------------------------------------------------------

	/**
	 * Update unique token
	 *
	 * @access	private
	 */
	private function _csrf_refresh()
	{
		// Only refresh if it was posted
		if( ! $sent = $this->input->get_post('act_s'))
		{
			$this->load->helper('cookie');
			
			// Work out transaction signature for this uri
			$random	= ']rnu<^hdgg%y|\T$w?lva$~U3+hM0Jp{HOr!<,qSdxM-!fEE07q_IwRO"B1=5.~';
			$csrf_token	= md5( $_SERVER['PATH_INFO'] . $this->input->user_agent() . $random . $this->input->ip_address() );

			// Store relevant data
			set_cookie('act_s', $csrf_token, 2*60*60);
			$this->session->set_userdata('token_time', $_SERVER['REQUEST_TIME']);
		}
		else
		{
			// Keep the current one
			$csrf_token = $this->input->cookie('act_s');
		}

		// Set response data
		$this->javascript->set_constant('act_s', $csrf_token);
		$this->javascript->add_response('act_s', $csrf_token);
		
		// Add user information
		if ($this->access->logged_in())
		{
			$user_js = current_user('js');
			$this->javascript->set_constant('user', $user_js['user']);
			$this->javascript->set_constant('interface', $user_js['interface']);
		}
	}
}


/* End of file SP_Controller.php */
/* Location: ./application/libraries/SP_Controller.php */