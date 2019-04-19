<?php

/*
 * MAIN PROGRAM FOR SPECULUM SEARCHING.
 * 
 * All data is cleaned up in searchparams.php.
 * That should be the only file that contains references to _GET. 
 * Everyplace else we should be pulling data from the 'clean' associative array.
 *
 * I use indexes and pre-built browses to speed up searching.
 * The files are in the 'indexes' and 'browses' directories.
 * The indexes are just associative arrays marshalled to disk. Each index
 * is an uppercased and accent mark-less version of different in different
 * nodes of the document, and the values are arrays of speculum doc numbers
 * like 'A1'. 'And' is implied between all search terms, and as of Nov. 2007 
 * I don't have exact searching with quotation marks set up. 
 *
 * The browses are just canned xml files, processed ahead of time, to speed 
 * things up.
 * 
 * The original metadata is located in the 'metadata' directory.
 *
 * To rebuild indexes and browses, visit 'buildindexes.php' and 'buildbrowses.php'.
 * (for security I've chmodded them to be readable by jej only- unset this
 * to visit them online.)
 *
 * last rev. 10/31/2007
 * jej
 */

ini_set('error_reporting', E_ALL | E_STRICT);
ini_set('log_errors', 'On');

$pagelength = 20;

/*
 * FUNCTION DEFINITIONS
 */

require_once('indexdefs.php');
require_once('htmlchunks.php');
require_once('shareddefs.php');
require_once('searchdefs.php');

/*
 * DATA CLEANING
 */

require('searchparams.php');

/* 
 * MAIN
 */

$starttime = getmicrotime();

/*
 * PROCESS DIFFERENTLY DEPENDING ON SEARCH OR BROWSE.
 * AFTER THIS THE DOMDOCUMENT MAY BE EITHER FINDAID/WORK OR BROWSE/GROUP FORMAT
 */

$browseName = '';
if (isBrowse($clean)) {
	$domIN = new DOMDocument();
	$domIN->load('metadata/speculum.xml');
	$domOUT = processBrowse($clean, $domIN);
	if ($clean['group'] != '') {
		$browseName = getBrowse2Title($domOUT, $clean['group']);
		$domOUT = prepBrowse2($domOUT, $clean['group']);
	}
} elseif (isSearch($clean) && !isDetail($clean)) {
	$domOUT = processSearch($clean);
} elseif (isDetail($clean)) {
	$domIN = new DOMDocument();
	$domIN->load('metadata/speculum.xml');
	$r = array();
	$r[] = getChicagoNumber($clean['detail']);
	$domOUT = buildFindingAid($r);
} else {
	$domOUT = emptyResultSet();
}

/*
 * BUILD SOME HTML TO AVOID WRITING WEIRD CODE IN XSLT...
 */

$modeswitcher = buildModeSwitcher($clean);
$titlestring = buildTitleString($clean, $domOUT, $browseName);
$resultspager = buildResultsPager($clean, $domOUT);

/*
 * APPLY XSLT STYLESHEETS AND DISPLAY
 */

$domXSLT = new DOMDocument();
if ($domXSLT->load('xslt/speculum.xslt') == False) {
	die("Problem with XSLT document.");
}

$proc = new XSLTProcessor();
$proc->setParameter('','mode',$clean['mode']);
$proc->setParameter('', 'modeswitcher', $modeswitcher);
$proc->setParameter('', 'resultspager', $resultspager);
$proc->setParameter('','page',$clean['page']);
$proc->setParameter('','pagelength',$pagelength); 
$proc->setParameter('','browse',$clean['browse']);
$proc->setParameter('','group',$clean['group']);
$proc->setParameter('','detail',$clean['detail']);
$proc->setParameter('','titlestring',$titlestring);

$proc->importStyleSheet($domXSLT);
echo $proc->transformToXML($domOUT);

?>
