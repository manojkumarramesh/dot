<?php

//-------------------------------------------------------------------------------------------------------------------
// File name   : template-regenerate-css.php
// Description : File for UI templates file uploading functionality
//
// copyright(c), Inside Right, 2010-2011, all rights reserved.
//
// Author: Dot Com Infoway Ltd
// Created date : 06-09-2011
// Modified date: 28-11-2011
// -----------------------------------------------------------------------------------------------------------------

/*----- Include Files -----*/
include_once('../includes/configs/init.php');

/*----- Instantiate the class -----*/
$templates_obj = new templates();
$common_obj = new Common();

$fetch["tablename"] = "tbl_templates";
$fetch["fieldname"] = "p1te_id, p1te_css";
$fetch["whereCon"] = "p1te_stflag = 1";
$debug = array('file'=>'template-regenerate-css.php', 'line'=>'template_lists');
$templates = $templates_obj->get_component($fetch, $debug);/*template_lists*/

$css_path = "data/styles/templates/template_";

for($i=0;$i<count($templates);$i++)
{
    $template_id = $templates[$i]['p1te_id'];

    $update["tablename"] = "tbl_templates";
    $update["fieldname"] = "p1te_css = '".addslashes($css_path.$template_id.'/merchant.css')."'";
    $update["whereCon"] = "p1te_id = ".$template_id;
    $debug = array('file'=>'template-regenerate-css.php', 'line'=>'updates');
    $label_update = $templates_obj->update_styles($update, $debug);/*updates*/

    $file1 = FULL_PATH.$css_path.$template_id.'/screen.css';
    $file2 = FULL_PATH.$css_path.$template_id.'/merchant.css';

    rename($file1, $file2);
    chmod($file2, 0777);
}

echo "<br/>";
echo "COMPLETED";

//unlink(FULL_PATH."admin/template-regenerate-css.php");

?>