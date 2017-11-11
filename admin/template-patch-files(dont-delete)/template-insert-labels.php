<?php

//-------------------------------------------------------------------------------------------------------------------
// File name   : template-insert-labels.php
// Description : File for UI templates new label insert functionality
//
// copyright(c), Inside Right, 2010-2011, all rights reserved.
//
// Author: Dot Com Infoway Ltd
// Created date : 05-09-2011
// Modified date: 06-12-2011
// -----------------------------------------------------------------------------------------------------------------

/*----- Include Files -----*/
include_once('../includes/configs/init.php');

/*----- Instantiate the class -----*/
$templates_obj = new templates();
$template_func_obj = new TemplateFunctions();

function insert_labels($labels_array, $flag, $type)
{
    global $templates_obj, $template_func_obj;

    $args["tablename"] = "tbl_country_language_reference";
    $args["fieldname"] = "p1colg_id";
    $args["whereCon"] = "p1colg_stflag =1";
    $co_lang_result = $templates_obj->get_component($args);

    $fetch["tablename"] = "tbl_templates";
    $fetch["fieldname"] = "p1te_id, p1te_device_flag";
    $fetch["whereCon"] = "p1te_stflag = 1 and p1te_device_flag = ".$flag;
    $templates_result = $templates_obj->get_component($fetch);

    $count = 1;
    for($i=0;$i<count($templates_result);$i++)
    {
        $template_id = $templates_result[$i]['p1te_id'];
        for($j=0;$j<count($co_lang_result);$j++)
        {
            foreach ($labels_array as $key => $value)
            {
                $insert_caption["tablename"] = "tbl_template_captions";
                $insert_caption["fieldname"] = "p1te_id, p1tc_title, p1tc_value, p1tc_type, p1colg_id, p1tc_stflag";
                $insert_caption["fieldval"] = "'".$template_id."','".addslashes($key)."','".addslashes($value)."','".$type."','".$co_lang_result[$j]['p1colg_id']."',1";
                $insertcaption_arr = $templates_obj->template_insert($insert_caption);
            }
         }
        $count++;
        if($count == 100)
        {
            sleep(5);
            $count = 1;
        }
    }
}

/*---------------------DESKTOP LABELS----------------------------------------*/

$desktop_home_array = array(
    'Products not available.' => 'Products not available.'
);
insert_labels($desktop_home_array, 1, 1);       //insert for mobile label

sleep(5);

$desktop_mobile_array = array(
    'Operator ID Mismatch' => 'Operator ID mismatch, please select from one of the following available products.',
    'Products not available.' => 'Products not available.'
);
insert_labels($desktop_mobile_array, 1, 2);     //insert for mobile label

sleep(5);

$desktop_anyphone_array = array(
    'Product Not Supported' => 'Product/Price not available, please select from one of the following available products.',
    'Products not available.' => 'Products not available.'
);
insert_labels($desktop_anyphone_array, 1, 3);       //insert for anyphone label

/*---------------------SMARTPHONE LABELS----------------------------------------*/

sleep(5);

$smartphone_home_array = array(
    'Products not available.' => 'Products not available.'
);
insert_labels($smartphone_home_array, 2, 1);        //insert for home label

sleep(5);

$smartphone_mobile_array = array(
    'Operator ID Mismatch' => 'Operator ID mismatch, please select from one of the following available products.',
    'Products not available.' => 'Products not available.'
);
insert_labels($smartphone_mobile_array, 2, 2);      //insert for mobile label

sleep(5);

$smartphone_anyphone_array = array(
    'Product Not Supported' => 'Product/Price not available, please select from one of the following available products.',
    'Products not available.' => 'Products not available.'
);
insert_labels($smartphone_anyphone_array, 2, 3);        //insert for anyphone label

echo "<br/>";
echo "LABELS INSERTED SUCCESSFULLY";

//unlink(FULL_PATH."admin/template-insert-labels.php");

?>