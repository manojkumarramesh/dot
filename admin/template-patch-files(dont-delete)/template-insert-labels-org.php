<?php

//-------------------------------------------------------------------------------------------------------------------
// File name   : template-insert-labels.php
// Description : File for UI templates file uploading functionality
//
// copyright(c), Inside Right, 2010-2011, all rights reserved.
//
// Author: Dot Com Infoway Ltd
// Created date : 05-09-2011
// Modified date: 28-11-2011
// -----------------------------------------------------------------------------------------------------------------

/*----- Include Files -----*/
include_once('../includes/configs/init.php');

/*----- Instantiate the class -----*/
$templates_obj = new templates();
$template_func_obj = new TemplateFunctions();

function insert_labels($labels_array, $flag, $type)
{
    global $templates_obj, $template_func_obj;

    $args["tablename"] = "tbl_country_language_reference";
    $args["fieldname"] = "p1colg_id";
    $args["whereCon"] = "p1colg_stflag =1";
    $co_lang_result = $templates_obj->get_component($args);

    $fetch["tablename"] = "tbl_templates";
    $fetch["fieldname"] = "p1te_id, p1te_device_flag";
    $fetch["whereCon"] = "p1te_stflag = 1 and p1te_device_flag = ".$flag;
    $templates_result = $templates_obj->get_component($fetch);

    $count = 1;
    for($i=0;$i<count($templates_result);$i++)
    {
        $template_id = $templates_result[$i]['p1te_id'];
        for($j=0;$j<count($co_lang_result);$j++)
        {
            foreach ($labels_array as $key => $value)
            {
                $insert_caption["tablename"] = "tbl_template_captions";
                $insert_caption["fieldname"] = "p1te_id, p1tc_title, p1tc_value, p1tc_type, p1colg_id, p1tc_stflag";
                $insert_caption["fieldval"] = "'".$template_id."','".addslashes($key)."','".addslashes($value)."','".$type."','".$co_lang_result[$j]['p1colg_id']."',1";
                $insertcaption_arr = $templates_obj->template_insert($insert_caption);
            }
         }
        $count++;
        if($count == 100)
        {
            sleep(5);
            $count = 1;
        }
    }
}

/*---------------------DESKTOP LABELS----------------------------------------*/

$desktop_home_array = array(
    'Legal Disclaimer'=>'Legal Disclaimer',
    'IVR Per Call (Please Call)' => '<h3 style="margin:0;">Last Step, Please Call</h3><br/><span class="number"><strong>[international_ivr_number]</strong></span><br/><span class="instruction">from your home phone [international_home_phone] in the next 10 minutes and follow the instructions. While on the call, you will be prompted to enter a PIN in order to complete the transaction.</span><br/><strong>Enter PIN [xxxx].</strong>',
    'IVR Per Call (Follow Instructions)' => '<strong>If you do not complete this step within 10 minutes, this transaction will be cancelled.</strong>',
    'IVR Per Minute (Please Call)' => '<h3 style="margin:0;">Last Step, please call</h3><br/><span class="number"><strong>[international_ivr_number]</strong></span><br/><span class="instruction">from your home phone [international_home_phone] and follow the instructions.</span>',
    'IVR Per Minute (Follow Instructions)' => '<strong>If you do not complete this step within 10 minutes, this transaction will be cancelled.</strong>',
    'IVR Per Minute (Confirmation message)' => 'Thank you for your purchase! You were billed a total of [$] for [product].',
    'IVR (Please Call)' => '<h3 style="margin:0;">Last Step, Please Call</h3><br/><span class="number"><strong>[toll_free_number]</strong></span><br/><span class="instruction">from your home phone <strong>[home_phone]</strong> in the next 10 minutes to complete the transaction.</span>',
    'IVR (Follow Instructions)' => 'Call [toll_free_number] NOW from your home phone and follow the instructions.<br/><br/><strong>If you do not complete this step within 10 minutes, this transaction will be cancelled.</strong>',
    'Private and secure payment' => 'Private and secure payment',
    'IVR Per Call (Time remaining to call)'=>'Time remaining to call'
);
insert_labels($desktop_home_array, 1, 1);       //insert for mobile label

sleep(5);

$desktop_mobile_array = array(
    'MO Method' => 'Send a text message (SMS) with the following code [xxxx] to [short_code]',
    'MT Method' => 'Send a text message (SMS) with the following code [xxxx] to [short_code]',
    'Private and secure payment' => 'Private and secure payment',
    'Try Web Billing' => 'Try Web Billing',
    'Time remaining to send SMS' => 'Time remaining to send SMS',
    'Try again' => 'Try again',
     'MO/MT failure to iLEC' => 'If you have a fixed line home phone number that you would like to use. Please [click_here] to continue.'
);
insert_labels($desktop_mobile_array, 1, 2);     //insert for mobile label

sleep(5);

$desktop_anyphone_array = array(
    'Private and secure payment' => 'Private and secure payment',
    'Legal Disclaimer'=>'Charges go on your wireless account or phone bill. Msg&Data rates may apply for wireless carriers. For customer support, please contact us at 1-888-408-0018 or email support@paymentone.com. By clicking *CONTINUE*, you are confirming that you are the account owner or have authorization from the account owner to make purchases. Supported U.S. mobile carriers: AT&T, Sprint, T-Mobile, and Verizon Wireless.',
     'Select an item:' => 'Select an item:'
);
insert_labels($desktop_anyphone_array, 1, 3);       //insert for anyphone label

/*---------------------SMARTPHONE LABELS----------------------------------------*/

sleep(5);

$smartphone_home_array = array(
    'Legal Disclaimer'=>'Legal Disclaimer',
    'Private and secure payment' => 'Private and secure payment',
    'IVR (Please Call)' => '<h3 style="margin:0;">Last Step, Please Call</h3><br/><span class="number"><strong>[toll_free_number]</strong></span><br/><span class="instruction">from your home phone <strong>[home_phone]</strong> in the next 10 minutes and follow the instructions to complete the transaction.</span>',
    'IVR (Follow Instructions)' => '<strong>If you do not complete this step within 10 minutes, this transaction will be cancelled.</strong>',
    'IVR Per Call (Please Call)' => '<h3 style="margin:0;">Last Step, Please Call</h3><br/><span class="number"><strong>[international_ivr_number]</strong></span><br/><span class="instruction">from your home phone [international_home_phone] in the next 10 minutes and follow the instructions. While on the call, you will be prompted to enter a PIN in order to complete the transaction.</span><br/><strong>Enter PIN [xxxx].</strong>',
    'IVR Per Call (Follow Instructions)' => '<strong>If you do not complete this step within 10 minutes, this transaction will be cancelled.</strong>',
    'IVR Per Minute (Please Call)' => '<h3 style="margin:0;">Last Step, please call</h3><br/><span class="number"><strong>[international_ivr_number]</strong></span><br/><span class="instruction">from your home phone [international_home_phone] and follow the instructions.</span>',
    'IVR Per Minute (Follow Instructions)' => '<strong>If you do not complete this step within 10 minutes, this transaction will be cancelled.</strong>',
    'IVR Per Minute (Confirmation message)' => 'Thank you for your purchase! You were billed a total of [$] for [product].',
    'IVR Per Call (Time remaining to call)'=>'Time remaining to call'
);
insert_labels($smartphone_home_array, 2, 1);        //insert for home label

sleep(5);

$smartphone_mobile_array = array(
    'Legal Disclaimer'=>'Charges go on your wireless account or phone bill. Msg&Data rates may apply for wireless carriers. For customer support, please contact us at 1-888-408-0018 or email support@paymentone.com. By clicking *CONTINUE*, you are confirming that you are the account owner or have authorization from the account owner to make purchases. Supported U.S. mobile carriers: AT&T, Sprint, T-Mobile, and Verizon Wireless.',
    'Private and secure payment' => 'Private and secure payment',
    'MO Method' => 'Send a text message (SMS) with the following code [xxxx] to [short_code]',
    'MT Method' => 'Send a text message (SMS) with the following code [xxxx] to [short_code]',
    'Try Web Billing' => 'Try Web Billing',
    'Time remaining to send SMS' => 'Time remaining to send SMS',
    'Try again' => 'Try again',
    'MO/MT failure to iLEC' => 'If you have a fixed line home phone number that you would like to use. Please [click_here] to continue.'
);
insert_labels($smartphone_mobile_array, 2, 2);      //insert for mobile label

sleep(5);

$smartphone_anyphone_array = array(
    'Private and secure payment' => 'Private and secure payment',
    'Legal Disclaimer'=>'Charges go on your wireless account or phone bill. Msg&Data rates may apply for wireless carriers. For customer support, please contact us at 1-888-408-0018 or email support@paymentone.com. By clicking *CONTINUE*, you are confirming that you are the account owner or have authorization from the account owner to make purchases. Supported U.S. mobile carriers: AT&T, Sprint, T-Mobile, and Verizon Wireless.',
     'Select an item:' => 'Select an item:'
);
insert_labels($smartphone_anyphone_array, 2, 3);        //insert for anyphone label

echo "<br/>";
echo "LABELS INSERTED SUCCESSFULLY";

//unlink(FULL_PATH."admin/template-insert-labels.php");

?>