<?php
//-------------------------------------------------------------------------------------------------------------------
// File name   : templateadmin.php
// Description : File to handle -Admin manage the UI Templates
//
// © 2010-2011 PaymentOne Corportation. All rights reserved.
//
// Created date : 22-03-2011
// Modified date: 28-11-2011
// ------------------------------------------------------------------------------------------------------------------

/*----- Include Files -----*/
require_once(FULL_PATH.'includes/classes/templatefunctions.class.php');

/*-----Instantiate the class-----*/
$template_func_obj=new TemplateFunctions();

unset($_SESSION['template_id']);
unset($_SESSION['temp_country_id']);
unset($_SESSION['temp_country_code']);
unset($_SESSION['temp_language_id']);
unset($_SESSION['temp_language_code']);
unset($_SESSION['temp_cms_country']);

//set session for desktop and smartphone tabs
$d_where='';
if($_REQUEST['device']=='smart' && $_SESSION['d_type']!='smart')
{
    $_SESSION['d_type']="smart";
}
elseif($_REQUEST['device']=='desktop' && $_SESSION['d_type']!='desktop')
{
    $_SESSION['d_type']="desktop";
}
elseif($_SESSION['d_type']=='')
{
    $_SESSION['d_type']="desktop";
}
//set session for desktop and smartphone tabs

if($_SESSION['d_type']=="smart")
{
    $d_where="and p1te_device_flag =2";
    $page_qs="&device=smart";
}
else
{
    $d_where="and p1te_device_flag =1";
    $page_qs="&device=desktop";
}

$args["whereCon"]="p1ad_id = 1 and p1me_id IS NULL  and p1te_stflag = 1 $d_where order by p1te_added desc";
$args["start_lim"] = 0;
$args["lim"] = 0;
$debug = array('file'=>'templateadmin.php', 'line'=>'templatelist_admin');
$tmpslistcnt = $templates_obj->get_all_templatelist($args,$debug);/*templatelist_admin*/
$debug = array('file'=>'templateadmin.php', 'line'=>'tempcount_admin');
$total_records = $template_func_obj->get_template_count("admin","",$debug);/*tempcount_admin*/
$smarty->assign('tmpslistcnt', $total_records);

// code for setting pagination
$args["start_lim"] = $limit = 6;
$page="";
if(isset($_REQUEST['offset']) && ($_REQUEST['offset']!=""))
{
    $page=$_REQUEST['offset'];
    $args["lim"] = $start = ($page - 1) * $limit;
}
else
{
    $args["lim"] = $start = 0;
}
$varname="offset";

if($_REQUEST['assign'] == 'template')
{
    $target_page ="index.php?page=templates&assign=template";
}
else
{
    $target_page ="index.php?page=templates$page_qs";
}
$debug = array('file'=>'templateadmin.php', 'line'=>'templatelist_details');
$tmpslist = $templates_obj->get_all_templatelist($args,$debug);/*templatelist_details*/
$debug = array('file'=>'templateadmin.php', 'line'=>'page_admintemp');
$pager= $common_obj->Pagination($total_records,$limit,$target_page,$page,$start,$varname,$debug);/*page_admintemp*/
// code for setting pagination

//Code getting mechants count for current new template.
foreach( $tmpslist as $key => $value)
{
    $args["tablename"] = "tbl_templates";
    $args["fieldname"] = "p1te_refid";
    $args["whereCon"]="p1te_id =".$tmpslist[$key]['p1te_id'];
    $debug = array('file'=>'templateadmin.php', 'line'=>'getcomponent_mercount');
    $merchants = $templates_obj->get_component($args,$debug);/*getcomponent_mercount*/
    if($merchants[0]['p1te_refid'] == 0 && $merchants[0]['p1te_refid'] != '')
    {
        $tmpslist[$key]['merchants_cnt'] = 'All';
    }
    else if($merchants[0]['p1te_refid'] != 0)
    {
        $merchants = explode(',', $merchants[0]['p1te_refid']);
        $tmpslist[$key]['merchants_cnt'] = count($merchants);
    }
    else
    {
        $tmpslist[$key]['merchants_cnt']= 0;
    }
}
//Code getting mechants count for current new template.

//Code for delete the particular template's whole details.
if($_REQUEST['do'] == 'del')
{
    $debug = array('file'=>'templateadmin.php', 'line'=>'deletetemplate_admin');
    $template_func_obj->delete_admin_template($_REQUEST['tid'],$debug);/*deletetemplate_admin*/
}
//Code for delete the particular template's whole details.

//code for getting the last deleted template name
if(isset($_REQUEST['did']))
{
    $didno["tablename"] = "tbl_templates";
    $didno["fieldname"] = "p1te_name";
    $didno["whereCon"]="p1te_id =".$_REQUEST['did'];
    $debug = array('file'=>'templateadmin.php', 'line'=>'getservice_lastdelete');
    $getname = $templates_obj->get_category_services($didno,$debug);/*getservice_lastdelete*/
    $smarty->assign('delname', $getname[0]['p1te_name']);
}
//code for getting the last deleted template name

//Code for assigning selected merchants to the template
if(isset($_REQUEST['apply']))
{
    $args["tablename"] = "tbl_merchant_services";
    $args["whereCon"] = " p1ms_id = ".$_REQUEST['apply'];
    $args["fieldname"] = 'p1te_id ='.$_REQUEST['tid'];
    $debug = array('file'=>'templateadmin.php', 'line'=>'updatestyle_assigntempmerchant');
    $service_update =$templates_obj->update_styles($args,$debug);/*updatestyle_assigntempmerchant*/
    header("location:index.php?page=templates&sid=".$_REQUEST['apply']."&assign=template&err=1");	
}
//Code for assigning selected merchants to the template

if($_SESSION['d_type']=="smart")
{
    $spid = $template_func_obj->get_smartphone_default_id();
}

if($spid != '')
{
    $smarty->assign('def_spid',$spid);
}

$smarty->assign('pager',$pager);
$smarty->assign('tmpslist', $tmpslist);
$smarty->assign('d_type', $_SESSION['d_type']);
$smarty->assign('re_url', "index.php?page=templates");
$smarty->assign('page_name', 'templates');	
$page_contents=$glb_adm_tpl_path.'templates.tpl';

?>