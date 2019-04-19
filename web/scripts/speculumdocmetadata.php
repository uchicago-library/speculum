<?php

class xDigitalDocument {
	var $sNum;
	var $sSubj;
	var $sInscrip;
	var $sEngrv;
	var $sCity;
	var $sPub;
	var $sDate;
	var $sSize;
	var $sRef;
}

function characterDataDoc($parser, $data) {
	global $curTag;
	global $arManuscripts;
	global $manuscriptCount;	
	if ($curTag == "^RECORDS^RECORD^NUM") {
		$manuscriptCount++;
		$arManuscripts[$manuscriptCount] = new xDigitalDocument();
		$arManuscripts[$manuscriptCount]->sNum = $data;
	}
	elseif ($curTag == "^RECORDS^RECORD^SUBJ") {
		$arManuscripts[$manuscriptCount]->sSubj = $data;
	}
	elseif ($curTag == "^RECORDS^RECORD^INSCRIP") {
		$arManuscripts[$manuscriptCount]->sInscrip = $data;
	}
	elseif ($curTag == "^RECORDS^RECORD^ENGRV") {
		$arManuscripts[$manuscriptCount]->sEngrv = $data;
	}
	elseif ($curTag == "^RECORDS^RECORD^CITY") {
		$arManuscripts[$manuscriptCount]->sCity = $data;
	}
	elseif ($curTag == "^RECORDS^RECORD^PUB") {
		$arManuscripts[$manuscriptCount]->sPub = $data;
	}
	elseif ($curTag == "^RECORDS^RECORD^DATE") {
		$arManuscripts[$manuscriptCount]->sDate = $data;
	}
	elseif ($curTag == "^RECORDS^RECORD^SIZE") {
		$arManuscripts[$manuscriptCount]->sSize = $data;
	}
	elseif ($curTag == "^RECORDS^RECORD^REF") {
		$arManuscripts[$manuscriptCount]->sRef = $data;
	}
}

/*
 * importXMLDoc() attempts to open an XML file of item-level data. If the XML file
 * doesn't exist it bails out. If it does exist it makes no attempt
 * to validate the file in anyway, but it does allow the script to 
 * continue.
 *
 * DO NOT PASS THIS FUNCTION UN-CHECKED GET DATA.
 */
function importXMLDoc() {
	global $arManuscripts;
	global $manuscriptCount;

	$arManuscripts = array();
	$manuscriptCount = 0;

	$xml_parser = xml_parser_create();
	xml_set_element_handler($xml_parser, "startElement", "endElement");
	xml_set_character_data_handler($xml_parser, "characterDataDoc");
	if (!($fp = fopen("../metadata/speculum.xml","r"))) {
		die("could not open XML file for input");
	}
	while ($data = fread($fp,4096)) {
		if (!xml_parse($xml_parser, $data, feof($fp))) {
			die(sprintf("XML error: %s at line %d", xml_error_string(xml_get_error_code($xml_parser)),xml_get_current_line_number($xml_parser)));
		}
	}
	xml_parser_free($xml_parser);
}

function numsort($a, $b) {
	return (get_doc2($a->sNum) > get_doc2($b->sNum));
}

/*
 * loadCurrentDoc($doc) creates a new global variable to store metadata
 * for the current digital object. If the current digital object isn't 
 * present in the document, die. 
 */
function loadCurrentDoc($doc) {
	global $arManuscripts;
	global $currentManuscript;
	
	$currentManuscript = new xDigitalDocument();

	usort($arManuscripts, "numsort");
	foreach ($arManuscripts as $m) {
		if ($m->sNum == doc_to_speculum_number($doc)) {
			$currentManuscript = new xDigitalDocument();
			$currentManuscript = $m;
		}
	}
	if (!isset($currentManuscript)) {
		die();
	}
}

/*
 * helper function for loadCurrentDoc. This takes the 4 character long
 * string of integers that is encoded in a url parameter and translates
 * it into a speculum number. (no leading zeros, with appropriate leading
 * letter.)
 */
function doc_to_speculum_number($doc) {
	$num = ltrim($doc, '0');
	if ((int)$num <= 179) {
		$num = 'A' . $num;
	} else if ((int)$num <= 383) {
		$num = 'B' . $num;
	} else if ((int)$num <= 991) {
		$num = 'C' . $num;
	} else if ((int)$num <= 994) {
		$num = 'D' . $num;
	}
	return $num;
}

/*
 * MAIN.
 */

importXMLDoc();
/* in gms page turner I loadCurrentDoc() here. But since this 
 * version of the pageturner uses two separate windows, it makes more
 * sense to load it elsewhere. (also, there is no single 'doc' anymore.)
 */

?>
