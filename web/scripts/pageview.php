<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<?php

/*
 * Page template for Goodspeed interim page-turning interface. 
 * Be sure to include gms.php before any content, to parse parameters,
 * die if necessary, and load metadata into a global class (used here as
 * a convenient way to hold different kinds of data.)
 * 
 * functions and important global variables:
 * 
 * <?=currentObject->sDocument;?>
 * <?=currentObject->sObject;?>
 *	doc and object numbers, with leading zeros, pulled from 
 *	object level data files.
 *
 * <?=currentObject->sTitle;?>
 * <?=currentObject->sTitleAlternate1;?>
 * <?=currentObject->sTitleAlternate2;?>
 * <?=currentObject->sCollection;?>
 * <?=currentObject->sDescription;?>
 * <?=currentObject->sSubject1;?>
 * <?=currentObject->sSubject2;?>
 * <?=currentObject->sSubject3;?>
 *	Official item-level data, from gms.xml.
 *
 * <?linkPreviousObject("<li><!--previous--></li>");?>
 *	create a link in a <li> to the previous digital object labeled 'previous',
 *	only if there IS a previous object.
 *
 * <?linkNextObject("<li><!--next--></li>");?>
 *	create a link in a <li> to the previous digital object labeled 'previous',
 *	only if there IS a next object.
 *
 * <? manuscriptOptions();?>
 *	produces a 'select' pulldown with page numbers and milestones.
 */

include "gms.php";
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" type="text/JavaScript">
<!--
	function fullScreen(page) {
		url = "fullscreen.php?doc=<?php echo $currentObject->sDocument; ?>&obj=<?php echo $currentObject->sObject; ?>&width=" + screen.availWidth + "&height=" + screen.availHeight;
		newWindow = window.open(url,"fs","status");
	}
-->
</script>
<title><?php echo $currentManuscript->sTitle; ?></title>
<link href="../css/main.css" rel="stylesheet" type="text/css">
</head>
<body>
<!--HEADER-->
<link rel=stylesheet type="text/css" href="../css/nonhomepages.css">
<a href="http://goodspeed.lib.uchicago.edu"><img src="http://www.lib.uchicago.edu/images/dig/goodspeedlogo.jpg" alt="Goodspeed Collection Home" hspace="0" vspace="0" border="0" align="left"></a>
<img src="http://www.lib.uchicago.edu/images/dig/digliblogo.gif" border="0" align="right" alt="Digital Collections" vspace="0" hspace="0"> </a><br clear="all">
<div class="endheader">
	</div>

<!--TITLE-->
<div class="title">
<h1><?php echo $currentManuscript->sTitle; ?></h1>
</div>

<!--DROPDOWN-->
<div class="dropdown">
<form name="form" action="pageview.php" method=get>
<input type="hidden" name="doc" value="<?php echo $currentObject->sDocument; ?>">
<select name="obj" onchange="document.form.submit()">
<?php manuscriptOptions(); ?>
</select>

<script type="text/javascript" language=javascript></script>
<noscript>
<input type="submit" value="go">
</noscript>

</form>
</div>

<!--FLIPPERS-->
<div class="flippers">
<ul>
	<?php linkPreviousObject("<li><!--previous--></li>"); ?>
	<?php linkNextObject("<li><!--next--></li>"); ?>
</ul>
</div>

<!--FULL SCREEN LINK-->
<div class="fslink">
<script language="JavaScript" type="text/JavaScript">
<!--
data = "<a href=\"javascript:fullScreen(0)\">view full-screen</a> (opens a new browser window -- return to this window to continue navigating to the document)"
document.write(data)
-->
</script>
<noscript></noscript>
</div>

<!--MANUSCRIPT IMAGE-->
<div class="display">
<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" WIDTH="760" HEIGHT="420" id="zoomify">
<PARAM NAME=movie VALUE="http://www.lib.uchicago.edu/lib/public/zoomify.swf">
<PARAM NAME=menu VALUE=false>
<PARAM NAME=quality VALUE=high>
<PARAM NAME=bgcolor VALUE=#FFFFFF>
<PARAM NAME=FlashVars VALUE="imagepath=../images/<?php echo $currentObject->sDocument; ?>/gms-<?php echo $currentObject->sDocument; ?>-<?php echo $currentObject->sObject; ?>">
<EMBED src="http://www.lib.uchicago.edu/lib/public/zoomify.swf" FlashVars="imagepath=../images/<?php echo $currentObject->sDocument; ?>/gms-<?php echo $currentObject->sDocument; ?>-<?php echo $currentObject->sObject; ?>" menu=false quality=high bgcolor=#FFFFFF WIDTH="760" HEIGHT="420" NAME="zoomify" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer"></EMBED>
</OBJECT>
</div>

<!--METADATA DISPLAY-->
<div class="metadata">
<table style="background-color:#ccc;" cellpadding="10" cellspacing="1" border="0">
	<tr>
		<td>Title:</td>
		<td><?php echo $currentManuscript->sTitle; ?></td>
	</tr>
	<tr>
		<td>Alternate Title(s):</td>
		<td><?php echo $currentManuscript->sTitleAlternate1; ?></td>
	</tr>
	<tr>
		<td>Collection:</td>
		<td><?php echo $currentManuscript->sCollection; ?></td>
	</tr>
	<tr>
		<td>Description:</td>
		<td><?php echo $currentManuscript->sDescription; ?></td>
	</tr>
	<tr>
		<td>Subjects:</td>
		<td><?php echo $currentManuscript->sSubject1; ?></td>
	</tr>
	<tr>
		<td>Page Information:</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>Digital Object Information:</td>
		<td>gms-<?php echo $currentObject->sDocument . "-" . $currentObject->sObject; ?></td>
	</tr>
</table>
<p>&nbsp;</p>
<p><a href="aboutthismanuscript.php?doc=<?php echo $doc; ?>&obj=<?php echo $obj; ?>">More information about this manuscript</a></p>
</div>
</body>
</html>

