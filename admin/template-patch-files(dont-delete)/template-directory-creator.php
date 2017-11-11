<?php

//-------------------------------------------------------------------------------------------------------------------
// File name   : template-directory-creator.php
// Description : File for UI templates file uploading functionality
//
// copyright(c), Inside Right, 2010-2011, all rights reserved.
//
// Author: Dot Com Infoway Ltd
// Created date : 31-08-2011
// Modified date: 14-12-2011
// -----------------------------------------------------------------------------------------------------------------

/*----- Include Files -----*/
include_once('../includes/configs/init.php');

/*----- Instantiate the class -----*/
$templates_obj = new templates();
$debug_obj = new Debug();
$template_func_obj = new TemplateFunctions();
$common_obj = new Common();

$args["tablename"] = "tbl_templates";
$args["fieldname"] = "p1te_id";
$args["whereCon"] = "p1te_stflag =1";
$debug = array('file'=>'template-directory-creator.php', 'line'=>'template_lists');
$template_result = $templates_obj->get_component($args, $debug);/*template_lists*/

$debug = array('file'=>'template-directory-creator.php', 'line'=>'p1_country_lists');
$p1_country_result = $template_func_obj->fetch_country_list($debug);/*p1_country_lists*/

$template_path = FULL_PATH."data/xml/templates/template_";

$debug = array('file'=>'template-directory-creator.php', 'line'=>'cms_response');
$response =  $common_obj ->CmsCurlGet($glb_cms_country_url,$_SESSION['admin_email'],$_SESSION['admin_cms_pwd'],$query_string);/*cms_response*/

$xml = simplexml_load_string($response);

foreach($xml->elements->country as $key=> $value)
{
    foreach($value->attributes() as $key1=> $value1)
    {
        //echo $key1.' '.$value1."<br/>";
        if($key1 == 'country_code')
        {
            foreach($p1_country_result as $value2)
            {
                if($value1 == $value2['p1co_code'])
                {
                    $country_lists[] = $value2['p1co_code'];
                }
            }
        }
    }
}

foreach($template_result as $value3)
{
    $template_id = $value3['p1te_id'];
    foreach($country_lists as $value4)
    {
        $new_template_path = $template_path.$template_id.'/'.$value4;
        if(file_exists($new_template_path))
        {
            //echo "old ".$new_template_path."<br/>";
            chmod($new_template_path, 0777);
        }
        else
        {
            //echo "new ".$new_template_path."<br/>";
            mkdir($new_template_path, 0777);
            chmod($new_template_path, 0777);
        }
    }
}

echo "<br/>";
echo "Directory created for existing templates";

//unlink(FULL_PATH."admin/template-directory-creator.php");

?>