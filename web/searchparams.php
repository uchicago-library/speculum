<?php

/*
 * NOTES:
 * Make sure that the data file is sorted in Chicago Speculum number
 * order. The script assumes this order so it runs faster with 
 * common queries.
 *
 * speculum.xml must contain exactly one work per speculum
 * print. 
 *
 * Two of the browses aren't really browses, they're sorts.
 * The Chicago Number browse displays every document, sorted by 
 * our numbering system. The Huelsen sort is by their numbering
 * system. 
 */

ini_set('error_reporting', E_ALL | E_STRICT);
ini_set('display_errors', 'Off');
ini_set('log_errors', 'On');

require_once('searchdefs.php');

/*
 * INPUT VALIDATION
 */

$clean = array();

/*
 * BROWSE
 */

$clean['browse'] = '';
if (isset($_GET['browse'])) {
	switch ($_GET['browse']) {
		case 'city':
		case 'collection':
		case 'date':
		case 'engraver':
		case 'printmaker':
		case 'publisher':
		case 'subject':
			$clean['browse'] = $_GET['browse'];
			break;
		default:
			break;
	}
}

/*
 * DEBUG
 */

$clean['debug'] = 'off';
if (isset($_GET['debug'])) {
	if ($_GET['debug'] == 'on')
		$clean['debug'] = 'on';
}

/*
 * DETAILS. match speculum-[0-9]{4}.
 */

$clean['detail'] = '';
if (isset($_GET['detail'])) {
	if (preg_match('/^speculum-[0-9]{4}$/', $_GET['detail']) == 1 ||
      preg_match('/^[A-Z][0-9]{1,3}-[A-Z][0-9]{1,3}$/', $_GET['detail']) == 1) {
		$clean['detail'] = $_GET['detail'];
	}
}

/*
 * GROUP 
 */

$clean['group'] = '';
if (isset($_GET['group']) && ctype_digit($_GET['group'])) {
	$clean['group'] = $_GET['group'];
}

/*
 * LIST
 */

$clean['list'] = '';
if (isset($_GET['list'])) {
	switch($_GET['list']) {
		case 'huelsen':
		case 'number':
			$clean['list'] = $_GET['list'];
			break;
		default:
			break;
	}
}

/*
 * PAGE 
 */

$clean['page'] = '';
if (isset($_GET['page']) && ctype_digit($_GET['page'])) {
	$clean['page'] = $_GET['page'];
}

/*
 * RESULT 
 */

$clean['result'] = '';
if (isset($_GET['result']) && ctype_digit($_GET['result'])) {
	$clean['result'] = $_GET['result'];
}

/*
 * SEARCH
 */

function cleanSearch($get) {
	return replace_accents($get);
}
$clean['search'] = array();
$clean['search'][0] = '';
$clean['search'][1] = '';
$clean['search'][2] = '';
if (array_key_exists('search', $_GET)) {
	foreach ($_GET['search'] as $key => $value ) {
		$s = cleanSearch($value);
		if ($s != '') {
			$clean['search'][$key] = $s;
		}
	}
}

/*
 * SEARCHNODE
 */

function cleanSearchNode($get) {
	switch ($get) {
		case 'agent':
		case 'all':
		case 'city':
		case 'date':
		case 'engraver':
		case 'huelsen':
		case 'inscription':
		case 'number':
		case 'printmaker':
		case 'publisher':
		case 'subject':
		case 'title':
			return $get;
			break;
		default:
			return '';
			break;
	}
	
}
$clean['searchnode'] = array();
if (array_key_exists('searchnode', $_GET)) {
	foreach ($_GET['searchnode'] as $key => $value) {
		$s = cleanSearchNode($value);
		if ($s != '') {
			$clean['searchnode'][$key] = $s;
		}
	}
}

/*
 * SEARCHBOOLEAN
 */

function cleanSearchBoolean($get) {
	switch ($get) {
		case 'and':
		case 'or':
			return $get;
			break;
		default:
			return '';
			break;
	}
}
$clean['searchboolean'] = array();
$clean['searchboolean'][1] = 'and';
if (array_key_exists('searchboolean', $_GET)) {
	foreach ($_GET['searchboolean'] as $key => $value) {
		$s = cleanSearchBoolean($value);
		if ($s != '') {
			$clean['searchboolean'][$key] = $s;
		}
	}
}
$clean['searchboolean'][0] = 'or';

/*
 * EXTRA LOGIC FOR NUMBER SEARCHES. TRANSLATES '1' INTO 'A1'.
 */

$i = 0;
while ($i < 3) {
	if (isset($clean['searchnode'][$i]) && isset($clean['search'][$i]) && $clean['searchnode'][$i] == 'number') {
		if (preg_match('/^[0-9]*$/', $clean['search'][$i]) > 0) {
			$clean['search'][$i] = getChicagoNumber($clean['search'][$i]);
		}
	}
	$i = $i + 1;
}
?>
