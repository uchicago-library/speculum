<?xml version="1.0"?>
<xsl:stylesheet version="1.0"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:param name="list"/>
<xsl:param name="result"/>

<!-- page -->
<xsl:variable name="page" select="ceiling($result div 100)"/>
<!-- results -->
<xsl:variable name="results">
<xsl:choose>
<xsl:when test="$list = 'huelsen'"><xsl:value-of select="count(/findaid/work[huref])"/></xsl:when>
<xsl:otherwise><xsl:value-of select="count(/findaid/work)"/></xsl:otherwise>
</xsl:choose>
</xsl:variable>

<!-- header -->
<xsl:variable name="header">
<h1>
<a href="search.php?list={$list}&amp;page={$page}"><xsl:value-of select="document('lists.xml')/lists/list[@name = $list]"/></a> :
<xsl:choose>
<xsl:when test="$list = 'huelsen'">
(<xsl:value-of select="concat(/findaid/work[huref][position() = $result]/huref, '/Chicago ', /findaid/work[position() = $result]/@refid)"/>) <xsl:value-of select="/findaid/work[huref][position() = $result]/titleSet/title"/>
</xsl:when>
<xsl:otherwise>
(<xsl:value-of select="/findaid/work[position() = $result]/@refid"/>) <xsl:value-of select="/findaid/work[position() = $result]/titleSet/title"/>
</xsl:otherwise>
</xsl:choose>
</h1>
<p>
Result <xsl:value-of select="$result"/> of <xsl:value-of select="$results"/>: 
<xsl:choose>
<xsl:when test = "$result &gt; 1"><a href="search.php?list={$list}&amp;result={$result - 1}">previous</a></xsl:when>
<xsl:otherwise>previous</xsl:otherwise>
</xsl:choose>
|
<xsl:choose>
<xsl:when test="$result &lt; $results"><a href="search.php?list={$list}&amp;result={$result + 1}">next</a></xsl:when>
<xsl:otherwise>next</xsl:otherwise>
</xsl:choose>
</p>
</xsl:variable>

<xsl:include href="viewsingle.inc.xslt"/>

<!-- * -->
<xsl:template match="*">
<xsl:apply-templates/>
</xsl:template>

<!-- FINDAID -->
<xsl:template match="findaid">
<xsl:choose>
<xsl:when test="$list = 'huelsen'"><xsl:apply-templates select="work[huref][position() = $result]"/></xsl:when>
<xsl:otherwise><xsl:apply-templates select="work[position() = $result]"/></xsl:otherwise>
</xsl:choose>
</xsl:template>

</xsl:stylesheet>
