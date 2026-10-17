if not exist ..\%1\%2   goto  KONIEC
if not exist ..\%1\%3   mkdir ..\%1\%3
 move %2 ..\%1\%3\
: KONIEC
