<?php
$module = 'Sys';

require_once (_ROOT_DIR_.'/module/admin/setup.php');

setPage('ip_server', $_SERVER['SERVER_ADDR']);

showPage();