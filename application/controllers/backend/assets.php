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
 * Backend Asset Handler
 *
 * @package		SimplyPost
 * @subpackage	Controllers
 * @category	Assets
 * @author		Pascal Kriete
 */
class Assets extends Controller {

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function Assets()
	{
		parent::Controller();	
	}
	
	// --------------------------------------------------------------------

	/**
	 * Assets Home Page
	 *
	 * @access	public
	 */
	function index()
	{
		show_404();
	}

	// --------------------------------------------------------------------

	/**
	 * CSS Files
	 *
	 * @access	public
	 */
	function css()
	{
		$args = func_get_args();
		
		if (count($args) > 0)
		{
			$file = implode('/', $args);
			$file = APPPATH.'views/assets/css/'.$file.'.css';

			if (file_exists($file))
			{
				header('Content-Type: text/css');
				include $file;
				exit;
			}
		}
		
		show_error('Invalid CSS File: '.implode('/', $args));
	}

	// --------------------------------------------------------------------
	
	/**
	 * JavaScript Files
	 *
	 * @access	public
	 */
	function js()
	{
		$args = func_get_args();

		if (count($args) > 0)
		{
			$file = implode('/', $args);
			$file = APPPATH.'views/assets/js/'.$file.'.js';

			if (file_exists($file))
			{
				header('Content-Type: text/javascript');
				include $file;
				exit;
			}
		}
		
		show_error('Invalid Javascript File: '.implode('/', $args));
	}
	
	
	// --------------------------------------------------------------------
	
	/**
	 * Image Files
	 *
	 * @access	public
	 */
	function images()
	{
		$args = func_get_args();

		if (count($args) > 0)
		{
			$file = implode('/', $args);
			$file = APPPATH.'views/assets/images/'.$file;

			if (file_exists($file))
			{
				$ext = end(explode('.', $file));
				
				$mimes = array(
					'gif'	=>	'image/gif',
					'jpeg'	=>	'image/jpeg',
					'jpg'	=>	'image/jpeg',
					'jpe'	=>	'image/jpeg',
					'png'	=>	'image/png',
					'tiff'	=>	'image/tiff',
					'tif'	=>	'image/tiff'
				);
				
				if (isset($mimes[$ext]))
				{
					header('Content-Type: '.$mimes[$ext]);
					include $file;
					exit;
				}
			}
		}
		
		show_error('Invalid Image File: '.implode('/', $args));
	}
}

// END Assets class


/* End of file assets.php */
/* Location: ./application/controllers/backend/assets.php */