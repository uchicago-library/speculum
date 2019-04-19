<?xml version="1.0"?>
<xsl:stylesheet version="1.0"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:param name="group"/>
<xsl:param name="page"/>
<xsl:param name="urlstring"/>

<!-- results -->
<xsl:variable name="results" select="count(/browse/group[position() = $group]/findaid/work)"/>

<xsl:include href="pager.xslt"/>
<xsl:include href="viewmultiple.inc.xslt"/>

<!-- BROWSE -->
<xsl:template match="browse">
<xsl:variable name="name" select="@name"/>
<h1>
<a href="search.php?browse={@name}"><xsl:value-of select="document('browses.xml')/browses/browse[@name = $name]"/></a> :
<xsl:value-of select="group[position() = $group]/@name"/>
</h1>
<xsl:call-template name="pager"/>
<xsl:apply-templates select="group[position() = $group]"/>
<xsl:call-template name="pager"/>
</xsl:template>

<!-- FINDAID -->
<xsl:template match="findaid">
<xsl:apply-templates select="work[position() &gt;= $loresult and position() &lt;= $hiresult]"/>
</xsl:template>

<!-- GROUP -->
<xsl:template match="group">
<table>
<xsl:apply-templates/>
</table>
</xsl:template>

<!-- WORK -->
<xsl:template match="work">

<tr>
<td valign="top">
<p><xsl:value-of select="($page - 1) * $pagelength + position()"/>.</p>
</td>

<td style="height: 112px; text-align: center; vertical-align: middle;">
<a href="search.php?browse={ancestor::browse/@name}&amp;group={$group}&amp;result={($page - 1) * $pagelength + position()}">
<xsl:call-template name="getimage">
<xsl:with-param name="chicagonumber" select="@refid"/>
</xsl:call-template>
</a>
</td>

<td valign="top">

<p><a href="search.php?browse={ancestor::browse/@name}&amp;group={$group}&amp;result={($page - 1) * $pagelength + position()}"><b>(<xsl:value-of select="@refid"/>) <xsl:value-of select="titleSet/title"/></b></a></p>

<xsl:apply-templates/>

</td>
</tr>
</xsl:template>

</xsl:stylesheet>
