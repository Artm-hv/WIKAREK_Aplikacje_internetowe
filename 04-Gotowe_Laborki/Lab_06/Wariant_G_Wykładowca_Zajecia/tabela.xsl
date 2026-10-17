<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:template match="/">
<html>
<head><meta charset="utf-8"/><title>Baza XML: Wykładowca – Prowadzi – Zajęcia (3NF) - Tabela</title><link rel="stylesheet" href="style.css"/></head>
<body><div class="container">
<h1>Baza XML: Wykładowca – Prowadzi – Zajęcia (3NF)</h1>
<p class="info-box">Widok pojedynczej tabeli encji: <strong>wykladowcy</strong></p>
<table>
<tr><th>ID</th><th>tytul</th> <th>nazwisko</th> <th>katedra</th></tr>
<xsl:for-each select="baza/wykladowcy/wykladowca">
<tr>
    <td><xsl:value-of select="@id"/></td>
    <td><xsl:value-of select='tytul'/></td> <td><xsl:value-of select='nazwisko'/></td> <td><xsl:value-of select='katedra'/></td>
</tr>
</xsl:for-each>
</table>
</div></body></html>
</xsl:template>
</xsl:stylesheet>
