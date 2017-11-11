<?php
//-------------------------------------------------------------------------------------------------------------------
// File name   : editor.php
// Description : File to handle newly create UI template - style and labels information
//
// © 2010-2011 PaymentOne Corportation. All rights reserved.
//
// Created date : 22-02-2010
// Modified date: 19-12-2011
// ------------------------------------------------------------------------------------------------------------------

/*----- Include Files -----*/
include_once('../includes/configs/init.php');
include('../includes/configs/sessionadminc.php');
include('../includes/classes/templatefunctions.class.php');

/*-----Global Variables-----*/
global $glb_adm_tpl_path;

/*----- Instantiate the class ----*/
$templates_obj = new templates();
$template_func_obj=new TemplateFunctions();
$validator_obj = new Validator();
$common_obj = new Common();

if($_SESSION['sess_ser_id'] == '')
{
    unset($_SESSION['sess_auth_id']);
}

if($_SESSION['temp_country_id'] == "")
{
    $_SESSION['temp_country_id'] = 1;
    $_SESSION['temp_country_code'] = 'US';
    $_SESSION['temp_language_id'] = 1;
    $_SESSION['temp_language_code'] = 'EN';
}      //print_r($_SESSION);
$smarty->assign('temp_country_id', 1);
$smarty->assign('temp_country_code', 'US');
$smarty->assign('temp_language_id', 1);
$smarty->assign('temp_language_code', 'EN');

if($_SESSION['temp_cms_country'] == '')
{
    $query_string = "";
    $debug = array('file'=>'editor.php', 'line'=>'curlget');
    $response =  $common_obj ->CmsCurlGet($glb_cms_country_url,$_SESSION['admin_email'],$_SESSION['admin_cms_pwd'],$query_string,$debug);/*curlget*/
    if(!(stristr($response, '<?xml')))// if valid xml is not returned write log
    {
        $common_obj->WriteCMSLog("UI Template : Country URL : CMS Response: ".$response, $glb_adm_path."editor.php");
    }
    else // fetch the details for the displaying the preview
    {
        $xml = simplexml_load_string($response);
        foreach($xml->elements->country as $key=> $value) {
            foreach($value->attributes() as $key1=> $value1) {
                $cms_country[] = (string) $value1;
            }
        }
    }
    $_SESSION['temp_cms_country'] = $cms_country;
}
else
{
    $cms_country = $_SESSION['temp_cms_country'];
}

$debug = array('file'=>'editor.php', 'line'=>'country_p1_lists');
$p1_db_country = $template_func_obj->fetch_country_list($debug);/*country_p1_lists*/
for($i=0; $i<count($p1_db_country); $i++) {
    for($j=0; $j<count($cms_country); $j++) {
        if($cms_country[$j] == $p1_db_country[$i]['p1co_code']) {
            $country_lists[] = $p1_db_country[$i];
        }
    }
}
$smarty->assign('countrylist', $country_lists);

$debug = array('file'=>'editor.php', 'line'=>'fetch_country_language_listss');
$p1_db_country_languages = $template_func_obj->fetch_country_language_list($_SESSION['temp_country_id'], $debug);/*fetch_country_language_listss*/
if(count($p1_db_country_languages) > 0) {
    foreach($p1_db_country_languages as $key=> $value) {
        $p1_db_country_language .= $value['p1lg_id'].",";
    }
    $p1_db_country_language = substr($p1_db_country_language, 0, -1);
    $debug = array('file'=>'editor.php', 'line'=>'fetchlanguagelist');
    $p1_db_languages = $template_func_obj->fetch_language_list($p1_db_country_language, $debug);/*fetchlanguagelist*/
}       //print_r($p1_db_languages);
$smarty->assign('languagelist', $p1_db_languages);

//Code getting variable id for current new template
$minvar["tablename"] = "tbl_template_vars";
$minvar["fieldname"] = "min(p1tv_id) as minid";
if($_SESSION['template_id']!='' || $_GET['tid']!='')
{
    if($_GET['tid'] == '') {
        $minvar["whereCon"]='p1te_id ='.$_SESSION['template_id'];
    }
    else {
        $minvar["whereCon"]='p1te_id ='.$_GET['tid'];
    }
    $debug = array('file'=>'editor.php', 'line'=>'maxid_template');
    $minvarid = $templates_obj->get_maxid($minvar,$debug);/*maxid_template*/
    $smarty->assign('minvarid', $minvarid[0]['minid']);
}

//Get all attributes
$get_attribs["tablename"] = "tbl_form_attribs";
$get_attribs["whereCon"] = "1";
$debug = array('file'=>'editor.php', 'line'=>'getall_attributes');
$smarty->assign('allattributes', $templates_obj->get_all($get_attribs,$debug));/*getall_attributes*/

//get all the information for current template- starts
$get_template["tablename"]="tbl_templates";
if($_GET['tid'] == '') {
    $get_template["whereCon"]='p1te_id ='.$_SESSION['template_id'];
}
else {
    $get_template["whereCon"]='p1te_id ='.$_GET['tid'];
}

if($_SESSION['template_id']!='' || $_GET['tid']!='') {
    $debug = array('file'=>'editor.php', 'line'=>'getall_tid');
    $template_arr = $templates_obj->get_all($get_template,$debug);/*getall_tid*/
}
$currenttemplate = new templates($template_arr[0]);
$smarty->assign('currenttemplate',$currenttemplate);
//get all the information for current template- ends

//Block for edit template name-starts
if($_POST['action'] == 'edit')
{
    $debug = array('file'=>'editor.php', 'line'=>'updatetempname');
    $template_func_obj->update_template_name($_POST['tid'],$_POST['tname'],$debug);/*updatetempname*/
}
//Block for edit template name-ends

//Block to get service for templates - starts
if($_REQUEST['action'] == 'service' || $_REQUEST['edit'] == 'service')
{
    $debug = array('file'=>'editor.php', 'line'=>'getservices');
    $result_value=$template_func_obj->get_services_for_template($_SESSION['merchant_id'],$debug);/*getservices*/
    $smarty->assign('services', $result_value['services']);
    if($_REQUEST['action'] == 'service' && empty($result_value['services'])) {
        header("location: editor.php?err=2&created=yes");
    }
    $smarty->assign('cat_services', $result_value['cat_services']);
}
//Block to get service for templates - ends

//Block for apply services for templates - starts.
if($_REQUEST['action'] == 'applied'){
    array_pop($_REQUEST);
    array_pop($_REQUEST);
    foreach( $_REQUEST  as $key => $val) {
        $app["tablename"] = "tbl_merchant_services";
        $app["fieldname"] = "p1te_id = ".$_SESSION['template_id'].", p1me_id = ".$_SESSION['merchant_id'];
        $app["whereCon"] = 'p1ms_id ='.$key;
        $debug = array('file'=>'editor.php', 'line'=>'updatestyles_service');
        $services_update =$templates_obj->update_styles($app,$debug);/*updatestyles_service*/	
    }
    header("location: editor.php?err=2&applied=yes");
}
//Block for apply services for templates - ends.

//Block for fetching merchants list for templates-starts
if($_REQUEST['apply'] == 'merchants')
{
    $refmerchantslsts  = $currenttemplate->get_Field('p1te_refid');
    if($refmerchantslsts != '' && $refmerchantslsts != '0')
    {
        $refmerchantslst = explode(',', $currenttemplate->get_Field('p1te_refid'));
        $string = "and  mer.p1me_id NOT IN (".$refmerchantslsts.")";
        foreach( $refmerchantslst  as $key => $val) {
            //get seclected merchants details. - right side
            $args1["tablename"] = "tbl_merchants_profile";
            $args1["fieldname"] = "p1me_id, p1mp_company";
            $args1["whereCon"] = "p1me_id = ".$val." order by p1mp_company" ;
            $debug = array('file'=>'editor.php', 'line'=>'getcomponent_fetchmerchant');
            $selmerchants[] = $templates_obj->get_component($args1,$debug);/*getcomponent_fetchmerchant*/
        }
    }
    else {
        $string = "";
    }
    $active_list[0]['company_name'] = 'Search Merchants';
    $active_list[0]['p1me_id'] = '-99';
    $idkey = '-99';
    $smarty->assign('idkey', $idkey);
    $smarty->assign('selmerchants', $selmerchants);
    $smarty->assign('merchants', $active_list);
    $smarty->assign('req_template_id', $_REQUEST['tid']);
}
//Block for fetching merchants list for templates-ends

/*----- Block for creating the new template -----*/
if($_POST['action'] == 'new' || $_REQUEST['action'] == 'new')
{
    if($_POST['tname'] == '') { //Get the posted template name
        $tname = $_REQUEST['tname'];
        $t_type = $_REQUEST['t_type'];
    }
    else{
        $tname = $_POST['tname'];
        $t_type = $_POST['t_type'];
    }

    //verify data and insert details
    $debug = array('file'=>'editor.php', 'line'=>'addtemplate');
    $template_func_obj->add_new_template($tname,$_REQUEST['refid'],$currenttemplate,$_REQUEST['sid'],$t_type,$debug);/*addtemplate*/

    //block to redirect
    if($_REQUEST['sid'] != ''){
        $_SESSION['service_ref_id'] = trim($_REQUEST['sid']);
        header("location:editor.php?err=2&created=yes&sid=".$_REQUEST['sid']."&option=copy&refid=".$_REQUEST['refid']);
    }
    else if($_REQUEST['refid'] != ''){
        header("location:editor.php?err=2&created=yes&edit=yes&refid=".$_REQUEST['refid']);
    }
    else{
        header("location:editor.php?err=2&created=yes&edit=yes&refid=1");
    }
}
/*----- Block for creating the new template -----*/

//Block for new template's labels section.
if($_GET['option'] == 'labels')
{
    $args["tablename"] = "tbl_country_language_reference";
    $args["fieldname"] = "p1colg_id";
    $args["whereCon"] = "p1colg_stflag = 1 and p1co_id = ".$_SESSION['temp_country_id']." and  p1lg_id = ".$_SESSION['temp_language_id'];
    $debug = array('file'=>'editor.php', 'line'=>'fetch_country_language_ref');
    $coun_lang_ref = $templates_obj->get_component($args, $debug);/*fetch_country_language_ref*/
    $coun_lang_id = trim($coun_lang_ref[0]['p1colg_id']);
    $_SESSION['temp_coun_lang_id'] = $coun_lang_id;

    if($_GET['sub'] == 'home')
    {
        $debug = array('file'=>'editor.php', 'line'=>'gethomelabel');
        $label_detail=$template_func_obj->get_home_lables_template($_GET['tid'], $coun_lang_id, $debug);/*gethomelabel*/
        $defaultcaps = $label_detail['defaultcaps'];
        $defaultcapids = $label_detail['defaultcapids'];
        //$smarty->assign('allcaptions', $label_detail['allcaptions']);
    }
    else if($_GET['sub'] == 'mobile')
    {
        $debug = array('file'=>'editor.php', 'line'=>'getmobilelabel');
        $label_detail=$template_func_obj->get_mobile_lables_template($_GET['tid'], $coun_lang_id, $debug);/*getmobilelabel*/
        $defaultcaps = $label_detail['defaultcaps'];
        $defaultcapids = $label_detail['defaultcapids'];
        //$smarty->assign('allcaptions', $label_detail['allcaptions']);
    }
    else
    {
        $debug = array('file'=>'editor.php', 'line'=>'getanyphonelabel');
        $label_detail=$template_func_obj->get_anyphone_lables_template($_GET['tid'], $coun_lang_id, $debug);/*getanyphonelabel*/
        $defaultcaps = $label_detail['defaultcaps'];
        $defaultcapids = $label_detail['defaultcapids'];
    }
    if($_GET['lid']=='')
    {
        if($_SESSION['d_type']=="smart")
        {
            foreach($label_detail['allcaptions'] as $lb)
            {
                if($_GET['sub'] == 'anyphone')
                {
                    if(trim($lb['p1tc_title'])=="Pay with any phone")
                    {
                        $lid = $lb['p1tc_id'];
                    }
                }
                else
                {
                    if(trim($lb['p1tc_title'])=="Select an item:")
                    {
                        $lid=$lb['p1tc_id'];
                    }
                }
            }
        }
        else
        {
            foreach($label_detail['allcaptions'] as $lb)
            {
                if($_GET['sub'] == 'anyphone')
                {
                    if(trim($lb['p1tc_title']) == "How it Works?")
                    {
                        $lid=$lb['p1tc_id'];
                    }
                }
                else
                {
                    if(trim($lb['p1tc_title']) == "How it Works?")
                    {
                        $lid=$lb['p1tc_id'];
                    }
                }
            }
        }
        header("location:editor.php?option=labels&lid=$lid&tid=".$_GET['tid']."&sub=".$_GET['sub']);
        exit;
    }
    $smarty->assign('defaultcaps', $defaultcaps);
    $smarty->assign('tid', $_GET['tid']);

    //Get particular caption's details
    $getcaps["tablename"] = "tbl_template_captions";
    $getcaps["whereCon"] = 'p1te_id ='.$_GET['tid']." and p1tc_id='".$_GET['lid']."'";
    $debug = array('file'=>'editor.php', 'line'=>'getall_lables');
    $getcaps = $templates_obj->get_all($getcaps,$debug);/*getall_lables*/
    $smarty->assign('getcaps', $getcaps);

    $caption_html_tag = $template_func_obj->search_html_tags($getcaps[0]['p1tc_value']);
    $smarty->assign('caption_html_tag', $caption_html_tag);

    if($_SESSION['d_type']=="smart") {
        $def_teid=$template_func_obj->get_smartphone_default_id();
    }
    else {
        $def_teid=1;
    }

    $get_attr["tablename"]="tbl_template_captions";
    $get_attr["fieldname"]='p1tc_value';
    $get_attr["whereCon"]="p1te_id =".$def_teid." and p1tc_title ='".$getcaps[0]['p1tc_title']."' and p1tc_type = ".$getcaps[0]['p1tc_type']." and p1colg_id = ".$coun_lang_id;
    $debug = array('file'=>'editor.class.php', 'line'=>'getalltempflag');
    $def_value = $templates_obj->get_component($get_attr,$debug);/*getalltempflag*/
    if($def_value[0]['p1tc_value']==$getcaps[0]['p1tc_value']) {
        $smarty->assign('restore',"no");
    }

    if($_REQUEST['action'] == 'restore') {
        $debug = array('file'=>'editor.php', 'line'=>'restorelabel');
        $template_func_obj->restore_default_lables_values($_GET['lid'],$_GET['tid'],$currenttemplate,$getcaps,$def_value,$_GET['sub'],$coun_lang_id,$debug);/*restorelabel*/
    }

    if($_SESSION['merchant_id'] != '')
    {
        $debug = array('file'=>'editor.php', 'line'=>'checkforrestores');
        $check_for_restore_labs = $template_func_obj->check_for_restore_labs($_GET['tid'], $debug);/*checkforrestores*/
        if($check_for_restore_labs > 0) {
            $smarty->assign('restore_styles', 1);
        }
        else {
            $smarty->assign('restore_styles', 0);
        }
    }
}
else
{
    //unset($_SESSION['temp_country_id']);
    //unset($_SESSION['temp_country_name']);
    //unset($_SESSION['temp_language_id']);
    //unset($_SESSION['temp_language_code']);
    //unset($_SESSION['temp_coun_lang_id']);

    $get_style["tablename"]="tbl_template_vars";
    $get_style["whereCon"]='p1te_id ='.$_GET['tid'];
    if($_GET['tid'] != '') {
        $debug = array('file'=>'editor.php', 'line'=>'get_all_style_components');
        $all_components = $templates_obj->get_all($get_style,$debug);/*get_all_style_components*/
        $smarty->assign('allcomponents', $all_components);
    }

    if($_GET['cid'] && $_GET['tid'] )       //Get all template variables and form fields of selected template
    {
        $debug = array('file'=>'editor.php', 'line'=>'templatecount');
        $allformfields = $template_func_obj->get_template_components($_GET['cid'],$_GET['tid'],$debug);/*templatecount*/
        foreach($allformfields as $key1 => $value1) {
            $preview_styles .= $value1['p1ta_title']." : ".$value1['p1ta_value'].";";
        }
        //$smarty->assign('preview_styles',$preview_styles);  // to show preview of components in preview section
        $smarty->assign('allformfields',$allformfields);

        if($_SESSION['merchant_id'] != '')
        {
            $debug = array('file'=>'editor.php', 'line'=>'checkforrestore');
            $check_for_restore_styles = $template_func_obj->check_for_restore_styles($_GET['tid'], $debug);/*checkforrestore*/
            if($check_for_restore_styles > 0) {
                $smarty->assign('restore_styles', 1);
            }
            else {
                $smarty->assign('restore_styles', 0);
            }
        }
    }

    if($_POST['action'] == 'update') {      //Block for updating the new template
        $debug = array('file'=>'editor.php', 'line'=>'updatetempstyle');
        $template_func_obj->update_template_style($_POST['tid'],$_POST['cid'],$_POST,$all_components,$debug);/*updatetempstyle*/
    }
}
/*----- Procedure Call and Coding Part End-----*/

$smarty->assign('currentpage', 'templates');
$smarty->assign('pagetitle', 'PaymentOne: UI Template');
$smarty->assign('page_name', 'UI Template');
$smarty->assign('glb_temp', $glb_adm_tpl_path.'formfields.tpl');

if($_REQUEST['option'] == 'styles')     // load template styles
{
    $page_contents = $glb_adm_tpl_path.'styles.tpl';
}
elseif($_REQUEST['option'] == 'labels')     // load template labels
{
    if($_REQUEST['sub'] == 'home') {
        $page_contents = $glb_adm_tpl_path.'labels.tpl';       // load home labels
    }
    else if($_REQUEST['sub'] == 'mobile') {
        $page_contents = $glb_adm_tpl_path.'mobilelabels.tpl';         // load mobile labels
    }
    else {
        $page_contents = $glb_adm_tpl_path.'anyphonelabels.tpl';      // load anyphone labels
    }
}
else {
    $page_contents=$glb_adm_tpl_path.'editor.tpl';
}

if($_SESSION['merchant_id'] != '') {        //set the return path for the done button
    $done_value="merchant.php?page=templates";
}
else {
    $done_value="index.php?page=templates";
}

$tid=($_SESSION['template_id']!='')?$_SESSION['template_id']:$_GET['tid'];
if($tid != '') {
    $get_attr["tablename"]="tbl_templates";
    $get_attr["fieldname"]='p1te_device_flag';
    $get_attr["whereCon"]='p1te_id ='.$tid;
    $debug = array('file'=>'editor.php', 'line'=>'getalltempflag');
    $flag = $templates_obj->get_component($get_attr,$debug);/*getalltempflag*/
    $type=$flag[0][p1te_device_flag];
}

if($type==2) {
    $spid=$template_func_obj->get_smartphone_default_id();
    $smarty->assign('def_spid',$spid);
    $preview_page="previewtemplate_sp.php";
    $done_value=$done_value.'&device=smart';
    $_SESSION['d_type']="smart";
}
else {
    $preview_page="previewtemplate.php";
    $done_value=$done_value.'&device=desktop';
    $_SESSION['d_type']="desktop";
}

$smarty->assign('preview_page',$preview_page);
$smarty->assign('ret_value',$done_value);
$smarty->assign('header', $glb_adm_tpl_path.'header.tpl' );
$smarty->assign('sidebar', $glb_adm_tpl_path.'sidebar.tpl' );
$smarty->assign('content', $page_contents );
$smarty->assign('footer', $glb_adm_tpl_path.'footer.tpl' );
$smarty->display($glb_adm_tpl_path.'index.tpl');

?>