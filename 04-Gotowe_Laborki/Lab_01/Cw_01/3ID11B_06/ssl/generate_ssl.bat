@echo off
echo Generowanie certyfikatu SSL dla www.meble.pl (Grupa 3ID11B)
openssl req -new -newkey rsa:2048 -nodes -keyout meble.key -out meble.csr -subj "/C=PL/ST=3ID11B/L=97882-97889/O=Politechnika Swietokrzyska/CN=www.meble.pl"
openssl x509 -req -days 365 -in meble.csr -signkey meble.key -out meble.crt
echo Certyfikaty meble.key oraz meble.crt zostaly poprawnie wygenerowane!
pause
