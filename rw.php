<?php
global $_cfg;

$_cfg = array();

define('_ROOT_DIR_', realpath(dirname(__FILE__))); 

define('_LOG_DIR_', _ROOT_DIR_.'/logs/');

define('_TPL_DIR_', _ROOT_DIR_.'/tpl/');

require_once _ROOT_DIR_.'/vendor/autoload.php';

require_once _ROOT_DIR_.'/_config.php';

if (defined('DEBUG_MODE') && DEBUG_MODE) {

 	error_reporting(E_ALL);

	error_reporting(-1);

	ini_set('error_reporting', E_ALL);
    
    
    if (defined('DEBUG_LOG') && DEBUG_LOG) {
        
        ini_set('log_errors', TRUE); // Error logging

        ini_set('error_log', _LOG_DIR_.'php-errors.log'); // Logging file

        //ini_set('log_errors_max_len', 1024); // Logging file size
    }
    
    if (defined('DEBUG_DISPLAY') && !DEBUG_DISPLAY) {
     
        ini_set('display_errors', FALSE); // Error display - OFF in production env or real server
        
    } else {
        
        ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
    }

} else {
    
    error_reporting(0);
}

require_once _ROOT_DIR_.'/lib/main.php';

// Rewrite module

$_GS['https'] = ($_GS['https'] || isset($_SERVER['HTTPS']) || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') == 'https'));
$_GS['root_url'] = getRootURL($_GS['https']);
$_GS['module_dir'] = 'module/';

@include_once($_GS['module_dir'] . '_config.php');

function linkToModule($l)
{
	global $_rwlinks;
	$l = trim($l);
	if ('' === $l)
		$l = moduleToLink('index');
	foreach ($_rwlinks as $m => $r)
		if ($r[0] == $l)
			return $m;
	return '';
}

// https: 0-default / 1-on / 2-off
function moduleToLink($m = '', $chpu = false, $https = 0) // chpu - array(id, text[, text2...])
{
	global $_GS, $_rwlinks;
        
	if (!$m)
		$m = $_GS['module'];
	$r = $_rwlinks[$m];
	if (!$r)
		return '';
	if (is_array($chpu) and ($chpu[0] > 0) and ($chpu[1]))
		foreach ($chpu as $i => $m)
			if ($i == 0)
				$r[0] .= '/' . (0 + $chpu[0]);
			else
				$r[0] .= '/' . toTranslitURL($m);
	if ($https < 1)
		$https = $_GS['https_mode'] ?? '';
	if ($https >= 1)
		$r['https'] = ($https == 1);
	elseif ((isset($r['https']) && !$r['https']) || !isset($r['https']))
		$r['https'] = $_GS['https'] ?? '';
	return (($r['https'] xor $_GS['https']) ? fullURL($r[0], $r['https']) : ((!empty($r[0]) && $r[0] != 'home')?$_GS['root_url']:"").$r[0]);
}

function useLib($m = '')
{
	global $_GS, $_rwlinks;
	if (!$m)
		$m = $_GS['module'];
	while (!file_exists($f = $_GS['module_dir'] . $m .'/lib.php'))
	{
		cutElemR($m, '/');
		if (!$m)
			return;
	}
	require_once($f);
}

// Process URI

$p = $_GS['uri'];
$f = cutElemL($p, '?'); // get only link
if (!$_cfg['cfg_link'] or ($f == $_cfg['cfg_link']))
	$m = '_config';
elseif ($l = moduleToLink('index'))
{
	if (preg_match('|(.+)\/(\d+)\/|', $f, $m)) // chpu
	{
		$f = $m[1];
		$_GET['id'] = $m[2];
	}
    
	$m = linkToModule($f);
	if (!$m and ($f != $l))
	{
		xAddToLog($_GS['uri'], 'ul');
		header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
		header('Status: 404 Not Found');
		readfile('404.html');
/*		$sapi_name = php_sapi_name();
		if ($sapi_name == 'cgi' || $sapi_name == 'cgi-fcgi')
			header('Status: 404 Not Found');
		else
			header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');*/
		exit;
//		goToURL($l . ($p ? '?' . $p : '')); // on unknown link - go home
	}
}
else
	$m = 'index';
    
if (!file_exists($f = $_GS['module_dir'] . $m . '/index.php'))
	if (!file_exists($f = $_GS['module_dir'] . $m . '.php'))
		xSysStop("Rewrite: Module '$m' not found");

$_GS['module'] = $m; // account/login
$_GS['vmodule'] = (isset($_rwlinks[$m]['admin']) ? 'admin' : $m);
$_GS['script'] = $f; // module/account/login/*.php

if ($m != '_config')
{
	// Smarty init

	require_once('lib/tplutils.php');

	function tplModuleToLink($params)
	{
		return moduleToLink($params['module'] ?? '', $params['chpu'] ?? '', $params['https'] ?? '');
	}
	$tpl_page->registerPlugin('function', '_link', 'tplModuleToLink');
	setPage('_selfLink', moduleToLink());

	$_GS['demo'] = file_exists('tpl_c/demo');

	// onLoad init

	foreach ($_onload as $m => $s)
		if (file_exists($f = $_GS['module_dir'] . $m . '/onload.php'))
		{
//xecho($f);
			@include_once($f);
		}

	useLib(); // use default module lib
	
}

require($_GS['script']);
