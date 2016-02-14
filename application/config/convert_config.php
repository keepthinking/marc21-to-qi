<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('memory_limit','1024M');

// File to use
$config['xmlfile'] = APPPATH . "data/data.xml";

// Mapping control fields
$config['controlfields'] = array(
	'001' => array('type' => 'text', 'name' => 'marc_control_number'),
	'003' => array('type' => 'text', 'name' => 'marc_control_number_identifier'),
	'005' => array('type' => 'text', 'name' => 'marc_latest_transaction'),
	'008' => array('type' => 'text', 'name' => 'marc_fixed_data')
);

// Mapping for data fields
$config['datafields'] = array(
    '020' => array('type' => 'field', 
    	'config' => array(
        	'a' => array('type' => 'text', 'name' => 'isbn_number')
        ),
    ),
    '022' => array('type' => 'field', 
    	'config' => array(
            'a' => array('type' => 'text', 'name' => 'issn_number')
        ),
    ),
    '040' => array('type' => 'field', 
    	'config' => array(
    		'a' => array('type' => 'list', 'name' => 'catalogue_source_id', 'table' => 'catalogue_source', 'table_type' => 'list')
    	),
    ),
    '041' => array('type' => 'field', 
    	'config' => array(
    		'a' => array('type' => 'list', 'name' => 'language_id', 'table' => 'language', 'table_type' => 'list')
    	),
    ),
    '080' => array('type' => 'field', 
    	'config' => array(
    		'a' => array('type' => 'text', 'name' => 'ucdn')
    	),
    ),
    '100' => array('type' => 'xref', 'table' => 'actor_bibliography_xrefs', 'relationship' => 'author_bibliography',
    	'config' => array(
            'a' => array('type' => 'list', 'name' => 'actor_id', 'table' => 'actor', 'table_type' => 'content',
                'static_fields' => array('actor_type_id' => '30')
            ),
            '4' => array('type' => 'list', 'name' => 'actor_qualifier_id', 'table' => 'actor_qualifier', 'table_type' => 'list'),
        )
    ),
    '110' => array('type' => 'xrefs', 'table' => 'actor_bibliography_xrefs', 'relationship' => 'bibliography_corporate',
    	'config' => array(
            'a' => array('type' => 'list', 'name' => 'actor_id', 'table' => 'actor', 'table_type' => 'content',
                'static_fields' => array('actor_type_id' => '601')
            ),
            '4' => array('type' => 'text', 'name' => 'actor_qualifier_id', 'table' => 'actor_qualifier', 'table_type' => 'list')
        )
    ),
    '245' => array('type' => 'field', 
   		'config' => array(
            'a' => array('type' => 'text', 'name' => array('name', 'title')),
            'b' => array('type' => 'text', 'name' => 'title_remainder'),
            'c' => array('type' => 'text', 'name' => 'title_responsibility')
        )
    ),
    '246' => array('type' => 'field', 
   		'config' => array(
            'a' => array('type' => 'text', 'name' => 'title_alt'),
        )
   	),
    '250' => array('type' => 'field', // '250' => 'edition',
   		'config' => array(
            'a' => array('type' => 'text', 'name' => 'edition'),
        )
   	),   
    '260' => array('type' => 'field', // '260' => 'publication',
   		'config' => array(
            'a' => array('type' => 'text', 'name' => 'published_place'),
            'b' => array('type' => 'text', 'name' => 'publisher'),
            'c' => array('type' => 'text', 'name' => 'published_date'),
        )
   	), // '300' => 'description',
    '300' => array('type' => 'field', // '300' => 'description',
   		'config' => array(
            'a' => array('type' => 'text', 'name' => 'description_extent'),
            'b' => array('type' => 'text', 'name' => 'description_other_detail'),
            'c' => array('type' => 'text', 'name' => 'description_dimension'),
        )
   	),    
    '340' => array('type' => 'field', // '340' => 'physical medium',
   		'config' => array(
            'a' => array('type' => 'list', 'name' => 'bibliography_material_base_id', 'table' => 'bibliography_material_base', 'table_type' => 'list'),
        )
   	),
    '490' => array('type' => 'field', // '490' => 'series',
   		'config' => array(
            'a' => array('type' => 'text', 'name' => 'series_statement'),
            'v' => array('type' => 'text', 'name' => 'series_volume'),
        )
   	),    
    '500' => array('type' => 'field', 
   		'config' => array(
            'a' => array('type' => 'text', 'name' => 'general_note'),
        )
   	),
    '525' => array('type' => 'field', 
   		'config' => array(
            'a' => array('type' => 'text', 'name' => 'supplement_note'),
        )
   	),    
    '543' => array('type' => 'field', 
   		'config' => array(
            'a' => array('type' => 'text', 'name' => 'original_version_note'),
        )
   	), 
    '650' => array('type' => 'xref', 'table' => 'bibliography_thesaurus_term_xrefs', 'relationship' => 'bibliography_topical_term', 
    	'config' => array(
            'a' => array('type' => 'list', 'name' => 'thesaurus_term_id', 'table' => 'thesaurus_term', 'table_type' => 'list', 
            	'static_fields' => array('thesaurus_facet_id' => '777'),
                'extra_fields' => array('2' => 'marc_code')
            ),
        )
    ),
    '651' => array('type' => 'xref', 'table' => 'bibliography_thesaurus_term_xrefs', 'relationship' => 'bibliography_geographic_term', 
    	'config' => array(
            'a' => array('type' => 'list', 'name' => 'thesaurus_term_id', 'table' => 'thesaurus_term', 'table_type' => 'list', 
            	'static_fields' => array('thesaurus_facet_id' => '778'),
                'extra_fields' => array('2' => 'marc_code')
            ),
        )
    ),
    '653' => array('type' => 'xref', 'table' => 'bibliography_thesaurus_term_xrefs', 'relationship' => 'bibliography_uncontrolled_term', 
    	'split' => array(
    		'code' => 'a', 
    		'separator' => ';'
    	), 
    	'config' => array(
            'a' => array('type' => 'list', 'name' => 'thesaurus_term_id', 'table' => 'thesaurus_term', 'table_type' => 'list', 
            	'static_fields' => array('thesaurus_facet_id' => '779')
            ),
        )
    ),
    '655' => array('type' => 'xref', 'table' => 'bibliography_thesaurus_term_xrefs', 'relationship' => 'bibliography_genre_term', 
    	'config' => array(
            'a' => array('type' => 'list', 'name' => 'thesaurus_term_id', 'table' => 'thesaurus_term', 'table_type' => 'list', 
            	'static_fields' => array('thesaurus_facet_id' => '780')
            ),
        )
    ),
    '700' => array('type' => 'xref', 'table' => 'actor_bibliography_xrefs', 'relationship' => 'person_bibliography',
        'config' => array(
            'a' => array('type' => 'list', 'name' => 'actor_id', 'table' => 'actor', 'table_type' => 'content',
                'static_fields' => array('actor_type_id' => '30'),
                'extra_fields' => array('0' => 'marc_code', )
            ),
        )
    ),
    // Location
    '852' => array('type' => 'field', 
        'config' => array(
            'a' => array('type' => 'list', 'name' => 'bibliography_location_collection_id', 'table' => 'bibliography_location_collection', 'table_type' => 'list'),
            'b' => array('type' => 'list', 'name' => 'bibliography_location_section_id', 'table' => 'bibliography_location_section', 'table_type' => 'list'),
            'c' => array('type' => 'text', 'name' => 'location_shelf'),
            'z' => array('type' => 'text', 'name' => 'location_public_note'),
            'x' => array('type' => 'text', 'name' => 'location_non_public_note'),
            'p' => array('type' => 'text', 'name' => 'location_piece_designation'),
       ),
    ),
    // Conversion
    '884' => array('type' => 'field', 
        'config' => array(
            'a' => array('type' => 'text', 'name' => 'marc_conversion_process'),
            'g' => array('type' => 'text', 'name' => 'marc_conversion_date'),
            'k' => array('type' => 'text', 'name' => 'marc_source_indentifier'),
            'q' => array('type' => 'text', 'name' => 'marc_conversion_agency'),
       ),
    ),
);