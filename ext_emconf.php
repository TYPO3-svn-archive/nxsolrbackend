<?php

########################################################################
# Extension Manager/Repository config file for ext "nxsolrbackend".
#
# Auto generated 09-02-2010 09:53
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Extbase Solr Storage Backend',
	'description' => 'A storage backend that enables extbase extensions to use a Apache Solr server for object retrieval.',
	'category' => 'misc',
	'author' => 'Lienhart Woitok',
	'author_email' => 'lienhart.woitok@netlogix.de',
	'shy' => '',
	'dependencies' => 'extbase',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'alpha',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 1,
	'lockType' => '',
	'author_company' => '',
	'version' => '0.1.0',
	'constraints' => array(
		'depends' => array(
			'extbase' => '1.3.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:5:{s:9:"ChangeLog";s:4:"4d62";s:10:"README.txt";s:4:"ee2d";s:12:"ext_icon.gif";s:4:"1bdc";s:19:"doc/wizard_form.dat";s:4:"f527";s:20:"doc/wizard_form.html";s:4:"31bc";}',
);

?>