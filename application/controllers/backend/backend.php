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
class Backend extends Backend_Controller {

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function Backend()
	{
		parent::Backend_Controller();

		$this->permission->secure_restrict();
		$this->layout->set_section('home');
	}

	// --------------------------------------------------------------------

	/**
	 * Backend Home Page
	 *
	 * @access	public
	 */
	function index()
	{
	   	$this->layout->render('main/home');
	}
}

// END Backend class

/* End of file backend.php */
/* Location: ./application/controllers/backend/backend.php */