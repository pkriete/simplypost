<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

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
 * Router Class
 *
 * Extends CI Router
 *
 * @package		SimplyPost
 * @subpackage	Libraries
 * @category	Core Extension
 * @author		Pascal Kriete
 */
class SP_Router extends CI_Router {
	
	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function SP_Router()
	{
		parent::CI_Router();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Validate Routing Request
	 *
	 * @access	public
	 */
	function _validate_request($segments)
	{
		// Check the root folder first
		if (file_exists(APPPATH.'controllers/'.$segments[0].EXT))
		{
			return $segments;
		}

		// Not in the root, but not enough segments
		if (count($segments) < 2)
		{
			//Calling the index function of a controller of the same directory...
			//We'll cheat and just set our segment
			$segments[1] = $segments[0];
		}

		// Does the requested controller exist as a full path including the directory?
		if (file_exists(APPPATH.'controllers/'.$segments[0].'/'.$segments[1].EXT))
		{
			//Set the directory
			$this->set_directory($segments[0]);
			
			//Drop the directory segment
			$segments = array_slice($segments, 1);
			return $segments;
		}
		
		//Ok, that didn't work, let's try duplicating segment 0, maybe it's the same ;).
		if (file_exists(APPPATH.'controllers/'.$segments[0].'/'.$segments[0].EXT))
		{
			//Set the directory
			$this->set_directory($segments[0]);
			
			//We cheated so there's nothing to drop
			return $segments;
		}

		// Can't find the requested controller...
		die('Fatal Error');
	}
}
// END SP_Router class

/* End of file SP_Router.php */
/* Location: ./application/libraries/SP_Router.php */