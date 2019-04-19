<?php

function isBrowse($clean) {
	return ($clean['browse'] != '');
}

function isDetail($clean) {
	return ($clean['detail'] != '');
}

function isList($clean) {
	return ($clean['list'] != '');
}

function isSearch($clean) {
	return ($clean['search'] != '');
}

/*
 * buildFindingAid
 * 
 * array:
 * speculum numbers to build DOMDocument
 *
 * output:
 * DOMDocument:
 * /findaid/work
 *
 * notes:
 * The script spends a lot of time in here.
 */

function buildFindingAid($r) {
	$xml = new DOMDocument();
	$xml->load('metadata/speculum.xml');

	$xp = new DOMXPath($xml);
	$q = '/findaid/*';
	$nodeList = $xp->query($q);

	$dom = new DOMDocument();
	$root = $dom->createElement('findaid');	
	$dom->appendChild($root);
	foreach ($nodeList as $node) {
		$nodeid = $xp->query('@refid', $node);
		if ($nodeid->length != 1) 
			continue;
		$id = strtoupper($nodeid->item(0)->nodeValue);
		if (in_array($id, $r)) { 
			$newNode = $dom->importNode($node, true);
			$root->appendChild($newNode);
		}
	}
	return $dom;
}

/* 
 * Boolean logic for arrays of chicago numbers. 
 * input:
 * a string specifying what kind of boolean you'd like to do:
 * 'and' | 'or' | 'not'
 * array of chicago number arrays.
 * [['A1'], ['A1', 'A2', 'A3'], ['A2']]
 * 
 * output:
 * chicago number array.
 * ['A1', 'A2', 'A3']
 */

function chiBoolean($b, $xml) {
	// if xml is an empty array, return it.
	if (count($xml) == 0) {
		return $xml;
	}
	// if xml is an array with one element, return that element. (should
	// be another array.)
	if (count($xml) == 1) {
		if (is_array($xml[0])) {
			return $xml[0];
		} else {
			die('boolean error.');
		}
	}
	// test to be sure every element in xml is an array itself.
	foreach ($xml as $x) {
		if (!is_array($x)) {
			die('boolean error.');
		}
	}
	// do logic here. 
	$r = array();
	switch($b) {
		case 'AND':
			$r = call_user_func_array('array_intersect',$xml);
			break;
		case 'OR':
			$r = array_unique(call_user_func_array('array_merge',$xml));
			break;
		case 'NOT':
			$sub = chiBoolean('AND', array_slice($xml, 1));
			$r = array_diff($xml[0], $sub);
			break;
	}
	return $r;
}

/*
 * return collection refids as-is, or transform a [0-9]{4} format pid
 * into a chicago speculum number.
 *
 * input:
 * a number string, without leading speculum letter a, b, c, d,
 * with or without leading zeros.
 *
 * output:
 * a speculum number with leading letter and no leading zeros.
 */

function getChicagoNumber($pid) {
	$collection = '/^[A-Z][0-9]{1,3}-[A-Z][0-9]{1,3}$/';
	if (preg_match($collection, $pid)) {
		return $pid;
	} else {
		$c = ltrim($pid, '0-a..z');
		if ((integer)$c <= 179) {
			$c = 'A' . $c;
		} elseif ((integer)$c <= 383) {
			$c = 'B' . $c;
		} elseif ((integer)$c <= 991) {
			$c = 'C' . $c;
		} elseif ((integer)$c <= 994) {
			$c = 'D' . $c;
		} else {
			return 'A1';
		}
		return $c;
	}
}

/*
 * search node for text.
 *
 * input:
 * DOMDocument
 * /findaid/work
 * xpath query to search, inside each work:
 * 'titleSet/title'
 * a string to search for in every node.
 * no wildcards, and every search is for the exact string. 
 * (no implied and)
 * 
 * output:
 * DOMDocument
 * /findaid/work
 */

/*
 * preg_match('/\b' . quotemeta($searchstring) . '\b/i')
 * try the line above for word boundary searches???
 */ 

function nodeSearch($xml, $subquery, $searchstring, $searchexact) {
	$r = array();
	$xp = new DOMXPath($xml);
	$nl = $xp->query('/findaid/work');

	foreach ($nl as $n) {
		$searchnl = $xp->query($subquery, $n);
		$haystack = '';
		foreach ($searchnl as $searchnode) {
			$haystack = $haystack . $searchnode->nodeValue . ' ';
		}

		$haystack = replace_accents($haystack);

		if ($searchexact == 'on') {
			if (trim(strtolower($haystack)) == trim(strtolower($searchstring))) {
				$r[] = $n;
			}
		} else {
			if (stripos($haystack, $searchstring) !== false) {
				$r[] = $n;
			} 
		}
	}
	$out = new DOMDocument;
	$root = $out->createElement('findaid');
	$out->appendChild($root);

	foreach ($r as $node) {
		$newNode = $out->importNode($node, true);
		$root->appendChild($newNode);
	}
	return $out;
}		

/*
 * input:
 * string to search for- can contain lowercase, etc.
 * node to search through: agent, all, etc.
 * 
 * output:
 * array of chicago numbers containing that string.
 */

function exactStringSearch($p, $node) {

	// TRIM WHITESPACE FROM BEGINNING AND END.
	$p = trim($p);
	// CONVERT ALL ? TO .
	$p = str_replace('?', '[A-Z0-9]', $p); 
	// CONVERT ALL * TO .*
	$p = str_replace('*', '[A-Z0-9]*', $p);
	// WRAP WHOLE THING IN /.../
	$p = '/\b' . $p . '\b/';

	// OPEN SERIALIZED FILE WE'RE SEARCHING. ex: indexes.all.text
	$fh = fopen('indexes/' . $node . '.text', 'r');
	$records = unserialize(fread($fh, filesize('indexes/' . $node . '.text')));
	fclose($fh);

	// TRY TO FIND MATCHES
	$matches = array();
	foreach ($records as $k=>$v) {
	 if (preg_match($p, $v)) 
			$matches[] = $k;	
	}
	
	return $matches;
}

/*
 * helper functions for dateSearch. Pass them yyyy strings. 
 */

function isBetween($test, $lo, $hi) {
	return ((int)$test >= (int)$lo && (int)$test <= (int)$hi);
}
function isDate($date) {
	return (strlen($date) == 4 && ctype_digit($date));
}

/* 
 * search for a single date or range of dates.
 * a single date may be contained in a range.
 * a range matches if part of the range touches any part of another range. 
 */

function dateSearch($xml, $searchstring) {
	$dateAr = array();
	$dateAr = split('-', $searchstring);
	
	switch (count($dateAr)) {
		case 1:
			$searchlo = trim($dateAr[0]);
			$searchhi = $searchlo;
			break;
		case 2:
			$searchlo = trim($dateAr[0]);
			$searchhi = trim($dateAr[1]);
			break;
		default:
			return emptyResultSet();
			break;
	}

	if (!isDate($searchlo) || !isDate($searchhi)) {
		return emptyResultSet();
	}
		
	$r = array();
	$xp = new DOMXPath($xml);
	$nl = $xp->query('/findaid/work');

	foreach ($nl as $n) {
		$nllo = $xp->query("dateSet/date[@type='publisher']/earliestDate", $n);
		if ($nllo->length > 0) 
			$nodelo = $nllo->item(0)->nodeValue;
		else
			$nodelo = '1000';
		if (!isDate($nodelo))
			$nodelo = '1000';

		$nlhi = $xp->query("dateSet/date[@type='publisher']/latestDate", $n);
		if ($nlhi->length > 0)
			$nodehi = $nlhi->item(0)->nodeValue;
		else 
			$nodehi = '9999';
		if (!isDate($nodehi))
			$nodehi = '9999';

		if ($nodelo == '1000' &&  $nodehi == '9999') 
			continue;

		if (isBetween($searchlo, $nodelo, $nodehi) || isBetween($searchhi, $nodelo, $nodehi)) 
			$r[] = $n;
	}

	$out = new DOMDocument;
	$root = $out->createElement('findaid');
	$out->appendChild($root);

	foreach ($r as $node) {
		$newNode = $out->importNode($node, true);
		$root->appendChild($newNode);
	}
	return $out;
}

/*
 * function to build an empty result set.
 * 
 * input: 
 * (none)
 *
 * output:
 * /findaid
 */

function emptyResultSet() {
	$out = new DOMDocument;
	$root = $out->createElement('findaid');
	$out->appendChild($root);
	return $out;
}

?>
