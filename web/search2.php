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

