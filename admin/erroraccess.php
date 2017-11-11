<?php
//-------------------------------------------------------------------------------------------------------------------
// File name   : erroraccess.php
// Description : This is the common error redirect page
//
// © 2010-2011 PaymentOne Corportation. All rights reserved.
//
// Created date : 16-02-2010
// Modified date: 31-10-2011
// ------------------------------------------------------------------------------------------------------------------
include_once('../includes/configs/init.php' );
//include_once('../includes/configs/sessionadminc.php');
$smarty->assign('img_path', DOMAIN_NAME);

if($_GET['page_err']=="script"){
$smarty->assign('error_type','noscript');
$page_contents=FULL_PATH."templates/default/dbconnecterror.tpl";
}

if($_GET['page_err']=="yes")
{
	$page_contents=FULL_PATH."templates/default/dbconnecterror.tpl";
}
$smarty->assign('re',$_GET['re']);
$smarty->assign('error_page','error');
//Assign the values for templates
$smarty->assign('currentpage', 'home' );
$smarty->assign('header', $glb_adm_tpl_path.'header.tpl' );
$smarty->assign('content', $page_contents);
$smarty->assign('sidebar', $glb_adm_tpl_path.'sidebar.tpl' );
$smarty->assign('footer', $glb_adm_tpl_path.'footer.tpl' );
$smarty->display($glb_adm_tpl_path.'index.tpl');
?> 
