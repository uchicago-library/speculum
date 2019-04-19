<?xml version="1.0"?>
<xsl:stylesheet version="1.0"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!-- SKIP -->
<xsl:template match="date | descriptionSet | display | group | huref | id | inscriptionSet | measurementsSet | name | notes | pub | ref | refid | relationSet | role | subjectSet | techniqueSet | textref | titleSet"/>

<!-- * -->
<xsl:template match="*">
<xsl:apply-templates/>
</xsl:template>

<!-- AGENT -->
<xsl:template match="agent[role/@refid='300025574']/name">
<p>Publisher: <xsl:apply-templates/></p>
</xsl:template>

<!-- CITY -->
<xsl:template match="location[@type='creation']/name">
<p>Location: <xsl:apply-templates/></p>
</xsl:template>

<!-- DATESET/DISPLAY -->
<xsl:template match="dateSet/display">
<p>Date: <xsl:apply-templates/></p>
</xsl:template>

<!-- HELPER TEMPLATES -->
<xsl:template name="getimage">
<xsl:param name="chicagonumber"/>
<img>
<xsl:attribute name="src">
<xsl:call-template name="buildpid">
<xsl:with-param name="number" select="substring($chicagonumber,2)"/>
</xsl:call-template>
</xsl:attribute>
</img>
</xsl:template>

<xsl:template name="buildpid">
<xsl:param name="number"/>
<xsl:choose>
<xsl:when test="string-length($number) &lt; 4">
	<xsl:call-template name="buildpid">
	<xsl:with-param name="number" select="concat('0',$number)"/>
	</xsl:call-template>
</xsl:when>
<xsl:otherwise>http://speculum.lib.uchicago.edu/images/searchthumbs/speculum-<xsl:value-of select="$number"/>-001g.jpg</xsl:otherwise>
</xsl:choose>
</xsl:template>

</xsl:stylesheet>
