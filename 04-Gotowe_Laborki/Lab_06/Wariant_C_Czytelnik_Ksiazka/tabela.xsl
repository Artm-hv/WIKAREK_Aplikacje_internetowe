<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:template match="/">
<html>
<head><meta charset="utf-8"/><title>Baza XML: Czytelnik – Książka – Wypożyczenie (3NF) - Tabela</title><link rel="stylesheet" href="style.css"/></head>
<body><div class="container">
<h1>Baza XML: Czytelnik – Książka – Wypożyczenie (3NF)</h1>
<p class="info-box">Widok pojedynczej tabeli encji: <strong>czytelnicy</strong></p>
<table>
<tr><th>ID</th><th>imie</th> <th>nazwisko</th> <th>karta</th></tr>
<xsl:for-each select="baza/czytelnicy/czytelnik">
<tr>
    <td><xsl:value-of select="@id"/></td>
    <td><xsl:value-of select='imie'/></td> <td><xsl:value-of select='nazwisko'/></td> <td><xsl:value-of select='karta'/></td>
</tr>
</xsl:for-each>
</table>
</div></body></html>
</xsl:template>
</xsl:stylesheet>
