<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:key name="k_e1" match="/baza/pracownicy/pracownik" use="@id"/>
<xsl:key name="k_e2" match="/baza/stanowiska/stanowisko" use="@id"/>

<xsl:template match="/">
<html>
<head>
    <meta charset="utf-8"/>
    <title>Baza XML: Pracownik – Pracuje – Stanowisko (3NF - Relacje z JOIN)</title>
    <link rel="stylesheet" href="style.css"/>
</head>
<body>
<div class="container">
    <header>
        <h1>Baza XML: Pracownik – Pracuje – Stanowisko (3NF)</h1>
        <p>Grupa: 3ID11B | Zespół: Artem Hizhovskyi (97882), Ihor Namachynskyi (97889)</p>
    </header>

    <div class="info-box">
        <strong>Raport relacyjny (Join 3NF):</strong> Tabela łącząca <code>zatrudnienia</code> z pełnym uzupełnieniem informacji z tabel podstawowych (<code>pracownicy</code> oraz <code>stanowiska</code>) za pomocą funkcji <code>key()</code> w XSLT.
    </div>

    <table>
        <thead>
            <tr>
                <th>ID Relacji</th>
                <th>Podmiot (pracownik)</th>
                <th>Obiekt (stanowisko)</th>
                <th>Data zatrudnienia</th>
                <th>Wymiar etatu</th>
            </tr>
        </thead>
        <tbody>
            <xsl:for-each select="/baza/zatrudnienia/umowa">
            <tr>
                <td><strong><xsl:value-of select="@id"/></strong></td>
                <td>
                    <strong><xsl:value-of select="key('k_e1', @ref1)/pesel"/></strong>&#160;
                    <xsl:value-of select="key('k_e1', @ref1)/imie"/>&#160;
                    <span style="color:#7f8c8d; font-size:12px;">(<xsl:value-of select="@ref1"/>)</span>
                </td>
                <td>
                    <strong><xsl:value-of select="key('k_e2', @ref2)/nazwa"/></strong>
                    <xsl:if test="key('k_e2', @ref2)/pensja">
                        &#160;- <xsl:value-of select="key('k_e2', @ref2)/pensja"/>
                    </xsl:if>
                    &#160;<span style="color:#7f8c8d; font-size:12px;">(<xsl:value-of select="@ref2"/>)</span>
                </td>
                <td><xsl:value-of select='od_dnia'/></td>
                <td><xsl:value-of select='etat'/></td>
            </tr>
            </xsl:for-each>
        </tbody>
    </table>
    
    <div style="margin-top:20px; font-size:13px; color:#555;">
        <a href="index.php" style="color:#2980b9; font-weight:bold; text-decoration:none;">&#8592; Przejdź do interaktywnego filtrowania SimpleXML (index.php)</a>
    </div>
</div>
</body>
</html>
</xsl:template>
</xsl:stylesheet>
