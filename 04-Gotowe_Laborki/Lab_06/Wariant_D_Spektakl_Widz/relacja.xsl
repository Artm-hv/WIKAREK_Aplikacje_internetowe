<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:template match="/">
<html>
<head><meta charset="utf-8"/><title>Baza XML: Spektakl – Rezerwacja – Widz (3NF) - Relacje 3NF</title><link rel="stylesheet" href="style.css"/></head>
<body><div class="container">
<h1>Baza XML: Spektakl – Rezerwacja – Widz (3NF)</h1>
<p class="info-box">Pełny raport powiązań relacyjnych (Join) z tabeli łączącej <strong>rezerwacje</strong>:</p>
<table>
<tr><th>ID Relacji</th><th>ID Ref1</th><th>ID Ref2</th><th>data</th> <th>miejsce</th></tr>
<xsl:for-each select="baza/rezerwacje/rezerwacja">
<tr>
    <td><strong><xsl:value-of select="@id"/></strong></td>
    <td><xsl:value-of select="@ref1"/></td>
    <td><xsl:value-of select="@ref2"/></td>
    <td><xsl:value-of select='data'/></td> <td><xsl:value-of select='miejsce'/></td>
</tr>
</xsl:for-each>
</table>
</div></body></html>
</xsl:template>
</xsl:stylesheet>
