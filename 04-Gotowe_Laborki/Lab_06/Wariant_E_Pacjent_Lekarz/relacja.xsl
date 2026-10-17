<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:key name="k_e1" match="/baza/pacjenci/pacjent" use="@id"/>
<xsl:key name="k_e2" match="/baza/lekarze/lekarz" use="@id"/>

<xsl:template match="/">
<html>
<head>
    <meta charset="utf-8"/>
    <title>Baza XML: Pacjent – Badanie – Lekarz (3NF - Relacje z JOIN)</title>
    <link rel="stylesheet" href="style.css"/>
</head>
<body>
<div class="container">
    <header>
        <h1>Baza XML: Pacjent – Badanie – Lekarz (3NF)</h1>
        <p>Grupa: 3ID11B | Zespół: Artem Hizhovskyi (97882), Ihor Namachynskyi (97889)</p>
    </header>

    <div class="info-box">
        <strong>Raport relacyjny (Join 3NF):</strong> Tabela łącząca <code>badania</code> z pełnym uzupełnieniem informacji z tabel podstawowych (<code>pacjenci</code> oraz <code>lekarze</code>) za pomocą funkcji <code>key()</code> w XSLT.
    </div>

    <table>
        <thead>
            <tr>
                <th>ID Relacji</th>
                <th>Podmiot (pacjent)</th>
                <th>Obiekt (lekarz)</th>
                <th>Data badania</th>
                <th>Rodzaj badania</th>
            </tr>
        </thead>
        <tbody>
            <xsl:for-each select="/baza/badania/badanie">
            <tr>
                <td><strong><xsl:value-of select="@id"/></strong></td>
                <td>
                    <strong><xsl:value-of select="key('k_e1', @ref1)/pesel"/></strong>&#160;
                    <xsl:value-of select="key('k_e1', @ref1)/imie"/>&#160;
                    <span style="color:#7f8c8d; font-size:12px;">(<xsl:value-of select="@ref1"/>)</span>
                </td>
                <td>
                    <strong><xsl:value-of select="key('k_e2', @ref2)/imie_nazwisko"/></strong>
                    <xsl:if test="key('k_e2', @ref2)/specjalizacja">
                        &#160;- <xsl:value-of select="key('k_e2', @ref2)/specjalizacja"/>
                    </xsl:if>
                    &#160;<span style="color:#7f8c8d; font-size:12px;">(<xsl:value-of select="@ref2"/>)</span>
                </td>
                <td><xsl:value-of select='data'/></td>
                <td><xsl:value-of select='opis'/></td>
            </tr>
            </xsl:for-each>
        </tbody>
    </table>
    
    <div style="margin-top:20px; font-size:13px; color:#555;">
        <a href="index.php" style="color:#2980b9; font-weight:bold; text-decoration:none;">&larr; Przejdź do interaktywnego filtrowania SimpleXML (index.php)</a>
    </div>
</div>
</body>
</html>
</xsl:template>
</xsl:stylesheet>
