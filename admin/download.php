<?php
//-------------------------------------------------------------------------------------------------------------------
// File name   : download.php
// Description : file to handle UI Template download functionality
//
// © 2010-2011 PaymentOne Corportation. All rights reserved.
//
// Created date : 08-03-2010
// Modified date: 28-11-2011
// ------------------------------------------------------------------------------------------------------------------

/*----- Include Files -----*/
include_once('../includes/configs/init.php');
include('../includes/configs/sessionadminc.php');

/*----- Instantiate the class ----*/
$templates_obj = new templates();

$args["tablename"]="tbl_templates";
$args["whereCon"]='p1te_id ='.$_GET['tid'];
$template_arr = $templates_obj->get_all($args); //get all the information from particular template
$currenttemplate = new templates($template_arr[0]);
$smarty->assign('currenttemplate',$currenttemplate);

$host  = $_SERVER['HTTP_HOST']; //Block for download option.

if($_GET['file'] == 'css')  //to download ui template css file
{
    $filepath = $currenttemplate->get_Field("p1te_css");
    if($filepath == '') //if css file path is empty
    {
        $getallvars["tablename"] = "tbl_template_vars";
        $getallvars["whereCon"] = "p1te_id ='".$_GET['tid']."'"." order by  p1tv_id asc";
        $getallvars_arr = $templates_obj->get_all($getallvars);
        $valu='';
        foreach( $getallvars_arr as $key => $value){
            $valu .= $value['p1tv_value'];
            $valu .= " { ";
            $getallclass["tablename"] = "tbl_template_attribs";
            $getallclass["whereCon"] = "'".$value['p1tv_id']."' = p1tv_id order by  p1ta_id asc";
            $getallclass_arr = $templates_obj->get_all($getallclass);
            foreach( $getallclass_arr as $key => $style){
                $valu .= $style['p1ta_title'];
                $valu .= ": ";
                $valu .= $style['p1ta_value'];
                $valu .= "; ";
            }
            $valu .= " }\n";
        }
        $cssfilename = ("../data/styles/templates/template_".$_GET['tid']);
        $cssfilename1="data/styles/templates/template_".$_GET['tid'];
        if (file_exists($cssfilename)) {    //if file exists css file overwrite it
            $fh = fopen($cssfilename."/merchant.css", 'w');
            chmod($cssfilename."/merchant.css", 0777);
            fwrite($fh, $valu);
        }
        else {  //to create new css file
            mkdir($cssfilename, 0777);
            chmod($cssfilename, 0777);
            $fh = fopen($cssfilename."/merchant.css", 'w');
            fwrite($fh, $valu);
        }
        $filepath1["tablename"] = "tbl_templates";
        $filepath1["fieldname"] = "p1te_css = '".$cssfilename1."/merchant.css'";
        $filepath1["whereCon"] = 'p1te_id ='.$_GET['tid'];
        $filepath1_update =$templates_obj->update_styles($filepath1);
    }
    $getstyle1["tablename"] = "tbl_templates";
    $getstyle1["whereCon"] = "p1te_id ='".$_GET['tid']."'";
    $getstyle1_arr = $templates_obj->get_all($getstyle1);
    $filepath1 = $getstyle1_arr[0]['p1te_css'];
    $path= FULL_PATH.$filepath;
    ob_start();
    header ("Cache-Control: must-revalidate, pre-check=0, post-check=0");
    header ("Content-Type: application/binary");
    header ("Content-Length: ".filesize($path));
    header ("Content-Disposition: attachment; filename=merchant.css");
    readfile($path);
}
else    //to download ui templates xml file
{
    $countrycode = $_REQUEST['country_code'];
    $languagecode = $_REQUEST['language_code'];

    $filepath = $currenttemplate->get_Field("p1te_xml");
    if($filepath == '') //to xml  file path is empty
    {
        $cpas["tablename"]="tbl_template_captions";
        $cpas["whereCon"]='p1te_id ='.$_GET['tid']." order by  p1tc_id asc";
        $allcpas = $templates_obj->get_all($cpas);
        $dom = new DOMDocument("1.0");  //Coding for xml file creation
        $dom->encoding = "utf-8";
        $gendetails = $dom->createComment('Generated by PaymentOne.com');   // Create a comment
        $dom->appendChild($gendetails);
        $tempdetails = $dom->createComment('label details for PaymentOne template');
        $dom->appendChild($tempdetails);
        $root = $dom->createElement("PaymentOnelabels");
        $dom->appendChild($root);
        $dom->formatOutput=true;
        foreach( $allcpas as $labels )
        {
            $name = $dom->createElement( "label" );
            $attrb = $dom->createAttribute("for");
            $name->appendChild($attrb);
            $value = $dom->createTextNode($labels['p1tc_title']);
            $attrb->appendChild($value);
            $name->appendChild(
                $dom->createTextNode( $labels['p1tc_value'] )
            );
            $root->appendChild( $name );
        }
        $xmlfilename = ("../data/xml/templates/template_".$_GET['tid']);
        $xmlfilename1 = "data/xml/templates/template_".$_GET['tid'];
        if(file_exists($xmlfilename)) {     //to check if exists xml file
            $dom->save($xmlfilename."/labels.xml"); // save tree to file
            chmod($xmlfilename."/labels.xml", 0777);
        }
        else {
            mkdir($xmlfilename, 0777);
            chmod($xmlfilename,  0777);
            $dom->save($xmlfilename."/labels.xml"); // save tree to file
            chmod($xmlfilename."/labels.xml", 0777);
        }
        $filepath["tablename"] = "tbl_templates";
        $filepath["fieldname"] = "p1te_xml = '".$xmlfilename1."/labels.xml'";
        $filepath["whereCon"] = 'p1te_id ='.$_GET['tid'];
        $filepath_update =$templates_obj->update_styles($filepath);
    }

    $getstyle["tablename"] = "tbl_templates";
    $getstyle["whereCon"] = "p1te_id ='".$_GET['tid']."'";
    $getstyle_arr = $templates_obj->get_all($getstyle);
    $filepath = $getstyle_arr[0]['p1te_xml'];
    $filename = 'labels_'.$languagecode.'.xml';
    $path = FULL_PATH.$filepath.'/'.$countrycode.'/'.$filename;

    ob_start();  //so only the data from the headers is sent
    header ("Cache-Control: must-revalidate, pre-check=0, post-check=0");
    header ("Content-Type: application/binary");
    header ("Content-Length: " . filesize($path));
    header ("Content-Disposition: attachment; filename=$filename");
    readfile($path);
}

?>