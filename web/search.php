<?php

ini_set('error_reporting', E_ALL | E_STRICT);
ini_set('log_errors', 'On');

require_once('shareddefs.php');
require_once('searchdefs.php');
require_once('indexdefs.php');
require_once('browsedefs.php');
require_once('htmlchunks.php');
require_once('parser.php');
require_once('searchparams.php');

function build_sortable_huref($n) {
    $m = array();
    preg_match("/^Hu ([0-9]*)([a-z]*)/", $n->getElementsByTagName('huref')->item(0)->nodeValue, $m);
    $h = str_pad($m[1], 5, "0", STR_PAD_LEFT) . str_pad($m[2], 5, "a", STR_PAD_LEFT);
    return $h;
}

function sort_by_huref($a, $b) {
    return strcmp(build_sortable_huref($a), build_sortable_huref($b));
}

/*
 * Split a set element, like agentSet, into groups. 
 * modify the original xmlDoc document.
 *
 * e.g.  speculum.xml document
 *       /findaid/work/agentSet[1]
 *       agent (from the set node, above.)
 *       role (from each node, above.) 
 */

function split_set(&$xmlDoc, $setNode, $elementXpath, $groupByXpath) {
	$xp = new DOMXPath($xmlDoc);
	$elementNodeList = $xp->query($elementXpath, $setNode);

	$groups = array();
	foreach ($elementNodeList as $elementNode) {
		$groupBy = $xp->query($groupByXpath, $elementNode)->item(0)->nodeValue;
		if (!array_key_exists($groupBy, $groups)) {
			$groups[$groupBy] = array();
		}
		$groups[$groupBy][] = $elementNode;
	}

	// group the elements into new set nodes. 
	foreach ($groups as $group => $elementNodes) {
		$newSet = $xmlDoc->createElement($setNode->nodeName);
		foreach ($elementNodes as $elementNode) {
			$newSet->appendChild($elementNode);
		}
		$setNode->parentNode->insertBefore($newSet, $setNode);
	}

	// delete the original set.
	$setNode->parentNode->removeChild($setNode);
}

function preprocess_speculum($xml) {
	$xp = new DOMXPath($xml);
	// for each work node...
	$nl = $xp->query('//work');
	foreach ($nl as $n) {
		$agentSetNodeList = $xp->query('agentSet', $n);
		foreach ($agentSetNodeList as $agentSetNode) {
			split_set($xml, $agentSetNode, 'agent', 'role');
		}
	}
	return $xml;
}

/*
 * BROWSES
 * input XML: /browse/group[*]/findaid[*]/work
 * browse1 throws the vast majority of data away- it only prints the
 * group/@name.  browse2 finds the appropriate group number and only displays
 * those results, like a search result. Finally, browse3 takes a group number
 * and result number, and displays the individual item, with previous and next
 * buttons to move back and forth in the result set. 
 * 
 * DETAILS 
 * input XML: /findaid/work (processed version of speculum.xml)
 * LISTS
 * input XML: /findaid/work[*] (speculum.xml)
 * You can 'browse' by chicago number of huelsen number- but because each browse
 * group uniquely identifies a single print, the browse code doesn't really work.
 * Basically, this option sorts by chicago number or huelsen number. list1 prints
 * a list of the items, and list2 displays a single item with previous and next 
 * buttons.
 * 
 * SEARCHES
 * input XML: /findaid/work[*] (processed version of speculum.xml)

 * 
 *
 * PROCESS DIFFERENTLY DEPENDING ON SEARCH OR BROWSE.
 * AFTER THIS THE DOMDOCUMENT MAY BE EITHER FINDAID/WORK OR RECORDS/RECORD FORMAT.
 * LOAD XML.
 */ 

/*
 * MAIN
 */
$blankpage = false;
$domXSLT = new DOMDocument();
$proc = new XSLTProcessor();
if (!empty($clean['browse'])) {
	$domOUT = processBrowse($clean);
	$domOUT = preprocess_speculum($domOUT);
	
	if (empty($clean['group']) && empty($clean['result'])) {
		// BROWSE, STEP 1. DISPLAY ALL BROWSE KEYWORDS.
		$domXSLT->load('xslt/browse1.xslt');
		$proc->importStyleSheet($domXSLT);
	} else if (empty($clean['result']) && getResultCount($clean, $domOUT) > 1) {
		// BROWSE, STEP 2. ALL RESULTS FOR A SINGLE BROWSE KEYWORD.
		$domXSLT->load('xslt/browse2.xslt');
		$proc->importStyleSheet($domXSLT);
		$proc->setParameter('', 'group', $clean['group']);
		if (!$clean['page'])
			$clean['page'] = 1;
		$proc->setParameter('', 'page', $clean['page']);
		$proc->setParameter('', 'urlstring', buildUrlString($clean));
	} else {
		// BROWSE, STEP 3. SINGLE RESULT.
		if (!$clean['result'])
			$clean['result'] = 1;
		$domXSLT->load('xslt/browse3.xslt');
		$proc->importStyleSheet($domXSLT);
		$proc->setParameter('', 'group', $clean['group']);
		$proc->setParameter('', 'result', $clean['result']);
	}
} else if (!empty($clean['list'])) {
	$domOUT = new DOMDocument();
	$domOUT->load('metadata/speculum.xml');
	$domOUT = preprocess_speculum($domOUT);

    // SORT METADATA BY HUELSEN NUMBER IF NECESSARY
    if ($clean['list'] == 'huelsen') {
        $domNEW = new DOMDocument();
        $root = $domNEW->createElement('findaid');
        $domNEW->appendChild($root);

        $xp = new DOMXPath($domOUT);
        $nl = $xp->query('/findaid/work[huref]');

        $hurefs = iterator_to_array($nl);
        usort ($hurefs, 'sort_by_huref');

        foreach ($hurefs as $huref) {
            $root->appendChild($domNEW->importNode($huref, true));
        }

        $domOUT = $domNEW;
    }

	if (empty($clean['result'])) {
		// LIST, STEP 1. ALL RESULTS FOR A LIST. (ADD 'RESULT PAGE' PAGER?)
		$domXSLT->load('xslt/list1.xslt');
		$proc->importStyleSheet($domXSLT);
		$proc->setParameter('', 'list', $clean['list']);
		$proc->setParameter('', 'urlstring', buildUrlString($clean));
		if (!$clean['page'])
			$clean['page'] = 1;
		$proc->setParameter('', 'page', $clean['page']);
	} else {
		// LIST, STEP 2. SINGLE RESULT WITH PREVIOUS / NEXT PAGER.
		$domXSLT->load('xslt/list2.xslt');
		$proc->importStyleSheet($domXSLT);
		$proc->setParameter('', 'list', $clean['list']);
		$proc->setParameter('', 'result', $clean['result']);
	}
} else if (!empty($clean['search'][0])) {
	$domOUT = processSearch($clean);
	if (empty($clean['result']) && getResultCount($clean, $domOUT) > 1) {
		// SEARCH, STEP 1. ALL SEARCH RESULTS FOR A SEARCH.
		$domXSLT->load('xslt/search1.xslt');
		$proc->importStyleSheet($domXSLT);
		if (!$clean['page'])
			$clean['page'] = 1;
		$proc->setParameter('', 'page', $clean['page']);
		$proc->setParameter('', 'titlestring', buildTitleString($clean, $domOUT));
		$proc->setParameter('', 'urlstring', buildUrlString($clean));
	} else {
		// SEARCH, STEP 2. SINGLE RESULT WITH PREVIOUS / NEXT PAGER.
		if (!$clean['result'])
			$clean['result'] = 1;
		$domXSLT->load('xslt/search2.xslt');
		$proc->importStyleSheet($domXSLT);
		$proc->setParameter('', 'result', $clean['result']);
		$proc->setParameter('', 'titlestring', buildTitleString($clean, $domOUT));
		$proc->setParameter('', 'urlstring', buildUrlString($clean));
	}
} else if (!empty($clean['detail'])) {
	$domIN = new DOMDocument();
	$domIN->load('metadata/speculum.xml');
	$domIN = preprocess_speculum($domIN);
	$r = array();
	$r[] = getChicagoNumber($clean['detail']);
	$domOUT = buildFindingAid($r);
	$domXSLT->load('xslt/detail.xslt');
	$proc->importStyleSheet($domXSLT);
} else {
	$domOUT = emptyResultSet();
	$blankpage = true;
}

/*
 * DEBUG 
 */
if ($clean['debug'] == 'on') 
	die(trim($domOUT->saveXML()));

/*
 * OUTPUT
 */
?>
<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
<title>The Speculum Romanae Magnificentiae</title>
<link href="css/speculum.css" type="text/css" rel="stylesheet"/>
<link href="css/speculum-contentpages.css" type="text/css" rel="stylesheet"/>
<script src="scripts/prototype.js" type="text/javascript"></script>
<script src="scripts/searchswitcher.js" type="text/javascript"></script>
<script type="text/javascript">
new Searchswitcher();
</script>
<script src="/scripts/ga.js"></script>
</head>
<body>

<!-- CONTENT -->
<div id="content">

<!-- HEADER -->
	<div id="header"><div style="float: left;"><a href="index.html"><img src="http://speculum.lib.uchicago.edu/images/content-bg.jpg"/></a></div><div style="float: right;"><a href="https://www.lib.uchicago.edu/h/dl"><img src="http://speculum.lib.uchicago.edu/images/banner-dla-logo.gif" alt="The University of Chicago Library Digital Activities and Collections"/></a></div></div>
<!-- /HEADER -->

<!-- NAVIGATION -->
<div class="navigation">
<ul>
<li><a href="index.html">HOME</a></li>
<li><a href="index.html">INTRODUCTION</a></li>
<li><a href="content/itineraries.html">ITINERARIES</a></li>
<li id="youarehere">SEARCH</li>
<li><a href="content/links.html">LINKS</a></li>
</ul>
</div><!--/NAVIGATION-->

<!-- SECTION -->
<div class="section">

<h1>Search the Speculum Romanae Magnificentiae</h1>

<?php include 'includes/searches.php'; ?>

<br style="height: 2em;"/>

<?php include 'includes/browses.php'; ?>

<br style="height: 2em;"/>

<?php if ($blankpage) { ?>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<?php } else { ?>
<hr style="border-bottom: 1px dashed #999;"/>
<?php echo $proc->transformToXML($domOUT); } ?>

</div><!--/SECTION-->

<br style="height: 2em;"/>

<div id="footer"></div>
</div><!--/CONTENT-->

</body>
</html>

