<?php
//-------------------------------------------------------------------------------------------------------------------
// File name   :smartphone_default.php
// Description : To creatre default default smartphone template
//
// copyright(c), Inside Right, 2010-2011, all rights reserved.
//
// Author: Dot Com Infoway Ltd
// Created date: 14-03-2010
//
// ------------------------------------------------------------------------------------------------------------------
/*----- Include Files -----*/
include_once('../includes/configs/init.php');
include_once(FULL_PATH.'includes/classes/templatefunctions.class.php');

/*----- Procedure Call Start-----*/
$templates_obj = new templates();
$template_func_obj=new TemplateFunctions();

$tname="New_desktop_UI_1";
$admin_value=1;
$type=1;

$args["tablename"] = "tbl_templates";
$args["fieldname"] = " p1me_id, p1ad_id, p1te_name, p1te_description, p1te_stflag, p1te_added, p1te_device_flag";	
$args["fieldval"] = "'','".$admin_value."','".$tname."', 'Template Description Here !!!','1',now(),$type";
$debug = array('file'=>'templatefunctions.class.php', 'line'=>'add_copy_template');	
$template_arr = $templates_obj->template_insert($args,$debug);/*add_copy_template*/


/*----- To fetch the last inserted templare id  start-----*/
$get_maxid["tablename"] = "tbl_templates";
$get_maxid["fieldname"] = "max(p1te_id) as maxid";
$get_maxid["whereCon"]='1';
$maxid = $templates_obj->get_maxid($get_maxid);		
/*----- To fetch the last inserted templare id  end-----*/

if($maxid[0]['maxid'] > 0){
	$_SESSION['template_id'] = $maxid[0]['maxid'];
}

$ref_id=1;

/*---- Array with the default lables and values start -----*/
/*----Fetch lables values from the selected template -----*/
$copy_caps["tablename"] = "tbl_template_captions";
$copy_caps["fieldname"] = "p1tc_title, p1tc_value, p1tc_type";
$copy_caps["whereCon"]= "p1te_id =".$ref_id." order by  p1tc_id asc";
$template_caption = $templates_obj->get_component($copy_caps);

/*----- Update the template with default labels start-----*/	
foreach($template_caption as $fc){
	$insert_caption["tablename"] = "tbl_template_captions";
	$insert_caption["fieldname"] = "p1te_id, p1tc_title, p1tc_value, p1tc_type, p1lg_id, p1tc_stflag";	
	$insert_caption["fieldval"] = "'".$_SESSION['template_id']."', '".$fc['p1tc_title']."', '".addslashes($fc['p1tc_value'])."','".$fc['p1tc_type']."', 1, 1";
	$insertcaption_arr = $templates_obj->template_insert($insert_caption);
}
/*----- Update the template with default labels ends-----*/

/*----- Array containing default styles end -----*/
$template_vars = array(
		array("Body", "body, #footer", array(array("font-family","Arial, Helvetica, sans-serif"), array("color","#2ddde3"),array("background-color","#ffffff"))),

		array("Links", "a, a:hover", array(array("color","#009"))),

		array("Page Title", "#title p", array(array("background-color","#f5f5f5"),array("color","#2ddde3"))),

		array("Phone Number", "#user-number h2", array(array("color","#000000"))),

		array("Item Details", ".txn-detail", array(array("color","#000000"))),

		array("Item List", ".item-list", array(array("border-top-color","#f5f5f5"),array("color","#000"))),

		array("Form Title","legend", array(array("color","#000000"))),

		array("Form Labels", "label", array(array("color","#000000"))),

		array("Form Textbox", "input[type=\"text\"\, input.text", array(array("border-color","#bbbbbb"),array("background-color","#FFFFFF"),array("color","#000000"))),

		array("Form Textbox(focus)", "input[type=\"text\"]: focus, input.text:focus", array(array("border-color","#333333"))),
		
		array("Inactive Button", "button", array(array("border-color","#cccccc"),array("background-color","#cccccc"),array("color","#111111"))),

		array("Active Button", "button.active",array(array("border-color","#ee9a00"),array("background-color","#FFA500"),array("color","#ffffff"))),


		array("IVR Timer Box", "#timer .notice",array(array("border-color","#f4f4f4"),array("background-color","#f4f4f4"),array("color","#000000"))),


		array("TOS Title", ".terms-box h6", array(array("color","#000000"))),

		array("Terms & Conditions", ".terms-box", array(array("border-color","#cccccc"),array("color","#000000"))),

		array("Footer", "#footer", array(array("color","#FFFFFF"),array("border-top-color","#1B4194"))),

		);

/*----- Array containing default styles end -----*/

/*----- Update the template with default styles start-----*/

for($i=0; $i<count($template_vars); $i++){
	$insert_vars["tablename"] = "tbl_template_vars";
	$insert_vars["fieldname"] = "p1te_id, p1tv_name, p1tv_value, p1tv_stflag, p1tv_added";	
	$insert_vars["fieldval"] = "'".$_SESSION['template_id']."', '".$template_vars[$i][0]."', '".$template_vars[$i][1]."', 1, now()";	
	$insertvars_arr = $templates_obj->template_insert($insert_vars);
	if($insertvars_arr == 1){
		//Code getting maxid for new template variables.
		$varmax["tablename"] = "tbl_template_vars";
		$varmax["fieldname"] = "max(p1tv_id) as varmaxid";
		$varmax["whereCon"]='1';
		$varmax = $templates_obj->get_maxid($varmax);
				
		if(empty($varmax[0]['varmaxid'])) $varmax[0]['varmaxid'] = $i+1;
		for($ii=0; $ii<count($template_vars[$i][2]); $ii++){
			$ta=$template_vars[$i][2][$ii];
			$insert_attr["tablename"] = "tbl_template_attribs";
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
	$insert_formfld["tablename"] = "tbl_form_fields";
	$insert_formfld["fieldname"] = "p1te_id, p1ft_id, p1ff_name, p1ff_fieldid, p1ff_caption, p1ff_stflag, p1ff_added";	
	$insert_formfld["fieldval"] = "'".$_SESSION['template_id']."', '".$ff[0]."', '".$ff[1]."', '".$ff[1]."', '".$ff[2]."', 1, now()";	
	$insertformfld_arr = $templates_obj->template_insert($insert_formfld);
}
/*----- Update the template with default font styles end-----*/


/*----- code to generate content to generate css file starts-----*/
$get_allvars["tablename"] = "tbl_template_vars";
$get_allvars["whereCon"] = "p1te_id ='".$_SESSION['template_id']."'";	
$get_allvars_arr = $templates_obj->get_all($get_allvars);
foreach( $get_allvars_arr as $key => $value){
	$valu .= $value['p1tv_value'];
	$valu .= " { ";			
	$get_allclass["tablename"] = "tbl_template_attribs";
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
$cssfilename = (FULL_PATH."data/styles/templates/template_".$_SESSION['template_id']);
$cssfilename1="data/styles/templates/template_".$_SESSION['template_id'];
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

//Get all updated captions details.
$get_caption1["tablename"]="tbl_template_captions";
$get_caption1["whereCon"]='p1te_id ='.$_SESSION['template_id'].' and p1tc_type =1 order by  p1tc_id asc';
$homeallcaptions = $templates_obj->get_all($get_caption1);

//Get all updated captions details.
$get_caption2["tablename"]="tbl_template_captions";
$get_caption2["whereCon"]='p1te_id ='.$_SESSION['template_id'].' and p1tc_type =2 order by  p1tc_id asc';
$moballcaptions = $templates_obj->get_all($get_caption2);

//Coding for xml file creation for updated labels
$dom = new DOMDocument("1.0");
$dom->encoding = "utf-8";
// Create a comment
$gendetails = $dom->createComment('Generated by PaymentOne.com');
// Put this comment at the Root of the XML doc
$dom->appendChild($gendetails);
$tempdetails = $dom->createComment('label details for '.$tname.' template');
// Put this comment at the Root of the XML doc
$dom->appendChild($tempdetails);

$root1 = $dom->createElement("PaymentOne");
$dom->appendChild($root1);
$dom->formatOutput=true;

$root = $dom->createElement("Home");
$dom->appendChild($root);
$dom->formatOutput=true;
$root1->appendChild( $root );
foreach( $homeallcaptions as $labels ){			
	$name = $dom->createElement( "homelabel" );
	
	// create attribute node
	$attrb = $dom->createAttribute("for");
	$name->appendChild($attrb);
		
	// create attribute value node
	$value = $dom->createTextNode($labels['p1tc_title']);
	$attrb->appendChild($value);
	
	$name->appendChild(
		$dom->createTextNode( $labels['p1tc_value'] )
	);	
	$root->appendChild( $name );
}

$root = $dom->createElement("Mobile");
$dom->appendChild($root);
$dom->formatOutput=true;
$root1->appendChild( $root );
foreach( $moballcaptions as $mlabels ){			
	$name = $dom->createElement( "mobilelabel" );
	
	// create attribute node
	$attrb = $dom->createAttribute("for");
	$name->appendChild($attrb);
		
	// create attribute value node
	$value = $dom->createTextNode($mlabels['p1tc_title']);
	$attrb->appendChild($value);
	
	$name->appendChild(
		$dom->createTextNode( $mlabels['p1tc_value'] )
	);	
	$root->appendChild( $name );
}		
		

$xmlfilename = (FULL_PATH."/data/xml/templates/template_".$_SESSION['template_id']);
$xmlfilename1 = "data/xml/templates/template_".$_SESSION['template_id'];
if (file_exists($xmlfilename)) { 
	// save tree to file
	$fl2 = $xmlfilename."/labels.xml";
    chmod($fl2, 0777);
	$dom->save($xmlfilename."/labels.xml");
	//chmod($xmlfilename."/labels.xml", 0777);
}else{
	mkdir($xmlfilename, 0777);
	chmod($xmlfilename,  0777);
	// save tree to file
	$dom->save($xmlfilename."/labels.xml");
	chmod($xmlfilename."/labels.xml", 0777);
}
$file_path["tablename"] = "tbl_templates";
$file_path["fieldname"] = "p1te_css = '".$cssfilename1."/screen.css', p1te_xml = '".$xmlfilename1."/labels.xml'";			
$file_path["whereCon"] = 'p1te_id ='.$_SESSION['template_id'];
$filepath_update =$templates_obj->update_styles($file_path);


	$args_smart["whereCon"]="p1ad_id = 1 and p1me_id = 0  and p1te_stflag = 1 and p1te_device_flag =2 order by p1te_id asc";
	$args_smart["start_lim"] = 0;
	$args_smart["lim"] = 1;
	$ref_smart = $templates_obj->get_all_templatelist($args_smart,$debug);
	$sp_tid=$ref_smart[0]['p1te_id'];
	
	$edit["tablename"] = "tbl_merchant_services";
	$edit["fieldname"] = "p1te_spid =$sp_tid";
	$edit["whereCon"] = "p1ms_id =2235";
	$temp_update =$templates_obj->update_styles($edit,$debug);




?> 
