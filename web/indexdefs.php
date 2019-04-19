<?php

require_once('shareddefs.php');

/*
 * Build an xml document of browse results.  
 * Pass this function an XPath statement, and it will
 * return a DOMDocument containing a browse.
 *
 * input:
 * DOMDocument (probably the entire speculum data file)
 * /findaid/work
 * string describing the browse
 * an xpath leading to a nodeList that will build the browse:
 * '//agent[role="engraver"]/name'
 *
 * output:
 * DOMDocument
 * /browse/group/findaid
 */

function nodeBrowse($xml, $browse, $q) {
	$xp = new DOMXPath($xml);
	$nl = $xp->query('/findaid/work');

	$r = array();

	foreach($nl as $n) {
		$subnl = $xp->query($q, $n);
		foreach ($subnl as $sn) {
			$text = $sn->nodeValue;

			// strip off leading and trailing whitespace.
			$text = trim($text);

			// de-quotulator
			$text = str_replace(array('[', ']', '?'), '', $text);

			// stretches of whitespace to a single space.
			$text = preg_replace('/\s+/', ' ', $text);

			if (!array_key_exists($text, $r)) {
				$r[$text] = array();
			}
			$r[$text][] = $n;
		}
	}

	$out = new DOMDocument();
	$root = $out->createElement('browse');	
	$browseAtt = $out->createAttribute('name');
	$root->appendChild($browseAtt);
	$browseAttText = $out->createTextNode($browse);
	$browseAtt->appendChild($browseAttText);
	$out->appendChild($root);

	$names = array_keys($r);
	if ($browse == 'date')
		usort($names, "cmpDates");
	else if ($browse == 'huelsen')
		usort($names, "cmpHurefs");
	else
		natcasesort($names);

	foreach ($names as $name) {
		$newGroup = $out->createElement('group');
		$root->appendChild($newGroup);
		$groupAtt = $out->createAttribute('name');
		$groupAttText = $out->createTextNode($name);
		$groupAtt->appendChild($groupAttText);
		$newGroup->appendChild($groupAtt);

		$findaid = $out->createElement('findaid');
		$newGroup->appendChild($findaid);

		foreach($r[$name] as $node) {
			$newNode = $out->importNode($node, true);
			$findaid->appendChild($newNode);
		}
	}
	return $out;	
}

function collectionBrowse($xml) {
	$xp = new DOMXPath($xml);
	$nl = $xp->query('/findaid/collection');

	$r = array();

    //for each one, need to get the (titleSet/title)[1].
    //then, nead each relationSet/relation/@relids.
    //then, look up the appropriate /findaid/work[@refid] that equals that relid. 
	foreach($nl as $n) {
		$titlenl = $xp->query('titleSet/title', $n);

        if ($titlenl->length < 1) {
            continue;
        }

        $title = $titlenl->item(0)->nodeValue;

		// strip off leading and trailing whitespace.
		$title = trim($title);

		// de-quotulator
		$title = str_replace(array('[', ']', '?'), '', $title);

		// stretches of whitespace to a single space.
		$title = preg_replace('/\s+/', ' ', $title);
       
		if (!array_key_exists($title, $r)) {
			$r[$title] = array();
		}

        $relidsnl = $xp->query('relationSet/relation[@relids]', $n);
        foreach ($relidsnl as $relidsn) {
            $relid = $relidsn->getAttribute('relids');

            $subnl = $xp->query(sprintf('/findaid/work[@refid="%s"]', $relid));
            foreach ($subnl as $sn) {
		        $r[$title][] = $sn;
            }
		}
	}

	$out = new DOMDocument();
	$root = $out->createElement('browse');	
	$browseAtt = $out->createAttribute('name');
	$root->appendChild($browseAtt);
	$browseAttText = $out->createTextNode('collection');
	$browseAtt->appendChild($browseAttText);
	$out->appendChild($root);

	$names = array_keys($r);
	natcasesort($names);

	foreach ($names as $name) {
		$newGroup = $out->createElement('group');
		$root->appendChild($newGroup);
		$groupAtt = $out->createAttribute('name');
		$groupAttText = $out->createTextNode($name);
		$groupAtt->appendChild($groupAttText);
		$newGroup->appendChild($groupAtt);

		$findaid = $out->createElement('findaid');
		$newGroup->appendChild($findaid);

		foreach($r[$name] as $node) {
			$newNode = $out->importNode($node, true);
			$findaid->appendChild($newNode);
		}
	}
	return $out;	
}

/* 
 * Sort date strings. See normalizeDate below.
 */

function cmpDates($a, $b) {
	$a = normalizeDate($a);
	$b = normalizeDate($b);
	if ($a == $b)
		return 0;
	return ($a < $b) ? -1 : 1;
}

/*
 * helper function for cmpDates. 
 * Deal with date strings with 4 digits,
 * date strings with 2 digits, and just plain strings. 
 */

function normalizeDate($string) {
	$matches = array();

	/* return the first 4 digit year, if it's there. */
	preg_match('/[0-9]{4}/', $string, $matches);
	if (!empty($matches)) 
		return $matches[0];

	/* if we find a century, normalize it to a 4 digit year. */
	preg_match('/[0-9]{2}/', $string, $matches);
	if (!empty($matches)) 
		return (string)(((int)$matches[0] - 1) * 100);

	/* otherwise, just return the string for sorting. */
	return $string;
}

/* 
 * See normalizeHuref below. 
 */

function cmpHurefs($a, $b) {
	$a = normalizeHuref($a);
	$b = normalizeHuref($b);
	if ($a == $b)
		return 0;
	return ($a < $b) ? -1 : 1;
}

/* 
 * find 1-3 digits, followed by a letter.
 * pad the result with zeros and sort that. 
 */

function normalizeHuref($string) {
	$matches = array();
	preg_match('/[0-9][0-9]?[0-9]?[a-z]/', $string, $matches);
	if (!empty($matches)) {
		return str_pad($matches[0], 4, "0", STR_PAD_LEFT);
	} else {
		return $string;
	}
}

/*
 * input: 
 * xml file (speculum.xml)
 * subquery (the location of the text to pull.)
 *
 * output:
 * associative array. keys are refids, values are long strings of the
 * text in that node.
 */

function extractText($xml, $q) {
	$xp = new DOMXPath($xml);
	$nl = $xp->query('/findaid/work');

	$r = array();
	foreach($nl as $n) {
		$idnl = $xp->query('@refid', $n);
		$id = $idnl->item(0)->nodeValue;
		$id = trim($id);
		$subnl = $xp->query($q, $n);
		$text = '';
		foreach ($subnl as $sn) {
			$t = $sn->nodeValue;
			// uppercase, strip out unwanted chars.
			$t = cleanStringAlphaNumeric($t, True);
			// stretches of whitespace to a single space.
			$t = preg_replace('/\s+/', ' ', $t);
			// strip off leading and trailing whitespace.
			$t = trim($t);
			$text = $text . ' ' . $t;
		}
		$r[$id] = trim($text);
	}
	return $r;	
}
/*
 * Make an index for my xml file.
 *
 * input:
 * xml file:
 * /findaid/work
 * subquery, for location of index:
 * ex: agentSet/agent/name
 *
 * output:
 * associative array of arrays.
 * The first key is the thing we're indexing (eg. a name.)
 * each array entry is the chicago number of the matching DOMNode.
 */

function buildIndex($xml, $subquery) {
	$r = array();
	$r['*'] = array();
	$xp = new DOMXPath($xml);
	$nodelist = $xp->query('/findaid/work');
	foreach ($nodelist as $node) {
		$subnodelist = $xp->query($subquery, $node);
		$subnodeid = $xp->query('@refid',$node);
		$id = $subnodeid->item(0)->nodeValue;
		foreach ($subnodelist as $subnode) {
			$keystring = $subnode->nodeValue;
			$keystring = cleanStringAlphaNumeric($keystring, True);
			$keys = array();
			$tmp = strtok($keystring, " \n\t");
			while ($tmp !== false) {
				$keys[] = $tmp;
				$tmp = strtok(" \n\t");
			}
			$k = 0;
			while ($k < count($keys)) {
				$key = $keys[$k];
				if (!array_key_exists($key, $r)) {
					$r[$key] = array();
				}
				if (!in_array($id, $r[$key])) {
					$r[$key][] = $id;
				}
				if (!in_array($id, $r['*'])) {
					$r['*'][] = $id;
				}
				$k = $k + 1;
			}
		}
	}
	return $r;
}

/*
 * helper function for buildIndex.
 */

function cleanStringAlphaNumeric($s, $index=False) {
	$r = replace_accents($s);
	$r = strtoupper($r);
	if ($index)
		$r = preg_replace('/[^A-Z0-9]/', ' ', $r);
	else
		$r = preg_replace('/[^A-Z0-9\'\"\?\*]/', ' ', $r);
	return $r;
}

function normalizeParensAndQuotes($s) {
	if (!parensAreBalanced($s)) {
		$s = str_replace('(', ' ', $s);
		$s = str_replace(')', ' ', $s);
	}
	if (!quotesAreBalanced($s)) {
		$s = str_replace("'", ' ', $s);
		$s = str_replace('"', ' ', $s);
	}
	return $s;
}

function parensAreBalanced($s) {
	$p = 0;
	$i = 0;
	while ($i < strlen($s)) {
 		if (substr($s, $i, 1) == '(') 
			$p++;
		if (substr($s, $i, 1) == ')') 
			$p--;
		if ($p < 0)  
			return False;
		$i++;
	}
	if ($p != 0) 
		return False;
	return True;
}

function quotesAreBalanced($s) {
	if (substr_count($s, "'") % 2 != 0) 
		return False;
	if (substr_count($s, '"') % 2 != 0)
		return False;
	return True;
}

/* 
 * the date index is designed to deal with the date ranges in vracore.
 * it starts with earliestdate and, one at a time, counts up, adding a 
 * new year for every date in the range until latest date. Then, when
 * somone types '1625', the search can find the range '1600-1700'.
 */

function buildDateIndex($xml) {
	$r = array();
	$xp = new DOMXPath($xml);
	$nodelist = $xp->query('/findaid/work');
	foreach ($nodelist as $node) {
		$subnodeid = $xp->query('@refid',$node);
		$id = $subnodeid->item(0)->nodeValue;

		$nodedates = $xp->query('dateSet', $node);
		foreach ($nodedates as $subnode) {
			if (($earliestDate = buildDateIndexSub($xp, 'date/earliestDate', $subnode)) === False) 
				continue;
			if (($latestDate = buildDateIndexSub($xp, 'date/latestDate', $subnode)) === False)
				continue;
			$n = $earliestDate;
			while ($n <= $latestDate) {
				if (!array_key_exists((string)$n, $r))
					$r[(string)$n] = array();
				if (!in_array($id, $r[(string)$n]))
					$r[(string)$n][] = $id;
				$n = $n + 1;
			}
		}
	}
	return $r;
}

/*
 * helper function for buildDateIndex.
 * returns either an integer date or boolean False 
 */

function buildDateIndexSub($xp, $query, $node) {
	$node = $xp->query($query, $node);
	if ($node->length != 1)
		return False;
	$s = $node->item(0)->nodeValue;
	if (preg_match('/^[0-9]*$/', $s) == 0)
		return False;
	if (strlen($s) != 4)
		return False;
	return (int)$s;
}

/*
 * Print index file. Use this to display input for cdb database building
 * over http. 
 * input:
 * associative array of index stuff. each key is the index term, and
 * each value is an array of chicago numbers.
 * name of index.
 *
 * output:
 * stream of data suitable for cdb database making. 
 */

function printIndex($r) {
	foreach ($r as $k=>$data) {
		if (is_string($data)) 
			$d = $data;
		else
			$d = serialize($data);
		$klen = strlen($k);
		$dlen = strlen($d);
		print '+' . (string)$klen . ',' . (string)$dlen . ':' . $k . '->' . $d . "\n";
	}
	print "\n";
}

/*
 * Load index data from data file. 
 * input: 
 * name of index.
 * term to return. 
 * 
 * output:
 * associative array of index stuff.
 */

function loadIndexData($name, $term) {
	$db = dba_open("indexes/" . $name . ".cdb", "r", "cdb");
	if ($db == False) {
		return array();
	}
	$r = dba_fetch($term, $db);
	dba_close($db);
	if ($r == False) 
		return array();
	else
		return unserialize($r);	
}
?>
