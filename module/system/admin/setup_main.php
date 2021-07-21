<?php
$_auth = 90;
require_once('module/auth.php');

$module = 'Sys';

require_once (_ROOT_DIR_.'/module/admin/setup.php');

setPage('ip_server', $_SERVER['SERVER_ADDR']);

showPage();