<?php

/* produce a full page image for the gms website. 
 * the script include's gms.php to grab some functions
 * for object metadata and item metadata grabbing. 
 *
 * rev. 2006-10-27 jej
 */

include "gms.php";
	
$width = $_GET["width"] * 1;
$height = $_GET["height"] * 1;
$width = $width * 0.98;
$height = $height * 0.91;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" type="text/JavaScript">
<!--
	window.resizeTo(screen.availWidth, screen.availHeight);
	window.moveTo(0,0);
-->
</script>
<title><? print $currentManuscript->sTitle;?></title>
<link href="main.css" rel="stylesheet" type="text/css">
</head>
<body onLoad="javascript:window.focus()">

<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" WIDTH="<? print $width;?>" HEIGHT="<? print $height;?>" align="center" id="zoomify">
<PARAM NAME=movie VALUE="http://www.lib.uchicago.edu/lib/public/zoomify.swf">
<PARAM NAME=menu VALUE=false>
<PARAM NAME=quality VALUE=high>
<PARAM NAME=bgcolor VALUE=#FFFFFF>
<PARAM NAME=FlashVars VALUE="imagepath=../images/<? print $currentObject->sDocument;?>/gms-<? print $currentObject->sDocument;?>-<? print $currentObject->sObject;?>&windowwidth=<? print $width;?>&windowheight=<? print $height;?>">
<EMBED src="http://www.lib.uchicago.edu/lib/public/zoomify.swf" FlashVars="imagepath=../images/<? print $currentObject->sDocument;?>/gms-<? print $currentObject->sDocument;?>-<? print $currentObject->sObject;?>&windowwidth=<? print $width;?>&windowheight=<? print $height;?>" menu=false quality=high align=center bgcolor=#FFFFFF WIDTH="<? print $width;?>" HEIGHT="<? print $height;?>" NAME="zoomify" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer"></EMBED>
</OBJECT>
</body>
</html>

