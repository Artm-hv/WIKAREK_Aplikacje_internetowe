<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:template match="/">
<html>
<head>
    <meta charset="utf-8"/>
    <title>Baza XML: Wykładowca – Prowadzi – Zajęcia - Tabele podstawowe</title>
    <link rel="stylesheet" href="style.css"/>
</head>
<body>
<div class="container">
    <header>
        <h1>Baza XML: Wykładowca – Prowadzi – Zajęcia (Tabele Słownikowe)</h1>
        <p>Grupa: 3ID11B | Zespół: Artem Hizhovskyi (97882), Ihor Namachynskyi (97889)</p>
    </header>

    <h2>1. Tabela encji: wykladowcy</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tytuł naukowy</th>
                <th>Imię i Nazwisko</th>
                <th>Katedra</th>
            </tr>
        </thead>
        <tbody>
            <xsl:for-each select="/baza/wykladowcy/wykladowca">
            <tr>
                <td><strong><xsl:value-of select="@id"/></strong></td>
                <td><xsl:value-of select='tytul'/></td>
                <td><xsl:value-of select='nazwisko'/></td>
                <td><xsl:value-of select='katedra'/></td>
            </tr>
            </xsl:for-each>
        </tbody>
    </table>

    <h2 style="margin-top:30px;">2. Tabela encji: zajecia_lista</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Przedmiot</th>
                <th>Forma</th>
                <th>Liczba godzin</th>
            </tr>
        </thead>
        <tbody>
            <xsl:for-each select="/baza/zajecia_lista/zajecia">
            <tr>
                <td><strong><xsl:value-of select="@id"/></strong></td>
                <td><xsl:value-of select='przedmiot'/></td>
                <td><xsl:value-of select='forma'/></td>
                <td><xsl:value-of select='godziny'/></td>
            </tr>
            </xsl:for-each>
        </tbody>
    </table>
</div>
</body>
</html>
</xsl:template>
</xsl:stylesheet>
