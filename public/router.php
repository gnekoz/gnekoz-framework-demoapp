<?php

$_SERVER['GNEKOZ_PROFILE'] = 'development';

if (preg_match('/\.(?:png|jpg|jpeg|gif|css|js)$/', $_SERVER["REQUEST_URI"])) {
    return false;
} else { 
    require 'bootloader.php';
}
?>
