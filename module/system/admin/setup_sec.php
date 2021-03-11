<?php

$module = 'Sec';
setPage('via_https', $_GS['https']);
setPage('curr_ip', $_GS['client_ip']);
require_once (_ROOT_DIR_.'/module/admin/setup.php');

showPage();