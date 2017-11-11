<?php
//------------------------------------------------------------
// File name   : init.php
// Description : file to handle initialize informations
//
// © 2010-2011 PaymentOne Corportation. All rights reserved.
//
// Created date : 16-02-2010
// Modified date: 31-10-2011
// ------------------------------------------------------------
ob_start();
session_start();
header("Cache-Control: no-cache, must-revalidate"); 
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
define('DEBUG_START',1);
define('DEBUG_STOP',2);
define("DOMAIN_NAME","http://$_SERVER[HTTP_HOST]/");
define('FULL_PATH', "$_SERVER[DOCUMENT_ROOT]/");
//Include Files
$include_files_array =  array( 'ERROR_HANDLING_FILE'   => FULL_PATH."includes/functions/errorhandle.php",
                                'SMARTY_CLASS_FILE'     => FULL_PATH."libs/Smarty.class.php",
                                'GENERAL_CLASS_FILE'    => FULL_PATH."includes/classes/general.class.php",
                                'DATABASE_CLASS_FILE'   => FULL_PATH."includes/classes/database.class.php",
                                'DB_CONFIG_FILE'        => FULL_PATH."includes/configs/db.php",
                                'TABLES_CONFIG_FILE'    => FULL_PATH."includes/configs/tables.php",
                                'GLOBAL_CONFIG_FILE'    => FULL_PATH."includes/configs/globalconfigs.php",
                                'PAGER_FILE'            => FULL_PATH."includes/functions/pager.php"
                              );
foreach($include_files_array as $file)
{
    if(!file_exists($file))
    {
        $file = base64_encode(basename($file));
        $re_direct=explode("/",$_SERVER['PHP_SELF']);
        if($re_direct[1]=='admin')
        {
            header("LOCATION:".DOMAIN_NAME."admin/nofile.php?file=".$file);
            die;
        }
        else 
        {
            header("LOCATION:".DOMAIN_NAME."nofile.php?file=".$file);
            die;
        }

    }
    else
    {
        require_once($file);
    }
}
/*
	Autoload the default class
*/
function __autoload($class_name)
{
	$class_name = strtolower($class_name);
	require FULL_PATH.'includes/classes/'.$class_name.'.class.php';
}

$glb_obj_genral = new ClassGeneral; //create object for general class
$glb_obj_genral->InitDb($glb_dbusername, $glb_dbpassword, $glb_dbname, $glb_dbhostname );
$debug_obj = new debug;
$merchants_obj= new Merchants();
$common_obj= new Common();
$smarty= new Smarty;
if(isset($_SESSION['admin_id']))
{
    $debug = array('file'=>'init.php', 'line'=>'cms_account_url_test');
    $account_xml = $common_obj ->CmsCurlGet($glb_cms_account_url,$_SESSION['admin_email'],$_SESSION['admin_cms_pwd'],$query_string,$debug);/*cms_account_url_test*/
    $debug = array('file'=>'init.php', 'line'=>'cms_product_url_test');
    $product_xml =  $common_obj ->CmsCurlGet($glb_cms_product_url,$_SESSION['admin_email'],$_SESSION['admin_cms_pwd'],$query_string,$debug);/*cms_product_url_test*/
}
else
{
    $debug = array('file'=>'init.php', 'line'=>'cms_account_url_test');
    $account_xml = $common_obj ->CmsCurlGet($glb_cms_account_url,$glb_cms_admin_email,$glb_cms_admin_pwd,$query_string,$debug);/*cms_account_url_test*/
    $debug = array('file'=>'init.php', 'line'=>'cms_product_url_test');
    $product_xml =  $common_obj ->CmsCurlGet($glb_cms_product_url,$glb_cms_admin_email,$glb_cms_admin_pwd,$query_string,$debug);/*cms_product_url_test*/
}
$re_direct=explode("/",$_SERVER ['PHP_SELF']);
if($re_direct[1]=='admin')
{
    if($re_direct[2] == 'activemerchants.php')
    {
        $page = "&page=select_merchant";
    }
}

if($account_xml=="CMS FAILED")
{
    $debug = array('file'=>'init.php', 'line'=>'write_cms_log');
    $common_obj->WriteCMSLog("CMS Account URL is wrong","init.php",$debug); /*write_cms_log*/
    header("LOCATION:".DOMAIN_NAME."admin/nofile.php?errormsg=account$page");
    die;
}

if($product_xml=="CMS FAILED")
{
    $debug = array('file'=>'init.php', 'line'=>'write_cms_log');
    $common_obj->WriteCMSLog("CMS Product URL is wrong","init.php",$debug); /*write_cms_log*/
    header("LOCATION:".DOMAIN_NAME."admin/nofile.php?errormsg=product$page");
    die;
}
// if(preg_match ("/[\*\,\.\'\"]/i", urldecode($_SERVER['QUERY_STRING'])) == '1')
// {
//     header('Location:index.php???try again');
// }
extract($_SERVER);
$phpself = explode("/", $PHP_SELF);
$phpself = array_reverse($phpself);
global $base_path;
if($phpself[1] == "admin")
{
    $base_path = FULL_PATH;
    $template_path = $base_path.'templates/default/admin/';
    $smarty->template_dir = FULL_PATH;
}
else
{
    $base_path = "";
    $template_path = $base_path.'templates/';
    $smarty->template_dir = $template_path;
}
$smarty->compile_dir = $base_path.'templates_c/';
$smarty->config_dir = $base_path.'configs/';
$smarty->cache_dir = $base_path.'cache/';
$smarty->caching = false;
// Smarty debugging on/off
$smarty->debugging=(trim($_GET['dev'])==DEBUG_START) ? true : false;
// PHP debugging on/off
if(isset($_GET['debug']))
{
    if($_GET['debug']==DEBUG_START)
    {
        $_SESSION['debug_session'] = "start";
    }
    if($_GET['debug']== DEBUG_STOP || isset($_GET['ai_resp_appl']))
    {
        if(stristr($_SERVER['REQUEST_URI'],'admin'))
        {
            $file = "../data/debug/debug.txt";
        }
        else
        {
            $file = "data/debug/debug.txt";
        }
        unlink(realpath($file));		
        $_SESSION['debug_session'] = "stop";
    }
}
?>