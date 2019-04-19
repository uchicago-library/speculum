<?php
function cleanPid($get) {
	$parts = explode('-', $get);

	// 'A1', '994', '0500'
	if (count($parts) == 1) {
		$numstr = ltrim($parts[0], " a..zA..Z");
		if ((int)$numstr > 994 || (int)$numstr < 1) {
			return 'speculum-0001-001';
		}
		if (!(strlen($numstr) <= 4 && ctype_digit($numstr))) {
			return 'speculum-0001-001';
		}
		$numpadded = "speculum-" . str_pad($numstr, 4, "0", STR_PAD_LEFT) . "-001";
		return $numpadded;
	}

	// 'speculum-0295-003'
	if (count($parts) != 3) {
		return 'speculum-0001-001';
	}
	if ($parts[0] != 'speculum') {
		return 'speculum-0001-001';
	}
	if (!(strlen($parts[1]) == 4 && ctype_digit($parts[1]))) {
		return 'speculum-0001-001';
	}
	if (!(strlen($parts[2]) == 3 && ctype_digit($parts[2]))) {
		return 'speculum-0001-001';
	}
	return implode('-',$parts);
}

$clean = array();
$clean['id'] = 'speculum-0001-001';
if (isset($_GET['id'])) {
	$clean['id'] = cleanPid($_GET['id']);
}
?>
<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
<title>The Speculum Romanae Magnificentiae : Links</title>
<link href="../css/speculum.css" type="text/css" rel="stylesheet"/>
<link href="../css/speculum-contentpages.css" type="text/css" rel="stylesheet"/>
<script src="/scripts/ga.js"></script>
</head>
<body>

<!-- CONTENT -->
<div id="content">

<!-- HEADER -->
	<div id="header"><div style="float: left;"><a href="../"><img src="../images/content-bg.jpg"/></a></div><div style="float: right;"><a href="http://www.lib.uchicago.edu/h/dla"><img src="../images/banner-dla-logo.gif" alt="The University of Chicago Library Digital Activities and Collections"/></a></div></div>

<!-- NAVIGATION -->
<div class="navigation">
	<ul>
	<li><a href="../index.html">HOME</a></li>
	<li><a href="content/introduction.html">INTRODUCTION</a></li>
	<li><a href="content/itineraries.html">ITINERARIES</a></li>
	<li id="youarehere">SEARCH</li>
	<li><a href="content/links.html">LINKS</a></li>
	</ul>
</div><!--navigation-->		

<!-- SECTION -->
<div class="section">

<h1>View a Printable Image</h1>

<p>Please be aware of our policy on <a
href="content/reproductions.html">reproductions</a>.</p>

<br style="height: 2em;"/>

<p><a href="images/printing-jpgs/<?php echo $clean['id']; ?>.jpg">Go to printable image</a>.</p>

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
<p>&nbsp;</p>

</div><!--/SECTION-->
<div id="footer"></div><!--/FOOTER-->
</div><!--/CONTENT-->

</body>
</html>

