<?php

spl_autoload_register('myAutoloader');

/**
 * Standard autoloader somewhat modelled on <http://www.php-fig.org/psr/psr-0/>
 * but with less legacy cruft and other minor local differences.
 */
function myAutoloader($class_name) {
    $path = 'include/';
    $class_name = str_replace('\\', '/', $class_name);
    require $path . $class_name . '.php';
}
