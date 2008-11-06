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
 * Backend Controller
 *
 * @package		SimplyPost
 * @subpackage	Backend
 * @category	Controller
 * @author		Pascal Kriete
 */
class Backend extends Controller {

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function Backend()
	{
		parent::Controller();	
	}

	// --------------------------------------------------------------------

	/**
	 * Backend Home Page
	 *
	 * @access	public
	 */
	function index()
	{
		echo 'backend home';
	}
	
	// --------------------------------------------------------------------

	/**
	 * Backend Info Page
	 *
	 * @access	public
	 */
	function info()
	{
		echo 'backend info';
	}
	

}

// END Backend class

/* End of file backend.php */
/* Location: ./application/controllers/backend/backend.php */