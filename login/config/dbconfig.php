<?php

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'memegust_dvid');
define('DB_PASSWORD', 'x-5Kvoce3,HM');
define('DB_DATABASE', 'memegust_info');
$connection = mysql_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD) or die(mysql_error());
$database = mysql_select_db(DB_DATABASE) or die(mysql_error());
?>
