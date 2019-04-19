<?xml version="1.0"?>
<xsl:stylesheet version="2.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="text()"/>

<xsl:template match="*">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="//subject/term[@vocab='LCSH']">
<xsl:copy-of select="."/>
</xsl:text>
</xsl:template>

</xsl:stylesheet>
