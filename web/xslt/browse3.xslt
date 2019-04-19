<?xml version="1.0"?>
<xsl:stylesheet version="1.0"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:param name="group"/>
<xsl:param name="result"/>

<!-- name -->
<xsl:variable name="name" select="/browse/@name"/>
<!-- results -->
<xsl:variable name="results" select="count(/browse/group[position() = $group]/findaid/work)"/>
<!-- header -->
<xsl:variable name="header">
<h1>
<a href="search.php?browse={$name}"><xsl:value-of select="document('browses.xml')/browses/browse[@name = $name]"/></a> :
<xsl:choose>
<xsl:when test="$results &gt; 1">
<a href="search.php?browse={$name}&amp;group={$group}&amp;page={$page}"><xsl:value-of select="/browse/group[position() = $group]/@name"/></a> :
</xsl:when>
<xsl:otherwise>
<xsl:value-of select="/browse/group[position() = $group]/@name"/> :
</xsl:otherwise>
</xsl:choose>
(<xsl:value-of select="/browse/group[position() = $group]/findaid/work[position() = $result]/@refid"/>) 
<xsl:value-of select="/browse/group[position() = $group]/findaid/work[position() = $result]/titleSet/title"/>
</h1>

<xsl:if test="$results = 1">
<p>1 result.</p>
</xsl:if>

<xsl:if test="$results &gt; 1">
<p>
Result <xsl:value-of select="$result"/> of <xsl:value-of select="$results"/>: 
<xsl:choose>
<xsl:when test = "$result &gt; 1"><a href="search.php?browse={$name}&amp;group={$group}&amp;result={$result - 1}">previous</a></xsl:when>
<xsl:otherwise>previous</xsl:otherwise>
</xsl:choose>
|
<xsl:choose>
<xsl:when test="$result &lt; $results"><a href="search.php?browse={$name}&amp;group={$group}&amp;result={$result + 1}">next</a></xsl:when>
<xsl:otherwise>next</xsl:otherwise>
</xsl:choose>
</p>
</xsl:if>
</xsl:variable>
<!-- page -->
<xsl:variable name="page" select="ceiling($result div 100)"/>

<xsl:include href="pager.xslt"/>
<xsl:include href="viewsingle.inc.xslt"/>

<!-- * -->
<xsl:template match="*">
<xsl:apply-templates/>
</xsl:template>

<!-- BROWSE -->
<xsl:template match="browse">
<xsl:apply-templates select="group[position() = $group]"/>
</xsl:template>

<!-- GROUP -->
<xsl:template match="group">
<xsl:apply-templates select="findaid/work[position() = $result]"/>
</xsl:template>

</xsl:stylesheet>
