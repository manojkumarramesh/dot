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



foreach($tmpslistcnt as $key=>$temp){


if($temp['p1te_device_flag']==1){

update_desktop_styles($temp['p1te_id']);

}
elseif($temp['p1te_device_flag']==2){

update_smart_styles($temp['p1te_id']);

}


}
Print_r("Updated successful");
unlink(FULL_PATH."/admin/update_all_templates.php");

function update_desktop_styles($tid){

global $templates_obj,$template_func_obj;


$new_tbl_template_vars="tbl_template_vars_new";
$new_tbl_template_attribs="tbl_template_attribs_new";
$new_tbl_form_fields="tbl_form_fields_new";

$template_vars = array(
		array("Body", "body, #footer", array(array("font-family","Arial, Helvetica, sans-serif"), array("color","#000000"),array("background-color","#ffffff"))),
		array("Links", "a, a:hover, a:focus", array(array("color","#009"))),
		array("Page Title", "#title p", array(array("background-color","#f5f5f5"),array("color","#2ddde3"))),
		array("Phone Number", "#user-number h2", array(array("color","#000000"))),
		array("Item Details", ".txn-detail", array(array("color","#000000"))),
		array("Item List", ".item-list", array(array("border-top-color","#f5f5f5"),array("color","#000"))),
		array("Form Title","legend", array(array("color","#000000"))),
		array("Form Labels", "label", array(array("color","#000000"))),
		array("Form Textbox", "input[type=\"text\"], input.text", array(array("border-color","#bbbbbb"),array("background-color","#FFFFFF"),array("color","#000000"))),
		array("Form Textbox(focus)", "input[type=\"text\"]:focus, input.text:focus", array(array("border-color","#333333"))),
		array("Inactive Button", "button, .clickable", array(array("border-color","#cccccc"),array("background-color","#cccccc"),array("color","#111111"))),
		array("Active Button", "button.active, .active", array(array("border-color","#ee9a00"),array("background-color","#FFA500"),array("color","#ffffff"))),
		array("IVR Timer Box", "#timer .notice",array(array("border-color","#f4f4f4"),array("background-color","#f4f4f4"),array("color","#000000"))),
		array("TOS Title", ".terms-box h6", array(array("color","#000000"))),
		array("Terms & Conditions", "fieldset.inline div.terms-box, .terms-box", array(array("border-color","#cccccc"),array("color","#000000"))),
		array("Footer", "#footer", array(array("border-top-color","#1B4194"))),
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

/*----- Array containing default font styles starts -----*/

$form_fiels = array (
	array(1,"font-family","font-family"),
	array(1,"font-style","font-style"),
	array(3,"color","color"),
	array(2,"font-size","font-size"),		
	array(1,"font-weight","font-weight"),
	array(1,"text-decoration","text-decoration"),
	array(1,"text-align","text-align"),
	array(1,"cursor","cursor"),		
	array(3,"background","background"),
	array(3,"background-color","background-color"),
	array(3,"border-top-color","border-top-color"),
	array(3,"border-color","border-color"),
	
	);

/*----- Array containing default font styles end -----*/

/*----- Update the template with default font styles start-----*/


foreach($form_fiels as $ff){
	$insert_formfld["tablename"] = $new_tbl_form_fields;
	$insert_formfld["fieldname"] = "p1te_id, p1ft_id, p1ff_name, p1ff_fieldid, p1ff_caption, p1ff_stflag, p1ff_added";	
	$insert_formfld["fieldval"] = "'".$tid."', '".$ff[0]."', '".$ff[1]."', '".$ff[1]."', '".$ff[2]."', 1, now()";	
	$insertformfld_arr = $templates_obj->template_insert($insert_formfld);
}
/*----- Update the template with default font styles end-----*/


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

$new_tbl_template_vars="tbl_template_vars_new";
$new_tbl_template_attribs="tbl_template_attribs_new";
$new_tbl_form_fields="tbl_form_fields_new";

$tbl_template_vars="tbl_template_vars";
$tbl_template_attribs="tbl_template_attribs";
$tbl_form_fields="tbl_form_fields";


$ref_id=$tid;

/*----Fetch style values from the selected template -----*/
$copy_vars["tablename"] = "$tbl_template_vars";
$copy_vars["fieldname"] = "p1tv_id, p1tv_name, p1tv_value";
$copy_vars["whereCon"]= "p1te_id =".$ref_id." order by p1tv_id asc";
$template_vars = $templates_obj->get_component($copy_vars);
foreach($template_vars as $key => $tv){
	$copy_attr["tablename"] = "$tbl_template_attribs";
	$copy_attr["fieldname"] = "p1ta_title, p1ta_value";
	$copy_attr["whereCon"]= "p1tv_id =".$tv['p1tv_id']." order by  p1ta_id asc";
	$template_vars[$key]['attr'] = $templates_obj->get_component($copy_attr);
}

/*----- Update the  new template with style values fetched-----*/
foreach($template_vars as $tv){
	$insert_vars["tablename"] = "$new_tbl_template_vars";
	$insert_vars["fieldname"] = "p1te_id, p1tv_name, p1tv_value, p1tv_stflag, p1tv_added";	
	$insert_vars["fieldval"] = "'".$tid."', '".$tv['p1tv_name']."', '".$tv['p1tv_value']."', 1, now()";	
	$insertvars_arr = $templates_obj->template_insert($insert_vars);
	if($insertvars_arr == 1){
		//Code getting maxid for new template variables.
		$varmax["tablename"] = "$new_tbl_template_vars";
		$varmax["fieldname"] = "max(p1tv_id) as varmaxid";
		$varmax["whereCon"]='1';
		$varmax = $templates_obj->get_maxid($varmax);
				
		if(empty($varmax[0]['varmaxid'])) $varmax[0]['varmaxid'] = $i+1;
		foreach($tv['attr'] as $ta){
			$insert_attr["tablename"] = "$new_tbl_template_attribs";
			$insert_attr["fieldname"] = "p1tv_id, p1ta_title, p1ta_value, p1ta_stflag";	
			$insert_attr["fieldval"] = "'".$varmax[0]['varmaxid']."', '".$ta['p1ta_title']."', '".$ta['p1ta_value']."', 1";	
			$insertattr_arr = $templates_obj->template_insert($insert_attr);
		}
	}	
}

/*----Fetch font style values from the selected template -----*/

$copy_fld["tablename"] = "$tbl_form_fields";
$copy_fld["fieldname"] = "p1ft_id, p1ff_name, p1ff_caption";
$copy_fld["whereCon"]= "p1te_id =".$ref_id." order by p1ff_id asc";
$form_fiels = $templates_obj->get_component($copy_fld);

/*----- Update the  new template with font style values fetched-----*/
	
foreach($form_fiels as $ff){
	$insert_formfld["tablename"] = "$new_tbl_form_fields";
	$insert_formfld["fieldname"] = "p1te_id, p1ft_id, p1ff_name, p1ff_fieldid, p1ff_caption, p1ff_stflag, p1ff_added";	
	$insert_formfld["fieldval"] = "'".$tid."', '".$ff['p1ft_id']."', '".$ff['p1ff_name']."', '".$ff['p1ff_name']."', '".$ff['p1ff_caption']."', 1, now()";	
	$insertformfld_arr = $templates_obj->template_insert($insert_formfld);
}


}


?>
 
