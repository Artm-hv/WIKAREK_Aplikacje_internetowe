<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:template match="/">
<html>
<head><meta charset="utf-8"/><title>Baza XML: Wykładowca – Prowadzi – Zajęcia (3NF) - Relacje 3NF</title><link rel="stylesheet" href="style.css"/></head>
<body><div class="container">
<h1>Baza XML: Wykładowca – Prowadzi – Zajęcia (3NF)</h1>
<p class="info-box">Pełny raport powiązań relacyjnych (Join) z tabeli łączącej <strong>prowadzenia</strong>:</p>
<table>
<tr><th>ID Relacji</th><th>ID Ref1</th><th>ID Ref2</th><th>dzien</th> <th>godzina</th></tr>
<xsl:for-each select="baza/prowadzenia/przydzial">
<tr>
    <td><strong><xsl:value-of select="@id"/></strong></td>
    <td><xsl:value-of select="@ref1"/></td>
    <td><xsl:value-of select="@ref2"/></td>
    <td><xsl:value-of select='dzien'/></td> <td><xsl:value-of select='godzina'/></td>
</tr>
</xsl:for-each>
</table>
</div></body></html>
</xsl:template>
</xsl:stylesheet>
