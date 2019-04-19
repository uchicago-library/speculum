<?xml version="1.0"?>
<xsl:stylesheet version="1.0"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:param name="result"/>
<xsl:param name="titlestring"/>
<xsl:param name="urlstring"/>

<!-- page -->
<xsl:variable name="page" select="ceiling($result div 100)"/>
<!-- results -->
<xsl:variable name="results" select="count(/findaid/work)"/>

<!-- header -->
<xsl:variable name="header">
<h1>
<xsl:choose>
<xsl:when test="$results &gt; 1">
<a href="{$urlstring}&amp;page={$page}"><xsl:value-of select="$titlestring"/></a> 
</xsl:when>
<xsl:otherwise>
<xsl:value-of select="$titlestring"/>
</xsl:otherwise>
</xsl:choose>
:
(<xsl:value-of select="/findaid/work[position() = $result]/@refid"/>) 
<xsl:value-of select="/findaid/work[position() = $result]/titleSet/title"/>
</h1>

<xsl:if test="$results = 1">
<p>1 result.</p>
</xsl:if>

<xsl:if test="$results &gt; 1">
<p>Result <xsl:value-of select="$result"/> of <xsl:value-of select="$results"/>.

<xsl:choose>
<xsl:when test="$result &gt; 1"><a href="{$urlstring}&amp;result={$result - 1}">previous</a></xsl:when>
<xsl:otherwise>previous</xsl:otherwise>
</xsl:choose>
|
<xsl:choose>
<xsl:when test="$result &lt; $results"><a href="{$urlstring}&amp;result={$result + 1}">next</a></xsl:when>
<xsl:otherwise>next</xsl:otherwise>
</xsl:choose>
</p>
</xsl:if>
</xsl:variable>

<xsl:include href="viewsingle.inc.xslt"/>

<!-- * -->
<xsl:template match="*">
<xsl:apply-templates/>
</xsl:template>

<!-- / -->
<xsl:template match="/">
<xsl:if test="not(findaid/work)">
<h1><xsl:value-of select="$titlestring"/></h1>
<p>No results found.</p>
</xsl:if>
<xsl:apply-templates select="findaid/work[position() = $result]"/>
</xsl:template>

</xsl:stylesheet>
