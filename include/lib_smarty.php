<?php

error_reporting(E_ERROR | E_PARSE);

require_once "../vendor/autoload.php";

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$dir = dirname(__FILE__);
$GLOBALS['timings']['smarty_comp_count']	= 0;
$GLOBALS['timings']['smarty_comp_time']		= 0;
$GLOBALS['cfg']['smarty_template_dir']	= $dir.'/../www/templates';
$GLOBALS['cfg']['smarty_compile_dir']	= $dir.'/../www/templates_c';
$GLOBALS['cfg']['smarty_compile']	= true;
$GLOBALS['cfg']['smarty_force_compile']	= true;

require '../vendor/smarty/smarty/libs/Smarty.class.php';

$GLOBALS['smarty'] = new Smarty();
$GLOBALS['smarty']->template_dir = $GLOBALS['cfg']['smarty_template_dir'];
$GLOBALS['smarty']->compile_dir  = $GLOBALS['cfg']['smarty_compile_dir'];
$GLOBALS['smarty']->compile_check = $GLOBALS['cfg']['smarty_compile'];
$GLOBALS['smarty']->force_compile = $GLOBALS['cfg']['smarty_force_compile'];

//Cache buster
$versions=time();
$GLOBALS['smarty']->assign('versions', $versions);

