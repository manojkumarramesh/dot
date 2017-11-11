<?php
//-------------------------------------------------------------------------------------------------------------------
// File name   : sessioninc.php
// Description : File to handle session checking
//
// © 2010-2011 PaymentOne Corportation. All rights reserved.
//
// Created date : 12-02-2010
// Modified date: 31-10-2011
// ------------------------------------------------------------------------------------------------------------------
//Session Check Start
if(($_SESSION['merchant_id']=='') && ($_REQUEST['out'] != '1'))
{
    header("location:index.php?do=login");
    //exit;
}
else
{
    include_once('includes/configs/init.php');
    // Check the Merchant is suspended starts
    $mid = $_SESSION['merchant_id'];
    $mer_sus_info = $merchants_obj->GetMerchantSelect("$glb_tbl_merchants","$glb_merchants_column[me_stflag]"," $glb_merchants_column[me_id]='$mid'");
    $mer_status = $mer_sus_info[0]["$glb_merchants_column[me_stflag]"];
    if($mer_status=="2")
    {
        session_destroy();
        unset($_SESSION);
        header("location:index.php?action=suspend");
        exit;
    }
    // Check the Merchant is suspended ends	 
    if ($current_navigate_page != "services")
    {
        if(isset($_SESSION['dummyserid']))
        {
	    $items_xml=exec('curl -u '.$_SESSION['admin_email'].':'.$_SESSION['admin_cms_pwd'].' '.$glb_cms_product_url.'?service='.$_SESSION['dummyserid']);
	    $result_xml = simplexml_load_string($items_xml);
	    $arrayValue = array();
            foreach($result_xml->elements->product as $key=> $value)
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
	            $str = exec('curl -u '.$_SESSION['admin_email'].':'.$_SESSION['admin_cms_pwd'].' '.$glb_cms_product_url.'/'.$itemList[$i]['productid']);
	            $xml_result = simplexml_load_string($str);
	            $productdetail = array();
	            foreach($xml_result as $key=> $value)
	            {			
	                $productId = (string)$xml_result->attributes()->product_id ;
	                $productdetail["productId"] = $productId ;
	                $productdetail[$key] = (string)$value ;	
	            }
	            // update the status in cms
	            $rand=rand(99,99999);	
	            $updateresult= exec('curl -u '.$_SESSION['admin_email'].':'.$_SESSION['admin_cms_pwd'].' -X PUT -H "Content-Type:application/xml" -d "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?><product product_id=\"'.$productdetail['productId'].'\"><name>'.$productdetail['name'].'-inactive-'.$rand.'</name><account_id>'.$productdetail['account_id'].'</account_id><mblox_id>'.$productdetail['mblox_id'].'</mblox_id> <short_code>'.$productdetail['short_code'].'</short_code><plan_id>'.$productdetail['plan_id'].'</plan_id><tariff>'.$productdetail['tariff'].'</tariff><description>'.$productdetail['description'].'</description><purchase_type>'.$productdetail['purchase_type'].'</purchase_type><service>'.$productdetail['service'].'</service><category>'.$productdetail['category'].'</category><status>inactive</status>
<effective_date>'.$productdetail['effective_date'].'</effective_date><end_date>'.$productdetail['end_date'].'</end_date><creation_date>'.$productdetail['creation_date'].'</creation_date><frequency>'.$productdetail['frequency'].'</frequency><sku>'.$productdetail['sku'].'</sku></product>" '.$glb_cms_product_url.'/'.$itemList[$i]['productid']);
	            // delete records from p1 database - items and items attribute table
	            $services_obj = new services();
	            $mid=$_SESSION['merchant_id'];
	            $tabname='tbl_merchants_items';
	            $wherecon="p1cms_product_id ='".$itemList[$i]['productid']."' and p1me_id='$mid' ";
	            $dele_itm=$services_obj->service_delete($tabname,$wherecon);
	            $tab_name_attr='tbl_merchants_item_attribs';
	            $where_attr="p1cms_product_id ='".$itemList[$i]['productid']."'";
	            $dele_attr=$services_obj->service_delete($tab_name_attr,$where_attr);
	        }
	    }
        }
	unset($_SESSION['dummyserid']);
    }
}
//Session Check End
?>