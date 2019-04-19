<?xml version="1.0"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output method="xml" encoding="utf-8" omit-xml-declaration="yes" indent="yes" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"/>

<!-- PARAMETERS AND VARIABLES -->
<xsl:param name="mode"/>
<xsl:param name="modeswitcher"/>
<xsl:param name="resultspager"/>
<xsl:param name="page"/>
<xsl:param name="pagelength"/>
<xsl:param name="browse"/>
<xsl:param name="group"/>
<xsl:param name="detail"/>
<xsl:param name="titlestring"/>
<xsl:param name="coins"/>

<xsl:variable name="loindex">
<xsl:value-of select="($page - 1) * $pagelength"/>
</xsl:variable> 

<xsl:variable name="hiindex">
<xsl:choose>
<xsl:when test="count(/findaid/work) &gt; ($page - 1) * $pagelength + $pagelength">
<xsl:value-of select="($page - 1) * $pagelength + $pagelength"/>
</xsl:when>
<xsl:otherwise>
<xsl:value-of select="count(/findaid/work)"/>
</xsl:otherwise>
</xsl:choose>
</xsl:variable>

<!-- IMPORT BASIC AND ADVANCED SEARCHES -->
<xsl:include href="searchboxes.xslt"/>
<!--<xsl:include href="searchlinks.xslt"/>-->

<!-- IMPORT BROWSE -->
<xsl:include href="browsesets.xslt"/>

<!-- IMPORT MULTIPLE RESULTS VIEW -->
<xsl:include href="findaid.xslt"/>
<xsl:include href="workmultiple.xslt"/>
<xsl:include href="workmultiplethumb.xslt"/>

<!-- IMPORT SINGLE RESULTS VIEW -->
<xsl:include href="worksingle.xslt"/>

<!-- MAIN HTML -->
<xsl:template match="/">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
<title>The Speculum Romanae Magnificentiae</title>
<link href="css/speculum.css" type="text/css" rel="stylesheet"/>
<link href="css/speculum-contentpages.css" type="text/css" rel="stylesheet"/>
</head>
<script type="text/javascript" src="/scripts/ga.js"></script>
<body>

<!-- CONTENT -->
<div id="content">

<!-- HEADER -->
	<div id="header"><div style="float: left;"><a href="index.html"><img src="../images/content-bg.jpg"/></a></div><div style="float: right;"><a href="http://www.lib.uchicago.edu/h/dla"><img src="../images/banner-dla-logo.gif" alt="The University of Chicago Library Digital Activities and Collections"/></a></div></div>
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

<!-- SIMPLE / ADVANCED SEARCH FLIPPER -->
<xsl:value-of select="$modeswitcher" disable-output-escaping="yes"/>

<br/>

<xsl:choose>
<xsl:when test="$mode = 'advanced'">
	<xsl:call-template name="advancedsearch"/>
</xsl:when>
<xsl:otherwise>
	<xsl:call-template name="simplesearch"/>
</xsl:otherwise>
</xsl:choose>

<br style="height: 2em;"/>

<hr style="border-bottom: 1px dashed #999;"/>

<!-- TITLE -->
<h1><xsl:value-of select="$titlestring"/></h1>
<xsl:if test="$coins">
<span 
   class="Z3988" 
   title="ctx_ver=Z39.88-2004&amp;rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Adc&amp;rfr_id=info%3Asid%2Focoins.info%3Agenerator&amp;rft.title=Title+page+of+the+Speculum+Romanae+Magnificentiae&amp;rft.contributor=Special+Collections+Research+Center%2C+University+of+Chicago&amp;rft.date=1577&amp;rft.format=44+x+29.7+cm&amp;rft.identifier=A1&amp;rft.source=Speculum+Romanae+Magnificentiae%2C+University+of+Chicago&amp;rft.language=La"></span>
</xsl:if>

<!-- CALL ZERO RESULTS, BROWSE, MULTIPLE RESULTS, OR SINGLE RESULT HERE -->
<xsl:apply-templates select="browse"/>
<xsl:apply-templates select="findaid">
<xsl:with-param name="loindex"><xsl:value-of select="$loindex"/></xsl:with-param>
<xsl:with-param name="hiindex"><xsl:value-of select="$hiindex"/></xsl:with-param>
</xsl:apply-templates>

</div><!--/SECTION-->

<br style="height: 2em;"/>

<div id="footer"></div>
</div><!--/CONTENT-->

</body>
</html>

</xsl:template>

</xsl:stylesheet>
