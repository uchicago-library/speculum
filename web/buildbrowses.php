<?php

ini_set('error_reporting', E_ALL | E_STRICT);
ini_set('log_errors', 'On');

require('indexdefs.php');

$domIN = new DOMDocument();
$domIN->load('metadata/speculum.xml');

$browses = array(
	'number' => '@refid',
	'huelsen' => 'huref',
	'date' => 'dateSet/display',
	'engraver' => 'agentSet/agent[role="engraver (printmaker)" or role="Engrager (printmaker)"]/name',
	'city' => 'locationSet/location[@type="creation"]/name',
    'collection' => '',
    'printmaker' => 'agentSet/agent[role="engraver (printmaker)" or role="Engrager (printmaker)"]/name | agentSet/agent[role="etcher (printmaker)" or role="Etcher (printmaker)"]/name',
	'publisher' => 'agentSet/agent[role="publisher" or role="Publisher"]/name',
	'subject' => 'subjectSet/subject/term',
);

/*
 * LOAD 'BROWSE' PARAMETER.
 */

$clean = array();
$clean['browse'] = '';
if (isset($_GET['browse'])) {
	if (in_array($_GET['browse'], array_keys($browses))) {
		$clean['browse'] = $_GET['browse'];
	}
}

if ($clean['browse'] == '') {
	print '<html><body>';
	print '<h1>Browse Builder</h1>';
	print '<p>browse param options:</p>';
	foreach (array_keys($browses) as $b) {
		print '<p>' . $b . '</p>';
	}
	die();
}

if ($clean['browse'] == 'collection') {
    $r = collectionBrowse($domIN);
} else {
    $r = nodeBrowse($domIN, $clean['browse'], $browses[$clean['browse']]);
}
echo $r->saveXML();
?>

