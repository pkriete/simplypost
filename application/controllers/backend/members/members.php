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
 * Member Management Controller
 *
 * @package		SimplyPost
 * @subpackage	Backend - Members
 * @category	Controller
 * @author		Pascal Kriete
 */
class Members extends Backend_Controller {

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function Members()
	{
		parent::Backend_Controller();
		
		$this->permission->secure_restrict();	
	}
	
	// --------------------------------------------------------------------

	/**
	 * Member Home Page
	 *
	 * @access	public
	 */
	function index()
	{
		echo 'MEMBERS!';
	}

	// --------------------------------------------------------------------

	

}

// END Members class


/* End of file members.php */
/* Location: ./application/controllers/backend/members/members.php */