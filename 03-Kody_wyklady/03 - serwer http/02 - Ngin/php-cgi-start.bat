    @ECHO OFF
    ECHO Starting PHP FastCGI...
    C:\Oprogramowanie\Nginx\RunHiddenConsole.exe C:\oprogramowanie\Php8\php-cgi.exe -b localhost:9000
    ECHO Starting NGINX
    start nginx.exe
    popd
    EXIT /b