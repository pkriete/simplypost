<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tree_iterator {

	var $tree;

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function Tree_iterator($options = array())
	{
		// constructor
	}
	
	// --------------------------------------------------------------------

	/**
	 * Initialize the class with a new tree
	 *
	 * @access	public
	 * @param	mixed	linear tree array
	 */
	function initialize($tree)
	{
		$root_depth = explode('.', $tree[0]->materialized_path);
		$root_depth = count($root_depth);
		
		$this->tree = $tree;
		$this->tree = $this->_recurse_tree($root_depth);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Get the parsed tree
	 *
	 * @access	public
	 */
	function get_tree()
	{
		return $this->tree;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Run through the linear array and convert to a nested structure
	 *
	 * @access	private
	 * @param	int	initial depth
	 * @param	mixed	internal recursion tracker
	 * @return	mixed	final nested array
	 */
	function _recurse_tree($depth, $_sub_tree = array())
	{
		// Current node
		$current = array_shift($this->tree);

		// End of tree
		if ( ! $current)
		{
			return $_sub_tree;
		}

		// Track depth changes
		$new_depth = count(explode('.', $current->materialized_path));

		// Relative depth
		$rel_depth = $new_depth - $depth;

		// On the way out, need to keep it on the stack
		if ($rel_depth < 0)
		{
			array_unshift($this->tree, $current);
			return $_sub_tree;
		}

		if ($rel_depth > 0)
		{
			// Create a new subtree and add the first child
			$new_subtree = array();
			$new_subtree[] = $current;

			// Recurse and add the whole lot to the last element of this subtree
			$latest = end($_sub_tree);
			$latest->children = $this->_recurse_tree($new_depth, $new_subtree);
		}
		else if ($rel_depth == 0)
		{
			// Add the node
			$_sub_tree[] = $current;
		}
		
		// Next node
		return $this->_recurse_tree($depth, $_sub_tree);
	}

}

// END Tree_Iterator class


/* End of file Tree_Iterator.php */
/* Location: ./application/libraries/Tree_Iterator.php */