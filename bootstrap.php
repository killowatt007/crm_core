<?php
declare(strict_types = 1);

session_start();

error_reporting(E_ALL);
ini_set('display_errors', '1');

define('EXE', 1);
define('PATH_CLIENT', $_SERVER['DOCUMENT_ROOT'].'/client');
define('PATH_ROOT', $_SERVER['DOCUMENT_ROOT'].'/server');

function qqq($data, $var = false, $backt = false) { 
	$method = $var ? 'var_dump' : 'print_r';
	echo '%%__dump__%%'; $method($data); 

	if ($backt)
		echo print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));

	exit; 
}

include PATH_ROOT .'/libraries/bs/f.php';
$app = \F::getApp();
$app->exe();

// setcookie('_ffilter[34][14]', 1, time()-100, '/', $_SERVER['HTTP_HOST']);