<?php
/*----- Include Files -----*/
include_once('../includes/configs/init.php');
include_once(FULL_PATH.'includes/classes/templatefunctions.class.php');

/*----- Procedure Call Start-----*/
$templates_obj = new templates();
$template_func_obj=new TemplateFunctions();

$args["whereCon"]="p1te_stflag = 1 and p1te_device_flag = 1 order by p1te_added asc";
$args["start_lim"] = 0;
$args["lim"] = 0;
$debug = array('file'=>'templateadmin.php', 'line'=>'templatelist_admin');
$tmpslistcnt = $templates_obj->get_all_templatelist($args,$debug);


foreach($tmpslistcnt as $key=>$val){

$tid=$val['p1te_id'];

$get_allvars["tablename"] = "tbl_template_vars";
$get_allvars["whereCon"] = "p1te_id ='".$tid."' AND  p1tv_name='Footer'  AND p1tv_value='#footer' ";	
$get_allvars_arr = $templates_obj->get_all($get_allvars);

$vid=$get_allvars_arr['0']['p1tv_id'];

if($vid != ''){

$insert_attr["tablename"] = "tbl_template_attribs";
$insert_attr["fieldname"] = "p1tv_id, p1ta_title, p1ta_value, p1ta_stflag";	
$insert_attr["fieldval"] = "'".$vid."', 'background-color', '#ffffff', 1";
$insertattr_arr = $templates_obj->template_insert($insert_attr);	

}



}


print_r("UPDATE COMPLETED");


?> 
