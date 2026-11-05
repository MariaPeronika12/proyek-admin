<?php

echo "PHP Version: " . phpversion() . "<br>";

echo "MySQLi: ";
var_dump(extension_loaded('mysqli'));

echo "<br>cURL: ";
var_dump(extension_loaded('curl'));

echo "<br>OpenSSL: ";
var_dump(extension_loaded('openssl'));

?>