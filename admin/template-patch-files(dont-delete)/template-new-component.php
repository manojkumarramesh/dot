<?php

/*----- Include Files -----*/
include_once('../includes/configs/init.php');
include_once(FULL_PATH.'includes/classes/templatefunctions.class.php');

/*----- Procedure Call Start-----*/
$templates_obj = new templates();
$template_func_obj=new TemplateFunctions();

$fetch["tablename"] = "tbl_templates";
$fetch["fieldname"] = "p1te_id";
$fetch["whereCon"] = "p1te_stflag = 1 and p1te_device_flag = 1";
$debug = array('file'=>'template-new-component.php', 'line'=>'templatelist_admin');
$tmpslistcnt = $templates_obj->get_component($fetch, $debug);/*templatelist_admin*/

foreach($tmpslistcnt as $key=> $temp)
{
    update_desktop_styles($temp['p1te_id']);
}

function update_desktop_styles($tid)
{
    global $templates_obj, $template_func_obj;

    $new_tbl_template_vars = "tbl_template_vars";
    $new_tbl_template_attribs = "tbl_template_attribs";

    $template_vars = array(
        array("IVR Calling Instruction", "#ivr-calling-instruction, #ivr-calling-instruction h3", array(array("background-color","#fff6bf"), array("border-color","#ffd324")))
    );

    for($i=0; $i<count($template_vars); $i++)
    {
        $insert_vars["tablename"] = $new_tbl_template_vars;
        $insert_vars["fieldname"] = "p1te_id, p1tv_name, p1tv_value, p1tv_stflag, p1tv_added";	
        $insert_vars["fieldval"] = "'".$tid."', '".$template_vars[$i][0]."', '".addslashes($template_vars[$i][1])."', 1, now()";
        $debug = array('file'=>'template-new-component.php', 'line'=>'template_insert');
        $insertvars_arr = $templates_obj->template_insert($insert_vars, $debug);/*template_insert*/
        if($insertvars_arr == 1)
        {
            //Code getting maxid for new template variables.
            $varmax["tablename"] = $new_tbl_template_vars;
            $varmax["fieldname"] = "max(p1tv_id) as varmaxid";
            $varmax["whereCon"] = '1';
            $debug = array('file'=>'template-new-component.php', 'line'=>'max');
            $varmax = $templates_obj->get_maxid($varmax);/*max*/

            if(empty($varmax[0]['varmaxid']))
                $varmax[0]['varmaxid'] = $i+1;

            for($ii=0; $ii<count($template_vars[$i][2]); $ii++)
            {
                $ta = $template_vars[$i][2][$ii];
                $insert_attr["tablename"] = $new_tbl_template_attribs;
                $insert_attr["fieldname"] = "p1tv_id, p1ta_title, p1ta_value, p1ta_stflag";
                $insert_attr["fieldval"] = "'".$varmax[0]['varmaxid']."', '".$ta[0]."', '".$ta[1]."', 1";
                $debug = array('file'=>'template-new-component.php', 'line'=>'template_inserts');
                $insertattr_arr = $templates_obj->template_insert($insert_attr);/*template_inserts*/
            }
        }
    }

    $get_allvars["tablename"] = $new_tbl_template_vars;
    $get_allvars["whereCon"] = "p1te_id = '".$tid."'";
    $get_allvars_arr = $templates_obj->get_all($get_allvars);
    foreach( $get_allvars_arr as $key => $value)
    {
        $valu .= $value['p1tv_value'];
        $valu .= " { ";
        $get_allclass["tablename"] = $new_tbl_template_attribs;
        $get_allclass["whereCon"] = "'".$value['p1tv_id']."' = p1tv_id order by  p1ta_id asc";	
        $get_allclass_arr = $templates_obj->get_all($get_allclass);
        foreach( $get_allclass_arr as $key => $style)
        {
            $valu .= $style['p1ta_title'];
            $valu .= ": ";
            $valu .= $style['p1ta_value'];
            $valu .= "; ";
        }
        $valu .= " }\n";
    }
    $cssfilename = FULL_PATH."data/styles/templates/template_".$tid;
    $cssfilename1 = "data/styles/templates/template_".$tid;
    if(file_exists($cssfilename))
    {
        $fh = fopen($cssfilename."/merchant.css", 'w');
        $fl1 = $cssfilename."/merchant.css";
        //chmod($fl1, 0777);
        fwrite($fh, $valu);
    }
    else
    {
        mkdir($cssfilename, 0777);
        chmod($cssfilename, 0777);
        $fh = fopen($cssfilename."/merchant.css", 'w');
        fwrite($fh, $valu);
    }
}

echo "<br/>";
echo "Completed Successfully";

//unlink(FULL_PATH."admin/template-new-component.php");

?>