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
   <H2> Uzyskano nastepujace wyniki w dyscyplinach :</H2> 
  </p>
  <xsl:apply-templates/>
 </xsl:template>

 <xsl:template match="dyscyplina"> 
   <p><h3> Dyscyplina: <xsl:value-of select="@nazwa"/> </h3></p>
  <xsl:apply-templates/>
 </xsl:template>

 <xsl:template match="plec"> 
  <xsl:apply-templates/>
 </xsl:template>


 <xsl:template match="konkurencja">
  <p><h3> Konkurencje: <xsl:value-of select="@nazwa"/>
  &#8194; <xsl:value-of select="../@nazwa"/>
  </h3></p>
    <BR></BR>
     ZLOTO DLA : <xsl:value-of select="zloto/nazwisko"/> 
       Z KRAJU : <xsl:value-of select="zloto/panstwo"/> 
         WYNIK : <xsl:value-of select="zloto/wynik"/>
 </xsl:template>

</xsl:stylesheet>
