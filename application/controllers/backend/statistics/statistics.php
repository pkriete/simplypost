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
 * Statistics Controller
 *
 * @package		SimplyPost
 * @subpackage	Backend - Stats
 * @category	Controller
 * @author		Pascal Kriete
 */
class Statistics extends Backend_Controller {

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function Statistics()
	{
		parent::Backend_Controller();
		
		$this->permission->secure_restrict();
		$this->layout->set_section('statistics');
	}
	
	// --------------------------------------------------------------------

	/**
	 * Statistics Home Page
	 *
	 * @access	public
	 */
	function index()
	{
		$this->layout->render('statistics/home');
	}

	// --------------------------------------------------------------------

	

}

// END Statistics class


/* End of file statistics.php */
/* Location: ./application/controllers/backend/statistics/statistics.php */