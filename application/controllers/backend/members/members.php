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
		$this->layout->set_section('members');
	}
	
	// --------------------------------------------------------------------

	/**
	 * Member Home Page
	 *
	 * @access	public
	 */
	function index()
	{
		$this->layout->gen_crumb(array(
							backend_url('')			=> 'Backend',
							backend_url('members')	=> 'Members'
		));
		
	   	$this->layout->render('members/home');
	}

	// --------------------------------------------------------------------

	

}

// END Members class


/* End of file members.php */
/* Location: ./application/controllers/backend/members/members.php */