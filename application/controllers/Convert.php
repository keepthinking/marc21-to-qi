<?php
/**
 * @package	   Convert MARC21 to Qi CMS
 * @author	   Keepthinking
 * @copyright  Copyright (c) 2015, Keepthinking (http://keepthinking.it/)
 * @license	   http://opensource.org/licenses/MIT	MIT License
 * @link	   http://keepthinking.it
 * @since	   Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Convert Controller Class
 *
 * This is a single controller class, where the entire process in managed
 * through the index function
 *
 * @package		Convert MARC21 to Qi CMS
 * @subpackage	Controllers
 * @category	Controllers
 * @author		Keepthinking
 * @link		http://keepthinking.it
 */
class Convert extends CI_Controller {
	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		parent::__construct();
		if(!$this->input->is_cli_request())
			exit("Only available via command line");
		$this->load->model('m_convert', 'model');
	}

	// --------------------------------------------------------------------

	/**
	 * Single function to run the conversion
	 * It expects a configuration in config_convet and a MARC21 XML file
	 * The MARC21 file needs 'marc:' removed from the node names throughout
	 *
	 * @param    bool $truncate Whether to truncate the database tables
	 * @return   void
	 */
	public function index($truncate = false)
	{
		// Configuration
		$xmlfile = config('xmlfile');
		$controlfields_mapping = config('controlfields');
		$datafields_mapping = config('datafields');
		$valid_data_tags = array_keys($datafields_mapping);

		// Initialise objects
		$reader = new XMLReader();
		$reader->open($xmlfile);

		// Counter to limit records for testing purposes - uncomment if limited run is required
		// $i = 0;
		// echo "<pre>";

		// Truncate tables if required
		if($truncate)
			$this->truncate();

		// Skip to the first record - note that the 'marc:record' nodes need to be renamed to just 'record'
		// Do a global search/replace for 'marc:' on the source XML file
		while ($reader->read() && $reader->name !== 'record');

		// Start reading records
		while ($reader->name === 'record')
		{
			// Create the result
		    $result = (object)array();

		    // Get the current node as SimpleXMLElement
		    $node = new SimpleXMLElement($reader->readOuterXML());

		    // Leader field
		    $result->leader = $node->children()->leader->__toString();

		    // Control fields
		    $controlfields = $node->children()->controlfield;
		    if($controlfields)
		    {
		        $result->controlfields = array();
		        foreach($controlfields as $field)
		        {
		            $tag = '' . $field->attributes()->tag;
		            $result->controlfields[$tag] = $field->__toString();
		        }
		    }

		    // Data fields
		    $datafields = $node->children()->datafield;
		    if($datafields)
		    {
		        $result->datafields = array();
		        foreach($datafields as $field)
		        {
		            $tag = '' . $field->attributes()->tag;
		            if(!in_array($tag, $valid_data_tags))
		                continue;
		            if(!isset($result->datafields[$tag]))
			            $result->datafields[$tag] = array();

			        $values = array();
		            $subfields = $field->children();
		            foreach($subfields as $subfield)
		            {
		                $code = '' . $subfield->attributes()->code;
		                if($subfield->__toString())
		                    $values[$code] = trim($subfield->__toString());
		            }
                    $result->datafields[$tag][] = $values;
		        }
		    }

		    // Initialise the record to write
		    $record = array();

		    // Write to the database
		    // Control fields
		    foreach($controlfields_mapping as $tag => $field)
		    {
		    	$record[$field['name']] = $result->controlfields[$tag];
		    }

		    // Data fields (single values)
		    foreach($datafields_mapping as $tag => $field)
		    {
		    	if($field['type'] == 'field' && !empty($result->datafields[$tag]))
		    	{
		    		// For single fields we don't need (and cannot have) multiple values
   					$result->datafields[$tag] = array_shift($result->datafields[$tag]);
	    			foreach($field['config'] as $code => $config)
	    			{
	    				if(!empty($result->datafields[$tag][$code]))
	    				{
	    					$value = $result->datafields[$tag][$code];
	    					if($config['type'] == 'list')
    							$value = $this->model->upsert($value, $config['table'], $config['table_type']);
    						
    						// A value can be for more than one field
    						if(is_array($config['name']))
    						{
    							foreach($config['name'] as $field_name)
			    					$record[$field_name] = $value;
    						}
    						else
		    					$record[$config['name']] = $value;
	    				}
	    			}
		    	}
		    }

		    // Data fields (XREFS)
		    if(!empty($record))
		    {
		    	// Insert the record and get its ID back
			    $bibliography_id = $this->model->insert($record, 'bibliography');
			    echo "Progress: {$bibliography_id}            \r";
			    
			    // Write to the database - XREFS
			    foreach($datafields_mapping as $tag => $field)
			    {
			    	if($field['type'] == 'xref' && !empty($result->datafields[$tag]))
			    	{
			    		// Split values - these are fields that have multiple values in a string, with separators
			    		if(!empty($field['split']))
						{
	    					// Loop through records (as there may be more than one)
	    					foreach($result->datafields[$tag] as $node)
	    					{
				    			foreach($field['config'] as $code => $config)
				    			{
				    				if(!empty($node[$code]))
				    				{
				    					// Multiple values
			    						$values = explode($field['split']['separator'], $node[$code]);
									    $xref = array();
				    					foreach($values as $value)
				    					{
				    						$value = trim($value);
				    						if($value)
				    						{
												if($config['type'] == 'list')
												{
							    					if(!empty($config['static_fields']))
							    						$static_fields = $config['static_fields'];
							    					else
							    						$static_fields = array();
													$value = $this->model->upsert($value, $config['table'], $config['table_type'], $static_fields);
												}
						    					$xref[$config['name']] = $value;
						    					$xref['relationship'] = $field['relationship'];
						    					$xref['bibliography_id'] = $bibliography_id;
						    					$this->model->insert($xref, $field['table'], 'xref');
				    						}
				    					}
								    }
		    					}
			    			}
						}
						// Single Values		    			
				    	else
				    	{
	    					foreach($result->datafields[$tag] as $node)
	    					{
						    	$xref = array();
				    			foreach($field['config'] as $code => $config)
				    			{
				    				if(!empty($node[$code]))
				    				{
				    					$value = trim($node[$code]);
										if($config['type'] == 'list')
										{
					    					if(!empty($config['static_fields']))
					    						$static_fields = $config['static_fields'];
					    					else
					    						$static_fields = array();
											$value = $this->model->upsert($value, $config['table'], $config['table_type'], $static_fields);
										}
										$xref[$config['name']] = $value;
					    			}
			    				}
			    				if(!empty($xref))
			    				{
			    					$xref['relationship'] = $field['relationship'];
			    					$xref['bibliography_id'] = $bibliography_id;
			    					$this->model->insert($xref, $field['table'], 'xref');
			    				}
	    					}
	    				}
		    		}
			    }
			}

			// Uncomment if limited run required
		    // $i++;
		    // if($i > 3000)
		    //     break;
		    $reader->next('record');
		}
		$reader->close();
	    echo "\n";
	}

	// --------------------------------------------------------------------

	/**
	 * Truncate the database tables involved
	 *
	 * @return   void
	 */
	public function truncate()
	{
		$this->model->truncate();
	}
}
