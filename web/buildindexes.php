<?php

ini_set('error_reporting', E_ALL | E_STRICT);
ini_set('log_errors', 'On');

set_time_limit(60);

require('indexdefs.php');

$indexes = array(
	'agent' => 'agentSet/agent/name',
	'all' => '*|@refid',
	'city' => 'locationSet/location[@type="creation"]/name',
	'date' => 'dateSet/display',
	'engraver' => 'agentSet/agent[role="engraver (printmaker)" or role="Engrager (printmaker)"]/name',
	'huelsen' => 'huref',
	'inscription' => 'inscriptionSet/inscription',
	'number' => '@refid',
    'printmaker' => 'agentSet/agent[role="engraver (printmaker)" or role="Engrager (printmaker)"]/name | agentSet/agent[role="etcher (printmaker)" or role="Etcher (printmaker)"]/name',
	'publisher' => 'agentSet/agent[role="publisher" or role="Publisher"]/name',
	'subject' => 'subjectSet/subject/term',
	'title' => 'titleSet/title',
);

/* 
 * LOAD 'INDEX' PARAMETER.
 */

$clean = array();
$clean['index'] = '';
if (isset($_GET['index'])) {
	if (in_array($_GET['index'], array_keys($indexes))) {
		$clean['index'] = $_GET['index'];
	}
}

if ($clean['index'] == '') {
	print '<html><body>';
	print '<h1>Index Builder</h1>';
	print '<p>index param options:</p>';
	foreach (array_keys($indexes) as $i) {
		print '<p>' . $i . '</p>';
	}
	die();
}

$domIN = new DOMDocument();
$domIN->load('metadata/speculum.xml');

if ($clean['index'] == 'date') {
	$r = buildDateIndex($domIN);
} else {
	$r = buildIndex($domIN, $indexes[$clean['index']]);
}
printIndex($r);

?>
