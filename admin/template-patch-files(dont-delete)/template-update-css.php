<?php

//-------------------------------------------------------------------------------------------------------------------
// File name   : template-update-css.php
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

$fetch["tablename"] = "tbl_template_vars as vars LEFT JOIN tbl_templates as temp ON (temp.p1te_id = vars.p1te_id)";
$fetch["fieldname"] = "vars.p1te_id";
$fetch["whereCon"] = "vars.p1tv_name = 'Form Title' AND temp.p1te_id = vars.p1te_id AND temp.p1te_stflag = 1 AND temp.p1te_device_flag = 1";
$debug = array('file'=>'template-regenerate-xml.php', 'line'=>'template_lists');
$result = $templates_obj->get_component($fetch, $debug);/*template_lists*/

for($i=0;$i<count($result);$i++)
{
    $template_id = $result[$i]['p1te_id'];

    $arg["tablename"] = "tbl_template_vars";
    $arg["fieldname"] = "p1tv_value = '.legend' ";
    $arg["whereCon"] = "p1tv_name = 'Form Title' AND p1te_id = ".$template_id;
    $debug = array('file'=>'autosave.php', 'line'=>'updates');
    $update =$templates_obj->update_styles($arg, $debug);/*updates*/

    if($update)
    {
        $get_components["tablename"] = "tbl_template_vars";
        $get_components["whereCon"] = "p1te_id =".$template_id;
        $debug = array('file'=>'template-regenerete-css.php', 'line'=>'template_vars');
        $allcomponents = $templates_obj->get_all($get_components, $debug);/*template_vars*/
        $valu = "";
        foreach( $allcomponents as $key => $value)
        {
            $get_components = '';
            $get_components["tablename"] ="tbl_template_attribs";
            $get_components["whereCon"] = "p1tv_id = ".$value['p1tv_id'];
            $debug = array('file'=>'template-regenerete-css.php', 'line'=>'template_attribs');
            $listallattributes = $templates_obj->get_all($get_components, $debug);/*template_attribs*/
            $valu .= $value['p1tv_value'];
            $valu .= " { ";
            foreach( $listallattributes as $key => $style)
            {
                $valu .= $style['p1ta_title'];
                $valu .= ": ";
                $valu .= $style['p1ta_value'];
                $valu .= "; ";
            }
            $valu .= " }\n";
        }
        $filename = "../data/styles/templates/template_".$template_id;
        if(file_exists($filename))
        {
            $fh = fopen('../data/styles/templates/template_'.$template_id.'/merchant.css', 'w');
            fwrite($fh, $valu);
            //chmod('../data/styles/templates/template_'.$template_id, 0777);
            chmod('../data/styles/templates/template_'.$template_id.'/merchant.css', 0777);
        }
    }
    else
    {
        echo "update failed";
        die;
    }
}

echo "<br/>";
echo "COMPLETED";

//unlink(FULL_PATH."admin/template-regenerate-css.php");

?>