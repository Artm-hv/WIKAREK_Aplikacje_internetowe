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
     O to dane na temat medalistow zawodow Olimpijskich rozegranych w 
      <xsl:value-of select="@miasto"/> w roku <xsl:value-of select="@rok"/>
   </h1></center>
   <H2> Wykaz zlotych medalistow  w ukadzie nazwisko wiek:</H2> 
  </p>
   <xsl:apply-templates select="wynik"/> 
 </xsl:template>

 <xsl:template match="wynik">
  <xsl:variable name="idZa">
    <xsl:value-of select="zloto/@idz" />
  </xsl:variable>
  <xsl:value-of 
     select="/igrzyskaOlimpijskie/Uczestnicy/Panstwo/Zawodnik[@id=$idZa]/Nazwisko"/>&#8194;
  <xsl:value-of 
      select="/igrzyskaOlimpijskie/Uczestnicy/Panstwo/Zawodnik[@id=$idZa]/Wiek"/> <BR/>
 </xsl:template>
</xsl:stylesheet>
