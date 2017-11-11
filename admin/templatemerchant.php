<?php
//-------------------------------------------------------------------------------------------------------------------
// File name   : templatemerchant.php
// Description : File to handle -Admin acting as Merchant manage the UI Templates
//
// © 2010-2011 PaymentOne Corportation. All rights reserved.
//
// Created date : 22-03-2011
// Modified date: 28-11-2011
// ------------------------------------------------------------------------------------------------------------------

/*----- Include Files -----*/
include_once(FULL_PATH.'includes/classes/templatefunctions.class.php');

/*-----Instantiate the class-----*/
$template_func_obj=new TemplateFunctions();

unset($_SESSION['template_id']);
unset($_SESSION['sess_ser_id']);
unset($_SESSION['service_ref_id']);
unset($_SESSION['temp_country_id']);
unset($_SESSION['temp_country_code']);
unset($_SESSION['temp_language_id']);
unset($_SESSION['temp_language_code']);
unset($_SESSION['temp_cms_country']);

//to set session for smartphone and desktop
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

// preparing where condition for query based on selected phone type 
if($_SESSION['d_type']=="smart")
{
    $spid=$template_func_obj->get_smartphone_default_id();
    $d_where="and p1te_device_flag =2";
    $page_qs="&device=smart";
    $adm_where="and p1te_id <> ".$spid;
}
else
{
    $d_where="and p1te_device_flag =1";
    $page_qs="&device=desktop";
}

if($_REQUEST['assign'] == 'template')
{
    $serv["tablename"] = "tbl_templates as temp, tbl_merchant_services as serv";
    $serv["fieldname"] = "serv.p1ms_id, serv.p1ms_name, temp.p1te_name, temp.p1me_id, temp.p1ad_id, temp.p1te_description, temp.p1te_id, temp.p1te_added";
    if($_SESSION['d_type']=="smart")
    {
        $serv["whereCon"]= "serv.p1ms_id =".$_REQUEST['sid']." and serv.p1te_spid = temp.p1te_id";
    }
    else
    {
        $serv["whereCon"]= "serv.p1ms_id =".$_REQUEST['sid']." and serv.p1te_id = temp.p1te_id";
    }
    $debug = array('file'=>'templatemerchant.php', 'line'=>'getcomponentmerchant');
    $servres = $templates_obj->get_component($serv,$debug);/*getcomponentmerchant*/
    $smarty->assign('currentemp', $servres);
}

//get templateid's created by admin but not applied for the current merchant
$ads["tablename"] = "tbl_templates";
$ads["fieldname"] = "p1te_refid, p1te_id";
$ads["whereCon"]="p1ad_id = 1 and p1me_id IS NULL and p1te_id <> 1 $adm_where and p1te_stflag = 1 ";
$debug = array('file'=>'templatemerchant.php', 'line'=>'getcomponentadminids');
$adslist = $templates_obj->get_component($ads,$debug);/*getcomponentadminids*/
if(!count($adslist))
{
    $adstemplist = "";
}
else
{
    $adstemplist = "";
    foreach( $adslist as $key => $value)
    {
        if($value['p1te_refid'] == 0 && $value['p1te_refid'] != '')
        {
            //No Action Here.
        }
        else
        {
            $notintid = $value['p1te_id'];
            if(array_search($_SESSION['merchant_id'], explode(',', $value['p1te_refid'])) > -1)
            {
                $intid = $value['p1te_id'];
            }
            if($notintid != $intid)
            {
                $adstemplist .= $value['p1te_id'].",";
            }
        }
    }
    $adstemplist = substr($adstemplist,0,-1);
    if($adstemplist!="")
    {
        $adstemplist = "and p1te_id NOT IN (".$adstemplist.")";
    }
}
//get templateid's created by admin but not applied for the current merchant

//Code for getting the template assigned for the selected merchant.
if($_SESSION['merchant_id'] == '')
{
    $args["whereCon"]="p1ad_id = 1";
}
else
{
    if($_REQUEST['assign'] == 'template')
    {
        $args["whereCon"]="((p1me_id = ".$_SESSION['merchant_id'].") or (p1me_id IS NULL and p1ad_id = 1)) ".$adstemplist." and p1te_stflag = 1  and p1te_id NOT IN (".$servres[0]['p1te_id'].") ".$d_where." order by p1te_added desc";
    }
    else
    {
        $args["whereCon"]="((p1me_id = ".$_SESSION['merchant_id'].") or (p1me_id IS NULL and p1ad_id = 1))".$adstemplist." and p1te_stflag = 1 ".$d_where." order by p1te_added desc";
    }
}

$debug = array('file'=>'templatemerchant.php', 'line'=>'templistmerchant_count');	
$tmpslistcnt = $templates_obj->get_all_templatelist($args,$debug);/*templistmerchant_count*/
$total_records = count($tmpslistcnt);
$smarty->assign('tmpslistcnt', $total_records);

$args["start_lim"] = 0;
$args["lim"] = 0;
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
$smarty->assign('page', $_REQUEST['offset']);
$varname="offset";

if($_REQUEST['assign'] == 'template')
{
    $targetpage ="merchant.php?page=templates&sid=".$_REQUEST['sid']."&assign=template";
}
else
{
    $targetpage ='merchant.php?page=templates';
}

$debug = array('file'=>'templateadmin.php', 'line'=>'page_adminmerchanttemp');
$pager= $common_obj->Pagination($total_records,$limit,$targetpage,$page,$start,$varname,$debug);/*page_adminmerchanttemp*/

$debug = array('file'=>'templatemerchant.php', 'line'=>'templistmerchant_detail');
$tmpslist = $templates_obj->get_all_templatelist($args,$debug);/*templistmerchant_detail*/
//code for updating the service count
foreach( $tmpslist as $key => $value)
{
    //Code getting minid variable id for current new template
    $minvar["tablename"] = "tbl_template_vars";
    $minvar["fieldname"] = "min(p1tv_id) as minid";
    $minvar["whereCon"]="p1te_id =".$value['p1te_id'];
    $debug = array('file'=>'templatemerchant.php', 'line'=>'maxidcurrent');
    $minvarid = $templates_obj->get_maxid($minvar,$debug);/*maxidcurrent*/
    $tmpslist[$key]['minvarid'] = $minvarid[0]['minid'];
    if($_SESSION['d_type']=="smart")
    {
        $whereCon="p1te_spid =".$value['p1te_id']." and p1ms_stflag != 3 and p1ms_stflag != 0";
    }
    else
    {
        $whereCon="p1te_id =".$value['p1te_id']." and p1ms_stflag != 3 and p1ms_stflag != 0";
    }
    $debug = array('file'=>'templatemerchant.php', 'line'=>'getservicemerchant');
    $service_cnt = $templates_obj->get_total_services($whereCon." and p1me_id = ".$_SESSION['merchant_id']." and p1ms_stflag !=0",$debug);/*getservicemerchant*/
    $tmpslist[$key]['service_cnt']=$service_cnt[0]['cnt'];
}
$smarty->assign('pager',$pager);
$smarty->assign('tmpslist', $tmpslist);
//Code for getting the template assigned for the selected merchant.

//Code for delete the particular template's whole details.
if($_REQUEST['do'] == 'del')
{
    $debug = array('file'=>'templatemerchant.php', 'line'=>'merchantdeletetemplate');
    $template_func_obj->merchant_delete_template($_REQUEST['tid'],$_REQUEST['ser_id'],$debug);/*merchantdeletetemplate*/
}

//Code for fetching last deleted template name
if(isset($_REQUEST['did']))
{
    $didno["tablename"] = "tbl_templates";
    $didno["fieldname"] = "p1te_name";
    $didno["whereCon"]="p1te_id =".$_REQUEST['did'];
    $debug = array('file'=>'templatemerchant.php', 'line'=>'catservicedeleted');
    $getname = $templates_obj->get_category_services($didno,$debug);/*catservicedeleted*/
    $smarty->assign('delname', $getname[0]['p1te_name']);
}

//Code for applying template to the service
if(isset($_REQUEST['apply']))
{
    $args["tablename"] = "tbl_merchant_services";
    $args["whereCon"] = " p1ms_id = ".$_REQUEST['apply'];
    if($_SESSION['d_type']=="smart")
    {
        $args["fieldname"] = 'p1ms_updated=now(),p1te_spid ='.$_REQUEST['tid'];
    }
    else
    {
        $args["fieldname"] = 'p1ms_updated=now(),p1te_id ='.$_REQUEST['tid'];
    }
    $debug = array('file'=>'templatemerchant.php', 'line'=>'updatestyle_applyservice');
    $service_update =$templates_obj->update_styles($args,$debug);/*updatestyle_applyservice*/	
    header("location: merchant.php?page=templates&sid=".$_REQUEST['apply']."&assign=template&ser_disp_done=".$_REQUEST['ser_disp_done']."&err=1");
}
if($spid!='')
{
    $smarty->assign('def_spid',$spid);
}
// success message for service creation.
if($_SESSION['success']=="service_updated")
{
    setcookie("disp_done_btn", "yes");
    unset($_SESSION['success']);
    header("location: merchant.php?page=templates&sid=".$_REQUEST['sid']."&assign=template&suc_message=yes");
}

if($_REQUEST["suc_message"]=='yes')
{
    $smarty->assign('success', 'Service created successfully');
}

$smarty->assign('d_type', $_SESSION['d_type']);
$smarty->assign('re_url', "merchant.php?page=templates");
if($_REQUEST['assign'] == 'template')
{
    $page_contents = $glb_adm_tpl_path.'templateservices.tpl';
}
else
{
    $page_contents = $glb_adm_tpl_path.'templates.tpl';
}
//$page_contents = $glb_adm_tpl_path.'templates.tpl';
$smarty->assign('currentpage', 'templates');
$smarty->assign('pagetitle', 'PaymentOne: UI Template');
$smarty->assign('page_name', 'UI Template');
//Code for applying template to the service

//Code for applying services for templates.
if($_REQUEST['editedtid'] != '')
{
    $tid = $_REQUEST['editedtid'];
    array_pop($_REQUEST);
    array_pop($_REQUEST);
    $debug = array('file'=>'templatemerchant.php', 'line'=>'merchantapplyservice');
    $is_update=$template_func_obj->apply_service_to_template($_REQUEST,$tid,$debug);/*merchantapplyservice*/	
    if($_SESSION['sess_ser_id'] == '')
    {
        if($is_update=="updated")
        {
            header("location: merchant.php?page=templates&err=2");
        }
        else
        {
            header("location: merchant.php?page=templates");
        }
    }
    else
    {
        unset($_SESSION['sess_ser_id']);
        header("location: merchant.php?page=services");
    }
}
//Code for applying services for templates.

?>