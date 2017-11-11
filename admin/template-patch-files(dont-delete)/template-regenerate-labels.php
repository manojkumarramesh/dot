<?php

//-------------------------------------------------------------------------------------------------------------------
// File name   : template-regenerate-labels.php
// Description : File for UI templates file uploading functionality
//
// copyright(c), Inside Right, 2010-2011, all rights reserved.
//
// Author: Dot Com Infoway Ltd
// Created date : 06-09-2011
// Modified date: 14-12-2011
// -----------------------------------------------------------------------------------------------------------------

/*----- Include Files -----*/
include_once('../includes/configs/init.php');

/*----- Instantiate the class -----*/
$templates_obj = new templates();
$template_func_obj = new TemplateFunctions();

$args["tablename"] = "tbl_country_language_reference";
$args["fieldname"] = "p1colg_id";
$args["whereCon"] = "p1colg_stflag =1";
$debug = array('file'=>'template-regenerate-labels.php', 'line'=>'fetch_country_language_ref');
$co_lang_result = $templates_obj->get_component($args, $debug);/*fetch_country_language_ref*/

$fetch["tablename"] = "tbl_templates";
$fetch["fieldname"] = "p1te_id, p1te_device_flag";
$fetch["whereCon"] = "p1te_stflag = 1";
$debug = array('file'=>'template-regenerate-labels.php', 'line'=>'template_lists');
$templates_result = $templates_obj->get_component($fetch, $debug);/*template_lists*/

function escape_string($str)
{
    $search = array("\\","\0","\n","\r","\x1a","'",'"');
    $replace = array("\\\\","\\0","\\n","\\r","\Z","''",'\"');
    return str_replace($search,$replace,$str);
}

$count = 1;
for($i=0;$i<count($templates_result);$i++)
{
    $template_id = $templates_result[$i]['p1te_id'];
    //echo "Template id : ".$template_id."<br/>";

    if($templates_result[$i]['p1te_device_flag'] == 2)
    {
        $default_template_id = $template_func_obj->get_smartphone_default_id();//template is smartphone
    }
    else
    {
        $default_template_id = 1;//template is desktop
    }

    $args["tablename"] = "tbl_template_captions";
    $args["fieldname"] = "p1tc_title, p1tc_value, p1tc_type, p1colg_id";
    $args["whereCon"] = "p1tc_stflag =1 and p1colg_id = 1 and p1te_id = ".$default_template_id;
    $debug = array('file'=>'template-regenerate-labels.php', 'line'=>'p1_language_lists');
    $template_captions = $templates_obj->get_component($args, $debug);/*p1_language_lists*/

    foreach($co_lang_result as $key=> $value)
    {
        $fetch["tablename"] = "tbl_template_captions";
        $fetch["fieldname"] = "p1te_id, p1tc_title, p1tc_value, p1tc_type, p1colg_id";
        $fetch["whereCon"] = "p1tc_stflag =1 and p1colg_id = ".$value['p1colg_id']." and p1te_id = ".$template_id;
        $debug = array('file'=>'template-regenerate-labels.php', 'line'=>'template_listss');
        $this_result = $templates_obj->get_component($fetch, $debug);/*template_listss*/
        //echo "lang id : ".$value['p1colg_id']."   ";
        //echo count($this_result)."<br/>";

        if(count($this_result) == 0)
        {
            foreach($template_captions as $key1=> $value1)
            {
                $insert["tablename"] = "tbl_template_captions";
                $insert["fieldname"] = "p1te_id, p1tc_title, p1tc_value, p1tc_type, p1colg_id, p1tc_stflag";
                $insert["fieldval"] = "'".$template_id."','".addslashes($value1['p1tc_title'])."','".escape_string($value1['p1tc_value'])."','".$value1['p1tc_type']."','".$value['p1colg_id']."',1";
                $debug = array('file'=>'template-regenerate-labels.php', 'line'=>'insert_labels');
                $this_insert = $templates_obj->template_insert($insert, $debug);/*insert_labels*/
            }
        }
    }
    $count++;
    if($count == 30)
    {
        sleep(5);
        $count = 1;
    }
}

echo "<br/>";
echo "LABELS INSERTED SUCCESSFULLY";

//unlink(FULL_PATH."admin/template-regenerate-labels.php");

?>