<?xml version="1.0"?>
<xsl:stylesheet version="1.0"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:param name="page" select="1"/>
<xsl:param name="titlestring"/>
<xsl:param name="urlstring"/>
<xsl:variable name="results" select="count(/findaid/work)"/>

<xsl:include href="viewmultiple.inc.xslt"/>
<xsl:include href="pager.xslt"/>

<!-- / -->
<xsl:template match="/">
<h1><xsl:value-of select="$titlestring"/></h1>
<xsl:call-template name="pager"/>
<table><xsl:apply-templates select="findaid/work[position() &gt;= $loresult and position() &lt;= $hiresult]" /></table>
<xsl:call-template name="pager"/>
</xsl:template>

<!-- WORK -->
<xsl:template match="work">
<tr>
<td valign="top">
<p><xsl:value-of select="($page - 1) * $pagelength + position()"/>.</p>
</td>

<td style="height: 112px; text-align: center; vertical-align: middle;">
<a href="{$urlstring}&amp;result={($page - 1) * $pagelength + position()}">
<xsl:call-template name="getimage">
<xsl:with-param name="chicagonumber" select="@refid"/>
</xsl:call-template>
</a>
</td>

<td valign="top">

<p><a href="{$urlstring}&amp;result={($page - 1) * $pagelength + position()}"><b>(<xsl:value-of select="@refid"/>) <xsl:value-of select="titleSet/title"/></b></a></p>

<xsl:apply-templates/>

</td>
</tr>
</xsl:template>

</xsl:stylesheet>
