<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:template match="/">
<html>
<head><meta charset="utf-8"/><title>Baza XML: Wycieczka – Rezerwacja – Uczestnik (3NF) - Tabela</title><link rel="stylesheet" href="style.css"/></head>
<body><div class="container">
<h1>Baza XML: Wycieczka – Rezerwacja – Uczestnik (3NF)</h1>
<p class="info-box">Widok pojedynczej tabeli encji: <strong>uczestnicy</strong></p>
<table>
<tr><th>ID</th><th>imie</th> <th>nazwisko</th> <th>kraj</th></tr>
<xsl:for-each select="baza/uczestnicy/uczestnik">
<tr>
    <td><xsl:value-of select="@id"/></td>
    <td><xsl:value-of select='imie'/></td> <td><xsl:value-of select='nazwisko'/></td> <td><xsl:value-of select='kraj'/></td>
</tr>
</xsl:for-each>
</table>
</div></body></html>
</xsl:template>
</xsl:stylesheet>
