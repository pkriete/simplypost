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
 * Preferences Library
 *
 * @package		SimplyPost
 * @subpackage	Libraries
 * @category	Preferences
 * @author		Pascal Kriete
 */
class Preferences {
	
	var $CI;
	var $prefs;
	
	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function Preferences()
	{
		$this->CI =& get_instance();
		
		$query = $this->CI->db->get('general', 1, 0);
		$this->prefs = $query->row();
		
		// Set the default language to our global language
		$this->CI->config->set_item('language', $this->prefs->language);
		
		// Global config overrides
		if ($this->CI->config->item('system_locked'))
		{
			$this->prefs->locked = 1;
		}
		
		// Backend URLs
		$backend_url = array_search('admin$1', $this->CI->router->routes);
		$backend_url = str_replace('(.*)', '/', $backend_url);
		
		$this->set('backend_base_url', $backend_url);
		$this->set('backend_login', $backend_url.'login');
		unset($admin_url);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Preferences Accessor
	 *
	 * @access	public
	 */
	function get($key)
	{
		if (isset($this->prefs->$key))
		{
			return $this->prefs->$key;
		}
		return FALSE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Preferences Setter
	 *
	 * @access	public
	 */
	function set($key, $value)
	{
		$this->prefs->$key = $value;
	}
}


/* End of file Preferences.php */
/* Location: ./application/libraries/Preferences.php */