<?php
/*----- Include Files -----*/
include_once('../includes/configs/init.php');
include_once(FULL_PATH.'includes/classes/templatefunctions.class.php');

/*----- Procedure Call Start-----*/
$templates_obj = new templates();
$template_func_obj=new TemplateFunctions();

$args["whereCon"]="p1te_stflag = 1 order by p1te_added asc";
$args["start_lim"] = 0;
$args["lim"] = 0;
$debug = array('file'=>'templateadmin.php', 'line'=>'templatelist_admin');
$tmpslistcnt = $templates_obj->get_all_templatelist($args,$debug);

print_r("<pre>");
print_r($tmpslistcnt);

foreach($tmpslistcnt as $key=>$temp){


if($temp['p1te_device_flag']==1){

print_r("desk");
update_desktop_styles($temp['p1te_id']);

}
elseif($temp['p1te_device_flag']==2){

update_smart_styles($temp['p1te_id']);

}


}


Print_r("UPDATE COMPLETED");

function update_desktop_styles($tid){

global $templates_obj,$template_func_obj;

$new_tbl_template_vars="tbl_template_vars";
$new_tbl_template_attribs="tbl_template_attribs";
//$new_tbl_form_fields="tbl_form_fields";

$template_vars = array(
		array("Success Message", ".success, .success a", array(array("background-color","#E6EFC2"), array("color","#264409"),array("border-color","#C6D880"))),
		array("Notice Message", ".notice, .notice a, .notice h3 ", array(array("background-color","#FFF6BF"), array("color","#514721"),array("border-color","#FFD324"))),
		array("Error Message", ".error, .error a", array(array("background-color","#FBE3E4"), array("color","#8a1f11"),array("border-color","#FBC2C4"))),
		);

/*----- Array containing default styles end -----*/

/*----- Update the template with default styles start-----*/

for($i=0; $i<count($template_vars); $i++){
	$insert_vars["tablename"] = $new_tbl_template_vars;
	$insert_vars["fieldname"] = "p1te_id, p1tv_name, p1tv_value, p1tv_stflag, p1tv_added";	
	$insert_vars["fieldval"] = "'".$tid."', '".$template_vars[$i][0]."', '".addslashes($template_vars[$i][1])."', 1, now()";	
	$insertvars_arr = $templates_obj->template_insert($insert_vars);
	if($insertvars_arr == 1){
		//Code getting maxid for new template variables.
		$varmax["tablename"] = "$new_tbl_template_vars";
		$varmax["fieldname"] = "max(p1tv_id) as varmaxid";
		$varmax["whereCon"]='1';
		$varmax = $templates_obj->get_maxid($varmax);
				
		if(empty($varmax[0]['varmaxid'])) $varmax[0]['varmaxid'] = $i+1;
		for($ii=0; $ii<count($template_vars[$i][2]); $ii++){
			$ta=$template_vars[$i][2][$ii];
			$insert_attr["tablename"] = $new_tbl_template_attribs;
			$insert_attr["fieldname"] = "p1tv_id, p1ta_title, p1ta_value, p1ta_stflag";	
			$insert_attr["fieldval"] = "'".$varmax[0]['varmaxid']."', '".$ta[0]."', '".$ta[1]."', 1";	
			$insertattr_arr = $templates_obj->template_insert($insert_attr);
		}			
	}			
}

/*----- Update the template with default styles end-----*/

/*----- code to generate content to generate css file starts-----*/
$get_allvars["tablename"] = "$new_tbl_template_vars";
$get_allvars["whereCon"] = "p1te_id ='".$tid."'";	
$get_allvars_arr = $templates_obj->get_all($get_allvars);
foreach( $get_allvars_arr as $key => $value){
	$valu .= $value['p1tv_value'];
	$valu .= " { ";			
	$get_allclass["tablename"] = "$new_tbl_template_attribs";
	$get_allclass["whereCon"] = "'".$value['p1tv_id']."' = p1tv_id order by  p1ta_id asc";	
	$get_allclass_arr = $templates_obj->get_all($get_allclass);
	foreach( $get_allclass_arr as $key => $style){
		$valu .= $style['p1ta_title'];
		$valu .= ": ";
		$valu .= $style['p1ta_value'];
		$valu .= "; ";
	}
	$valu .= " }\n";			
}
/*----- code to generate content to generate css file ends-----*/

/*----- code for creating and writing the contents to screen.css file starts-----*/	
$cssfilename = (FULL_PATH."data/styles/templates/template_".$tid);
$cssfilename1="data/styles/templates/template_".$tid;
if (file_exists($cssfilename)) { 
	$fh = fopen($cssfilename."/screen.css", 'w');
    $fl1 = $cssfilename."/screen.css";
	chmod($fl1, 0777);
	fwrite($fh, $valu);
}else{
	
	mkdir($cssfilename, 0777);	
	chmod($cssfilename, 0777);
	$fh = fopen($cssfilename."/screen.css", 'w');
	fwrite($fh, $valu);
}
/*----- code for creating and writing the contents to screen.css file starts-----*/
}


function update_smart_styles($tid){

global $templates_obj,$template_func_obj;

$new_tbl_template_vars="tbl_template_vars";
$new_tbl_template_attribs="tbl_template_attribs";
//$new_tbl_form_fields="tbl_form_fields";

$template_vars = array(
		array("User Number", "#user-number h3", array(array("color","#000000"))),
		array("Success Message", ".success, .success a", array(array("background-color","#E6EFC2"), array("color","#264409"),array("border-color","#C6D880"))),
		array("Notice Message", ".notice, .notice a, .notice h3 ", array(array("background-color","#FFF6BF"), array("color","#514721"),array("border-color","#FFD324"))),
		array("Error Message", ".error, .error a", array(array("background-color","#FBE3E4"), array("color","#8a1f11"),array("border-color","#FBC2C4"))),
		);

/*----- Array containing default styles end -----*/

/*----- Update the template with default styles start-----*/

for($i=0; $i<count($template_vars); $i++){
	$insert_vars["tablename"] = $new_tbl_template_vars;
	$insert_vars["fieldname"] = "p1te_id, p1tv_name, p1tv_value, p1tv_stflag, p1tv_added";	
	$insert_vars["fieldval"] = "'".$tid."', '".$template_vars[$i][0]."', '".addslashes($template_vars[$i][1])."', 1, now()";	
	$insertvars_arr = $templates_obj->template_insert($insert_vars);
	if($insertvars_arr == 1){
		//Code getting maxid for new template variables.
		$varmax["tablename"] = "$new_tbl_template_vars";
		$varmax["fieldname"] = "max(p1tv_id) as varmaxid";
		$varmax["whereCon"]='1';
		$varmax = $templates_obj->get_maxid($varmax);
				
		if(empty($varmax[0]['varmaxid'])) $varmax[0]['varmaxid'] = $i+1;
		for($ii=0; $ii<count($template_vars[$i][2]); $ii++){
			$ta=$template_vars[$i][2][$ii];
			$insert_attr["tablename"] = $new_tbl_template_attribs;
			$insert_attr["fieldname"] = "p1tv_id, p1ta_title, p1ta_value, p1ta_stflag";	
			$insert_attr["fieldval"] = "'".$varmax[0]['varmaxid']."', '".$ta[0]."', '".$ta[1]."', 1";	
			$insertattr_arr = $templates_obj->template_insert($insert_attr);
		}			
	}			
}

/*----- Update the template with default styles end-----*/

/*----- code to generate content to generate css file starts-----*/
$get_allvars["tablename"] = "$new_tbl_template_vars";
$get_allvars["whereCon"] = "p1te_id ='".$tid."'";	
$get_allvars_arr = $templates_obj->get_all($get_allvars);
foreach( $get_allvars_arr as $key => $value){
	$valu .= $value['p1tv_value'];
	$valu .= " { ";			
	$get_allclass["tablename"] = "$new_tbl_template_attribs";
	$get_allclass["whereCon"] = "'".$value['p1tv_id']."' = p1tv_id order by  p1ta_id asc";	
	$get_allclass_arr = $templates_obj->get_all($get_allclass);
	foreach( $get_allclass_arr as $key => $style){
		$valu .= $style['p1ta_title'];
		$valu .= ": ";
		$valu .= $style['p1ta_value'];
		$valu .= "; ";
	}
	$valu .= " }\n";			
}
/*----- code to generate content to generate css file ends-----*/

/*----- code for creating and writing the contents to screen.css file starts-----*/	
$cssfilename = (FULL_PATH."data/styles/templates/template_".$tid);
$cssfilename1="data/styles/templates/template_".$tid;
if (file_exists($cssfilename)) { 
	$fh = fopen($cssfilename."/screen.css", 'w');
    $fl1 = $cssfilename."/screen.css";
	chmod($fl1, 0777);
	fwrite($fh, $valu);
}else{
	
	mkdir($cssfilename, 0777);	
	chmod($cssfilename, 0777);
	$fh = fopen($cssfilename."/screen.css", 'w');
	fwrite($fh, $valu);
}
/*----- code for creating and writing the contents to screen.css file starts-----*/
}





?>
 
