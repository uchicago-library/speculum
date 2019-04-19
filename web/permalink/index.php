<?php
/* validate url parameters and load a string for zoomify viewer parameters */

/*
if (array_key_exists('v',$_GET) && $_GET['v'] == '1') {

$validateNumbersOnly = '/^[-0-9.]*$/';

if (!array_key_exists('zoom',$_GET) || !preg_match($validateNumbersOnly, $_GET['zoom'])) 
	die();
if (!array_key_exists('x',$_GET) || !preg_match($validateNumbersOnly, $_GET['x'])) 
	die();
if (!array_key_exists('y',$_GET) || !preg_match($validateNumbersOnly, $_GET['y'])) 
	die();

echo "<html><head/><body>";
echo "<p>zoom: ";
echo $_GET['zoom'];
echo "</p>";
echo "<p>x: ";
echo $_GET['x'];
echo "</p>";
echo "<p>y: ";
echo $_GET['y'];
echo "</p>";
echo "</body></html>";
die();
}
*/

$validateNumbersOnly = '/^[0-9]*$/';
$clean = array();

if (array_key_exists('view1',$_GET) && preg_match($validateNumbersOnly, $_GET['view1'])) {
	$clean['doc'] = substr($_GET['view1'],0,4);
	$clean['obj'] = substr($_GET['view1'],4,3);
} else {
	$clean['doc'] = '0001';
	$clean['obj'] = '001';
}
if (array_key_exists('zoom',$_GET) && preg_match($validateNumbersOnly, $_GET['zoom'])) {
	$clean['zoom'] = $_GET['zoom'];
} else {
	$clean['zoom'] = '-1';
}
if (array_key_exists('x',$_GET) && preg_match($validateNumbersOnly, $_GET['x'])) {
	$clean['x'] = $_GET['x'];
} else {
	$clean['x'] = '0';
}
if (array_key_exists('y',$_GET) && preg_match($validateNumbersOnly, $_GET['y'])) {
	$clean['y'] = $_GET['y'];
} else {
	$clean['y'] = '0';
}

$flashVarsValueString = '';
$flashVarsValueString .= 'zoomifyImagePath=speculum-' . $clean['doc'] . '-' . $clean['obj'];
$flashVarsValueString .= '&zoomifyX=' . $clean['x'];
$flashVarsValueString .= '&zoomifyY=' . $clean['y'];
$flashVarsValueString .= '&zoomifyZoom=' . $clean['zoom'];

?>
<!DOCTYPE html
 PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1-strict.dtd">

<html>
<head>
<title>Zoomify Test</title>
</head>
<body>
<div style="width: 750px; margin: 0 auto; border: 1px dotted pink;">
<object
	CLASSID="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
	CODEBASE="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0"
	WIDTH="750"
	HEIGHT="450"
	ID="theMovie">
<param NAME="FlashVars" VALUE="<?php echo $flashVarsValueString; ?>"/>
<param name="Menu" VALUE="FALSE"/>
<param NAME="src" VALUE="zoomifyViewer.swf"/>

<embed
	FlashVars="<?php echo $flashVarsValueString; ?>"
	SRC="zoomifyViewer.swf"
	MENU="false"
	PLUGINSPAGE="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash"
	WIDTH="750"
	HEIGHT="450"
	NAME="theMovie"/>
</object>
</div>
</body>
</html>
