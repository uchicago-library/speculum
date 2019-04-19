<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output omit-xml-declaration="yes"/>

<xsl:template match="browse">
<xsl:if test="@name='city'">
<h1>Browse by City</h1>
</xsl:if>
<xsl:if test="@name='collection'">
<h1>Browse by Group</h1>
</xsl:if>
<xsl:if test="@name='date'">
<h1>Browse by Date</h1>
</xsl:if>
<xsl:if test="@name='engraver'">
<h1>Browse by Engraver</h1>
</xsl:if>
<xsl:if test="@name='huelsen'">
<h1>Browse by Huelsen Number</h1>
</xsl:if>
<xsl:if test="@name='number'">
<h1>Browse by Chicago Number</h1>
</xsl:if>
<xsl:if test="@name='printmaker'">
<h1>Browse by Printmaker</h1>
</xsl:if>
<xsl:if test="@name='publisher'">
<h1>Browse by Publisher</h1>
</xsl:if>
<xsl:if test="@name='subject'">
<h1>Browse by Subject</h1>
</xsl:if>
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="group">
<p>
<a href="search.php?browse={parent::browse/@name}&amp;group={position()}">
<xsl:value-of select="@name"/> (<xsl:value-of select="count(findaid/work)"/>) 
</a>
</p>
</xsl:template>

</xsl:stylesheet>
