<?php

require_once('searchdefs.php');

function http_build_query_wrap($clean) {
	$out = array();
	foreach ($clean as $key => $value) {
		if (is_array($clean[$key])) {
			foreach ($clean[$key] as $k => $v) {
				if ($clean[$key][$k] != '') {
					$out[$key][$k] = $clean[$key][$k];
				}
			}
		} else {
			if ($clean[$key] != '') {
				$out[$key] = $clean[$key];
			}
		}
	}
	/* UNSET SEARCHBOOLEANS, IF POSSIBLE */
	unset($out['searchboolean'][0]);
	$n = 1;
	while ($n < 3) {
		if (isset($out['searchboolean'][$n]) && $out['searchboolean'][$n] == 'and') {
			unset($out['searchboolean'][$n]);
		}
		$n = $n + 1;
	} 
	/* UNSET SEARCH VARIABLES, IF POSSIBLE */
	$n = 0;
	while ($n < 3) {
		if (isset($out['search'][$n]) && $out['search'][$n] == '') {
			unset($out['search'][$n]);
			unset($out['searchboolean'][$n]);
			unset($out['searchnode'][$n]);
		}
		$n = $n + 1;
	}
	/* UNSET PAGE, IF POSSIBLE */
	if (isset($out['page']) && $out['page'] == 1) {
		unset($out['page']);
	}
	$s = http_build_query($out);
	return $s;
}

/*
 * BUILD MODE SWITCHER. PASS THIS HTML INTO XSLT TO SIMPLIFY PROCESSING.
 */

function buildModeSwitcher($clean) {
	$s = $_SERVER['SCRIPT_NAME'];
	if (array_key_exists('mode',$clean) && $clean['mode'] == 'advanced') {
		// WE'RE IN ADVANCED MODE. FLIP MODE FOR LINK.
		$clean['mode'] = 'basic';
		$q = http_build_query_wrap($clean);
		$modeswitcher = '<p><a href="' . $s . '?' . $q . '">basic</a> | advanced search</p>';
	} else {
		// WE'RE IN BASIC MODE. FLIP MODE FOR LINK.
		$clean['mode'] = 'advanced';
		$q = http_build_query_wrap($clean);
		$modeswitcher = '<p>basic | <a href="' . $s . '?' . $q . '">advanced search</a></p>';
	}
	return $modeswitcher;
}

/*
 * BUILD URL STRING FOR SEARCHES
 */

function buildUrlString($clean) {
	unset($clean['debug']);
	unset($clean['page']);
	unset($clean['result']);
	if (empty($clean['search'][0])) {
		unset($clean['searchboolean'][0]);
		unset($clean['searchnode'][0]);
	}
	if (empty($clean['search'][1])) {
		unset($clean['searchboolean'][1]);
		unset($clean['searchnode'][1]);
	}
	if (empty($clean['search'][2])) {
		unset($clean['searchboolean'][2]);
		unset($clean['searchnode'][2]);
	}
	return $_SERVER['SCRIPT_NAME'] . '?' . http_build_query_wrap($clean);
}

function getResultCount($clean, $domOUT) {
	if (array_key_exists('group', $clean) && $clean['group']) 
		$g = $clean['group'];
	else 
		$g = 1;
	
	$xp = new DOMXPath($domOUT);
	$count = $xp->evaluate('count(/findaid/work)');
	if ($count > 0) {
		return $count;
	} else {
		$count = $xp->evaluate('count(/browse/group[position() = ' . $g . ']/findaid/work)');
		return $count;
	}
}

/*
 * BUILD RESULTS SUMMARY AND PAGER.
 */

function buildResultsPager($clean, $domOUT) {

	global $pagelength;

	$resultspager = '';
	$xp = new DOMXPath($domOUT);
	$worknodecount = $xp->evaluate('count(/findaid/work)');
	if ($worknodecount > $pagelength) {
		$lonum = $pagelength * ($clean['page'] - 1) + 1;
		$hinum = $lonum + $pagelength - 1;
		if ($hinum > $worknodecount) {
			$hinum = $worknodecount;
		}
		$resultspager .= '<p>Results: ' . $lonum . ' to ' . $hinum . ' of ' . $worknodecount . '</p>';
		
		$resultspager .= '<p>';
		if ($clean['page'] > 1) {
			$tmpclean = $clean;
			$tmpclean['page'] = $clean['page'] - 1;
			$resultspager .= '<a href="search.php?' . http_build_query_wrap($tmpclean) . '">Previous Page.</a> ';
		}		
		$hipage = ceil($worknodecount / $pagelength);
		if ($clean['page'] < $hipage) {
			$tmpclean = $clean;
			$tmpclean['page'] = $clean['page'] + 1;
			$resultspager .= '<a href="search.php?' . http_build_query_wrap($tmpclean) . '">Next Page.</a>';
		}
		$resultspager .= '</p>';

		$resultspager .= '<p>Jump To Results Page: ';
		$i = 1;
		while ($i <= $hipage) {
			if ((string)$i == $clean['page']) {
				$resultspager .= (string)$i . ' ';
			} else {
				$tmpclean = $clean;
				$tmpclean['page'] = (string)$i;
				$resultspager .= '<a href="search.php?' . http_build_query_wrap($tmpclean) . '">' . (string)$i . '</a> ';
			}
			$i = $i + 1;
		}
		$resultspager .= '</p>';
	}
	return $resultspager;
}

/*
 * BUILD A TITLE STRING. PASS THIS HTML INTO XSLT TO SIMPLIFY PROCESSING. 
 */

function buildTitleString($clean, $domOUT) {

	$xp = new DOMXPath($domOUT);

	// BROWSE? If so, deal with this in the XSLT.
	$q = '/browse/@name';
	$nl = $xp->query($q);
	if ($nl->length > 0) 
		return '';

	// MULTIPLE OR ZERO 'WORKS' FOUND THROUGH SEARCH
	$q = '/findaid/work';
	$nl = $xp->query($q);
	if ($clean['browse'] == '') {
		$titlestring = '';
		$i = 0;
		while ($i < 3 && $clean['search'][$i] != '') {
			if ($titlestring == '') 
				$titlestring = 'Search for';
			if ($i > 0) {
				$titlestring = $titlestring . ' ' . $clean['searchboolean'][$i];
			}
			$titlestring = $titlestring . ' "' . $clean['search'][$i] . '"';
			$titlestring = $titlestring . ' in ' . $clean['searchnode'][$i];
			$i = $i + 1;
		}
		return $titlestring;
	} 

	return '';
}

?>
