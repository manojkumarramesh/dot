<?php

/*----- Include Files -----*/
include_once('../includes/configs/init.php');

/*----- Instantiate the class -----*/
$templates_obj = new templates();
$common_obj = new Common();

$args["tablename"] = "tbl_templates";
$args["fieldname"] = "p1te_id";
$args["whereCon"] = "p1te_stflag = 1 and p1te_device_flag = 1";
$debug = array('file'=>'template-new-component.php', 'line'=>'templatelist_admin');
$tmp_result = $templates_obj->get_component($args, $debug);/*templatelist_admin*/
$count = count($tmp_result);

die;

for($i=0;$i<$count;$i++)
{
    $template_id = $tmp_result[$i]['p1te_id'];

    $args1["tablename"] = "tbl_template_vars";
    $args1["fieldname"] = "p1tv_id, p1te_id, p1tv_name";
    $args1["whereCon"] = "p1tv_name = 'IVR Calling Instruction' and p1te_id = ".$template_id;
    $debug = array('file'=>'template-new-component.php', 'line'=>'templatelist_admin');
    $vars_result = $templates_obj->get_component($args1, $debug);/*templatelist_admin*/
    $attribs_id = $vars_result[0]['p1tv_id'];

    $insert_attrs["tablename"] = "tbl_template_attribs";
    $insert_attrs["fieldname"] = "p1tv_id, p1ta_title, p1ta_value, p1ta_stflag";
    $insert_attrs["fieldval"] = "'".$attribs_id."', 'color', '#000000', 1";
    $debug = array('file'=>'template-new-component.php', 'line'=>'template_insert');
    $insert = $templates_obj->template_insert($insert_attrs, $debug);/*template_insert*/

    if($insert)
    {
        $get_components["tablename"] = "tbl_template_vars";
        $get_components["whereCon"] = "p1te_id = ".$template_id;
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
            foreach($listallattributes as $key => $style)
            {
                $valu .= $style['p1ta_title'];
                $valu .= ": ";
                $valu .= $style['p1ta_value'];
                $valu .= "; ";
            }
            $valu .= " }\n";
        }
        $filename = "../data/styles/templates/template_".$template_id;
        $cssfilename = FULL_PATH."data/styles/templates/template_".$template_id;
        if(file_exists($filename))
        {
            $fh = fopen('../data/styles/templates/template_'.$template_id.'/merchant.css', 'w');
            fwrite($fh, $valu);
            chmod('../data/styles/templates/template_'.$template_id.'/merchant.css', 0777);
        }
        else
        {
            mkdir($cssfilename, 0777);
            chmod($cssfilename, 0777);
            $fh = fopen($cssfilename."/merchant.css", 'w');
            fwrite($fh, $valu);
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