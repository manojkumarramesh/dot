<?php
//-------------------------------------------------------------------------------------------------------------------
// File name   : sessionadminc.php
// Description : File to handle session checking
//
// © 2010-2011 PaymentOne Corportation. All rights reserved.
//
// Created date : 15-03-2010
// Modified date: 16-12-2011
//-------------------------------------------------------------------------------------------------------------------
class sessionAdmin
{
    var $debug_obj;
   // Check the SESSION Status
   public function __construct()
    {
        global $debug_obj, $glb_session_expire_time;
        $this->debug_obj = $debug_obj;
        //SESSION Lifetime
        //$inactive = 1800;
        $inactive = $glb_session_expire_time;
        $re_direct=explode("/",$_SERVER ['PHP_SELF']);
        //Check the SESSION Idle time
        if(isset($_SESSION['timeout'])) 
        {
            $session_life = time() - $_SESSION['timeout'];
            if($session_life > $inactive)
            { 
                if($re_direct[1]=='admin')
                {
                    if($re_direct[2] == 'activemerchants.php')
                    {
                        session_destroy(); 
                        $page = "&page=session_expired";
                        header("LOCATION:".DOMAIN_NAME."admin/nofile.php?errormsg=product$page");
                        exit;
                    }
                    else if($re_direct[2] == 'previewtemplate.php' || $re_direct[2] == 'selectp1buttons.php' || $re_direct[2] == 'editorpreview.php' || $re_direct[2] == 'previewtemplate_sp.php' || $re_direct[2] == 'responseajax.php' || $re_direct[2] == 'responsemsg.php' || $re_direct[2] == 'fileupload.php')
                    {
                        header("location: ".DOMAIN_NAME."admin/nofile.php?page=colorbox_session_time_out");
                        exit;
                    }
                    else if($re_direct[2] == 'createtemplate.php')
                    {
                        header("location: ".DOMAIN_NAME."admin/nofile.php?page=jquery_session_time_out");
                        exit;
                    }
                    else
                    {
                        session_destroy(); 
                        header("location:index.php?do=login"); 
                    }
                }
                else
                {
                    session_destroy(); 
                    header("location:index.php?do=login"); 
                }
            }
        }
        $_SESSION['timeout'] = time();
        //If SESSION is empty
        if(($_SESSION['admin_id']=='') && ($_REQUEST['out'] != '1'))
        {
            if($re_direct[1]=='admin')
            {
                if($re_direct[2] == 'activemerchants.php')
                {
                    $page = "&page=session_expired";
                    header("LOCATION:".DOMAIN_NAME."admin/nofile.php?errormsg=product$page");
                    exit;
                }
                else if($re_direct[2] == 'previewtemplate.php' || $re_direct[2] == 'selectp1buttons.php' || $re_direct[2] == 'editorpreview.php' || $re_direct[2] == 'previewtemplate_sp.php' || $re_direct[2] == 'responseajax.php')
                {
                    header("location: ".DOMAIN_NAME."admin/nofile.php?page=colorbox_session_time_out");
                    exit;
                }
                else if($re_direct[2] == 'fileupload.php')
                {
                    echo 401;
                    exit;
                }
                else if($re_direct[2] == 'createtemplate.php')
                {
                    header("location: ".DOMAIN_NAME."admin/nofile.php?page=jquery_session_time_out");
                    exit;
                }
                else
                {
                    header("location:index.php?do=login");
                    exit;
                }
            }
            else
            {
                header("location:index.php?do=login"); 
            }
        }
        if(isset($_SESSION['dummyserid']))
        {
            if ((CUR_NAVIGATE_PAGE != "services") || (CUR_NAVIGATE_PAGE=="services" && trim($_REQUEST['action'])=="listing"))
            {
                    $debug = array('file'=>'sessionadminc.php', 'line'=>'clear_products');
                    $this->clearProducts($debug);/*clear_products*/
            }
        }
    }
    function clearProducts($findline="")
    {
        $this->debug_obj->WriteDebug($class="sessionAdmin", $function="clearProducts", $findline['file'], $this->debug_obj->FindFunctionCalledline('clearProducts', $findline['file'], $findline['line']));
        global $common_obj,$glb_cms_product_url;
        $ser_obj = new services();
        if ((CUR_NAVIGATE_PAGE != "activemerchants") && ((CUR_NAVIGATE_PAGE != "services") || (CUR_NAVIGATE_PAGE=="services" && trim($_REQUEST['action'])=="listing")))
        {
            unset($_SESSION['image_nameTMPadm']);
            unset($_SESSION['new_default_image']);
            if(isset($_SESSION['dummyserid']))
            {
                $services_obj = new services();
                $query_string = "?service=".$_SESSION['dummyserid'];
                $debug = array('file'=>'sessionadminc.php', 'line'=>'cms_products_display');
                $items_xml =  $common_obj ->CmsCurlGet($glb_cms_product_url,$_SESSION['admin_email'],$_SESSION['admin_cms_pwd'],$query_string,$debug);/*cms_products_display*/
                if(stristr($items_xml, '<?xml'))
                {
                    $active_xml = simplexml_load_string($items_xml);
                    $arrayValue = array();
                    foreach($active_xml->elements->product as $key=> $value)
                    {
                        $productId = (string)$value->attributes()->product_id ;
                        unset($value->attributes()->product_id);
                        $value = (array) $value ;
                        $value["productid"] = $productId ;
                        $itemList[] = $value ;	
                    }
                    if(count($itemList) > 0)
                    {
                        for($i=0;$i<count($itemList);$i++)
                        {
                            // retrive the value from cms and update the status as inactive
                            $query_string = '/'.$itemList[$i]['productid'];
                            $str =  $common_obj ->CmsCurlGet($glb_cms_product_url,$_SESSION['admin_email'],$_SESSION['admin_cms_pwd'],$query_string);
                            $xml_result = simplexml_load_string($str);
                            $productdetail = array();
                            foreach($xml_result as $key=> $value)
                            {			
                                $productId = (string)$xml_result->attributes()->product_id ;
                                $productdetail["productId"] = $productId ;
                                $productdetail[$key] = (string)$value ;	
                            }
                            // update the status as inactive in cms
                            $find = array('<state>active</state>');
			    $replace = array('<state>inactive</state>');
			    $str_sku = str_replace($find, $replace, $str);
			    if(stristr($str_sku, '<purchase_type>onetime</purchase_type>') == TRUE) 
			    {
				$str_sku = str_replace('</end_date>','</end_date><frequency></frequency>',$str_sku);
			    }
			    $updateresult= $common_obj->CmsCurlPut($glb_cms_product_url,$_SESSION['admin_email'],$_SESSION['admin_cms_pwd'],$str_sku,'/'.$itemList[$i]['productid'],$debug);
                            // delete records from p1 database - items and items attribute table
                            $mid=$_SESSION['merchant_id'];
                            $tabname='tbl_merchants_items';
                            $wherecon="p1cms_product_id ='".$itemList[$i]['productid']."' and p1me_id='$mid' ";
                            $debug = array('file'=>'sessionadminc.php', 'line'=>'delete_item');
                            $dele_itm=$ser_obj->service_delete($tabname,$wherecon,$debug);/*delete_item*/
                        }
                    }
                }
                // delete dummy record from p1 database - from service table
                $where_con="p1ms_id ='".$_SESSION['dummyserid']."'";
                $debug = array('file'=>'sessionadminc.php', 'line'=>'delete_dummy_service');
                $dele_rec=$ser_obj->service_delete('tbl_merchant_services',$where_con,$debug);/*delete_dummy_service*/
            }
            unset($_SESSION['dummyserid']);
        }
    }
}
new sessionAdmin();
?>