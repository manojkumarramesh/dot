<?php
//-------------------------------------------------------------------------------------------------------------------
// File name   : previewtemplate.php
// Description : File to handle - Preivew the option and related details.
//
// © 2010-2011 PaymentOne Corportation. All rights reserved.
//
// Created date : 22-02-2010
// Modified date: 27-12-2011
// ------------------------------------------------------------------------------------------------------------------

header("Pragma: no-cache");
header("Cache: no-cahce");

/*----- Include files -----*/
include_once( '../includes/configs/init.php' );
include_once('../includes/configs/sessionadminc.php');

/*----- Global variable declaration -----*/
global $glb_adm_tpl_path;

/*----- Constant Definition -----*/
define("ANYPHONE_AUTHENTICATION_TYPE", 1);
define("HOME_AUTHENTICATION_TYPE", 2);
define("MOBILE_AUTHENTICATION_TYPE", 3);
define("ACTIVE", 1);
define("INACTIVE", 0);

$action = trim($_REQUEST['action']);

$month_array = array('Month', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');//Months array

$modified_date = "28122011";//version used in tpl

/*----- Instantiate the class -----*/
$services_obj = new services();
$templates_obj = new templates();
$template_func_obj = new TemplateFunctions();

function escape_string($str)
{
    $search = array(" ", "?", ":", ",", ".", "(", ")", "[", "]", "/", "&");
    $replace = array("_", "", "", "", "", "", "", "", "", "_", "_");
    return str_replace($search, $replace, $str);
}

if($_REQUEST['sid'] != '')
{
    $merchant_id = $_SESSION['cms_account_id'];
    $service_id = $_REQUEST['sid'];
    $query_string = "?merchant_id=$merchant_id&service_id=$service_id";
}
else
{
    $query_string = "";
}
//echo "Country : ".$glb_cms_country_url.$query_string;

$debug = array('file'=>'previewtemplate.php', 'line'=>'curlget');
$response=$common_obj->CmsCurlGet($glb_cms_country_url,$_SESSION['admin_email'],$_SESSION['admin_cms_pwd'],$query_string,$debug);/*curlget*/

if(!(stristr($response, '<?xml')))// if valid xml is not returned write log
{
    $common_obj->WriteCMSLog("UI Template : Country URL : CMS Response: ".$response, $glb_adm_path."previewtemplate.php");
}
else // fetch the details to display the preview
{
    $xml = simplexml_load_string($response);
    foreach($xml->elements->country as $key=> $value)
    {
        foreach($value->attributes() as $key1=> $value1)
        {
            $cms_country[] = (string) $value1;
        }
    }
}

$args["tablename"] = "tbl_country";
$args["fieldname"] = "p1co_id, p1co_name, p1co_code";
$args["whereCon"] = "p1co_stflag =1 order by p1co_name ASC";
$debug = array('file'=>'previewtemplate.php', 'line'=>'dbcountry');
$p1_db_country = $templates_obj->get_component($args, $debug);/*dbcountry*/
for($i=0; $i<count($p1_db_country); $i++)
{
    for($j=0; $j<count($cms_country); $j++)
    {
        if($cms_country[$j] == $p1_db_country[$i]['p1co_code'])
        {
            $country_lists[] = $p1_db_country[$i];
        }
    }
}
$smarty->assign('countrylist', $country_lists);

if($_REQUEST['country_code'] == "" && $_REQUEST['language_code'] == "" && $_REQUEST['is_official'] == "")
{
    $country_id = 1;
    $country_code = 'US';
    $language_code = 'EN';
    $is_official = 1;
}
else
{
    if($_REQUEST['actions'] == 'change_country')
    {
        $country_id = trim($_REQUEST['country_id']);
        $country_code = trim($_REQUEST['country_code']);
        $arg["tablename"] = "tbl_country_language_reference as ref left join tbl_languages as lang on (ref.p1lg_id = lang.p1lg_id)";
        $arg["fieldname"] = "lang.p1lg_code";
        $arg["whereCon"] = "ref.p1colg_stflag = 1 and ref.p1colg_official = 1 and ref.p1co_id = ".$country_id;
        $debug = array('file'=>'previewtemplate.php', 'line'=>'p1colgresult');
        $p1language_result = $templates_obj->get_component($arg, $debug);/*p1colgresult*/
        $language_code = $p1language_result[0]['p1lg_code'];
        $is_official = 1;
    }
    else
    {
        $country_id = trim($_REQUEST['country_id']);
        $country_code = trim($_REQUEST['country_code']);
        $language_code = trim($_REQUEST['language_code']);
        $is_official = trim($_REQUEST['is_official']);
    }
}

/*---preview template for services -----*/
if($_REQUEST['sid'] != '')
{
    $smarty->assign('current_page', 'service');

    $sid = $_REQUEST['sid'];
    $cond = "p1ms_id = ".$sid;
    $debug = array('file'=>'previewtemplate.php', 'line'=>'count_service');
    $result = $services_obj->get_countlists('tbl_merchant_services', 'p1ms_iflag', $cond, $debug);/*count_service*/
    if($result[0]['p1ms_iflag'] == ACTIVE)              // check if the item flag is active
    {
        $smarty->assign('item_display', ACTIVE);
    }
    else
    {
        $smarty->assign('item_display', INACTIVE);
    }

    if(isset($_REQUEST['product_id']) && $_REQUEST['product_id'] != '') //if product_id exist get the product details
    {
        $product_id = trim($_REQUEST['product_id']);
        $query_string = "/$product_id?country_code=$country_code&language_code=$language_code&start=0&max=50";
    }
    else
    {
        $query_string = "?service_id=$sid&country_code=$country_code&language_code=$language_code&start=0&max=50";
    }
    //echo "CMS : ".$glb_cms_button_product_url.$query_string;

    $debug = array('file'=>'previewtemplate.php', 'line'=>'products');
    $items_xml =  $common_obj ->CmsCurlGet($glb_cms_button_product_url,$_SESSION['admin_email'],$_SESSION['admin_cms_pwd'],$query_string,$debug);/*products*/

    if(!(stristr($items_xml, '<?xml')))     // if valid xml is not returned write log
    {
        $common_obj->WriteCMSLog("Preview template : Product Display: CMS Response: ".$items_xml, $glb_adm_path."previewtemplate.php");
        $products_avail = 'no';
    }
    else // fetch the details to display preview
    {
        $result_array = array();
        $result_items = array();
        $result_xml = simplexml_load_string($items_xml);
        //echo "<pre>";print_r($result_xml);die;

        if(isset($_REQUEST['product_id']))          //if itemid exist get the item details for the selcted service
        {
             foreach($result_xml as $key=> $value)
            {
                if($key == 'description')
                {
                    $item_des = (string) $value->description_literal;
                }
                else if($key == 'price')
                {
                    $item_val = (string) $value->price_amount;
                    $billing_type_code = (string) $value->billing_type_code;
                }
            }
            $smarty->assign('itm_des', $item_des);
            $smarty->assign('itm_value', $item_val);
            $smarty->assign('item_id', trim($_REQUEST['product_id']));
        }
        else
        {
            foreach($result_xml->elements->product as $key => $value)
            {
                $productId = (string) $value->attributes()->product_id;
                unset($value->attributes()->product_id);
                $value = (array) $value ;
                $xml_array = array();
                $xml_array['productid'] = $productId;
                $m = 0;
                foreach($value as $k => $v)
                {
                    if(is_object($v))
                    {
                        $xml_array1 = array();
                        if($k=="description")
                        {
                            $description_id = (string) $v->attributes()->description_id;
                            unset($v->attributes()->description_id);
                            $v1 = (array) $v;
                            $xml_array["description_id"] = $description_id;
                            foreach($v1 as $deskey => $desval)
                            {
                                $xml_array[$deskey] = (string) $desval;
                            }
                        }
                        else if($k == "price")
                        {
                            $price_id = (string) $v->attributes()->price_id;
                            unset($v->attributes()->price_id);
                            $v = (array) $v;
                            foreach($v as $k1 => $v1)
                            {
                                $xml_array1[$m]["price_id"] = $price_id;
                                $xml_array1[$m][$k1] = (string) $v1;
                            }
                            $xml_array["price"] = $xml_array1;
                        }
                        else
                        {
                            $v1 = (string) $v;
                            $xml_array[$k] = $v1;
                        }
                    }
                    else
                    {
                        $xml_array1 = array();
                        if(is_array($v))
                        {
                            if($k == "price")
                            {
                                foreach($v as $k1=>$v1)
                                {
                                    $price_id=(string)$v1->attributes()->price_id;
                                    unset($v1->attributes()->price_id);
                                    $v1=(array)$v1;
                                    $xml_array1[$k1]["price_id"] = $price_id;
                                    foreach($v1 as $prik => $priv)
                                    {
                                        $xml_array1[$k1][$prik] = (string) $priv;
                                    }
                                }
                                $xml_array["price"]=$xml_array1;
                            }
                            else if($k=="description")
                            {
                                foreach($v as $k1=>$v1)
                                {
                                    $description_id=(string)$v1->attributes()->description_id;
                                    unset($v1->attributes()->description_id);
                                    $v1=(array)$v1;
                                    $xml_array1[$k1]["description_id"]=$description_id;
                                    foreach($v1 as $desk => $desv)
                                    {
                                        $xml_array1[$k1][$desk] = (string) $desv;
                                    }
                                }
                                $xml_array["description"] = $xml_array1;
                            }
                            else
                            {
                                $xml_array[$k] = $v;
                            }
                        }
                        else
                        {
                            $xml_array[$k] = (string) $v;
                        }
                    }
                }
                $m++;
                $result_array[] = $xml_array;
            }
            $result_items = $result_array;
            //echo "<pre>";print_r($result_items);die;

            $aid = $_REQUEST['aid'];
            if($aid == MOBILE_AUTHENTICATION_TYPE)
            {
                $products_type = "DMB";
            }
            else if($aid == HOME_AUTHENTICATION_TYPE)
            {
                $products_type = "LEC";
            }

            //old code for sorting products
            $args1["tablename"] = "tbl_merchants_items";
            $args1["fieldname"] = "p1mi_code as sku,p1mi_disp_order as order_id, p1cms_product_id as cms_pid";
            $args1["whereCon"] = "p1ms_id= $sid order by order_id ASC,p1mi_id ";
            $debug = array('file'=>'previewtemplate.php', 'line'=>'sorting_products');
            //$prolist = $templates_obj->get_category_services($args1,$debug);/*sorting_products*/

            //new code for sorting products
            $prolist = $services_obj->get_countlists("tbl_merchants_items mi inner join tbl_merchants_items_order mo on mi.p1mi_id=mo.p1mi_id inner join tbl_country c on mo.p1co_id=c.p1co_id","mi.p1cms_product_id as cms_pid,mi.p1mi_code as sku,mo.p1mio_order as order_id","mi.p1ms_id=".$sid." and c.p1co_code='".$country_code."' order by mo.p1mio_order ASC,mi.p1mi_id");

            if(count($result_array) == count($prolist))
            {
                foreach ($prolist as $k => $v)
                {
                    foreach($result_array as $vals)
                    {
                        if($v['cms_pid'] == $vals['productid'])
                        {
                            if($products_type == null)
                            {
                                $result_item[] = $vals;
                            }
                            else
                            {
                                if($vals['product_offering_code'] == $products_type)
                                {
                                    $result_item[] = $vals;
                                }
                                else
                                {
                                    $result_item[] = $vals;
                                }
                            }
                        }
                    }
                }
            }
            else
            {
                $result_item = $result_array;
            }
            //echo "<pre>";print_r($result_item);die;
        }

        $debug = array('file'=>'previewtemplate.php', 'line'=>'dropdown_preview_sid');
        $sdropchk = $services_obj->get_drop_downlists('tbl_merchant_services mser,tbl_templates tmp', 'mser.p1ms_comments as comments,mser.p1ms_logo as logo, tmp.p1te_id as tid, mser.p1ms_name as servname,tmp.p1te_css as tmploc,mser.p1au_id as authid,mser.p1_payment_id as auth_method',"mser.p1te_id=tmp.p1te_id and mser.p1ms_id=$sid",$debug);/*dropdown_preview_sid*/
        $home_flow = $sdropchk[0]['auth_method'];
        $comments = $sdropchk[0]['comments'];// fetch details of how it works

        if($_REQUEST['sid'] != '' || $_REQUEST['tid'] != '' || $_REQUEST['aid'] != '')
        {
            $sid = $_REQUEST['sid'];
            $tid = $_REQUEST['tid'];
            $aid = $_REQUEST['aid'];
        }
        else
        {
            $sid = $_POST['sid'];
            $tid = $_POST['tid'];
            $aid = $_POST['aid'];
        }
    }
}
else // display default template
{
    $smarty->assign('current_page', 'template');
}

$args2["tablename"] = "tbl_currency";
$args2["fieldname"] = "p1cu_symbol";
$args2["whereCon"] = "p1cu_stflag =1 and p1co_id = ".$country_id;
$debug = array('file'=>'previewtemplate.php', 'line'=>'fetchcurrency');
$fetch_currency = $templates_obj->get_component($args2, $debug);/*fetchcurrency*/
$curreny_symbol = $fetch_currency[0]['p1cu_symbol'];

if($is_official == 1 && $language_code != 'EN')
{
    $smarty->assign('is_default', 1);
}
else
{
    $arg2["tablename"] = "tbl_country_language_reference as ref left join tbl_languages as lang on (ref.p1lg_id = lang.p1lg_id)";
    $arg2["fieldname"] = "lang.p1lg_name, lang.p1lg_code";
    $arg2["whereCon"] = "ref.p1colg_stflag = 1 and ref.p1colg_official = 1 and ref.p1co_id = ".$country_id;
    $debug = array('file'=>'previewtemplate.php', 'line'=>'getdefaultlang');
    $p1language_name = $templates_obj->get_component($arg2, $debug);/*getdefaultlang*/
    $language_name = $p1language_name[0]['p1lg_name'];
    $this_language_name = $p1language_name[0]['p1lg_code'];
}

if($_REQUEST['chk'] == 'preview')  //To fetch css values for preview while change before updating
{
    $class = $_REQUEST['comp'];
    $smarty->assign('preview', 'preview');
}
else  //To fetch css values for preview after updating
{
    $class = '';
}

if($sdropchk[0]['logo'] != '')           //to fetch logo
{
    $logo = "../data/images/thumb/".$sdropchk[0]['logo'];
}
else
{
    $logo = "../images/logo.gif";
}

if($_REQUEST['tid'] != '')      /*----- To fetch template id -----*/
{
    $tid = $_REQUEST['tid'];
}
else if($sdropchk[0]['tid'] != '')
{
    $tid = $sdropchk[0]['tid'];
}
else
{
    $tid = $_POST['tid'];
}

$get_style["tablename"] = "tbl_templates";
$get_style["whereCon"] = "p1te_id ='".$tid."'";
$debug = array('file'=>'previewtemplate.php', 'line'=>'To_fetch_template_details');
$getstyle_arr = $templates_obj->get_all($get_style,$debug);/*To_fetch_template_details*/
if($_REQUEST['temp'] == '1' && $_REQUEST['file'] == 'xml')      //preview xml file
{
    $template_path = $country_code."/tmp_labels_".$language_code.".xml";
    $xml = simplexml_load_file("../".$getstyle_arr[0]['p1te_xml']."/".$template_path);
    $smarty->assign('temp', '1');
    $smarty->assign('file', 'xml');
}
else
{
    $template_path = $country_code."/labels_".$language_code.".xml";
    $xml = simplexml_load_file("../".$getstyle_arr[0]['p1te_xml']."/".$template_path);
}
//echo "Template path : ".$getstyle_arr[0]['p1te_xml']."/".$template_path;

/*----- homelabel -----*/
foreach ($xml->Home[0]->homelabel as $homelabel)
{
    $homelablenames[] = (string) $homelabel;//To fetch home labels
}
    $option = "";
    $option .= "<br>";
    $option .= "<input type='radio' name='q1'>";
    $option .= "<span class='faux-label'>Yes</span>";
    $option .= "<input type='radio' name='q1'>";
    $option .= "<span class='faux-label'>No</span>";
    $homelablenames= str_replace("[radiooption1]", $option, $homelablenames);
    $homelablenames= str_replace("[radiooption2]", $option, $homelablenames);
    $homelablenames= str_replace("[radiooption3]", $option, $homelablenames);
foreach($xml->Home->homelabel as $val1)
{
    $key_h[] = strtolower(escape_string( (string) $val1->attributes() ));
}
$home_labels = array_combine($key_h, $homelablenames);
$smarty->assign('home_labels', $home_labels);

/*----- mobilelabel -----*/
foreach ($xml->Mobile[0]->mobilelabel as $mobilelabel)
{
    $mobilelablenames[] = (string) $mobilelabel;//To fetch mobile labels
}
foreach($xml->Mobile->mobilelabel as $val2)
{
    $key_m[] = strtolower(escape_string( (string) $val2->attributes() ));
}
$mobile_labels = array_combine($key_m, $mobilelablenames);
$smarty->assign('mobile_labels', $mobile_labels);

/*----- anyphonelabel -----*/
foreach ($xml->Anyphone[0]->anyphonelabel as $anyphonelabel)
{
    $anyphonelablenames[] = (string) $anyphonelabel;//To fetch anyphone labels
}
foreach($xml->Anyphone->anyphonelabel as $val3)
{
    $key_a[] = strtolower(escape_string( (string) $val3->attributes() ));
}
$anyphone_labels = array_combine($key_a, $anyphonelablenames);
$smarty->assign('anyphone_labels', $anyphone_labels);

if($aid == '')
{
    $aid = $sdropchk[0]['authid'];
}

if($aid == MOBILE_AUTHENTICATION_TYPE ) // assign product based on authentication
{
    $p_type = explode(",",$sdropchk[0]['auth_method']);
    $smarty->assign('p_type', MOBILE_AUTHENTICATION_TYPE);//$smarty->assign('p_type', $p_type[1]);
    $sdropchk[0]['authid'] = MOBILE_AUTHENTICATION_TYPE;

    if($billing_type_code == 'MO' && $_REQUEST['prev'] == 'mobile_nonus_item')
    {
        $_REQUEST['page'] = 'mobile_nonus_momt_timer';
    }
    else if($billing_type_code == 'WEB' && $_REQUEST['prev'] == 'mobile_nonus_item')
    {
        $_REQUEST['page'] = 'mobile_nonus_web';
    }
}
else if($aid == HOME_AUTHENTICATION_TYPE )
{
    $p_type = explode(",",$sdropchk[0]['auth_method']);
    $smarty->assign('p_type', $p_type[0]);
    $sdropchk[0]['authid'] = HOME_AUTHENTICATION_TYPE;
}
else if($aid == ANYPHONE_AUTHENTICATION_TYPE )
{
    if($_REQUEST['next'] == 'detect_anyphone_flow' && $_REQUEST['prev'] == 'anyphone_nonus_item')
    {
        if($billing_type_code == 'PPC' || $billing_type_code == 'PPM')
        {
            $_REQUEST['page'] = 'ivr_nonus_timer';
            $sdropchk[0]['authid'] = HOME_AUTHENTICATION_TYPE;
        }
        else if($billing_type_code == 'MO' || $billing_type_code == 'MT')
        {
            $_REQUEST['page'] = 'mobile_nonus_momt_timer';
            $sdropchk[0]['authid'] = MOBILE_AUTHENTICATION_TYPE;
        }
        else if($billing_type_code == 'WEB')
        {
            $_REQUEST['page'] = 'mobile_nonus_web';
            $sdropchk[0]['authid'] = MOBILE_AUTHENTICATION_TYPE;
        }
        else
        {
            $_REQUEST['page'] = 'ivr_nonus_timer';
            $sdropchk[0]['authid'] = HOME_AUTHENTICATION_TYPE;
        }
    }
}

if($_REQUEST['sub'] == 'mobile')
{
    $sdropchk[0]['authid'] = MOBILE_AUTHENTICATION_TYPE;
}

if($sdropchk[0]['authid'] == ANYPHONE_AUTHENTICATION_TYPE)
{
    $get_attr["tablename"]="tbl_payone_images";
    $get_attr["fieldname"]='p1pimg_name,p1pimg_image';
    $get_attr["whereCon"]="p1pimg_default =1 order by p1au_id desc";
    $debug = array('file'=>'previewtemplate.php', 'line'=>'getdefimage');
    $def_img = $templates_obj->get_component($get_attr, $debug);/*getdefimage*/
    $mobile_button_image="../data/images/thumb/".$def_img[0][p1pimg_image];
    $home_button_image="../data/images/thumb/".$def_img[1][p1pimg_image];
    $smarty->assign('mobile_button_image', $mobile_button_image);
    $smarty->assign('home_button_image', $home_button_image);
}

if($_REQUEST['temp'] == '1' && $_REQUEST['file'] == 'css')
{
    $css_file = "../data/styles/templates/template_".$_REQUEST['tid']."/tmp_merchant.css";
    $smarty->assign('temp_css', $css_file);
    $smarty->assign('temp', '1');
    $smarty->assign('file', 'css');
}
else
{
    $smarty->assign('temp_css', $getstyle_arr[0]['p1te_css']);
}

if(isset($_REQUEST['product_id']))       //if itemid exist get the item details for the selcted service
{
    $result_item = 1;
    //$result_array = 1;
}

/*----- assign value to tpl -----*/
$smarty->assign('modified_date', $modified_date);
$smarty->assign('class', $class);
$smarty->assign('tid', $tid);
$smarty->assign('sid', $sid);
$smarty->assign('temp_logo', $logo);
$smarty->assign('comments', $comments);
$smarty->assign('aid', $sdropchk[0]['authid']);
$smarty->assign('item_list_tmp', $result_item);
//$smarty->assign('item_list_tmp', $result_array);
$smarty->assign('sub', trim($_REQUEST['sub']));
$smarty->assign('months', $month_array);
$smarty->assign('mobile_auth', MOBILE_AUTHENTICATION_TYPE);
$smarty->assign('curreny_symbol', html_entity_decode($curreny_symbol));
$smarty->assign('is_official', $is_official);
$smarty->assign('country_id', $country_id);
$smarty->assign('country_code', $country_code);
$smarty->assign('language_code', $language_code);
$smarty->assign('language_name', $language_name);
$smarty->assign('this_language_name', $this_language_name);

$ui_temp_path = $glb_adm_tpl_path."uitemplates/desktop/";

if($products_avail == 'no')
{
    $logo_no = "../images/logo.gif";
    $smarty->assign('temp_logo', $logo_no);
    $smarty->display($ui_temp_path.'viewpreviewtemp_noprod.tpl');
    exit;
}

if(($_REQUEST['page']))
{
    $_REQUEST['next'] = $_REQUEST['page'];
}

if($sdropchk[0]['authid'] == ANYPHONE_AUTHENTICATION_TYPE && $_REQUEST['next'] == '')
{
     if($country_code == 'US')
    {
        $smarty->display($ui_temp_path.'paymentmode.tpl');
    }
    else
    {
        $smarty->display($ui_temp_path.'anyphone_nonus_item.tpl');
    }
}
else if($_REQUEST['next'] == '1')
{
    $smarty->display($ui_temp_path.'viewpreviewtemp1.tpl');
}
else if($_REQUEST['next'] == '2')
{
    $smarty->display($ui_temp_path.'viewpreviewtemp2.tpl');
}
else if($_REQUEST['next'] == '3')
{
    $smarty->display($ui_temp_path.'viewpreviewtemp3.tpl');
}
else if($_REQUEST['next'] == '4')
{
    $smarty->display($ui_temp_path.'viewpreviewtemp4.tpl');
}
else if($_REQUEST['next'] == 'ivr_nonus_item')
{
    $smarty->display($ui_temp_path.'ivr_nonus_item.tpl');
}
else if($_REQUEST['next'] == 'ivr_nonus_tos')
{
    $smarty->display($ui_temp_path.'ivr_nonus_tos.tpl');
}
else if($_REQUEST['next'] == 'ivr_nonus_timer')
{
    if($billing_type_code == 'PPC')
    {
        $smarty->assign('timer', 1);
    }
    else if($billing_type_code == 'PPM')
    {
        $smarty->assign('timer', 2);
    }
    else
    {
        $smarty->assign('timer', 1);
    }
    $smarty->display($ui_temp_path.'ivr_nonus_timer.tpl');
}
else if($_REQUEST['next'] == 'ivr_nonus_success')
{
    $smarty->display($ui_temp_path.'ivr_nonus_success.tpl');
}
else if($_REQUEST['next'] == 'mobile_nonus_momt_timer')
{
    $smarty->display($ui_temp_path.'mobile_nonus_momt_timer.tpl');
}
else if($_REQUEST['next'] == 'mobile_nonus_momt_success')
{
    $smarty->display($ui_temp_path.'mobile_nonus_momt_success.tpl');
}
else if($_REQUEST['next'] == 'mobile_nonus_web')
{
    $smarty->display($ui_temp_path.'mobile_nonus_web.tpl');
}
else if($_REQUEST['next'] == 'mobile_nonus_web_pin')
{
    $smarty->display($ui_temp_path.'mobile_nonus_web_pin.tpl');
}
else if($_REQUEST['next'] == 'anyphone_nonus_item')
{
    $smarty->display($ui_temp_path.'anyphone_nonus_item.tpl');
}
else
{
    if($sdropchk[0]['authid'] == MOBILE_AUTHENTICATION_TYPE)
    {
        if($country_code == 'US')
        {
            $smarty->assign('nextid', 4);
            $smarty->display($ui_temp_path.'viewpreviewtemp.tpl');
        }
        else
        {
            $smarty->display($ui_temp_path.'mobile_nonus_item.tpl');
        }
    }
    else
    {
        if($country_code != 'US' && $home_flow == '4' || $country_code != 'US' && $home_flow == '5' || $country_code != 'US' && $home_flow == '6' || $country_code != 'US' && $home_flow == '')
        {
            $smarty->display($ui_temp_path.'ivr_nonus_item.tpl');
        }
        else
        {
            $smarty->assign('nextid', 1);
            $smarty->display($ui_temp_path.'viewpreviewtemp.tpl');
        }
    }
}

?>