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
 * Frontend Controller
 *
 * @package		SimplyPost
 * @subpackage	Frontend
 * @category	Controller
 * @author		Pascal Kriete
 */

class Frontend extends Controller {

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function Frontend()
	{
		parent::Controller();	
	}
	
	// --------------------------------------------------------------------

	/**
	 * Home Page
	 *
	 * @access	public
	 */
	function index()
	{
		/*
		// Config Dependant Constants
		define('SP_VERSION'		, $this->prefs->version);
		define('BOARD_TITLE'	, $this->prefs->title);
		define('BOARD_LOCKED'	, $this->prefs->locked);
		define('TEMPLATE'		, $this->prefs->template.'/');
		*/
		
		// Load the main libraries
		$this->load->library('preferences');
		$this->load->library('permission');
		$this->load->library('template');

		// Grab the template folder
		$template_name = $this->preferences->get('template');
		
		// Process the template basics and work out where we are
		$requested = $this->template->initialize($template_name);
		
		// Ok, before we start parsing stuff, a locked system goes nowhere		
		$locked = $this->permission->_check_locked();
		
		// @TODO Force login page when locked.
/*
		if ($locked && $this->uri->uri_string() != '/member/login')
		{
			$this->load->library('authentication');
			$this->authentication->logout();
			
			$this->session->set_flashdata('error', $this->lang->line('error_board_locked'));
			redirect('member/login');
		}
*/
		
		// Take off an id if there is one
		$id = end($this->uri->segment_array());
		
		if (is_numeric($id))
		{
			$segments = array_slice($this->uri->segment_array(), 0, -1);
			$requested = implode('/', $segments);
		}

		$final = $this->template->render($requested);
		$this->output->set_output($final);
	/*	
		echo $final;
		die;
		
		echo '<pre>';
		print_r($this->template->_processed);
		echo '</pre>';
	*/	
	}

	// --------------------------------------------------------------------
	
	/**
	 * Home Home Page
	 *
	 * @access	public
	 */
	function create_admin()
	{
		// For my own sanity
		die('locked');
		
		$this->load->model('authentication_model');
		
		$data = array(
					'group_id'	=> 1,
					'username'	=> 'inparo',
					'email'		=> 'inparo@example.com',
					'password'	=> 'emma',
					'join_date'	=> time()
		);
		
		if ($this->authentication_model->register_user($data))
		{
			echo 'created!';
		}
		else
		{
			echo 'failed';
		}
	}

	// --------------------------------------------------------------------

	

}

// END Frontend class

/* End of file frontend.php */
/* Location: ./application/controllers/frontend.php */