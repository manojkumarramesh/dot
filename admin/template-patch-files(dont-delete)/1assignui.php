<?php
//-------------------------------------------------------------------------------------------------------------------
// File name   : assignui.php
// Description : File to handle - assign UI templates to merchants
//
// © 2010-2011 PaymentOne Corportation. All rights reserved.
//
// Created date : 20-09-2010
// Modified date: 28-11-2011
// ------------------------------------------------------------------------------------------------------------------

/*----- Include Files -----*/
include_once('../includes/configs/init.php');

//Instantiate the class
$templates_obj = new templates();
$debug_obj = new Debug();

//To assign ui template to merchants
if($_REQUEST['appiled'] == 'merchants')
{
    $template_id = trim($_REQUEST['id']);
    //if assign ui template to all the merchants
    if(trim($_REQUEST['temp_all']) == 'yes')
    {
        $template["tablename"] = "tbl_templates";
        $template["fieldname"] = "p1te_refid = 0";
        $template["whereCon"] = "p1te_id = '$template_id'";
        $debug = array('file'=>'assignui.php', 'line'=>'applytoallmerchants');
        $template_arr = $templates_obj->update_styles($template, $debug);/*applytoallmerchants*/
        $option = 'all';
    }
    //if assign ui template to selected merchants
    else
    {
        $reference_id = trim($_REQUEST['te_refid']);
        $reference_id = substr($reference_id, 0, -1);
        $reference_id = trim($reference_id);
        $template["tablename"] = "tbl_templates";
        $template["fieldname"] = "p1te_refid = '$reference_id'";
        $template["whereCon"] = "p1te_id = '$template_id'";
        $debug = array('file'=>'assignui.php', 'line'=>'applytoselectedmerchants');
        $template_arr = $templates_obj->update_styles($template, $debug);/*applytoselectedmerchants*/
        $option = 'added';
    }
    echo $option;
}

?>