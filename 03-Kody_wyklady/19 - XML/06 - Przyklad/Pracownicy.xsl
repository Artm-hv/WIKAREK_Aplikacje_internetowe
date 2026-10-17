<?xml version="1.0" encoding="UTF-8" ?>
 <xsl:stylesheet version="1.0"
   xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
 
  <xsl:template match="/">
    <html>
     <head>
      <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
      <title>XML PRZYKLADOWA BAZA</title>
      <link rel="stylesheet" type="text/css" href="style.css" />
     </head >
     <body>
      <div id="header">
       <b>ZATRUDNIENIE</b>
      </div>
      <div id="content">
       <xsl:apply-templates/>
     </div>
    </body>
   </html>
 </xsl:template>
 
 <xsl:template match="pracowanie">
 <h2>Lista prac:</h2>
  <table>
   <tr>
    <td>ID</td>
    <td>ID - Pracownika</td>
    <td>Nazwisko pracownika</td>
    <td>Imie pracownika</td>
    <td>ID - Stanowiska</td>
    <td>Stanowisko</td>
    <td>Pensja</td>
    <td>Lata przepracowane</td>
    <td>Miesiące przepracowane</td>
   </tr>
   <xsl:for-each select="pracuje">
    <tr>
     <xsl:variable name="id_pracownik">
      <xsl:value-of select="@pracownik"/>
     </xsl:variable>
     <xsl:variable name="id_stanowisko">
      <xsl:value-of select="@stanowisko"/>
     </xsl:variable>
     <td><xsl:value-of select="@id"/></td>
     <td><xsl:value-of select="$id_pracownik"/></td>
     <td><xsl:value-of select="//pracownicy/pracownik[@id=$id_pracownik]/nazwisko"/></td>
     <td><xsl:value-of select="//pracownicy/pracownik[@id=$id_pracownik]/imie"/></td>
     <td><xsl:value-of select="$id_stanowisko"/></td>
     <td><xsl:value-of select="//stanowiska/stanowisko[@id=$id_stanowisko]/nazwa"/></td>
     <td><xsl:value-of select="//stanowiska/stanowisko[@id=$id_stanowisko]/pensja"/></td>
     <td><xsl:value-of select="lata"/></td>
     <td><xsl:value-of select="miesiace"/></td>
    </tr>
   </xsl:for-each>
  </table>
 </xsl:template>

 <xsl:template match="pracownicy">
  <h2>Lista pracowników:</h2>
  <table>
   <tr>
    <td>ID</td>
    <td>Nazwisko</td>
    <td>Imie</td>
    <td>Wiek</td>
   </tr>
   <xsl:for-each select="pracownik">
    <tr>
     <td><xsl:value-of select="@id"/></td>
     <td><xsl:value-of select="nazwisko"/></td>
     <td><xsl:value-of select="imie"/></td>
     <td><xsl:value-of select="wiek"/></td>
    </tr>
   </xsl:for-each>
  </table>
 </xsl:template>
 <xsl:template match="stanowiska">
  <h2>Lista stanowisk:</h2>
  <table>
   <tr>
    <td>ID</td>
    <td>Nazwa</td>
    <td>Pensja</td>
   </tr>
   <xsl:for-each select="stanowisko">
    <tr>
     <td><xsl:value-of select="@id"/></td>
     <td><xsl:value-of select="nazwa"/></td>
     <td><xsl:value-of select="pensja"/></td>
    </tr>
   </xsl:for-each>
  </table>
 </xsl:template>
  
</xsl:stylesheet>
