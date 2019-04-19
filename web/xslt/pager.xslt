<?xml version="1.0"?>
<xsl:stylesheet version="1.0"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!-- 
     This template needs the count of results (the results variable) to be available globally.
     That's because for the browses, lists, and searches you figure out the total number
     of results differently each time. 
  -->

<!-- pagelength -->
<xsl:variable name="pagelength" select="100"/>
<!-- pages -->
<xsl:variable name="pages" select="ceiling($results div $pagelength)"/>
<!-- hiresult -->
<xsl:variable name="hiresult">
<xsl:choose>
<xsl:when test="$results &lt; $page * $pagelength"><xsl:value-of select="$results"/></xsl:when>
<xsl:otherwise><xsl:value-of select="$page * $pagelength"/></xsl:otherwise>
</xsl:choose>
</xsl:variable>
<!-- loresult -->
<xsl:variable name="loresult" select="($page - 1) * $pagelength + 1"/>

<!-- PAGER -->
<xsl:template name="pager">
<xsl:choose>
<xsl:when test="$results &lt; $pagelength"><p><xsl:value-of select="$results"/> results.</p></xsl:when>
<xsl:otherwise><p>Results <xsl:value-of select="$loresult"/> to <xsl:value-of select="$hiresult"/> of <xsl:value-of select="$results"/>.</p></xsl:otherwise>
</xsl:choose>

<xsl:if test="$results &gt; $pagelength">
<p>
<xsl:choose>
<xsl:when test="$page &gt; 1"><a href="{$urlstring}&amp;page={$page - 1}">previous page</a></xsl:when>
<xsl:otherwise>previous page</xsl:otherwise>
</xsl:choose>
|
<xsl:choose>
<xsl:when test="$page &lt; $pages"><a href="{$urlstring}&amp;page={$page + 1}">next page</a></xsl:when>
<xsl:otherwise>next page</xsl:otherwise>
</xsl:choose>
</p>
</xsl:if>

<xsl:if test="$pages &gt; 1">
<p>Jump to results page: <xsl:call-template name="pagersub"/></p>
</xsl:if>
<br/>
</xsl:template>

<!-- PAGERSUB -->
<xsl:template name="pagersub">
<xsl:param name="p" select="1"/>
<xsl:choose>
<xsl:when test="$p = $page"><xsl:value-of select="$p"/></xsl:when>
<xsl:otherwise><a href="{$urlstring}&amp;page={$p}"><xsl:value-of select="$p"/></a></xsl:otherwise>
</xsl:choose>
<xsl:text> </xsl:text>
<xsl:if test="$p &lt; $pages"><xsl:call-template name="pagersub"><xsl:with-param name="p" select="$p + 1"/></xsl:call-template></xsl:if>
</xsl:template>

</xsl:stylesheet>
