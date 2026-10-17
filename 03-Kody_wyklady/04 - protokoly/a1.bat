@ECHO OFF
REM - PARAMETER PIERWZY NAZWA SERWERA
REM - PARAMETER DRUGI NAZWA KATALOG DOCELOWEGO
ECHO PRZEGRYWAM DANE Z SERWERA 	: %1
ECHO DO KATALOGU DOCELOWEGO   	: %2
set godz=%time:~0,5%
set go=%time:~0,2%
set mi=%time:~3,2%
set godz1=%go%%m1%
set mies=%DATE:~0,2%
set dzi=%DATE:~3,2%
set dat1=k%dzi%%mies%%go%%mi%
ECHO DZISIAJ JEST %DATE:~3% GODZINA %godz%
ECHO KATALOG TYMCZASOWY TMP
if EXIST TMP DEL TMP/*.*
if EXIST TMP RMDIR TMP
MKDIR TMP
IF NOT EXIST %2 MKDIR %2
cd TMP
ftp -i -s:..\polecenia.txt %1
echo PLIKI SCIAGNIETE 
for %%a in (*.*) do call ..\a2.bat %2 %%a %dat1%
move *.* ..\%2\
cd ..


