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
 * Main Controller
 *
 * @package		SimplyPost
 * @subpackage	Home
 * @category	Controller
 * @author		Pascal Kriete
 */

class Main extends Controller {

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function Main()
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
		
		// We'll need to know where we are
		$requested = $this->uri->uri_string();
		
		// Grab the template folder
		$template_name = $this->preferences->get('template');
		
		// Process the template basics
		$this->template->initialize($template_name);
		
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
		
		echo $final;
		die;
		
		echo '<pre>';
		print_r($this->template->_processed);
		echo '</pre>';
		
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

// END Main class

/* End of file main.php */
/* Location: ./application/controllers/main.php */