@echo off
echo ========================================================
echo AUTOMATYCZNE PRZESYLANIE PLIKOW NA SERWER FTP (10.0.0.41)
echo Grupa: 3ID11B | Hizhovskyi & Namachynskyi
echo ========================================================
ftp -i -s:ftp_commands.txt 10.0.0.41 > ftp2.txt
type ftp2.txt
echo ========================================================
echo Gotowe! Log sesji zapisany do pliku ftp2.txt
pause
