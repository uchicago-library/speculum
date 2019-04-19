<?xml version="1.0"?>
<xsl:stylesheet version="1.0"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:param name="list"/>
<xsl:param name="page"/>
<xsl:param name="urlstring"/>

<!-- results -->
<xsl:variable name="results">
<xsl:choose>
<xsl:when test="$list = 'huelsen'"><xsl:value-of select="count(/findaid/work[huref])"/></xsl:when>
<xsl:otherwise><xsl:value-of select="count(/findaid/work)"/></xsl:otherwise>
</xsl:choose>
</xsl:variable>

<xsl:include href="pager.xslt"/>
<xsl:include href="viewmultiple.inc.xslt"/>

<!-- description -->
<xsl:template match="description"/>

<!-- FINDAID -->
<xsl:template match="findaid">
<xsl:choose>
<xsl:when test="$list = 'huelsen'"><h1>Browse by Huelsen Number</h1></xsl:when>
<xsl:otherwise><h1>Browse by Chicago Number</h1></xsl:otherwise>
</xsl:choose>
<xsl:call-template name="pager"/>
<table>
<xsl:choose>
	<xsl:when test="$list = 'huelsen'">
	    <xsl:apply-templates select="work[huref][position() &gt;= $loresult and position() &lt;= $hiresult]"/>
	</xsl:when>
    <xsl:otherwise>
	    <xsl:apply-templates select="work[position() &gt;= $loresult and position() &lt;= $hiresult]"/>
    </xsl:otherwise>
</xsl:choose>
</table>
<xsl:call-template name="pager"/>
</xsl:template>

<!-- WORK -->
<xsl:template match="work">
<!-- number -->
<xsl:variable name="number">
<xsl:choose>
<xsl:when test="$list = 'huelsen'"><xsl:value-of select="huref"/></xsl:when>
<xsl:otherwise><xsl:value-of select="@refid"/></xsl:otherwise>
</xsl:choose>
</xsl:variable>

<tr>
<td valign="top">
<p><xsl:value-of select="($page - 1) * $pagelength + position()"/>.</p>
</td>

<td style="height: 112px; text-align: center; vertical-align: middle;">
<a href="search.php?list={$list}&amp;result={($page - 1) * $pagelength + position()}">
<xsl:call-template name="getimage">
<xsl:with-param name="chicagonumber" select="@refid"/>
</xsl:call-template>
</a>
</td>

<td valign="top">
<p><a href="search.php?list={$list}&amp;result={($page - 1) * $pagelength + position()}"><b>(<xsl:value-of select="$number"/>) <xsl:value-of select="titleSet/title"/></b></a></p>
<xsl:apply-templates/>
</td>
</tr>
</xsl:template>

</xsl:stylesheet>
