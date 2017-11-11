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
$fetch["fieldname"] = "p1tc_title";
$fetch["whereCon"] = "p1tc_type = 3 and p1te_id = 1 and p1colg_id = 1";
$templates_result = $templates_obj->get_component($fetch);

function escape_string($str)
{
    $search = array(" ", "?", ":", ",", ".", "(", ")", "[", "]", "/", "&");
    $replace = array("_", "", "", "", "", "", "", "", "", "_", "_");
    return str_replace($search,$replace,$str);
}

foreach($templates_result as $val1)
{
    $key1[] = strtolower(escape_string($val1['p1tc_title']));
}

echo "<pre>";
print_r($key1);

foreach($templates_result as $key => $val)
{
    //echo $key." => ".$val['p1tc_title']."<br/>";
}


?>