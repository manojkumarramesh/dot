<?php

//-------------------------------------------------------------------------------------------------------------------
// File name   : template-update-query.php
// Description : File for UI templates file uploading functionality
//
// copyright(c), Inside Right, 2010-2011, all rights reserved.
//
// Author: Dot Com Infoway Ltd
// Created date : 06-09-2011
// Modified date: 06-09-2011
// -----------------------------------------------------------------------------------------------------------------

/*----- Include Files -----*/
include_once('../includes/configs/init.php');

/*----- Procedure Call and Coding Part Start-----*/
$templates_obj = new templates();

$fetch["tablename"] = "tbl_template_captions";
$fetch["fieldname"] = "p1tc_title, p1tc_value";
$fetch["whereCon"] = "p1tc_type = 1 and p1colg_id= 1 and p1te_id = 1";
$templates_result = $templates_obj->get_component($fetch);

echo "<pre>";
echo "HOME";
print_r($templates_result);

$fetch["tablename"] = "tbl_template_captions";
$fetch["fieldname"] = "p1tc_title, p1tc_value";
$fetch["whereCon"] = "p1tc_type = 2 and p1colg_id= 1 and p1te_id = 1";
$templates_result2 = $templates_obj->get_component($fetch);

echo "MOBILE";
print_r($templates_result2);

$fetch["tablename"] = "tbl_template_captions";
$fetch["fieldname"] = "p1tc_title, p1tc_value";
$fetch["whereCon"] = "p1tc_type = 3 and p1colg_id= 1 and p1te_id = 1";
$templates_result3 = $templates_obj->get_component($fetch);

echo "ANY Phone";
print_r($templates_result3);

?>