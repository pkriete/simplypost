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
 * Pair Parser
 *
 * @package		SimplyPost
 * @subpackage	Parsers - Pairs
 * @category	Library
 * @author		Pascal Kriete
 */
class Pairs {

	var $CI;
	var $node_meta = array();		// available node meta data
	
	var $dynamic_parsed = FALSE;	// boolean to limit dynamic parsing
	var $dynamic_tags = array();	// array of dynamic tags

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function Pairs()
	{
		$this->CI =& get_instance();
		
		$this->node_meta = array(
								'title',
								'description',
								'node_id',
								'node_type'
								);
		
		$this->dynamic_tags = array(
								'category',
								'forum',
								'thread',
								'post',
								'member'
								);
	}

	// --------------------------------------------------------------------

	/**
	 * Cleans up the matched tag and calls the appropriate function
	 *
	 * @access	public
	 */
	function dispatch($matches)
	{
		// For readability
		$text	= $matches[0];
		$tag	= $matches[1];
		$param	= $matches[2];
		$optional = $matches[3];
		$inner	= $matches[4];
		
		// The function that will do the heavy lifting
		$pf = '_parse_'.$tag;
		
		if (! method_exists($this, $pf))
		{
			// Unknown tag - do nothing
			return $text;
		}
		
		// If it's in the dynamic list, it needs an id of some sort.
		// The first time we use the function parameter (url id)
		// after that it must have a :id to be parsed
		if (in_array($tag, $this->dynamic_tags))
		{
			// No parameter, check dynamic
			if ( ! $param)
			{
				// Already parsed and not the same type, or no id? Skip
				if ($this->dynamic_parsed && $this->dynamic_parsed != $tag OR ! $this->CI->parser->id)
				{
					return $text;
				}
				else
				{
					// Redundant after the first time
					$this->dynamic_parsed = $tag;
					$id = $this->CI->parser->id;
				}
			}
			else
			{
				$id = $param;
			}
		}
		
		// Make an optionals array
		$optional = $this->CI->parser->_split_optional($optional);
		
		return $this->$pf($inner, $optional, $id);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Parse the root tag
	 *
	 * The root tag will grab a list of all root - node elements of the tree
	 *
	 * @access	public
	 * @param	text to parse
	 * @param	optional parameters
	 */
	function _parse_root($text, $optional)
	{
		// Required Model
		$this->CI->load->model('tree_model', 'tree');
		
		// Tree Helper
		$this->CI->load->helper('tree');

		// Get the root nodes and direct descendants
		$subtree = $this->CI->tree->get_tree();
		
		$subtree = reorder_tree($subtree);
		
		// Loop through all the trees, parsing everything along the way		
		foreach($subtree as $current)
		{
			$node_type = $current->node_type;
			$tmp = $this->_parse_node($current, $text, $node_type);
			
			if ($current->children)
			{
				$restrict_child = $current->restrict_child_type;
				$tmp = $this->_parse_children($tmp, $optional, $current->children, $restrict_child);
			}
			
			$newt .= $tmp;
		}
		
		return $newt;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Parse the category tags
	 *
	 * Categories are essentially forums.  They have a different
	 * name to make the homepage listing more efficient.
	 *
	 * One exception: by default no threads are listed :: manual override
	 * required.
	 *
	 * @access	public
	 * @param	text to parse
	 * @param	optional parameters
	 * @param	node id
	 */
	function _parse_category($text, $optional, $id)
	{
		// Category Identifier
		$optional['is_category'] = TRUE;
		
		// Parse like a forum
		return $this->_parse_forum($text, $optional, $id);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Parse the forum tags
	 *
	 * @access	public
	 * @param	text to parse
	 * @param	optional parameters
	 * @param	node id
	 */
	function _parse_forum($text, $optional, $id)
	{
		// Required Model
		$this->CI->load->model('tree_model', 'tree');
		
		// Category calls this
		$node_type	= isset($optional['is_category']) ? 'category' : 'forum';

		// Get the current node and direct descendants
		$subtree = $this->CI->tree->get_subtree($id, 1, TRUE);
		$current = array_shift($subtree);

		$restrict_child = $current->restrict_child_type;
		
		$text = $this->_parse_node($current, $text, $node_type);
		$text = $this->_parse_children($text, $optional, $subtree, $restrict_child);
		$text = $this->_parse_parents($text, $current);
		
		return $text;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Parse the thread tags
	 *
	 * @access	public
	 * @param	text to parse
	 * @param	optional parameters
	 * @param	node id
	 */
	function _parse_thread($text, $optional, $id)
	{
		// Required Model
		$this->CI->load->model('tree_model', 'tree');
		
		// Get the current node
		$node = $this->CI->tree->get_node($id);
		
		if ($node)
		{
			// @TODO: Check for pagination
			$this->CI->load->model('post_model', 'posts');
			$children = $this->posts->get_from_parent($id);
		}

		$restrict_child = $current->restrict_child_type;
		
		$text = $this->_parse_node($current, $text, $node_type);
		$text = $this->_parse_children($text, $optional, $subtree, $restrict_child);
		
		return $text;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Parse a tree node
	 *
	 * @access	public
	 * @param	node data
	 * @param	text to parse
	 * @param	node type
	 */
	function _parse_node($current, $text, $node_type, $prefix = '')
	{
		// The tag controls the node type
		if ($current->node_type != $node_type)
		{
			die('node type missmatch');
		}

		// Meta Data for the parent
		foreach($this->node_meta as $meta_key)
		{
			$tmp_key = $prefix.$meta_key;
			$tmp_val = $current->$meta_key;
			
			$text = $this->CI->parser->_parse_single($tmp_key, (string)$tmp_val, $text);
		}
		return $text;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Parse the child tree
	 *
	 * @access	public
	 * @param	the text to parse
	 * @param	tag parameters
	 * @param	child tree
	 * @param	restrict child type
	 */
	function _parse_children($text, $optional, $tree, $restrict_type = FALSE)
	{
		// An array of data based on child type
		$variable = array();
		
		foreach($tree as $kid)
		{
			$prefix = substr($kid->node_type, 0, 1).':';
			
			$tmp = array();
			foreach($this->node_meta as $meta_key)
			{
				$tmp[$prefix.$meta_key] = $kid->$meta_key;
			}
			
			$variable[$kid->node_type.'s'][] = $tmp;
		}
		
		foreach($variable as $var => $data)
		{
			$text = $this->CI->parser->_add_db($var, $data, $text);
		}

		// Cleanup child tags
		$pattern = '#'.T_OPEN.'(forums|threads)'.T_CLOSE.'(.+?)'.T_OPEN.'/\\1'.T_CLOSE.'#is';
		$text = preg_replace_callback($pattern, array($this, '_clean_children'), $text);
		
		// Cleanup 'empty' tags
		$pattern = '#'.T_OPEN.'(f:empty|t:empty)'.T_CLOSE.'(.+?)'.T_OPEN.'/empty'.T_CLOSE.'#is';
		return preg_replace($pattern, '', $text);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Cleans up the child tags
	 *
	 * @access	private
	 */
	function _clean_children($matches)
	{
		$prefix = substr($matches[1], 0 , 1);

		if (preg_match('#' . T_OPEN . $prefix . ':empty' . T_CLOSE . '(.+?)' . T_OPEN .'/empty' . T_CLOSE . '#is', $matches[2], $match))
		{
			return $match[1];
		}

		return '';
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Parse the parent chain
	 *
	 * @access	public
	 * @param	the text to parse
	 * @param	current node
	 */
	function _parse_parents($text, $current)
	{
		// Only do this if the template has parent tags
		if ( ! strpos($text, T_OPEN.'parents'))
		{
			return $text;
		}
		
		$ancestors = $this->CI->tree->get_ancestors($current->node_id);
		
		// Root node is always returned - remove it
		$ancestors = array_slice($ancestors, 0, -1);

		// Anything left?
		if (count($ancestors) < 1)
		{
			// Clean up
			$pattern = '#'.T_OPEN.'parents'.T_CLOSE.'(.+?)'.T_OPEN.'/parents'.T_CLOSE.'#is';
			return preg_replace($pattern, '', $text);
		}

		$parents = array();
		
		foreach($ancestors as $parent)
		{
			$prefix = substr($parent->node_type, 0, 1).':';
			
			$tmp = array();
			foreach($this->node_meta as $meta_key)
			{
				$tmp['p:'.$meta_key] = $parent->$meta_key;
			}
			
			$parents[] = $tmp;
		}

		// Add it to our db cache
		$text = $this->CI->parser->_add_db('parents', $parents, $text);
		return $text;
	}
}

// END Pairs class


/* End of file pairs.php */
/* Location: ./application/libraries/parsers/pairs.php */