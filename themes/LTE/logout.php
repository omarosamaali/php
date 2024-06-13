<?php
define("FA_LOGOUT_PHP_FILE","");

$page_security = 'SA_OPEN';
$path_to_root="../..";
include($path_to_root . "/includes/session.inc");
session_unset();
@session_destroy();
header("location:".$path_to_root);
exit;
?>