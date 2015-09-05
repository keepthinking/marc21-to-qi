<?php
/**
 * @package	   Convert MARC21 to Qi CMS
 * @author	   Keepthinking
 * @copyright      Copyright (c) 2015, Keepthinking (http://keepthinking.it/)
 * @license	   http://opensource.org/licenses/MIT	MIT License
 * @link	   http://keepthinking.it
 * @since	   Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Convert Model Class
 *
 * Has methods to insert records and to insert/update list values
 *
 * @package        Convert MARC21 to Qi CMS
 * @subpackage	   Models
 * @category	   Models
 * @author	   Keepthinking
 * @link	   http://keepthinking.it
 */
class M_convert extends CI_Model {

	var $qi_fields;
	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->qi_fields = array(
			'content' => array('online' => 1, 'created_by_id' => 1, 'node_id' => 1, 'version_id' => 1),
			'list' => array('online' => 1, 'created_by_id' => 1, 'version_id' => 1),
			'xref' => array('online' => 1, 'created_by_id' => 1)
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Truncates tables if invoked
	 *
	 * @return	void
	 */
	public function truncate()
	{
		$this->db->truncate('actor');
		$this->db->truncate('bibliography');
		$this->db->truncate('actor_bibliography_xrefs');
		$this->db->truncate('bibliography_thesaurus_term_xrefs');
		$this->db->truncate('thesaurus_term');
	}

	// --------------------------------------------------------------------

	/**
	 * Inserts a record into the database
	 *
	 * @return	int ID of the inserted record
	 */
	public function insert($record, $table, $type = 'content')
	{
		$this->db->insert($table, array_merge($record, $this->qi_fields[$type]));
		return $this->db->insert_id();
	}

	// --------------------------------------------------------------------

	/**
	 * Inserts a record into the database checking if it exists first
	 *
	 * @return	int ID of the inserted or found record
	 */
	public function upsert($value, $table, $type = 'content', $static_fields = array())
	{
		// Check of the value exists
		$result = $this->db->select('id')->from($table)->where('name', $value)->get()->result();
		if($result)
			return $result[0]->id;
		$this->db->insert($table, array_merge(array('name' => $value), $this->qi_fields[$type], $static_fields));
		return $this->db->insert_id();
	}
}
