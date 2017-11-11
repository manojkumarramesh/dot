<?php

//-------------------------------------------------------------------------------------------------------------------
// File name   : template-regenerate-xml.php
// Description : File for UI templates file uploading functionality
//
// copyright(c), Inside Right, 2010-2011, all rights reserved.
//
// Author: Dot Com Infoway Ltd
// Created date : 05-09-2011
// Modified date: 14-12-2011
// -----------------------------------------------------------------------------------------------------------------

/*----- Include Files -----*/
include_once('../includes/configs/init.php');

/*----- Instantiate the class -----*/
$templates_obj = new templates();
$template_func_obj = new TemplateFunctions();

function generate_label_xml($tid, $filename, $country_code, $language_code, $colg_id)
{
    global $templates_obj;

    $args["tablename"]="tbl_template_captions";
    $args["whereCon"]='p1te_id ='.$tid.' and p1tc_type =1 and p1colg_id = '.$colg_id.' order by  p1tc_id asc';
    $homeallcaptions = $templates_obj->get_all($args);//Get all updated captions details.

    $args1["tablename"]="tbl_template_captions";
    $args1["whereCon"]='p1te_id ='.$tid.' and p1tc_type =2 and p1colg_id = '.$colg_id.' order by  p1tc_id asc';
    $moballcaptions = $templates_obj->get_all($args1);//Get all updated captions details.

    $args2["tablename"]="tbl_template_captions";
    $args2["whereCon"]='p1te_id ='.$tid.' and p1tc_type =3 and p1colg_id = '.$colg_id.' order by  p1tc_id asc';
    $anyophoneallcaptions = $templates_obj->get_all($args2);//Get all updated captions details.

    //Coding for xml file creation for updated labels
    $dom = new DOMDocument("1.0");
    $dom->encoding = "utf-8";

    $gendetails = $dom->createComment('Generated by PaymentOne.com');// Create a comment
    $dom->appendChild($gendetails);// Put this comment at the Root of the XML doc

    $tempdetails = $dom->createComment('label details for paymentone template');
    $dom->appendChild($tempdetails);// Put this comment at the Root of the XML doc

    $root1 = $dom->createElement("PaymentOne");

    $attrb = $dom->createAttribute("country_code");// create attribute node
    $root1->appendChild($attrb);
    $value = $dom->createTextNode($country_code);// create attribute value node
    $attrb->appendChild($value);

    $attrb = $dom->createAttribute("language_code");// create attribute node
    $root1->appendChild($attrb);
    $value = $dom->createTextNode($language_code);// create attribute value node
    $attrb->appendChild($value);

    $dom->appendChild($root1);
    $dom->formatOutput=true;

    //home array
    $root = $dom->createElement("Home");
    $dom->appendChild($root);
    $dom->formatOutput=true;
    $root1->appendChild( $root );
    foreach( $homeallcaptions as $hlabels )
    {
        $name = $dom->createElement( "homelabel" );
        $attrb = $dom->createAttribute("for");// create attribute node
        $name->appendChild($attrb);
        $value = $dom->createTextNode($hlabels['p1tc_title']);// create attribute value node
        $attrb->appendChild($value);
        $name->appendChild(
            $dom->createTextNode( $hlabels['p1tc_value'] )
        );
        $root->appendChild( $name );
    }

    //mobile array
    $root = $dom->createElement("Mobile");
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

    //anyphone array
    $root = $dom->createElement("Anyphone");
    $dom->appendChild($root);
    $dom->formatOutput=true;
    $root1->appendChild( $root );
    foreach( $anyophoneallcaptions as $anylabels )
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

    $dom->save($filename);// save tree to file
}

function regenerate_xml()
{
    global $templates_obj;

    $args["tablename"] = "tbl_country";
    $args["fieldname"] = "p1co_id, p1co_code";
    $args["whereCon"] = "p1co_stflag =1";
    $debug = array('file'=>'template-regenerate-xml.php', 'line'=>'p1_country_lists');
    $p1_country_result = $templates_obj->get_component($args, $debug);/*p1_country_lists*/

    $args["tablename"] = "tbl_languages";
    $args["fieldname"] = "p1lg_id, p1lg_code";
    $args["whereCon"] = "p1lg_stflag =1";
    $debug = array('file'=>'template-regenerate-xml.php', 'line'=>'p1_language_lists');
    $p1_language_result = $templates_obj->get_component($args, $debug);/*p1_language_lists*/

    $args["tablename"] = "tbl_country_language_reference";
    $args["fieldname"] = "p1colg_id, p1co_id, p1lg_id";
    $args["whereCon"] = "p1colg_stflag =1";
    $debug = array('file'=>'template-regenerate-xml.php', 'line'=>'fetch_country_language_ref');
    $result = $templates_obj->get_component($args, $debug);/*fetch_country_language_ref*/

    $fetch["tablename"] = "tbl_templates";
    $fetch["fieldname"] = "p1te_id";
    $fetch["whereCon"] = "p1te_stflag = 1";
    $debug = array('file'=>'template-regenerate-xml.php', 'line'=>'template_lists');
    $templates = $templates_obj->get_component($fetch, $debug);/*template_lists*/

    $template_path = FULL_PATH."data/xml/templates/template_";

    $count = 1;
    for($i=0;$i<count($templates);$i++)
    {
        $template_id = $templates[$i]['p1te_id'];
        foreach($result as $key=> $value)
        {
            foreach($p1_country_result as $key1=> $value1)
            {
                if($value1['p1co_id'] == $value['p1co_id'])
                {
                    $country_code = $value1['p1co_code'];
                }
            }
            foreach($p1_language_result as $key2=> $value2)
            {
                if($value2['p1lg_id'] == $value['p1lg_id'])
                {
                    $language_code = $value2['p1lg_code'];
                }
            }
            $new_template_path = $template_path.$template_id.'/'.$country_code;
            if(file_exists($new_template_path))
            {
                //echo $new_template_path.'/labels_'.$language_code.".xml"."<br>";
                if(file_exists($new_template_path.'/labels_'.$language_code.".xml"))
                {
                    //chmod($new_template_path.'/labels_'.$language_code.".xml", 0777);
                    generate_label_xml($template_id, $new_template_path.'/labels_'.$language_code.".xml", $country_code, $language_code, $value['p1colg_id']);
                    //chmod($new_template_path.'/labels_'.$language_code.".xml", 0777);
                }
                else
                {
                    fopen($new_template_path.'/labels_'.$language_code.".xml", "w");
                    chmod($new_template_path.'/labels_'.$language_code.".xml", 0777);
                    generate_label_xml($template_id, $new_template_path.'/labels_'.$language_code.".xml", $country_code, $language_code, $value['p1colg_id']);
                    chmod($new_template_path.'/labels_'.$language_code.".xml", 0777);
                }
            }
            else
            {
                $new_template_path = $template_path.$template_id.'/'.$country_code;
                mkdir($new_template_path, 0777);
                chmod($new_template_path, 0777);
                //echo $new_template_path.'/labels_'.$language_code.".xml"."<br>";
                if(file_exists($new_template_path.'/labels_'.$language_code.".xml"))
                {
                    chmod($new_template_path.'/labels_'.$language_code.".xml", 0777);
                    generate_label_xml($template_id, $new_template_path.'/labels_'.$language_code.".xml", $country_code, $language_code, $value['p1colg_id']);
                    chmod($new_template_path.'/labels_'.$language_code.".xml", 0777);
                }
                else
                {
                    fopen($new_template_path.'/labels_'.$language_code.".xml", "w");
                    chmod($new_template_path.'/labels_'.$language_code.".xml", 0777);
                    generate_label_xml($template_id, $new_template_path.'/labels_'.$language_code.".xml", $country_code, $language_code, $value['p1colg_id']);
                    chmod($new_template_path.'/labels_'.$language_code.".xml", 0777);
                }
            }
        }
        //echo "---------------------------------"."<br/>";
        $count++;
        if($count == 100)
        {
            sleep(5);
            $count = 1;
        }
    }
}

regenerate_xml();

echo "<br/>";
echo "XML GENERATED SUCCESSFULLY";

//unlink(FULL_PATH."admin/template-regenerate-xml.php");

?>