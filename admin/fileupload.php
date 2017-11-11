<?php
//-------------------------------------------------------------------------------------------------------------------
// File name   : fileupload.php
// Description : File for UI templates file uploading functionality
//
// © 2010-2011 PaymentOne Corportation. All rights reserved.
//
// Created date : 09-03-2010
// Modified date: 28-11-2011
// -----------------------------------------------------------------------------------------------------------------

/*----- Include Files -----*/
include_once('../includes/configs/init.php');
include_once('../includes/configs/sessionadminc.php');

/*----- Instantiate the class ----*/
$templates_obj = new templates();
$debug_obj = new Debug();
$template_func_obj = new TemplateFunctions();
$validator_obj = new Validator();
$common_obj = new Common();

$actions = trim($_REQUEST['actions']);

/*----- Constant Definition for switch case ----- */
define("EMPTY_VALUE", 0);
define('REVERT_UPLOADED_FILE','revert_uploaded_file');
define('APPLY_UPLOADED_FILE','apply_uploaded_file');
define('ENCODE_VALUE','encode_value');
define('UPLOAD','upload');
define('SUCCESS', 1);
define('FAILURE', 0);
define('CMS_FAILURE', 2);
define('FAIL', 'Upload failed.');
define('FILE_NOT_FOUND', 'File Not Found.');
define('APPLY_TO_ALL_TEMPLATES', 'apply_to_all_templates');
define('APPLY_TO_ALL_TEMPLATES_STYLES', 'apply_to_all_templates_styles');
define('AFFECT_ALL_TEMPLATE_LABELS', 'affect_all_template_labels');
define('RESTORE_DEFAULT_TEMPLATE_DATA', 'restore_default_template_data');
define('TEMP_FETCH_LANGUAGE', 'temp_fetch_language');
define('TEMP_SELECT_LANGUAGE', 'temp_select_language');
define('SET_LANGUAGE', 'set_language');
define('UPLOAD_XML_FILES', 'upload_xml_files');
define('APPLY_TO_MERCHANTS', 'apply_to_merchants');
define('REQUEST_NOT_FOUND', "REQUEST NOT FOUND");

switch($actions)
{
    case REVERT_UPLOADED_FILE:
        $id = trim($_REQUEST['ids']);
        $format = trim($_REQUEST['file']);
        $country_id = trim($_REQUEST['country_id']);
        $country_code = trim($_REQUEST['country_code']);
        $language_id = trim($_REQUEST['language_id']);
        $language_code = trim($_REQUEST['language_code']);
        $debug = array('file'=>'fileupload.php', 'line'=>'revertuploadedfile');
        echo $result = revert_uploaded_file($id, $format, $country_id, $country_code, $language_id, $language_code, $debug);/*revertuploadedfile*/
        break;
    case APPLY_UPLOADED_FILE:
        $id = trim($_REQUEST['ids']);
        $file = trim($_REQUEST['file']);
        $country_id = trim($_REQUEST['country_id']);
        $country_code = trim($_REQUEST['country_code']);
        $language_id = trim($_REQUEST['language_id']);
        $language_code = trim($_REQUEST['language_code']);
        $debug = array('file'=>'fileupload.php', 'line'=>'applyuploadedfile');
        echo $result = apply_uploaded_file($id, $file, $country_id, $country_code, $language_id, $language_code, $debug);/*applyuploadedfile*/
        break;
    case UPLOAD:
        $id = trim($_REQUEST['id']);
        $files_name = 'tmp_'.$_FILES['uploadfile']['name'];
        $ext = strtolower(strrchr($files_name,'.'));
        $dirName="../data/styles/templates/template_".$id."/";
        $upload_dir = $dirName.$files_name;
        if(move_uploaded_file($_FILES['uploadfile']['tmp_name'], $upload_dir)){
            $debug = array('file'=>'fileupload.php', 'line'=>'checkfiles');
            echo $result = checkfile($id, $ext, $files_name, $dirName, $debug);/*checkfiles*/
        }
        else{
            echo FAIL;
        }
        break;
    case UPLOAD_XML_FILES:
        $id = trim($_REQUEST['tid']);
        $country_id = $_REQUEST['xml_country_id'];
        $language_id = $_REQUEST['xml_language_id'];
        $country_code = $_REQUEST['xml_country_code'];
        $language_code = $_REQUEST['xml_language_code'];
        if($language_id != '') {
            $files_name = 'tmp_'.$_FILES['xmlfile']['name'];
            $ext = strtolower(strrchr($files_name,'.'));
            if($ext == '.xml'){
                $dirName = "../data/xml/templates/template_".$id."/".$country_code."/";
                $upload_dir = $dirName.$files_name;
                if(move_uploaded_file($_FILES['xmlfile']['tmp_name'], $upload_dir)){
                    chmod($upload_dir, 0777);
                    rename($upload_dir, $dirName."tmp_labels_".$language_code.".xml");
                    $target_path = $dirName."tmp_labels_".$language_code.".xml";
                    $debug = array('file'=>'fileupload.php', 'line'=>'fetchdefaultlabels');
                    $defaultlabels = fetch_default_labels($id, $country_id, $language_id, $debug);/*fetchdefaultlabels*/
                    $debug = array('file'=>'fileupload.php', 'line'=>'forxml');
                    $message = check_valid_xml($id, $target_path, $defaultlabels, 0, $country_code, $language_code, $debug);/*forxml*/
                    echo $message;
                }
                else{
                    echo "<div class='error' id='0'>Upload XML failed.</div>";
                }
            }
            else{
                echo "<div class='error' id='0'>Select valid XML File.</div>";
            }
        }
        else {
            echo "<div class='error' id='0'>Select Language.</div>";
        }
        break;
    case ENCODE_VALUE:
        $value = trim($_REQUEST['values']);
        $result = base64_encode($value);
        echo $result;
        break;
    case APPLY_TO_ALL_TEMPLATES:
        $template_id = trim($_REQUEST['tid']);
        $label_id = trim($_REQUEST['lid']);
        $template_type = trim($_REQUEST['type']);
        $label_type = trim($_REQUEST['sub']);
        $value = trim($_REQUEST['val']);
        $debug = array('file'=>'fileupload.php', 'line'=>'applyalltemplates');
        $result = $template_func_obj->apply_all_templates($template_id, $label_id, $template_type, $label_type, $value, $debug);/*applyalltemplates*/
        echo $result;
        break;
    case APPLY_TO_ALL_TEMPLATES_STYLES:
        $template_id = trim($_REQUEST['tid']);
        $style_id = trim($_REQUEST['cid']);
        $template_type = trim($_REQUEST['type']);
        $style_title = trim($_REQUEST['label']);
        $style_value = trim($_REQUEST['value']);
        $style_value = $validator_obj->escape_string($style_value);
        $component = trim($_REQUEST['comp']);
        $debug = array('file'=>'fileupload.php', 'line'=>'applyalltemplatestyles');
        $result = $template_func_obj->apply_all_templates_styles($template_id, $style_id, $template_type, $style_title, $style_value, $component, $debug);/*applyalltemplatestyles*/
        echo $result;
        break;
    case AFFECT_ALL_TEMPLATE_LABELS:
        $template_id = trim($_REQUEST['tid']);
        $label_id = trim($_REQUEST['lid']);
        $template_type = trim($_REQUEST['type']);
        $label_type = trim($_REQUEST['sub']);
        $value = trim($_REQUEST['val']);
        $debug = array('file'=>'fileupload.php', 'line'=>'affectalltemplatelabels');
        $result = $template_func_obj->affect_all_template_labels($template_id, $label_id, $template_type, $label_type, $value, $debug);/*affectalltemplatelabels*/
        echo $result;
        break;
    case RESTORE_DEFAULT_TEMPLATE_DATA:
        $template_id = trim($_REQUEST['tid']);
        $template_type = trim($_REQUEST['type']);
        $type = trim($_REQUEST['typ']);
        $debug = array('file'=>'fileupload.php', 'line'=>'restoredefaulttemplatedata');
        $result = $template_func_obj->restor_default_template_data($template_id, $template_type, $type, $debug);/*restoredefaulttemplatedata*/
        echo $result;
        break;
    case TEMP_FETCH_LANGUAGE:
        $country_id = trim($_REQUEST['countryid']);
        $country_code = trim($_REQUEST['countrycode']);
        $request_page = trim($_REQUEST['page']);
        $debug = array('file'=>'fileupload.php', 'line'=>'fetch_country_language_listss');
        $p1_db_country_languages = $template_func_obj->fetch_country_language_list($country_id, $debug);/*fetch_country_language_listss*/
        if(count($p1_db_country_languages) > 0)
        {
            foreach($p1_db_country_languages as $key=> $value)
            {
                $p1_db_country_language .= $value['p1lg_id'].",";
                if($value['p1colg_official'] == 1) {
                    $official = $value['p1lg_id'];
                }
            }
            $p1_db_country_language = substr($p1_db_country_language, 0, -1);//print_r($p1_db_country_language);
            $debug = array('file'=>'fileupload.php', 'line'=>'fetchlanguagelist');
            $p1_db_languages = $template_func_obj->fetch_language_list($p1_db_country_language, $debug);/*fetchlanguagelist*/
            if(count($p1_db_languages) > 0)
            {
                $smarty->assign('listLanguage', $p1_db_languages);
                $smarty->assign('country_code', $country_code);
                $smarty->assign('page', $request_page);
                $smarty->assign('official', $official);
                $page_contents = $glb_adm_tpl_path.'templatedisplaylang.tpl';
                $result = $smarty->fetch($page_contents);
                echo $result;
            }
            else {
                echo FAILURE;
            }
        }
        else {
            echo FAILURE;
        }
        break;
    case SET_LANGUAGE:
        $_SESSION['temp_country_id'] = trim($_REQUEST['country_id']);
        $_SESSION['temp_country_code'] = trim($_REQUEST['country_code']);
        $_SESSION['temp_language_id'] = trim($_REQUEST['language_id']);
        $_SESSION['temp_language_code'] = trim($_REQUEST['language_code']);
        echo 1;
        break;
    case APPLY_TO_MERCHANTS:
        $template_id = trim($_REQUEST['id']);
        $template["tablename"] = "tbl_templates";
        if(trim($_REQUEST['temp_all']) == 'yes') { //if assign ui template to all the merchants
            $template["fieldname"] = "p1te_refid = 0";
            $applied_to_temp = 'all';
        }
        else { //if assign ui template to selected merchants
            $reference_id = trim($_REQUEST['te_refid']);
            $reference_id = substr($reference_id, 0, -1);
            $reference_id = trim($reference_id);
            $template["fieldname"] = "p1te_refid = '$reference_id'";
            $applied_to_temp = 'added';
        }
        $template["whereCon"] = "p1te_id = ".$template_id;
        $debug = array('file'=>'fileupload.php', 'line'=>'applytomerchants');
        $template_arr = $templates_obj->update_styles($template, $debug);/*applytomerchants*/
        echo $applied_to_temp;
        break;
    case TEMP_SELECT_LANGUAGE:
        $country_id = trim($_REQUEST['countryid']);
        $country_code = trim($_REQUEST['countrycode']);
        $arg["tablename"] = "tbl_country_language_reference as ref left join tbl_languages as lang on (ref.p1lg_id = lang.p1lg_id)";
        $arg["fieldname"] = "lang.p1lg_id, lang.p1lg_code";
        $arg["whereCon"] = "ref.p1colg_stflag = 1 and lang.p1lg_stflag = 1 and ref.p1colg_official = 1 and ref.p1co_id = ".$country_id;
        $debug = array('file'=>'fileupload.php', 'line'=>'db_language');
        $p1_db_languages = $templates_obj->get_component($arg, $debug);/*db_language*/
        if(count($p1_db_languages) > 0)
        {
            $_SESSION['temp_country_id'] = $country_id;
            $_SESSION['temp_country_code'] = $country_code;
            $_SESSION['temp_language_id'] = $p1_db_languages[0]['p1lg_id'];
            $_SESSION['temp_language_code'] = $p1_db_languages[0]['p1lg_code'];
            echo SUCCESS;
        }
        else
        {
            echo FAILURE;
        }
        break;
    default:
        echo REQUEST_NOT_FOUND;
        break;
}

//-------------------------------------------------------------------------------------------------------------
// function: escape_string ( -- arguments -- )
//-------------------------------------------------------------------------------------------------------------
// purpose: To fetch default templates labels
// arguments:  $str
//-------------------------------------------------------------------------------------------------------------
function escape_string($str)
{
    $search = array("\\","\0","\n","\r","\x1a","'",'"');
    $replace = array("\\\\","\\0","\\n","\\r","\Z","''",'\"');
    return str_replace($search,$replace,$str);
}

//-------------------------------------------------------------------------------------------------------------
// function: fetch_default_labels ( -- arguments -- )
//-------------------------------------------------------------------------------------------------------------
// purpose: To fetch default templates labels
// arguments:  $id, $findline=""
//-------------------------------------------------------------------------------------------------------------
function fetch_default_labels($id, $country_id, $language_id, $findline="")
{
    global $templates_obj, $template_func_obj, $debug_obj;

    $arguments = array($id, $country_id, $language_id);
    $debug_obj->WriteDebug($class="fileupload", $function="fetch_default_labels", $file=$findline['file'], $debug_obj->FindFunctionCalledline('fetch_default_labels', $findline['file'], $findline['line']), $arguments);

    $debug = array('file'=>'fileupload.php', 'line'=>'getcolgid');
    $p1colg_result = $template_func_obj->fetch_labels_list($country_id, $language_id, $debug);/*getcolgid*/

    $get_labels["tablename"] = "tbl_template_captions";
    $get_labels["fieldname"] = 'p1tc_title';
    $get_labels["whereCon"] = 'p1te_id = '.$id.' and p1colg_id = '.$p1colg_result[0]['p1colg_id'];
    $debug = array('file'=>'fileupload.php', 'line'=>'getalllabels');
    $form_labels = $templates_obj->get_component($get_labels, $debug);/*getalllabels*/
    foreach( $form_labels as $key => $value)
    {
        $defaultlabels[] = $value['p1tc_title'];
    }
    return $defaultlabels;
}

//-------------------------------------------------------------------------------------------------------------
// function: checkfile ( -- arguments -- )
//-------------------------------------------------------------------------------------------------------------
// purpose: To check if valid css or xml file
// arguments:  $id, $ext, $files_name, $dirName, $findline=""
//-------------------------------------------------------------------------------------------------------------
function checkfile($id, $ext, $files_name, $dirName, $findline="")
{
    global $templates_obj, $debug_obj;

    $arguments = array($id, $ext, $files_name, $dirName);
    $debug_obj->WriteDebug($class="fileupload", $function="checkfile", $file=$findline['file'], $debug_obj->FindFunctionCalledline('checkfile', $findline['file'], $findline['line']), $arguments);

    if($ext == '.css')// check the extension of the file
    {
        rename($dirName.$files_name, $dirName."tmp_merchant.css");
        $target_path = $dirName."tmp_merchant.css";
        //Get all components - styles
        $get_style["tablename"] = "tbl_template_vars";
        $get_style["whereCon"] = "p1te_id = ".$id;
        $debug = array('file'=>'fileupload.php', 'line'=>'getallstylecomp');
        $all_components = $templates_obj->get_all($get_style, $debug);/*getallstylecomp*/
        foreach($all_components as $k=>$acomp)
        {
            $get_attr["tablename"]="tbl_template_attribs";
            $get_attr["fieldname"]='p1ta_title,p1ta_id';
            $get_attr["whereCon"]='p1tv_id ='.$acomp['p1tv_id'];
            $debug = array('file'=>'fileupload.php', 'line'=>'getallcss');
            $form_attr = $templates_obj->get_component($get_attr,$debug);/*getallcss*/
            foreach ($form_attr as $fa)
            {
                $defaultattrbs[]=trim($fa['p1ta_title']);
            }
            $defaultclass[$k]=trim($acomp['p1tv_value']);
        }
        $debug = array('file'=>'fileupload.php', 'line'=>'forcss');
        $message = check_valid_css($target_path, $defaultclass, $all_components, $defaultattrbs, 0, $debug);/*forcss*/
    }
    else
    {
        $message = FAILURE.'@Select valid CSS file.';// display error message for wrong file format
    }
    return $message;
}

//-------------------------------------------------------------------------------------------------------------
// function: check_valid_css ( -- arguments -- )
//-------------------------------------------------------------------------------------------------------------
// purpose: To check if valid css or xml file
// arguments:  $id, $target_path, $defaultclass, $all_components, $defaultattrbs, $apply, $findline=""
//-------------------------------------------------------------------------------------------------------------
function check_valid_css($target_path, $defaultclass, $all_components, $defaultattrbs, $apply, $findline='')
{
    global $debug_obj, $dirName, $templates_obj;

    $arguments = array($target_path, $defaultclass, $all_components, $defaultattrbs, $apply);
    $debug_obj->WriteDebug($class="fileupload", $function="check_valid_css", $file=$findline['file'], $debug_obj->FindFunctionCalledline('check_valid_css', $findline['file'], $findline['line']), $arguments);

    // code for comparing the default css file and uploaded css file
    $lines = file($target_path);
    $class = '';
    foreach ($lines as $line_num => $line) 
    {
        $classnames = explode("{", trim($line));
        $class[$line_num] = trim($classnames[0]);
        $attrs[$line_num] = trim($classnames[1]);
    }
    $res = array_diff($class,$defaultclass);
    if(empty($res) && count($class) == count($defaultclass))//check if the classes in the default file and the uploaded files are same
    {
        $attrres = array();
        foreach( $all_components as $key => $value)//comparing the class names present in the uploaded file 
        {
            if( strcmp(trim($value['p1tv_value']),trim($class[$key])) == EMPTY_VALUE) {
                $attrres[] = 1;
            }
            else {
                $attrres[] = 0;
            }
        }
        if (in_array(0, $attrres))// display error if there is mismatch in the classes compared 
        {
            $message = FAILURE.'@There was a problem in the CSS uploaded. Reason: Class name has been changed.';
        }
        else
        {   //comparing the attibutes
            foreach( $all_components as $key => $value)
            {
                $attrbutes = explode(";",trim(substr_replace(trim(substr_replace($attrs[$key],"",-1)),"",-1)));
                foreach ($attrbutes as $attr_num => $attr) {
                    $attrbute = explode(":", trim($attr));
                    $attbs[] = trim($attrbute[0]);
                }
            }
            foreach( $attbs as $k => $val)
            {
                if( strcmp(trim($val),trim($defaultattrbs[$k])) == EMPTY_VALUE) {
                    $attrbsres[] = 1;
                }
                else {
                    $attrbsres[] = 0;
                }
            }
            if (in_array(0, $attrbsres))//Display error if there is a mismatch in attributes 
            {
                $message = FAILURE.'@There was a problem in the CSS uploaded. Reason: Attribute name has been changed/New attribute is added.';
            }
            else
            {
                if($apply == 1)
                {
                    foreach( $all_components as $key => $value)//update the tables with the values in the uploaded file
                    {
                        $attrbutes = explode(";", trim(substr_replace(trim(substr_replace($attrs[$key],"",-1)),"",-1)));
                        foreach ($attrbutes as $attr_num => $attr)
                        {
                            $attrbute = explode(":", trim($attr));
                            $att["tablename"]="tbl_template_attribs";
                            $att["fieldname"] = "p1ta_value = '".trim($attrbute[1])."'";
                            $att["whereCon"]="p1tv_id =".$value['p1tv_id']." and p1ta_title = '".trim($attrbute[0])."'";
                            $debug = array('file'=>'fileupload.php', 'line'=>'updatestyleuploadcss');
                            $attr_update =$templates_obj->update_styles($att,$debug);/*updatestyleuploadcss*/
                        }
                    }
                    $message = SUCCESS."@Style Sheet applied successfully.";
                }
                else
                {
                    $message = SUCCESS."@Style Sheet uploaded successfully.";
                }
            }
        }
    }
    else
    {// disply error if there is mismatch in the classes used
        $message = FAILURE.'@There was a problem in the CSS uploaded. Reason: New class was added/Class name has been changed.';
    }
    return $message;
}

//-------------------------------------------------------------------------------------------------------------
// function: check_valid_xml ( -- arguments -- )
//-------------------------------------------------------------------------------------------------------------
// purpose: To check if valid css or xml file
// arguments:  $id, $target_path, $defaultlabels, $apply, $findline=""
//-------------------------------------------------------------------------------------------------------------
function check_valid_xml($id, $target_path, $defaultlabels, $apply, $country_code, $language_code, $findline='')
{
    global $debug_obj, $templates_obj;

    $arguments = array($id, $target_path, $defaultlabels, $apply);
    $debug_obj->WriteDebug($class="fileupload", $function="check_valid_xml", $file=$findline['file'], $debug_obj->FindFunctionCalledline('check_valid_xml', $findline['file'], $findline['line']), $arguments);

    $xml = new DOMDocument();
    $xml->load($target_path);
    if(!$xml->schemaValidate("../data/xml/temp/labels.xsd"))   // validate xml with the pre defined name space 
    {
        $message = "<div class='error' id='0'>There was a problem in the XML uploaded. Reason: XML Tags are missing/mismatch.</div>";
    }
    else
    {
        $labels = array();
        $lablenames = array();
        $labels = '';
        $xml = simplexml_load_file($target_path);
        foreach ($xml->Home[0]->homelabel as $homelabel) {
            $lablenames[] = (string) $homelabel['for'];
        }
        foreach ($xml->Mobile[0]->mobilelabel as $mobilelabel) {
            $lablenames[] = (string) $mobilelabel['for'];
        }
        foreach ($xml->Anyphone[0]->anyphonelabel as $anyphonelabel) {
            $lablenames[] = (string) $anyphonelabel['for'];
        }
        foreach($lablenames as $child)
        {
            if (in_array($child, $defaultlabels)) {
                $labels[] = 1;
            }
            else {
                $labels[] = 0;
            }
        }
        if (in_array(0, $labels))  {  // display error if lables is missing 
            $message = "<div class='error' id='0'>There was a problem in the XML uploaded. Reason: Label attributes changed.</div>";
        }
        elseif(count($defaultlabels) != count($lablenames)) {
            $message = "<div class='error' id='0'>There was a problem in the XML uploaded. Reason: Label attributes changed.</div>";
        }
        else
        {
            $xml = simplexml_load_file($target_path);
            foreach($xml->attributes() as $key => $value)
            {
                if($key == 'country_code') {
                    if($value != $country_code) {
                        $error = "<div class='error' id='0'>Select Valid Country XML.";
                    }
                }
                if($key == 'language_code') {
                    if($value != $language_code) {
                        $error = "<div class='error' id='0'>Select Valid Language XML.";
                    }
                }
            }
            if($error == '')
                $message = "<div class='success' id='1'>XML file uploaded successfully.</div>";
            else
                $message = $error;
        }
    }
    return $message;
}

//-------------------------------------------------------------------------------------------------------------
// function: revert_uploaded_file ( -- arguments -- )
//-------------------------------------------------------------------------------------------------------------
// purpose: To check if valid css or xml file
// arguments:  $id, $format, $findline=""
//-------------------------------------------------------------------------------------------------------------
function revert_uploaded_file($id, $format, $country_id="", $country_code="", $language_id="", $language_code="", $findline='')
{
    global $debug_obj, $templates_obj, $template_func_obj;

    $arguments = array($id, $format);
    $debug_obj->WriteDebug($class="fileupload", $function="revert_uploaded_file", $file=$findline['file'], $debug_obj->FindFunctionCalledline('revert_uploaded_file', $findline['file'], $findline['line']), $arguments);

    if($format == 'css')
    {
        $dirName="../data/styles/templates/template_".$id."/";
        unlink($dirName."merchant.css");
        rename($dirName."bk-merchant.css", $dirName."merchant.css");
        $get_style["tablename"] = "tbl_template_vars";
        $get_style["whereCon"] = "p1te_id = ".$id;
        $debug = array('file'=>'fileupload.php', 'line'=>'getallfileupload');
        $all_components = $templates_obj->get_all($get_style,$debug);/*getallfileupload*/
        foreach($all_components as $k=>$acomp)
        {
            $get_attr["tablename"]="tbl_template_attribs";
            $get_attr["fieldname"]='p1ta_title,p1ta_id';
            $get_attr["whereCon"]='p1tv_id ='.$acomp['p1tv_id'];
            $debug = array('file'=>'fileupload.php', 'line'=>'getallcss');
            $form_attr = $templates_obj->get_component($get_attr,$debug);/*getallcss*/
            foreach ($form_attr as $fa) {
                $defaultattrbs[]=trim($fa['p1ta_title']);
            }
            $defaultclass[$k]=trim($acomp['p1tv_value']);
        }
        $path = $dirName."merchant.css";
        $debug = array('file'=>'fileupload.php', 'line'=>'checkvalidcss');
        $message = check_valid_css($path, $defaultclass, $all_components, $defaultattrbs, 1, $debug);/*checkvalidcss*/
        return SUCCESS."@Style Sheet uploaded successfully.";
    }
    else
    {
        $dirName = "../data/xml/templates/template_".$id."/".$country_code."/";
        unlink($dirName."labels_".$language_code.".xml");
        rename($dirName."bk-labels_".$language_code.".xml", $dirName."labels_".$language_code.".xml");
        $debug = array('file'=>'fileupload.php', 'line'=>'fetch_labels_lists');
        $p1colg_result = $template_func_obj->fetch_labels_list($country_id, $language_id, $debug);/*fetch_labels_lists*/
        $p1colg_id = $p1colg_result[0]['p1colg_id'];
        $target_path = $dirName."labels_".$language_code.".xml";

        $xml = simplexml_load_file($target_path);
        foreach ($xml->Home[0]->homelabel as $homelabel) {
            $lablenames1[] = (string) $homelabel."|:|".$homelabel['for']."|:|1";
        }
        foreach ($xml->Mobile[0]->mobilelabel as $mobilelabel) {
            $lablenames1[]= (string) $mobilelabel."|:|".$mobilelabel['for']."|:|2";
        }
        foreach ($xml->Anyphone[0]->anyphonelabel as $anyphonelabel) {
            $lablenames1[]= (string) $anyphonelabel."|:|".$anyphonelabel['for']."|:|3";
        }
        foreach($lablenames1 as $labelsarray) {
            $bothlabels = explode('|:|', $labelsarray);
            $label["tablename"] = "tbl_template_captions";
            $label["fieldname"] = "p1tc_value = '".escape_string($bothlabels[0])."'";
            $label["whereCon"] = "p1te_id =".$id." and p1tc_type = ".$bothlabels[2]." and p1colg_id = ".$p1colg_id." and p1tc_title = '".$bothlabels[1]."'";
            $debug = array('file'=>'fileupload.php', 'line'=>'updatestyleuploadxml');
            $label_update =$templates_obj->update_styles($label,$debug);/*updatestyleuploadxml*/
        }
        return SUCCESS."@xml file uploaded successfully.";
    }
}

//-------------------------------------------------------------------------------------------------------------
// function: apply_uploaded_file ( -- arguments -- )
//-------------------------------------------------------------------------------------------------------------
// purpose: To check if valid css or xml file
// arguments:  $id, $format, $findline=""
//-------------------------------------------------------------------------------------------------------------
function apply_uploaded_file($id, $format, $country_id="", $country_code="", $language_id="", $language_code="", $findline='')
{
    global $templates_obj, $template_func_obj, $debug_obj;

    $arguments = array($id, $format);
    $debug_obj->WriteDebug($class="fileupload", $function="apply_uploaded_file", $file=$findline['file'], $debug_obj->FindFunctionCalledline('apply_uploaded_file', $findline['file'], $findline['line']), $arguments);

    if($format == 'css')
    {
        $dirName="../data/styles/templates/template_".$id."/";
        $files = "merchant.css";
        $tmp_file = "tmp_merchant.css";
        rename($dirName."merchant.css", $dirName."bk-merchant.css");
    }
    else
    {
        $dirName = "../data/xml/templates/template_".$id."/".$country_code."/";
        $files = "labels_".$language_code.".xml";
        $tmp_file = "tmp_labels_".$language_code.".xml";
        rename($dirName."labels_".$language_code.".xml", $dirName."bk-labels_".$language_code.".xml");
    }
    if(file_exists($dirName.$tmp_file))
    {
        rename($dirName.$tmp_file, $dirName.$files);
        if($format == 'css')
        {
            //Get all components - styles
            $get_style["tablename"] = "tbl_template_vars";
            $get_style["whereCon"] = "p1te_id = ".$id;
            $debug = array('file'=>'fileupload.php', 'line'=>'getallfileupload');
            $all_components = $templates_obj->get_all($get_style,$debug);/*getallfileupload*/
            foreach($all_components as $k=>$acomp)
            {
                $get_attr["tablename"]="tbl_template_attribs";
                $get_attr["fieldname"]='p1ta_title,p1ta_id';
                $get_attr["whereCon"]='p1tv_id ='.$acomp['p1tv_id'];
                $debug = array('file'=>'fileupload.php', 'line'=>'getallcss');
                $form_attr = $templates_obj->get_component($get_attr,$debug);/*getallcss*/
                foreach ($form_attr as $fa) {
                    $defaultattrbs[]=trim($fa['p1ta_title']);
                }
                $defaultclass[$k]=trim($acomp['p1tv_value']);
            }
            $path = $dirName."merchant.css";
            $debug = array('file'=>'fileupload.php', 'line'=>'checkvalidcss');
            $message = check_valid_css($path, $defaultclass, $all_components, $defaultattrbs, 1, $debug);/*checkvalidcss*/
        }
        else
        {
            $debug = array('file'=>'fileupload.php', 'line'=>'fetch_labels_lists');
            $p1colg_result = $template_func_obj->fetch_labels_list($country_id, $language_id, $debug);/*fetch_labels_lists*/
            $p1colg_id = $p1colg_result[0]['p1colg_id'];
            $target_path = $dirName."labels_".$language_code.".xml";

            $xml = simplexml_load_file($target_path);
            foreach ($xml->Home[0]->homelabel as $homelabel) {
                $lablenames1[] = (string) $homelabel."|:|".$homelabel['for']."|:|1";
            }
            foreach ($xml->Mobile[0]->mobilelabel as $mobilelabel) {
                $lablenames1[]= (string) $mobilelabel."|:|".$mobilelabel['for']."|:|2";
            }
            foreach ($xml->Anyphone[0]->anyphonelabel as $anyphonelabel) {
                $lablenames1[]= (string) $anyphonelabel."|:|".$anyphonelabel['for']."|:|3";
            }
            foreach($lablenames1 as $labelsarray) {
                $bothlabels = explode('|:|', $labelsarray);
                $label["tablename"] = "tbl_template_captions";
                $label["fieldname"] = "p1tc_value = '".escape_string($bothlabels[0])."'";
                $label["whereCon"] = "p1te_id =".$id." and p1tc_type = ".$bothlabels[2]." and p1colg_id = ".$p1colg_id." and p1tc_title = '".$bothlabels[1]."'";
                $debug = array('file'=>'fileupload.php', 'line'=>'updatestyleuploadxml');
                $label_update =$templates_obj->update_styles($label,$debug);/*updatestyleuploadxml*/
            }
            $message = SUCCESS."@XML file applied successfully.";
        }
        return $message;
    }
    else
    {
        return FILE_NOT_FOUND;
    }
}

?>