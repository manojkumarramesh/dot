<?php
//-------------------------------------------------------------------------------------------------------------------
// File name   : copypage.php
// Description : File to handle newly create UI template - style and labels information
//
// © 2010-2011 PaymentOne Corportation. All rights reserved.
//
// Created date : 22-02-2010
// Modified date: 28-11-2011
// ------------------------------------------------------------------------------------------------------------------

/*----- To fetch the last inserted templare id  start-----*/
$get_maxid["tablename"] = "tbl_templates";
$get_maxid["fieldname"] = "max(p1te_id) as maxid";
$get_maxid["whereCon"]='1';
$maxid = $templates_obj->get_maxid($get_maxid);
/*----- To fetch the last inserted templare id  end-----*/

if($maxid[0]['maxid'] > 0)
{
    $_SESSION['template_id'] = $maxid[0]['maxid'];
}

/*-----update the tbl_merchant_services if the template was created for a service start-----*/
if($_SESSION['d_type']=="smart")
{
    $field_te = "p1te_spid";
}
else
{
    $field_te = "p1te_id";
}

if($_SESSION['sess_ser_id'] != '')
{
    $apply_service["tablename"] = "tbl_merchant_services";
    $apply_service["fieldname"] = "p1ms_updated=now(),".$field_te." = ".$_SESSION['template_id'];
    $apply_service["whereCon"] = "p1ms_id =".$_SESSION['sess_ser_id'];
    $temp_update =$templates_obj->update_styles($apply_service);
}
else if($sid != '')
{
    $apply_service["tablename"] = "tbl_merchant_services";
    $apply_service["fieldname"] = "p1ms_updated=now(),".$field_te." = ".$_SESSION['template_id'];
    $apply_service["whereCon"] = "p1ms_id =".$sid;
    $temp_update =$templates_obj->update_styles($apply_service);
}
/*-----update the tbl_merchant_services if the template was created for a service end-----*/

function escape_string($str)
{
    $search = array("\\","\0","\n","\r","\x1a","'",'"');
    $replace = array("\\\\","\\0","\\n","\\r","\Z","''",'\"');
    return str_replace($search,$replace,$str);
}

/*----Fetch lables values from the selected template -----*/
$copy_caps["tablename"] = "tbl_template_captions";
$copy_caps["fieldname"] = "p1tc_title, p1tc_value, p1tc_type, p1colg_id";
$copy_caps["whereCon"]= "p1te_id =".$ref_id." order by  p1tc_id asc";
$template_caption = $templates_obj->get_component($copy_caps);

/*----- Update the  new template with labels values fetched-----*/
$count = 1;
foreach($template_caption as $fc)
{
    $insert_caption["tablename"] = "tbl_template_captions";
    $insert_caption["fieldname"] = "p1te_id, p1tc_title, p1tc_value, p1tc_type, p1colg_id, p1tc_stflag";
    $insert_caption["fieldval"] = "'".$_SESSION['template_id']."', '".$fc['p1tc_title']."', '".escape_string($fc['p1tc_value'])."', '".$fc['p1tc_type']."', '".$fc['p1colg_id']."', 1";
    $insertcaption_arr = $templates_obj->template_insert($insert_caption);

    $count++;
    if($count == 100)
    {
        sleep(2);
        $count = 1;
    }
}

/*----Fetch style values from the selected template -----*/
$copy_vars["tablename"] = "tbl_template_vars";
$copy_vars["fieldname"] = "p1tv_id, p1tv_name, p1tv_value";
$copy_vars["whereCon"]= "p1te_id =".$ref_id." order by p1tv_id asc";
$template_vars = $templates_obj->get_component($copy_vars);
foreach($template_vars as $key => $tv)
{
    $copy_attr["tablename"] = "tbl_template_attribs";
    $copy_attr["fieldname"] = "p1ta_title, p1ta_value";
    $copy_attr["whereCon"]= "p1tv_id =".$tv['p1tv_id']." order by  p1ta_id asc";
    $template_vars[$key]['attr'] = $templates_obj->get_component($copy_attr);
}

/*----- Update the  new template with style values fetched-----*/
foreach($template_vars as $tv)
{
    $insert_vars["tablename"] = "tbl_template_vars";
    $insert_vars["fieldname"] = "p1te_id, p1tv_name, p1tv_value, p1tv_stflag, p1tv_added";	
    $insert_vars["fieldval"] = "'".$_SESSION['template_id']."', '".$tv['p1tv_name']."', '".addslashes($tv['p1tv_value'])."', 1, now()";	
    $insertvars_arr = $templates_obj->template_insert($insert_vars);
    if($insertvars_arr == 1)
    {
        //Code getting maxid for new template variables.
        $varmax["tablename"] = "tbl_template_vars";
        $varmax["fieldname"] = "max(p1tv_id) as varmaxid";
        $varmax["whereCon"]='1';
        $varmax = $templates_obj->get_maxid($varmax);
        if(empty($varmax[0]['varmaxid'])) $varmax[0]['varmaxid'] = $i+1;
        foreach($tv['attr'] as $ta)
        {
            $insert_attr["tablename"] = "tbl_template_attribs";
            $insert_attr["fieldname"] = "p1tv_id, p1ta_title, p1ta_value, p1ta_stflag";	
            $insert_attr["fieldval"] = "'".$varmax[0]['varmaxid']."', '".$ta['p1ta_title']."', '".$ta['p1ta_value']."', 1";	
            $insertattr_arr = $templates_obj->template_insert($insert_attr);
        }
    }
}
/*----Fetch font style values from the selected template -----*/

$copy_fld["tablename"] = "tbl_form_fields";
$copy_fld["fieldname"] = "p1ft_id, p1ff_name, p1ff_caption";
$copy_fld["whereCon"]= "p1te_id =".$ref_id." order by p1ff_id asc";
$form_fiels = $templates_obj->get_component($copy_fld);

/*----- Update the  new template with font style values fetched-----*/
foreach($form_fiels as $ff)
{
    $insert_formfld["tablename"] = "tbl_form_fields";
    $insert_formfld["fieldname"] = "p1te_id, p1ft_id, p1ff_name, p1ff_fieldid, p1ff_caption, p1ff_stflag, p1ff_added";	
    $insert_formfld["fieldval"] = "'".$_SESSION['template_id']."', '".$ff['p1ft_id']."', '".$ff['p1ff_name']."', '".$ff['p1ff_name']."', '".$ff['p1ff_caption']."', 1, now()";
    $insertformfld_arr = $templates_obj->template_insert($insert_formfld);
}

/*----- code to generate content to generate css file starts-----*/
$get_allvars["tablename"] = "tbl_template_vars";
$get_allvars["whereCon"] = "p1te_id ='".$_SESSION['template_id']."'"." order by  p1tv_id asc";	
$get_allvars_arr = $templates_obj->get_all($get_allvars);
foreach( $get_allvars_arr as $key => $value)
{
    $valu .= $value['p1tv_value'];
    $valu .= " { ";
    $get_allclass["tablename"] = "tbl_template_attribs";
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
/*----- code to generate content to generate css file ends-----*/

/*----- code for creating and writing the contents to merchant.css file starts-----*/	
$cssfilename = ("../data/styles/templates/template_".$_SESSION['template_id']);
$cssfilename1="data/styles/templates/template_".$_SESSION['template_id'];
if (file_exists($cssfilename))
{
    $fh = fopen($cssfilename."/merchant.css", 'w');
    chmod($cssfilename."/merchant.css", 0777);
    fwrite($fh, $valu);
}
else
{
    mkdir($cssfilename, 0777);
    chmod($cssfilename, 0777);
    $fh = fopen($cssfilename."/merchant.css", 'w');
    fwrite($fh, $valu);
}
/*----- code for creating and writing the contents to merchant.css file starts-----*/

$args["tablename"] = "tbl_template_captions";
$args["fieldname"] = "p1colg_id";
$args["whereCon"] = "p1te_id = ".$_SESSION['template_id'].' group by p1colg_id';
$debug = array('file'=>'copypage.php', 'line'=>'this_template_co_lg');
$this_template_co_lg_result = $templates_obj->get_component($args, $debug);/*this_template_co_lg*/

for($i=0; $i<count($this_template_co_lg_result); $i++)
{
    $colg_id = $this_template_co_lg_result[$i]['p1colg_id'];

    $args["tablename"] = "tbl_country_language_reference";
    $args["fieldname"] = "p1colg_id, p1co_id, p1lg_id";
    $args["whereCon"] = "p1colg_stflag = 1 and p1colg_id = ".$colg_id;
    $debug = array('file'=>'copypage.php', 'line'=>'fetch_country_language_ref');
    $result = $templates_obj->get_component($args, $debug);/*fetch_country_language_ref*/
    $country_id = $result[0]['p1co_id'];
    $language_id = $result[0]['p1lg_id'];

    $args["tablename"] = "tbl_country";
    $args["fieldname"] = "p1co_id, p1co_code";
    $args["whereCon"] = "p1co_stflag = 1 and p1co_id = ".$country_id;
    $debug = array('file'=>'copypage.php', 'line'=>'p1_country_lists');
    $p1_country_result = $templates_obj->get_component($args, $debug);/*p1_country_lists*/
    $country_code = $p1_country_result[0]['p1co_code'];

    $args["tablename"] = "tbl_languages";
    $args["fieldname"] = "p1lg_id, p1lg_code";
    $args["whereCon"] = "p1lg_stflag =1 and p1lg_id = ".$language_id;
    $debug = array('file'=>'copypage.php', 'line'=>'p1_language_lists');
    $p1_language_result = $templates_obj->get_component($args, $debug);/*p1_language_lists*/
    $language_code = $p1_language_result[0]['p1lg_code'];

    //Get all home labels
    $args["tablename"]="tbl_template_captions";
    $args["whereCon"]='p1te_id ='.$_SESSION['template_id'].' and p1tc_type = 1 and p1colg_id = '.$colg_id.' order by  p1tc_id asc';
    $homeallcaptions = $templates_obj->get_all($args);

    //Get all mobile labels
    $args1["tablename"]="tbl_template_captions";
    $args1["whereCon"]='p1te_id ='.$_SESSION['template_id'].' and p1tc_type = 2 and p1colg_id = '.$colg_id.' order by  p1tc_id asc';
    $moballcaptions = $templates_obj->get_all($args1);

    //Get all any phone labels
    $args2["tablename"]="tbl_template_captions";
    $args2["whereCon"]='p1te_id ='.$_SESSION['template_id'].' and p1tc_type = 3 and p1colg_id = '.$colg_id.' order by  p1tc_id asc';
    $anyphonecaptions = $templates_obj->get_all($args2);

    $dom = new DOMDocument("1.0");//Coding for xml file creation
    $dom->encoding = "utf-8";
    $gendetails = $dom->createComment('Generated by PaymentOne.com');// Create a comment
    $dom->appendChild($gendetails);
    $tempdetails = $dom->createComment('label details for '.$currenttemplate->get_Name().' template');//
    $dom->appendChild($tempdetails);

    $root1 = $dom->createElement("PaymentOne");
    $attrb = $dom->createAttribute("country_code");// create country node
    $root1->appendChild($attrb);
    $value = $dom->createTextNode($country_code);
    $attrb->appendChild($value);
    $attrb = $dom->createAttribute("language_code");// create language node
    $root1->appendChild($attrb);
    $value = $dom->createTextNode($language_code);
    $attrb->appendChild($value);
    $dom->appendChild($root1);
    $dom->formatOutput=true;

    $root = $dom->createElement("Home");//home array
    $dom->appendChild($root);
    $dom->formatOutput=true;
    $root1->appendChild( $root );
    foreach( $homeallcaptions as $labels )
    {
        $name = $dom->createElement( "homelabel" );
        $attrb = $dom->createAttribute("for");// create attribute node
        $name->appendChild($attrb);
        $value = $dom->createTextNode($labels['p1tc_title']);// create attribute value node
        $attrb->appendChild($value);
        $name->appendChild(
            $dom->createTextNode( $labels['p1tc_value'] )
        );
        $root->appendChild( $name );
    }

    $root = $dom->createElement("Mobile");//mobile array
    $dom->appendChild($root);
    $dom->formatOutput=true;
    $root1->appendChild( $root );
    foreach( $moballcaptions as $mlabels )
    {
        $name = $dom->createElement( "mobilelabel" );
        $attrb = $dom->createAttribute("for");// create attribute node
        $name->appendChild($attrb);
        $value = $dom->createTextNode($mlabels['p1tc_title']);// create attribute value node
        $attrb->appendChild($value);
        $name->appendChild(
            $dom->createTextNode( $mlabels['p1tc_value'] )
        );
        $root->appendChild( $name );
    }

    $root = $dom->createElement("Anyphone");//anyphone array
    $dom->appendChild($root);
    $dom->formatOutput=true;
    $root1->appendChild( $root );
    foreach( $anyphonecaptions as $anylabels )
    {
        $name = $dom->createElement( "anyphonelabel" );
        $attrb = $dom->createAttribute("for");// create attribute node
        $name->appendChild($attrb);
        $value = $dom->createTextNode($anylabels['p1tc_title']);// create attribute value node
        $attrb->appendChild($value);
        $name->appendChild(
            $dom->createTextNode( $anylabels['p1tc_value'] )
        );
        $root->appendChild( $name );
    }

    $template_path = "../data/xml/templates/template_".$_SESSION['template_id']."/";
    if(file_exists($template_path))
    {
        $country_path = $template_path.$country_code."/";
        if(file_exists($country_path))
        {
            chmod($country_path,  0777);
            $file_path = $country_path.'labels_'.$language_code.".xml";
            fopen($file_path, "w");
            chmod($file_path, 0777);
            $dom->save($file_path);// save tree to file
            chmod($file_path, 0777);
        }
        else
        {
            mkdir($country_path, 0777);
            chmod($country_path,  0777);
            $file_path = $country_path.'labels_'.$language_code.".xml";
            fopen($file_path, "w");
            chmod($file_path, 0777);
            $dom->save($file_path);// save tree to file
            chmod($file_path, 0777);
        }
    }
    else
    {
        mkdir($template_path, 0777);
        chmod($template_path,  0777);
        $country_path = $template_path.$country_code."/";
        mkdir($country_path, 0777);
        chmod($country_path,  0777);
        $file_path = $country_path.'labels_'.$language_code.".xml";
        fopen($file_path, "w");
        chmod($file_path, 0777);
        $dom->save($file_path);// save tree to file
        chmod($file_path, 0777);
    }
}

$cssfilepath = "data/styles/templates/template_".$_SESSION['template_id'];
$xmlfilepath = "data/xml/templates/template_".$_SESSION['template_id']."/";

$temp_file_path["tablename"] = "tbl_templates";
$temp_file_path["fieldname"] = "p1te_css = '".$cssfilepath."/merchant.css', p1te_xml = '".$xmlfilepath."'";
$temp_file_path["whereCon"] = 'p1te_id = '.$_SESSION['template_id'];
$debug = array('file'=>'copypage.php', 'line'=>'inserttemppaths');
$file_path_updates =$templates_obj->update_styles($temp_file_path);/*inserttemppaths*/

?>