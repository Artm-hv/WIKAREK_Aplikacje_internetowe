<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:template match="/">
<html>
<head>
    <meta charset="utf-8"/>
    <title>Baza XML: Student – Ocena – Przedmiot - Tabele podstawowe</title>
    <link rel="stylesheet" href="style.css"/>
</head>
<body>
<div class="container">
    <header>
        <h1>Baza XML: Student – Ocena – Przedmiot (Tabele Słownikowe)</h1>
        <p>Grupa: 3ID11B | Zespół: Artem Hizhovskyi (97882), Ihor Namachynskyi (97889)</p>
    </header>

    <h2>1. Tabela encji: studenci</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nr Albumu</th>
                <th>Imię</th>
                <th>Nazwisko</th>
            </tr>
        </thead>
        <tbody>
            <xsl:for-each select="/baza/studenci/student">
            <tr>
                <td><strong><xsl:value-of select="@id"/></strong></td>
                <td><xsl:value-of select='nr_albumu'/></td>
                <td><xsl:value-of select='imie'/></td>
                <td><xsl:value-of select='nazwisko'/></td>
            </tr>
            </xsl:for-each>
        </tbody>
    </table>

    <h2 style="margin-top:30px;">2. Tabela encji: przedmioty</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Kod Przedmiotu</th>
                <th>Nazwa Przedmiotu</th>
                <th>Punkty ECTS</th>
            </tr>
        </thead>
        <tbody>
            <xsl:for-each select="/baza/przedmioty/przedmiot">
            <tr>
                <td><strong><xsl:value-of select="@id"/></strong></td>
                <td><xsl:value-of select='kod'/></td>
                <td><xsl:value-of select='nazwa'/></td>
                <td><xsl:value-of select='ects'/></td>
            </tr>
            </xsl:for-each>
        </tbody>
    </table>
</div>
</body>
</html>
</xsl:template>
</xsl:stylesheet>
