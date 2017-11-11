<?php
//-------------------------------------------------------------------------------------------------------------------
// File name   : defpage.php
// Description : File to handle newly create UI template - style and labels information
//
// © 2010-2011 PaymentOne Corportation. All rights reserved.
//
// Created date : 22-02-2010
// Modified date: 28-11-2011
// ------------------------------------------------------------------------------------------------------------------

/*----- Include Files -----*/

/*----- To fetch the last inserted templare id  start-----*/
$get_maxid["tablename"] = "tbl_templates";
$get_maxid["fieldname"] = "max(p1te_id) as maxid";
$get_maxid["whereCon"]='1';
$maxid = $templates_obj->get_maxid($get_maxid);		
/*----- To fetch the last inserted templare id  end-----*/

if($maxid[0]['maxid'] > 0){
    $_SESSION['template_id'] = $maxid[0]['maxid'];
}

/*-----update the tbl_merchant_services if the template was created for a service start-----*/
if($_SESSION['sess_ser_id'] != ''){
    $apply_service["tablename"] = "tbl_merchant_services";
    $apply_service["fieldname"] = "p1te_id = ".$_SESSION['template_id'];
    $apply_service["whereCon"] = "p1ms_id =".$_SESSION['sess_ser_id'];
    $temp_update =$templates_obj->update_styles($apply_service);
}else if ($_REQUEST['sid'] != ''){
    $apply_service["tablename"] = "tbl_merchant_services";
    $apply_service["fieldname"] = "p1te_id = ".$_SESSION['template_id'];
    $apply_service["whereCon"] = "p1ms_id =".$_REQUEST['sid'];
    $temp_update =$templates_obj->update_styles($apply_service);
}
/*-----update the tbl_merchant_services if the template was created for a service end-----*/


$ref_id=61;

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
		

		
/*----- Array containing default styles start -----*/
// $template_vars = array(
// 		array("Background", "body", array(array("font-family","Arial, Helvetica, sans-serif, sans-serif"),array("color","#111111"),array("background-color","#FFFFFF"))),
// 		array("Title", "#title p", array(array("background-color","#F5F5F5"))),
// 		array("Items detail", ".txn-detail", array(array("color","#000000"))),		
// 		array("Main Content", ".box", array(array("color","#000000"))),
// 		array("Loading", ".loading .blurb", array(array("background-color","#FFFFFF"))),
// 		array("Timer", "#timer .notice", array(array("background-color","#F4F4F4"))),
// 		array("Success Message", ".success, .success a", array(array("background-color","#E6EFC2"), array("color","#264409"))),
// 		array("Notice Message", ".notice, .notice a", array(array("background-color","#FFF6BF"),array("color","#514721"))),
// 		array("Error Message", ".error,  .error a", array(array("background-color","#FBE3E4"),array("color","#8a1f11"))),
// 		array("Terms & Conditions", "fieldset.inline div.terms-box", array(array("background-color","#FFFFFF"),array("font-size","10px")))
// 		);
$template_vars = array(
		array("Body Color", "body", array(array("color","#222222"),array("background-color","#ffffff"))),
		array("Header Background Color", "#body-bg", array(array("background","#ffffff"))),
		array("Header Bottom Border", "header", array(array("border-bottom-color","#CCCCCC"))),
		array("Pay Type Border", "#info img", array(array("border-left-color","#CCCCCC"))),
		array("Selected Item", "#item", array(array("border-top-color","#CCCCCC"),array("border-bottom-color","#CCCCCC"))),
		array("IVR Timer Box", "#timer .notice", array(array("background-color","#f4f4f4"),array("border-color","#ededed"))),
		array("IVR Timer Clock","#timer .clock input[type=text]", array(array("background-color","#f4f4f4"),array("border-color","#dddddd"),array("color","#333333"))),
		array("Footer", "footer .footer",array(array("background-color","#ffffff"),array("border-top-color","#CCCCCC"))),
		//array("Form Font Color", "section, fieldset ",array(array("color","#111111"))),
		array("Form Title", "legend",array(array("color","#111111"))),
		array("TOS Title", "form h3", array(array("color","#111111"))),
		array("Form Label", "label",array(array("color","#111111"))),
		array("Form Textbox & Textarea", "input, textarea, select",array(array("border-color","#cccccc"),array("background","#FFFFFF"),array("color","#111111"))),
		array("Form Textbox & Textarea (Focus)", "input:focus, textarea:focus, select:focus", array(array("border-color","#058CF5"))),
		array("Inactive Button", "input.button, .button", array(array("color","#111111"))),
		array("Active Button", "button.active", array(array("color","#FFFFFF"),array("background","#FFA84C"),array("border-color","#EE9A00"))),
		array("Checkbox & Radio Button Labels", "section.checkradio label,section.yesno label", array(array("color","#111111"),array("background","#FFFFFF"),array("border-color","#cccccc"))),
		array("Radio Button Group", "section.yesno", array(array("border-bottom-color"," #cccccc"))),
		array("Radio Button Group Text", "section.yesno .tos", array(array("color","#111111"))),
		array("Checkbox & Radio Button Selected", "section.checkradio label.checked, section.yesno label.checked", array(array("background","#FFFFFF"))),	
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
	
/************************ This is used to create the default template - (START)  ***********************/
/*
$fiels_types = array (
array ("Drop Down Select", "Single Drop Down box", "select"),
array ("Drop Down Numeric List", "Drop Down Numeric List populate from PHP", "select"),
array ("Color Palet", "Javascript Color Palet", "text")
);

for($j=0; $j<count($fiels_types); $j++){
	$insertfldtyp["tablename"] = "tbl_field_types";
	$insertfldtyp["fieldname"] = "p1ft_name, p1ft_description, p1ft_html_type, p1ft_stflag, p1ft_added";	
	$insertfldtyp["fieldval"] = "'".$fiels_types[$j][0]."', '".$fiels_types[$j][1]."', '".$fiels_types[$j][2]."', 1, now()";	
	$insertfldtyp_arr = $templates_obj->template_insert($insertfldtyp);
}
*/
/************************ This is used to create the default template - (END)  ***********************/			

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
	array(3,"border-bottom-color","border-bottom-color"),
	array(3,"border-top-color","border-top-color"),
	array(3,"border-color","border-color"),
	array(3,"border-left-color","border-left-color"),
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

/************************ This is used to create the default template - (START)  ***********************/
/*	
 $form_attribs = array (
		array (1,
		array ("Arial, Helvetica, sans-serif",
		"Garamond, serif",
		"Georgia, serif",
		"Tahoma, Geneva, sans-serif",
		"Verdana, Geneva, sans-serif"
		)
		),
		array (2,
		array ("normal","italic","oblique")
		),
		array (3,
		array ("normal","bold ")
		),
		array (4,
		array ("none","underline")
		),
		array (5,
		array ("left","right","center","justify")
		),
		array (6,
		array ("text","pointer")
		) 
		);

for($gi=0; $gi<count($form_attribs); $gi++){
	$fat = $form_attribs[$gi];
	for($g=0; $g<count($fat[1]); $g++){
		$insertfrmattr["tablename"] = "tbl_form_attribs";
		$insertfrmattr["fieldname"] = "p1ff_id, p1fa_value, p1fa_display, p1fa_stflag, p1fa_added";	
		$insertfrmattr["fieldval"] = "'".$fat[0]."', '".addslashes($fat[1][$g])."', '".addslashes($fat[1][$g])."', 1, now()";	
		$insertfrmattr_arr = $templates_obj->template_insert($insertfrmattr);
	}
} 
*/
/************************ This is used to create the default template - (END)  ***********************/



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

/*----- code for creating and writing the contents to merchant.css file starts-----*/	
$cssfilename = ("../data/styles/templates/template_".$_SESSION['template_id']);
$cssfilename1="data/styles/templates/template_".$_SESSION['template_id'];
if (file_exists($cssfilename)) { 
	$fh = fopen($cssfilename."/merchant.css", 'w');
    $fl1 = $cssfilename."/merchant.css";
	chmod($fl1, 0777);
	fwrite($fh, $valu);
}else{
	
	mkdir($cssfilename, 0777);	
	chmod($cssfilename, 0777);
	$fh = fopen($cssfilename."/merchant.css", 'w');
	fwrite($fh, $valu);
}
/*----- code for creating and writing the contents to merchant.css file starts-----*/

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
$tempdetails = $dom->createComment('label details for '.$currenttemplate->get_Name().' template');
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
		

$xmlfilename = ("../data/xml/templates/template_".$_SESSION['template_id']);
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
$file_path["fieldname"] = "p1te_css = '".$cssfilename1."/merchant.css', p1te_xml = '".$xmlfilename1."/labels.xml'";			
$file_path["whereCon"] = 'p1te_id ='.$_SESSION['template_id'];
$filepath_update =$templates_obj->update_styles($file_path);

/*
===================================================================================


$template_vars = array(
		array("Content", "body", array(array("color","#111111"),array("background-color","#FFFFFF"))),
		array("Header", "header", array(array("background-color","#F5F5F5"),array("border-bottom-color","#F5F5F5"))),
		array("Item", "#item", array(array("border-top-color","#F5F5F5"),array("border-bottom-color","#F5F5F5"))),
		array("Timer-Noticebox", "#timer .notice", array(array("background-color","#f4f4f4"),array("border-color","#ededed"))),
		array("Timer-Clock", "#timer .clock", array(array("background-color","#f4f4f4"),array("border-color","#ededed"))),
		array("Footer", "footer .footer", array(array("border-top-color","#1b4194"),array("background-color","#fff"))),
		array("Form-Element", "input textarea select",
		 array(array("border-color","#ccc"),array("background","#e9f6fd"))),
		array("Button-inactive", "input.button .button", array(array("color","#111"))),
		array("Button-active", "input.button .button", array(array("color","#FFF"),array("background","#2c539e"),array("border-color","#2c539e"))),
		array("TOS-Checkbox", "section.yesno label", array(array("color","#111"),array("background","#e9f6fd"),array("border-color","#ccc"))),
		array("TOS-Content", "section.yesno .tos", array(array("color","#111"))),
		);

=================================================================================
*/

?>