<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:template match="/">
<html>
<head>
    <meta charset="utf-8"/>
    <title>Baza XML: Samolot – Rezerwacja – Pasażer - Tabele podstawowe</title>
    <link rel="stylesheet" href="style.css"/>
</head>
<body>
<div class="container">
    <header>
        <h1>Baza XML: Samolot – Rezerwacja – Pasażer (Tabele Słownikowe)</h1>
        <p>Grupa: 3ID11B | Zespół: Artem Hizhovskyi (97882), Ihor Namachynskyi (97889)</p>
    </header>

    <h2>1. Tabela encji: pasazerowie</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Imię</th>
                <th>Nazwisko</th>
                <th>Nr Paszportu</th>
            </tr>
        </thead>
        <tbody>
            <xsl:for-each select="/baza/pasazerowie/pasazer">
            <tr>
                <td><strong><xsl:value-of select="@id"/></strong></td>
                <td><xsl:value-of select='imie'/></td>
                <td><xsl:value-of select='nazwisko'/></td>
                <td><xsl:value-of select='paszport'/></td>
            </tr>
            </xsl:for-each>
        </tbody>
    </table>

    <h2 style="margin-top:30px;">2. Tabela encji: samoloty</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Model samolotu</th>
                <th>Linie lotnicze</th>
                <th>Liczba miejsc</th>
            </tr>
        </thead>
        <tbody>
            <xsl:for-each select="/baza/samoloty/samolot">
            <tr>
                <td><strong><xsl:value-of select="@id"/></strong></td>
                <td><xsl:value-of select='model'/></td>
                <td><xsl:value-of select='linie'/></td>
                <td><xsl:value-of select='miejsca'/></td>
            </tr>
            </xsl:for-each>
        </tbody>
    </table>
</div>
</body>
</html>
</xsl:template>
</xsl:stylesheet>
