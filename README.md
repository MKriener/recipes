# recipebook
## setup
to setup debug on windows

File-> Settings -> Framework -> PHP
as PHP Version choose *php 7.4*

at CLI interpreter click on ...  
select *docker*  
select *rbphp-fpm*   
set *xdebug.so* as extension  

Run -> Edit Configuration  
add PHP-RemoteDebug  
add new server  
set *localhost* Port *80* Debugger *Xdebug*  

at path mappings go to *src* and define */var/www* as remote path  

to add Symfony after default setup log in to 