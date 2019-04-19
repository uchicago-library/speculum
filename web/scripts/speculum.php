<?php

/*
 * utility functions for speculum scripts.
 * Run this script first, to make sure
 * URL parameters are ok.
 */

error_reporting(E_ALL);
//error_reporting(0);

$scriptURL = 'http://speculum.lib.uchicago.edu/scripts/view.php';

/*
 * load get parameters from url. 
 * the user must specify a numeric 'view1' parameter.
 * view1 is loaded into an array called 'view'. this is because at a certain
 * point this script was used with two viewers in the same page. 
 * because it's for gathering zooming instructions from researchers only,
 * i'm not going to clean it up that much. (famous last words.)
 */
function load_parameters() {
	global $view;
	$view = array();
	if ( isset($_GET['view1']) && ereg('^[0-9]{7}$', $_GET['view1']) ) {
		array_push($view, $_GET['view1']);
	} else {
		die('view1 must be a 7 digit number');
	}

	global $zoomifyX;
	$zoomifyX = '';
	if ( isset($_GET['x']) && ereg('^[-.0-9]*$', $_GET['x']) ) {
		$zoomifyX = $_GET['x'];
	} else {
		$zoomifyX = 0;
	}

	global $zoomifyY;
	$zoomifyY = '';
	if ( isset($_GET['y']) && ereg('^[-.0-9]*$', $_GET['y']) ) {
		$zoomifyY = $_GET['y'];
	} else {
		$zoomifyY = 0;
	}

	global $zoomifyZoom;
	$zoomifyZoom = '';
	if ( isset($_GET['zoom']) && ereg('^[0-9]*$', $_GET['zoom']) ) {
		$zoomifyZoom = $_GET['zoom'];
	} else {
		$zoomifyZoom = -1;
	}
}

/*
 * this is set up differently from gms, because there can be
 * two separate doc/obj pairs in each page. pull information 
 * from view array when needed. 
 */
function get_doc($view, $i) {
	return substr($view[$i],0,4);
}

function get_doc2 ($doc_string) {
	$num_string = substr($doc_string, 1);
	$num_string = sprintf("%04d", $num_string);
	return $num_string;
}

function get_obj($view, $i) {
	return substr($view[$i],4,3);
}

/*
 * Helper functions for importXML. see http://www.php.net/xml for more info.
 */

function startElement($parser, $name, $attrs) {
	global $curTag;
	$curTag .= "^$name";
}

function endElement($parser, $name) {
	global $curTag;
	$caret_pos = strrpos($curTag,'^');
	$curTag = substr($curTag,0,$caret_pos);
}

load_parameters();

?>
