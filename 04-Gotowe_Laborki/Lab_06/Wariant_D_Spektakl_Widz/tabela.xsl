<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:template match="/">
<html>
<head>
    <meta charset="utf-8"/>
    <title>Baza XML: Spektakl – Rezerwacja – Widz - Tabele podstawowe</title>
    <link rel="stylesheet" href="style.css"/>
</head>
<body>
<div class="container">
    <header>
        <h1>Baza XML: Spektakl – Rezerwacja – Widz (Tabele Słownikowe)</h1>
        <p>Grupa: 3ID11B | Zespół: Artem Hizhovskyi (97882), Ihor Namachynskyi (97889)</p>
    </header>

    <h2>1. Tabela encji: widzowie</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Imię</th>
                <th>Nazwisko</th>
                <th>Telefon</th>
            </tr>
        </thead>
        <tbody>
            <xsl:for-each select="/baza/widzowie/widz">
            <tr>
                <td><strong><xsl:value-of select="@id"/></strong></td>
                <td><xsl:value-of select='imie'/></td>
                <td><xsl:value-of select='nazwisko'/></td>
                <td><xsl:value-of select='telefon'/></td>
            </tr>
            </xsl:for-each>
        </tbody>
    </table>

    <h2 style="margin-top:30px;">2. Tabela encji: spektakle</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tytuł Spektaklu</th>
                <th>Reżyser</th>
                <th>Cena biletu (PLN)</th>
            </tr>
        </thead>
        <tbody>
            <xsl:for-each select="/baza/spektakle/spektakl">
            <tr>
                <td><strong><xsl:value-of select="@id"/></strong></td>
                <td><xsl:value-of select='tytul'/></td>
                <td><xsl:value-of select='rezyser'/></td>
                <td><xsl:value-of select='cena'/></td>
            </tr>
            </xsl:for-each>
        </tbody>
    </table>
</div>
</body>
</html>
</xsl:template>
</xsl:stylesheet>
