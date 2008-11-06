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
 * Admin Controller
 *
 * @package		SimplyPost
 * @subpackage	Controllers
 * @category	Administration
 * @author		Pascal Kriete
 */
class Admin extends Controller {

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function Admin()
	{
		parent::Controller();	
	}
	
	// --------------------------------------------------------------------

	/**
	 * Admin Home Page
	 *
	 * @access	public
	 */
	function index()
	{
		echo 'backend';
	}

	// --------------------------------------------------------------------

	/**
	 * Backend Login Page
	 *
	 * @access	public
	 */
	function login()
	{
		echo 'login';
	}

	// --------------------------------------------------------------------

	

}

// END Admin class



/* End of file admin.php */
/* Location: ./application/controllers/home/admin.php */