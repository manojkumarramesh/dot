<?php
//-------------------------------------------------------------------------------------------------------------------
// File name   : editorpreview.php
// Description : File to handle - UI Template editor preivew functionality
//
// © 2010-2011 PaymentOne Corportation. All rights reserved.
//
// Created date : 22-03-2011
// Modified date: 16-12-2011
// ------------------------------------------------------------------------------------------------------------------

header("Pragma: no-cache");
header("Cache: no-cahce");

/*----- Include files -----*/
include_once( '../includes/configs/init.php' );
include_once('../includes/configs/sessionadminc.php');

/*----- Global variable declaration -----*/
global $glb_adm_tpl_path;

/*----- Constant Definition -----*/
define("MOBILE_AUTHENTICATION_TYPE", 4);
define("ACTIVE",1);
define("INACTIVE",0);
define("MOBILE", 4);
define("HOME", 2);
define("ANYPHONE", 5);

$month_array = array('Month', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');//Months array

$modified_date = "28122011";//version used in tpl

/*----- Object creation -----*/
$templates_obj = new templates();
$template_func_obj = new TemplateFunctions();
$debug_obj = new Debug();

/*----- fetch request -----*/
$template_id = trim($_REQUEST['tid']);
$label_id = trim($_REQUEST['lid']);
$type = trim($_SESSION['d_type']);
$aid = trim($_REQUEST['sub']);
$preview_type = trim($_REQUEST['option']);
$current_value = trim($_REQUEST['preview_text']);
$current_value = str_replace(' ','+', $current_value);
$current_value = base64_decode($current_value);

if($_REQUEST['chk'] == 'preview')  { //To fetch css values for preview while change before updating
    $class = $_REQUEST['comp'];
    $smarty->assign('preview', 'preview');
}
else {  //To fetch css values for preview after updating
    $class = '';
}

function escape_string($str)
{
    $search = array(" ", "?", ":", ",", ".", "(", ")", "[", "]", "/", "&");
    $replace = array("_", "", "", "", "", "", "", "", "", "_", "_");
    return str_replace($search, $replace, $str);
}

$get_style["tablename"] = "tbl_templates";
$get_style["whereCon"] = "p1te_id ='".$template_id."'";
$debug = array('file'=>'previewtemplate.php', 'line'=>'getall_temppreview');
$getstyle_arr = $templates_obj->get_all($get_style, $debug);/*getall_temppreview*/

if($type != 'smart') {
    $option = "";
    $option .= "<br>";
    $option .= "<input type='radio' name='q1'>";
    $option .= "<span class='faux-label'>Yes</span>";
    $option .= "<input type='radio' name='q1'>";
    $option .= "<span class='faux-label'>No</span>";

    $template_path = "/uitemplates/desktop/";
    $css = $glb_adm_tpl_path.$template_path."desktop_css.xml";
    $label = $glb_adm_tpl_path.$template_path."desktop_labels.xml";
}
else {
    $option = "";
    $option .= "</span><div class='boolean'>";
    $option .= "<label for='q1_y' >";
    $option .= "<input type='radio' name='q1' id='q1_y' /> Yes";
    $option .= "</label>";
    $option .= "<label for='q1_n' >";
    $option .= "<input type='radio' name='q1' id='q1_n' />No";
    $option .= "</label>";
    $option .= "</div></section><section class='yesno'><span class='tos'>";

    $template_path = "/uitemplates/smartphones/";
    $css = $glb_adm_tpl_path.$template_path."smart_css.xml";
    $label = $glb_adm_tpl_path.$template_path."smart_labels.xml";
}

if($aid == "mobile") {
    $aid = MOBILE;
    $type = 2;
}
else if($aid == "home") {
    $aid = HOME;
    $type = 1;
}
else if($aid == "anyphone") {
    $aid = ANYPHONE;
    $type = 3;
}
else {
    $type = 1;
    $p1colg_id = 1;
}

if($p1colg_id == '')
{
    $p1colg_result = $template_func_obj->fetch_labels_list($_SESSION['temp_country_id'], $_SESSION['temp_language_id']);
    $p1colg_id = $p1colg_result[0]['p1colg_id'];

    $arg["tablename"] = "tbl_template_captions";
    $arg["whereCon"] = 'p1te_id = '.$template_id.' and p1tc_id = '.$label_id.' and p1tc_type = '.$type;
    $debug = array('file'=>'editorpreview.php', 'line'=>'getall_labless');
    $getcaps = $templates_obj->get_all($arg, $debug);/*getall_labless*/

    $arg1["tablename"] = "tbl_template_captions";
    $arg1["whereCon"] = 'p1te_id = '.$template_id.' and p1tc_type = '.$type.' and p1colg_id = '.$p1colg_id;
    $debug = array('file'=>'editorpreview.php', 'line'=>'getall_lables');
    $getcaps2 = $templates_obj->get_all($arg1, $debug);/*getall_lables*/

    for($i=0;$i<count($getcaps2);$i++)
    {
        if($getcaps2[$i]['p1tc_title'] == $getcaps[0]['p1tc_title']) {
            $array1[] = $i;
        }
    }
    $key = trim($array1[0]);

    if($aid == MOBILE)
    {
        foreach($getcaps2 as $val1) {
            $key_m[] = strtolower(escape_string($val1['p1tc_title']));
        }
        foreach($getcaps2 as $val) {
            $mobilelabels[] = $val['p1tc_value'];
        }
        $mobilelabels = str_replace($mobilelabels[$key], $current_value, $mobilelabels);
        $mobilelabels = str_replace("[click_here]", "<a href='#' onclick='parent.$.fn.colorbox.close(); return false;'>Click Here</a>", $mobilelabels);
        $mobile_labels = array_combine($key_m, $mobilelabels);
        $smarty->assign('mobile_labels', $mobile_labels);

        $loading_array = array("3", "4", "5", "21");
        if(in_array($key, $loading_array)) {
            $smarty->assign('loading', 1);
            $smarty->assign('loading_value', $mobilelabels[$key]);
        }
        else {
            $smarty->assign('loading', '');
        }
    }
    else if($aid == HOME)
    {
        foreach($getcaps2 as $val1) {
            $key_h[] = strtolower(escape_string($val1['p1tc_title']));
        }
        foreach($getcaps2 as $val) {
            $homelabels[] = $val['p1tc_value'];
        }
        $homelabels[$key] = $current_value;
        $homelabels = str_replace("[radiooption1]", $option, $homelabels);
        $homelabels = str_replace("[radiooption2]", $option, $homelabels);
        $homelabels = str_replace("[radiooption3]", $option, $homelabels);
        $home_labels = array_combine($key_h, $homelabels);
        $smarty->assign('home_labels', $home_labels);

        if($_SESSION['d_type'] == 'smart') {
            $loading_array = array("5", "18", "19");
        }
        else {
            $loading_array = array("5", "18", "19");
        }
        if(in_array($key, $loading_array)) {
            $smarty->assign('loading', 1);
            $smarty->assign('loading_value', $homelabels[$key]);
        }
        else {
            $smarty->assign('loading', '');
        }
    }
    else if($aid == ANYPHONE)
    {
        foreach($getcaps2 as $val1) {
            $key_a[] = strtolower(escape_string($val1['p1tc_title']));
        }
        foreach($getcaps2 as $val) {
            $anyphonelabels[] = $val['p1tc_value'];
        }
        $anyphonelabels[$key] = $current_value;
        $anyphone_labels = array_combine($key_a, $anyphonelabels);
        $smarty->assign('anyphone_labels', $anyphone_labels);

        if($_SESSION['d_type'] == 'smart') {
            $loading_array = array("10");
        }
        else {
            $loading_array = array("11", "15");
        }
        if(in_array($key, $loading_array)) {
            $smarty->assign('loading', 1);
            $smarty->assign('loading_value', $anyphonelabels[$key]);
        }
        else {
            $smarty->assign('loading', '');
        }
    }
}
else
{
    $arg1["tablename"] = "tbl_template_captions";
    $arg1["whereCon"] = 'p1te_id = '.$template_id.' and p1tc_type = 1 and p1colg_id = '.$p1colg_id;
    $debug = array('file'=>'editorpreview.php', 'line'=>'getall_lables');
    $getcaps2 = $templates_obj->get_all($arg1, $debug);/*getall_lables*/
    foreach($getcaps2 as $val) {
        $homelabels[] = $val['p1tc_value'];
    }
    $homelabels[$key] = $current_value;
    $homelabels = str_replace("[radiooption1]", $option, $homelabels);
    $homelabels = str_replace("[radiooption2]", $option, $homelabels);
    $homelabels = str_replace("[radiooption3]", $option, $homelabels);
    $smarty->assign('labels', $homelabels);//Assign values to tpl
}


//-------------------------------------------------------------------------------------------------------------
// function: fetchPage ( -- arguments -- )
//-------------------------------------------------------------------------------------------------------------
// purpose: To fetch labels and styles
// arguments:  $file, $format, $template_id, $label_id, $sub=""
//-------------------------------------------------------------------------------------------------------------
function fetchPage($file, $format, $template_id, $label_id, $sub="")
{
    global $templates_obj;

    if($format == "css")
    {
        $getcaps["tablename"] = "tbl_template_vars";
        $getcaps["whereCon"] = 'p1te_id ='.$template_id.' and p1tv_id ='.$label_id;
        $debug = array('file'=>'editor.php', 'line'=>'getcss');
        $getcaps = $templates_obj->get_all($getcaps, $debug);/*getcss*/
        $css_title = $getcaps[0]['p1tv_name'];
        $xml =  simplexml_load_file($file);
    }
    else
    {
        $getcaps["tablename"] = "tbl_template_captions";
        $getcaps["whereCon"] = 'p1te_id ='.$template_id.' and p1tc_id ='.$label_id;
        $debug = array('file'=>'editor.php', 'line'=>'getxml');
        $getcaps = $templates_obj->get_all($getcaps, $debug);/*getxml*/
        $css_title = $getcaps[0]['p1tc_title'];
        $xml =  simplexml_load_file($file);
        if($sub == "mobile") {
            $xml = $xml->mobile;
        }
        else if($sub == "home") {
            $xml = $xml->home;
        }
        else if($sub == "anyphone") {
            $xml = $xml->anyphone;
        }
    }
    foreach($xml->name as $val) {
        $title[] = (string) $val['title'];
        $value[] = (string) $val['value'];
    }
    $no = array_keys($title, $css_title);
    return $value[$no[0]];
}

if($preview_type == 'labels') {
    $label_id = trim($_REQUEST['lid']);
    $temp = fetchPage($label, "label", $template_id, $label_id, trim($_REQUEST['sub']));
}
else {
    $label_id = trim($_REQUEST['cid']);
    $temp = fetchPage($css, "css", $template_id, $label_id);
}

$args["tablename"] = "tbl_currency";
$args["fieldname"] = "p1cu_symbol";
$args["whereCon"] = "p1cu_stflag =1 and p1co_id = ".$_SESSION['temp_country_id'];
$debug = array('file'=>'editorpreview.php', 'line'=>'fetchcurrency');
$fetch_currency = $templates_obj->get_component($args, $debug);/*fetchcurrency*/
$curreny_symbol = $fetch_currency[0]['p1cu_symbol'];

$args2["tablename"] = "tbl_country";
$args2["fieldname"] = "p1co_name";
$args2["whereCon"] = "p1co_id = ".$_SESSION['temp_country_id'];
$debug = array('file'=>'editorpreview.php', 'line'=>'fetchcountry');
$fetch_country = $templates_obj->get_component($args2, $debug);/*fetchcountry*/
$country_name = $fetch_country[0]['p1co_name'];

/*----- Assign values -----*/
$smarty->assign('modified_date', $modified_date);
$smarty->assign('months', $month_array);
$smarty->assign('aid', $aid);
$smarty->assign('temp_css', $getstyle_arr[0]['p1te_css']);
$smarty->assign('next', $temp);
$smarty->assign('key', $key);
$smarty->assign('class', $class);
$smarty->assign('curreny_symbol', $curreny_symbol);
$smarty->assign('country_name', $country_name);

//echo $key."--".$temp;

if($preview_type == 'labels') {
    $smarty->display($glb_adm_tpl_path.$template_path.'preview_'.trim($temp).'.tpl');
}
else {
    $smarty->display($glb_adm_tpl_path.$template_path.'preview_style.tpl');
}

?>