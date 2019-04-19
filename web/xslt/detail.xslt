<?xml version="1.0"?>
<xsl:stylesheet version="1.0"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:variable name="header">
<h1>(<xsl:value-of select="/findaid/*/@refid"/>) <xsl:value-of select="/findaid/*/titleSet/title"/></h1>
</xsl:variable>

<xsl:include href="viewsingle.inc.xslt"/>

</xsl:stylesheet>
