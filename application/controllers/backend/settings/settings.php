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
 * Backend Settings Controller
 *
 * @package		SimplyPost
 * @subpackage	Backend - Settings
 * @category	Controller
 * @author		Pascal Kriete
 */
class Settings extends Backend_Controller {

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function Settings()
	{
		parent::Backend_Controller();
		
		$this->permission->secure_restrict();
		$this->layout->set_section('settings');
	}
	
	// --------------------------------------------------------------------

	/**
	 * Settings Home Page
	 *
	 * @access	public
	 */
	function index()
	{
		$this->layout->gen_crumb(array(
							backend_url('')				=> 'Backend',
							backend_url('statistics')	=> 'Settings'
		));
		
		$this->layout->render('settings/home');
	}

	// --------------------------------------------------------------------

	

}

// END Settings class


/* End of file settings.php */
/* Location: ./application/controllers/backend/settings/settings.php */