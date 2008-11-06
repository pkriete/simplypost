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
 * Tree Model
 *
 * @package		SimplyPost
 * @subpackage	Models
 * @category	Forum Hierarchy
 * @author		Pascal Kriete
 */
class Tree_model extends Model {

	var $table	= 'forum_tree';
	var $c_table = 'forum_tree_meta';

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function Tree_model()
	{
		parent::Model();
	}
	
	// --------------------------------------------------------------------

	/**
	 * Gets the full tree(s)
	 *
	 * @access	private
	 * @return	mixed
	 */
	function get_tree()
	{
		$this->db->join($this->c_table, "{$this->c_table}.meta_id = {$this->table}.node_id", 'inner');
		$i = $this->db->get($this->table);
		return $var = ($i->num_rows() > 0) ? $i->result() : FALSE;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Gets all nodes of a given type
	 *
	 * @access	private
	 * @return	mixed
	 */
	function get_all_by_node_type($type)
	{
		$this->db->where('node_type', $type);
		$this->db->join($this->c_table, "{$this->c_table}.meta_id = {$this->table}.node_id", 'inner');
		$i = $this->db->get($this->table);
		return $var = ($i->num_rows() > 0) ? $i->result() : FALSE;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Gets a single node
	 *
	 * @access	private
	 * @return	mixed
	 */
	function get_node($id)
	{
		$this->db->where('node.id', $id);
		$this->db->join($this->c_table, "{$this->c_table}.meta_id = {$this->table}.node_id", 'inner');
		$i = $this->db->get($this->table, 1, 0);
		return $var = ($i->num_rows() > 0) ? $i->row() : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Gets all node ancestors
	 *
	 * @access	private
	 * @return	mixed
	 */
	function get_ancestors($node_id)
	{
		$this->db->select("t2.*, {$this->c_table}.*");
		$this->db->from($this->table.' t1, '.$this->table.' t2');
		$this->db->where('t1.node_id', $node_id);

		$this->db->where("POSITION(t2.materialized_path IN t1.materialized_path) = 1", NULL, FALSE);
		
		$this->db->join($this->c_table, "{$this->c_table}.meta_id = t2.node_id", 'inner');

		$this->db->order_by('t2.materialized_path', 'ASC');
		$i = $this->db->get();

		return $var = ($i->num_rows() > 0) ? $i->result() : FALSE;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Gets node subtree
	 *
	 * @access	private
	 * @return	mixed
	 */
	function get_subtree($node_id, $depth = 0, $include = FALSE)
	{
		$begin = $include ? '%' : '%.';
		$this->db->where("materialized_path LIKE CONCAT((SELECT materialized_path from {$this->table} where node_id = {$node_id}),'{$begin}')");
		
		if ($depth > 0)
		{
			$stop = '%';
			for($i = 0; $i < $depth; $i++)
			{
				$stop .= '.%';
			}
			$stop .= '.';
			
			$this->db->where("materialized_path NOT LIKE CONCAT((SELECT materialized_path from {$this->table} where node_id = {$node_id}),'{$stop}')");
		}

		$this->db->join($this->c_table, "{$this->c_table}.meta_id = {$this->table}.node_id", 'inner');
		$this->db->order_by($this->table.'.materialized_path', 'ASC');
		$i = $this->db->get($this->table);

		return $var = ($i->num_rows() > 0) ? $i->result() : FALSE;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Insert Node
	 *
	 * @access	private
	 * @return	mixed
	 */
	function insert($data, $parent_path = FALSE, $node_type = 'root')
	{
		$allowed	= array('user_id', 'title', 'url_title', 'description');
		$data		= filter_input_data($allowed, $data);
				
		$this->db->insert($this->c_table, $data);
		$node_id = $this->db->insert_id();
		
		$path = ($parent_path) ? $parent_path.'.'.$node_id.'.' : $node_id.'.';
		
		$t_data = array(
					'materialized_path' => $path,
					'node_id'			=> $node_id,
					'node_type'			=> $node_type
		);
		$this->db->insert($this->table, $t_data);
		
		return $node_id;
	}

}
// END Tree_model class

/* End of file tree_model.php */
/* Location: ./application/models/tree_model.php */