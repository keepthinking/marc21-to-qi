# marc21-to-qi
Conversion for MARC21 XML to [Qi CMS](http://www.qi-cms.com) - Uses [CodeIgniter 3.0.1](http://www.codeigniter.com)

Purpose
----------
This is a tool to convert MARC21 files into the Qi CMS (http://www.qi-cms.com). It requires all of the following.

 - A Qi CMS database with the required tables and fields (see the [configuration file](application/config/convert_config.php))
 - A MARC21 XML file (location configurable in the [same file](application/config/convert_config.php))

Due to XML limitation in node names, all nodes name should have 'marc:' removed prior to running the script.

Configuration
----------------
The following files may require modification and configuration.

 - [application/config/config_cms.php](application/config/config_cms.php) is where the source file is specified and where all the mapping is done - which should be self-explanatory
 - [application/config/database.php](application/config/database.php) is where database parameters need to be configured

Usage
-------
The tool can only be used via command line.

 - <code>php index.php</code> runs the script
 - <code>php index.php convert index true</code> runs the script after truncating the required tables
 - <code>php index.php convert truncate</code> only truncates the tables

Warning
-------
- The tool will not create tables and fields if they don't exist not it has error control. So it's the user's responsibility to check that the configuration is correct before running the script.
- As with anything making changes to a database, this tool __may corrupt your data__. Do make sure __you make a full backup__ before any use. 
- Keepthinking will not accept any responsibility for misues or data loss.

Consulting
----------
If you need any help configuring or using this tool, please [contact us](mailto:info@keepthinking.it).

Copyright
------------
Copyright Keepthinking 2015 - [www.keepthinking.it](http://www.keepthinking.it)
