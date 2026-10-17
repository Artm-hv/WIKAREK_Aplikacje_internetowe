<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
 <xsl:template match="/">
 <html>
  <title>
    STRONA Z WYNIKAMI ZAWODOW
 </title>
 <body>
   <xsl:apply-templates/> 
 </body>
 </html>
 </xsl:template>


 <xsl:template match="igrzyskaOlimpijskie">
  <p> 
   <center> <h1>
    Tabelarczny wykaz zlotych medalistow 
      <xsl:value-of select="@miasto"/> w roku <xsl:value-of select="@rok"/>
   </h1></center>
  </p>
   <table border="frame">
   <tr> <th>Dyscyplina</th>
        <th>Konkurencja</th>
        <th>Plec</th>
        <th>Nazwisko</th>
        <th>Panstwo</th>
    </tr>
    <xsl:apply-templates select="wynik"/> 
   </table>
 </xsl:template>

 <xsl:template match="wynik">
  <xsl:variable name="idZa">
    <xsl:value-of select="zloto/@idz" />
  </xsl:variable>
  <xsl:variable name="pan">
    <xsl:value-of select="/igrzyskaOlimpijskie/Uczestnicy/Panstwo/Zawodnik[@id=$idZa]/../@nazwa" />
  </xsl:variable>
  <xsl:if test="$pan='Polska'"> 
    <xsl:variable name="idKo">
      <xsl:value-of select="zloto/@idk" />
    </xsl:variable>
    <tr>
      <xsl:apply-templates select="id($idKo)"/>  
      <xsl:apply-templates select="id($idZa)"/>   
    </tr>
  </xsl:if>
 </xsl:template>


 <xsl:template match="Konkurencja">
   <td> <xsl:value-of select="@Nazwa" /></td>
   <td> <xsl:value-of select="../../@Nazwa" /></td>
   <td> <xsl:if test="../@Nazwa='K'">Kobieta </xsl:if>
        <xsl:if test="../@Nazwa='M'">Mezczyzna </xsl:if>
   </td>
 </xsl:template>
 

 <xsl:template match="Zawodnik">
   <td> <xsl:value-of select="Nazwisko" /></td>
   <td> <xsl:value-of select="../@nazwa" /></td>
 </xsl:template>

 



</xsl:stylesheet>
