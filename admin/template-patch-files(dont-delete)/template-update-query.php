<?php

//-------------------------------------------------------------------------------------------------------------------
// File name   : template-update-query.php
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

/*----- Procedure Call and Coding Part Start-----*/
$templates_obj = new templates();

$fetch["tablename"] = "tbl_templates";
$fetch["fieldname"] = "p1te_id, p1te_xml";
$fetch["whereCon"] = "p1te_stflag = 1";
$templates_result = $templates_obj->get_component($fetch);

$template_path = "data/xml/templates/template_";

for($i=0;$i<count($templates_result);$i++)
{
    $tid = $templates_result[$i]['p1te_id'];

    $update["tablename"] = "tbl_templates";
    $update["fieldname"] = "p1te_xml = '".addslashes($template_path.$tid.'/')."'";
    $update["whereCon"] = "p1te_id = ".$tid;
    $debug = array('file'=>'template-update-query.php', 'line'=>'updates');
    $label_update = $templates_obj->update_styles($update, $debug);/*updates*/
}

echo "<br/>";
echo "Updated Successfully";

?>