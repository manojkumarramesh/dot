<?php
//-------------------------------------------------------------------------------------------------------------------
// File name   : createtemplate.php
// Description : File to handle newly create UI template - style and labels information
//
// © 2010-2011 PaymentOne Corportation. All rights reserved.
//
// Created date : 22-02-2010
// Modified date: 28-11-2011
// ------------------------------------------------------------------------------------------------------------------

/*----- Include Files -----*/
include_once('../includes/configs/init.php');
include('../includes/configs/sessionadminc.php');
include('../includes/classes/templatefunctions.class.php');

/*----- Instantiate the class -----*/
$templates_obj = new templates();
$template_func_obj=new TemplateFunctions();

$tname=trim($_POST['tname']);
$t_type=trim($_POST['t_type']);
$sid=trim($_POST['sid']);

if(trim($tname) == '')
{
    echo "Enter UI Template Name";//throw error if template name is empty
    exit;
}
elseif(strlen(trim($tname)) >50)
{
    echo "Maximum UI template name limit is 50 characters";//throw error if template name is more than 50 characters
    exit;
}
else if(!preg_match("/^[A-Za-z0-9\-\#\.\_\!\@\$\%\^\(\)\/ ]+$/", $tname))
{
    echo "Enter valid UI Template Name";
    exit;
}

if($_SESSION['merchant_id'] == "" && $_SESSION['admin_id'] != "")
{
    if($_SESSION['merchant_id'] =='')
    {
        $admin_value='1';
    }
    //Code for checking if ui template name already exists
    $debug = array('file'=>'createtemplate.php', 'line'=>'createtempcheck');
    $is_duplicate = $template_func_obj->check_template_duplication(trim($tname),$admin_value,"",$debug);/*createtempcheck*/
}
elseif($_SESSION['merchant_id'] != "" && $_SESSION['admin_id'] != "")
{
    //Code for checking if ui template name already exists
    $debug = array('file'=>'createtemplate.php', 'line'=>'creatmerchanttempcheck');
    $is_duplicate=$template_func_obj->check_template_duplication(trim($tname),"",$_SESSION['merchant_id'],$debug);/*creatmerchanttempcheck*/
}

if($is_duplicate == 'duplicate')
{
    echo "UI Template Name already exists";// throw error if template name is already available
    exit;
}

$args["tablename"] = "tbl_templates";
if(isset($_SESSION['merchant_id']))
{
    $args["fieldname"] = "p1me_id, p1te_name, p1te_description, p1te_stflag, p1te_added, p1te_device_flag";
    $args["fieldval"] = "'".$_SESSION['merchant_id']."','".$tname."', 'Template Description Here !!!','1',now(),$t_type";
}
else
{
    $args["fieldname"] = "p1ad_id, p1te_name, p1te_description, p1te_stflag, p1te_added, p1te_device_flag";
    $args["fieldval"] = "'".$admin_value."','".$tname."', 'Template Description Here !!!','1',now(),$t_type";
}
$debug = array('file'=>'createtemplate.php', 'line'=>'add_new_template');
$template_arr = $templates_obj->template_insert($args,$debug);/*add_new_template*/

if($t_type == '1')// if type is desktop
{
    $ref_id = 1;
    $_SESSION['d_type']="desktop";
}
elseif($t_type=="2")// if type is smartphone
{
    $args_smart["whereCon"]="p1ad_id = 1 and p1me_id IS NULL and p1te_stflag = 1 and p1te_device_flag = 2 order by p1te_id asc";
    $args_smart["start_lim"] = 0;
    $args_smart["lim"] = 0;
    $debug = array('file'=>'createtemplate.php', 'line'=>'args_sma');
    $ref_smart = $templates_obj->get_all_templatelist($args_smart, $debug);/*args_sma*/
    $ref_id = $ref_smart[0]['p1te_id'];
    $_SESSION['d_type'] = "smart";
}

$currenttemplate = new templates();

include_once(FULL_PATH.'admin/copypage.php');

echo $ref_id;
exit;

?>