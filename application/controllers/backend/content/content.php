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
 * Content Management Controller
 *
 * @package		SimplyPost
 * @subpackage	Backend -Content
 * @category	Controller
 * @author		Pascal Kriete
 */
class Content extends Backend_Controller {

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function Content()
	{
		parent::Backend_Controller();
		
		$this->permission->secure_restrict();
		$this->layout->set_section('content');
	}
	
	// --------------------------------------------------------------------

	/**
	 * Content Home Page
	 *
	 * @access	public
	 */
	function index()
	{
		$this->layout->render('content/home');
	}

	// --------------------------------------------------------------------

	

}

// END Content class


/* End of file content.php */
/* Location: ./application/controllers/backend/content/content.php */