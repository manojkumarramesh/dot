<?php
//-------------------------------------------------------------------------------------------------------------------
// File name   : service_ajax.php
// Description : file to handle add service ajax information
//
// © 2010-2011 PaymentOne Corportation. All rights reserved.
//
// Created date : 23-02-2010
// Modified date: 27-12-2011
// ------------------------------------------------------------------------------------------------------------------

/*----- Include Files -----*/
include_once('../includes/configs/init.php');
include('../includes/configs/sessionadminc.php');

/*----- Object creation start-----*/
$merchants_obj= new Merchants();
$templates_obj = new templates();
$common_obj= new Common();

$mid=$_SESSION['merchant_id'];

if($_REQUEST['action'] == "getfilter")
{
    //get all merchants details.  - left side
    $tid= $_REQUEST['tid'];
    $fchar= $_REQUEST['filterval'];
    $filterids=$_REQUEST['filterids'];
    $filterarr=explode(",",$filterids);
    //Getting active merchants from CMS based on the search value
    if($_REQUEST['filterval']!= '')
    {
        $fchar = "*".$_REQUEST['filterval']."*";
    }
    else
    {
        $fchar = '';
    }
    active_merchants_list($total=0,array(),0, 50, 0, $fchar,0);
    $smarty->assign('merchants', $mer_ser_dtls);
    $page_contents = $glb_adm_tpl_path.'editorselect.tpl';
    $list_all_merchants = $smarty->fetch($page_contents) ;
    echo $list_all_merchants;
}
else if($_REQUEST['action'] == "preSelect"){
    //Coding for Share Template with all Merchants - Apply merchants
    $tid= $_REQUEST['tid'];
    $get_select["tablename"] = "tbl_templates";
    $get_select["fieldname"] = "p1te_refid";
    $get_select["whereCon"] = "p1te_id =".$tid;
    $get_select = $templates_obj->get_component($get_select);
    if($get_select[0]['p1te_refid'] == 0 && $get_select[0]['p1te_refid'] != '')
    {
        echo 0;
    }
    else
    {
        echo 1;
    }
}

// recursive function to get the merchant list based on the search string
function active_merchants_list($total=0,$mer_dtls,$start,$max,$active,$filter_str,$start_value,$flag=0)
{
    $merchants_obj_func = new Merchants();
    //$start_value is used to pass the total value again
    global $glb_cms_account_url,$glb_tbl_merchants,$glb_merchants_column,$mer_dtls,$mer_ser_dtls,$glb_cms_xml,$common_obj,$refmerchantslst,$filterarr;

    $filter_str = urlencode($filter_str);
    $query_string = "?role=merchant&company_name=*$filter_str*&status=active,suspend&start=$start&max=$max";
    $debug = array('file'=>'editorajax.php', 'line'=>'getmerchants_admintemplate');
    $get_active_list=$common_obj->CmsCurlGet($glb_cms_account_url,$_SESSION['admin_email'],$_SESSION['admin_cms_pwd'],$query_string,$debug);/*getmerchants_admintemplate*/
    if(strstr($get_active_list, $glb_cms_xml))
    {
        $active_xml_list = simplexml_load_string($get_active_list);
        $total = (string)$active_xml_list->attributes()->total;
        $arrayValue = array();
        foreach($active_xml_list->elements->account as $key=> $value)
        {
            $account_id="";
            $account_id = (string)$value->attributes()->account_id ;
            $active=$active+1;
            $value = (array) $value ;
            $mer_dtls[$account_id]['company_name'] = $value['company_name'] ;
            $mer_dtls[$account_id]['account_id'] = $account_id;
            //Select Merchant Id from tbl_merchants
            $slt_mrcnt = $merchants_obj_func->GetMerchantSelect("$glb_tbl_merchants","$glb_merchants_column[me_id]","$glb_merchants_column[cms_account_id]='$account_id'");
            $mer_dtls[$account_id]['p1me_id'] = $slt_mrcnt[0]["$glb_merchants_column[me_id]"];
            $mmid=$slt_mrcnt[0]["$glb_merchants_column[me_id]"];
            if(! in_array($mmid,$filterarr))
            {
                $mer_ser_dtls[$account_id]['company_name'] = $value['company_name'] ;
                $mer_ser_dtls[$account_id]['account_id'] = $account_id;
                $mer_ser_dtls[$account_id]['p1me_id'] = $slt_mrcnt[0]["$glb_merchants_column[me_id]"];
            }
        }
    }
    if($total > 50 || $flag == 0)
    {
        $i = ($total < 50 ? 1 : 0);
        $totals= $total- $max;
        $start_value= $start_value+50;
        active_merchants_list($totals,$mer_dtls, $start_value, 50,$active, urldecode($filter_str),$start_value,$i);
    }
    else if($flag==1)
    {
        active_merchants_list($totals,$mer_dtls, $start_value, 50, $active,urldecode($filter_str),$start_value,2);
    }
}

?>