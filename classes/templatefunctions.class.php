<?php
//-------------------------------------------------------------------------------------------------------------------
// File name   : templatefunctions.class.php
// Description : File to handle UI templates related tasks
//
// © 2010-2011 PaymentOne Corportation. All rights reserved.
//
// Created date : 10-07-2011
// Modified date : 31-10-2011
// ------------------------------------------------------------------------------------------------------------------
class TemplateFunctions{

    private $templates_obj;
    var $debug_obj;

    function __construct()
    {
        global $templates_obj,$debug_obj;
        $this->templates_obj=$templates_obj;
        $this->debug_obj=$debug_obj;
    }

    //-------------------------------------------------------------------------------------------------------------
    // function: check_template_duplication ( -- arguments -- )
    //-------------------------------------------------------------------------------------------------------------
    // purpose:	 check if UI template already exists.
    // arguments:	 $template_name,$adminid="",$merchantid=""
    //-------------------------------------------------------------------------------------------------------------
    function check_template_duplication($template_name,$adminid="",$merchantid="",$findline='')
    {
        $arguments = array($template_name,$adminid,$merchantid);
        $this->debug_obj->WriteDebug($class="TemplateFunctions.class", $function="check_template_duplication", $file=$findline['file'], $this->debug_obj->FindFunctionCalledline('check_template_duplication', $findline['file'], $findline['line']), $arguments);

        if($_SESSION['d_type']=="smart"){
            $d_where="and p1te_device_flag =2";
        }
        else{
            $d_where="and p1te_device_flag =1";
        }

        $nameres="";
        $argslist["tablename"]="tbl_templates";
        $argslist["fieldname"] = "p1te_name";
        if($adminid!=''){
            $argslist["whereCon"]='p1ad_id =1 and p1me_id IS NULL '.$d_where.' and p1te_stflag = 1';
        }
        if($merchantid!=''){
            $argslist["whereCon"]=" p1me_id = ".$merchantid." and p1te_stflag = 1 $d_where";
        }

        $debug = array('file'=>'templatefunctions.class.php', 'line'=>'duplicationcheck');
        $template_namelist = $this->templates_obj->get_maxid($argslist,$debug);/*duplicationcheck*/
        foreach( $template_namelist  as $keylist => $vallist)
        {
            if(strcmp(trim($vallist['p1te_name']),$template_name)){
                //check for template name duplication
            }
            else{
                $nameres ="duplicate";
            }
        }
        return $nameres;
    }

    //-------------------------------------------------------------------------------------------------------------
    // function: update_template_name ( -- arguments -- )
    //-------------------------------------------------------------------------------------------------------------
    // purpose:	 update template name
    // arguments:	 $template_id,$template_name"
    //-------------------------------------------------------------------------------------------------------------
    function update_template_name($template_id,$template_name,$findline="",$findline='')
    {
        $arguments = array($template_id,$template_name);
        $this->debug_obj->WriteDebug($class="TemplateFunctions.class", $function="update_template_name", $file=$findline['file'], $this->debug_obj->FindFunctionCalledline('update_template_name', $findline['file'], $findline['line']), $arguments);

        if(trim($template_name) == ''){//throw error if template name is empty
            header("location:editor.php?tid=".$template_id."&edit=yes&err=2&errid=3");
            exit;
        }
        elseif(strlen(trim($template_name)) >50){//throw error if template name is more than 50 characters
            header("location:editor.php?tid=".$template_id."&edit=yes&err=2&errid=4");
            exit;
        }
        else if(!preg_match("/^[A-Za-z0-9\-\#\.\_\!\@\$\%\^\(\)\/ ]+$/", $template_name)){
            header("location: editor.php?tid=".$template_id."&edit=yes&err=2&errid=6");
            exit;
        }

        if($_SESSION['merchant_id'] == "" && $_SESSION['admin_id'] != "")// to identify if templates are accessed by admin
        {//function for checking if ui template already exists.
            $debug = array('file'=>'templatefunctions.class.php', 'line'=>'updateadmintempcheck');
            $is_duplicate=$this->check_template_duplication(trim($template_name),$_SESSION['admin_id'],"",$debug);/*updateadmintempcheck*/
        }
        elseif($_SESSION['merchant_id'] != "" && $_SESSION['admin_id'] != "")// to identify if templates are accessed by admin acting as merchant
        {//function for checking if ui template already exists.
            $debug = array('file'=>'templatefunctions.class.php', 'line'=>'updatemerchanttempcheck');
            $is_duplicate=$this->check_template_duplication(trim($template_name),"",$_SESSION['merchant_id'],$debug);/*updatemerchanttempcheck*/
        }

        if($is_duplicate=="duplicate"){// throw error if template name is already available
            header("location:editor.php?tid=".$template_id."&edit=yes&err=2&errid=5");
            exit;
        }
        else{//update the changes in database
            $edit["tablename"] = "tbl_templates";
            $edit["fieldname"] = "p1te_name = '".$template_name."'";
            $edit["whereCon"] = 'p1te_id ='.$template_id;
            $debug = array('file'=>'ttemplatefunctions.class.php', 'line'=>'updatetemplate');
            $temp_update =$this->templates_obj->update_styles($edit,$debug);/*updatetemplate*/
            header("location:editor.php?tid=".$template_id."&edited=yes&err=2");
            exit;
        }
    }

    //-------------------------------------------------------------------------------------------------------------
    // function: add_new_template ( -- arguments -- )
    //-------------------------------------------------------------------------------------------------------------
    // purpose:	 add new template
    // arguments:	 $tname,$ref_id="",$currenttemplate
    //-------------------------------------------------------------------------------------------------------------
    function add_new_template($tname,$ref_id="",$currenttemplate,$sid,$type,$findline='')
    {
        $arguments = array($tname,$ref_id,$currenttemplate);
        $this->debug_obj->WriteDebug($class="TemplateFunctions.class", $function="add_new_template", $file=$findline['file'], $this->debug_obj->FindFunctionCalledline('add_new_template', $findline['file'], $findline['line']), $arguments);

        if($ref_id != "" && trim($tname) > 50) { //to generate a name for copy template
            $t=explode("_","$tname");
            $t[0]=substr("$t[0]",0,45);
            $tname=implode("_",$t);
        }

        if($type == '' && $ref_id != '') {
            $get_attr["tablename"]="tbl_templates";
            $get_attr["fieldname"]='p1te_device_flag';
            $get_attr["whereCon"]='p1te_id ='.$ref_id;
            $debug = array('file'=>'templatefunctions.class.php', 'line'=>'getalltempflag');
            $flag = $this->templates_obj->get_component($get_attr,$debug);/*getalltempflag*/
            $type=$flag[0][p1te_device_flag];
        }

        if(trim($tname) == '') { //throw error if template name is empty
            header("location: editor.php?err=3");
            exit;
        }
        elseif(strlen(trim($tname)) > 50) {//throw error if template name is more than 50 characters
            header("location: editor.php?err=4");
            exit;
        }
        else if(!preg_match("/^[A-Za-z0-9\-\#\.\_\!\@\$\%\^\(\)\/ ]+$/", $tname)){
            header("location: editor.php?err=6");
            exit;
        }

        if($_SESSION['merchant_id'] ==''){
            $admin_value = '1';
        }

        if($_SESSION['merchant_id'] == "" && $_SESSION['admin_id'] != ""){
            //Code for checking if ui template already exists
            $debug = array('file'=>'templatefunctions.class.php', 'line'=>'addadmintempcheck');
            $is_duplicate=$this->check_template_duplication(trim($tname),$admin_value,"",$debug);/*addadmintempcheck*/
        }
        elseif($_SESSION['merchant_id'] != "" && $_SESSION['admin_id'] != ""){
            //Code for checking if ui template already exists
            $debug = array('file'=>'templatefunctions.class.php', 'line'=>'addmerchanttempcheck');
            $is_duplicate=$this->check_template_duplication(trim($tname),"",$_SESSION['merchant_id'],$debug);/*addmerchanttempcheck*/
        }

        if($is_duplicate =='duplicate'){// throw error if template name is already available
            header("location: editor.php?err=5");
            exit;
        }
        else
        {   //update the changes in database
            $args["tablename"] = "tbl_templates";
            if(isset($_SESSION['merchant_id'])) {
                $args["fieldname"] = "p1me_id, p1te_name, p1te_description, p1te_stflag, p1te_added, p1te_device_flag";	
                $args["fieldval"] = "'".$_SESSION['merchant_id']."','".$tname."', 'Template Description Here !!!','1',now(),$type";
            }
            else {
                $args["fieldname"] = "p1ad_id, p1te_name, p1te_description, p1te_stflag, p1te_added, p1te_device_flag";
                $args["fieldval"] = "'".$admin_value."','".$tname."', 'Template Description Here !!!','1',now(),$type";
            }
            $debug = array('file'=>'templatefunctions.class.php', 'line'=>'add_copy_template');
            $template_arr = $this->templates_obj->template_insert($args,$debug);/*add_copy_template*/

            if($ref_id != '') {
                $templates_obj=$this->templates_obj;
                include_once(FULL_PATH.'admin/copypage.php');
            }
            else {
                $templates_obj=$this->templates_obj;
                include_once(FULL_PATH.'admin/defpage.php');
            }
        }
    }

    //-------------------------------------------------------------------------------------------------------------
    // function: get_template_components ( -- arguments -- )
    //-------------------------------------------------------------------------------------------------------------
    // purpose:	 get template components
    // arguments:	 $cid,$tid
    //-------------------------------------------------------------------------------------------------------------
    function get_template_components($cid,$tid,$findline='')
    {
        $arguments = array($cid,$tid);
        $this->debug_obj->WriteDebug($class="TemplateFunctions.class", $function="get_template_components", $file=$findline['file'], $this->debug_obj->FindFunctionCalledline('get_template_components', $findline['file'], $findline['line']), $arguments);

        $debug = array('file'=>'templatefunctions.class.php', 'line'=>'getcomponents');
        $allformfields = $this->templates_obj->get_component_attr('tbl_template_vars as var, tbl_template_attribs as att','var.p1tv_id,var.p1te_id,var.p1tv_name,var.p1tv_value,att.p1ta_id,att.p1tv_id,att.p1ta_title,att.p1ta_value','var.p1tv_id = att.p1tv_id and var.p1tv_id ='.$cid.' and var.p1te_id ='.$tid,$debug);/*getcomponents*/
        foreach( $allformfields as $key => $value) {
            //Get all form field's type
            $debug = array('file'=>'templatefunctions.class.php', 'line'=>'getcomponents_groupby');
            $allformtype = $this->templates_obj->join_group_by('tbl_form_fields as fld, tbl_field_types as typ','fld.p1ft_id as typ, fld.p1ff_id as field, fld.p1ff_caption as caption ',"fld.p1ff_caption = '".$allformfields[$key]["p1ta_title"]."' and fld.p1ft_id = typ.p1ft_id and fld.p1te_id  =".$allformfields[$key]['p1te_id'],'fld.p1ft_id',$debug);/*getcomponents_groupby*/
            $allformfields[$key]['field_type']=$allformtype[0]['typ'];
            $allformfields[$key]['field_id']=$allformtype[0]['field'];
            $allformfields[$key]['caption']=$allformtype[0]['caption'];
        }
        return $allformfields;
    }

    //-------------------------------------------------------------------------------------------------------------
    // function: update_template_css ( -- arguments -- )
    //-------------------------------------------------------------------------------------------------------------
    // purpose:	 update template css
    // arguments:	 $filename,$valu,$tid
    //-------------------------------------------------------------------------------------------------------------
    function update_template_css($filename,$value,$tid,$findline='')
    {
        $arguments = array($filename,$value);
        $this->debug_obj->WriteDebug($class="TemplateFunctions.class", $function="update_template_css", $file=$findline['file'], $this->debug_obj->FindFunctionCalledline('update_template_css', $findline['file'], $findline['line']), $arguments);

        if (file_exists($filename)) {   //check if file name exists
            $fh = fopen(FULL_PATH.'/data/styles/templates/template_'.$tid.'/merchant.css', 'w');
            $fl1 = FULL_PATH.'/data/styles/templates/template_'.$tid.'/merchant.css';
            chmod($fl1, 0777);
            fwrite($fh, $value);
        }
        else {  // create a new file
            mkdir(FULL_PATH."/data/styles/templates/template_$tid", 0777);
            $fh = fopen(FULL_PATH.'/data/styles/templates/template_'.$tid.'/merchant.css', 'w');
            chmod(FULL_PATH.'/data/styles/templates/template_'.$tid.'/merchant.css');
            fwrite($fh, $value);
        }
    }

    //-------------------------------------------------------------------------------------------------------------
    // function: update_template_style ( -- arguments -- )
    //-------------------------------------------------------------------------------------------------------------
    // purpose:	 update template style
    // arguments:	 $tid,$cid,$post_var,$all_components
    //-------------------------------------------------------------------------------------------------------------
    function update_template_style($tid,$cid,$post_var,$all_components,$findline='')
    {
        $arguments = array($tid,$cid,$post_var,$all_components);
        $this->debug_obj->WriteDebug($class="TemplateFunctions.class", $function="update_template_style", $file=$findline['file'], $this->debug_obj->FindFunctionCalledline('update_template_style', $findline['file'], $findline['line']), $arguments);

        foreach($post_var as $key => $value){
            $args["tablename"]="tbl_template_attribs";
            $args["fieldname"]="p1ta_value = '".addslashes($value)."' ";
            $args["whereCon"]="p1ta_title = '$key' and p1tv_id = '".$cid."'";
            $debug = array('file'=>'templatefunctions.class.php', 'line'=>'updatestyles');
            $template_update =$this->templates_obj->update_styles($args,$debug);/*updatestyles*/
        }
        foreach( $all_components as $key => $value){
            $args = '';
            $args["tablename"]="tbl_template_attribs";
            $args["whereCon"]= "p1tv_id = ".$value['p1tv_id'];
            $debug = array('file'=>'templatefunctions.class.php', 'line'=>'getall_updatestyles');
            $listallattributes = $this->templates_obj->get_all($args,$debug);/*getall_updatestyles*/
            $valu .= $value['p1tv_value'];
            $valu .= " { ";
            foreach( $listallattributes as $key => $style){
                $valu .= $style['p1ta_title'];
                $valu .= ": ";
                $valu .= $style['p1ta_value'];
                $valu .= "; ";
            }
            $valu .= " }\n";
        }
        $filename = (FULL_PATH."/data/styles/templates/template_".$tid);
        $debug = array('file'=>'templatefunctions.class.php', 'line'=>'updatecss');
        $this->update_template_css($filename,$valu,$tid,$debug);/*updatecss*/
        header("location:editor.php?option=styles&cid=$cid&tid=$tid&err=1");
        exit;
    }

    //-------------------------------------------------------------------------------------------------------------
    // function: get_home_lables_template ( -- arguments -- )
    //-------------------------------------------------------------------------------------------------------------
    // purpose:	 get home lables template
    // arguments:	 $tid
    //-------------------------------------------------------------------------------------------------------------
    function get_home_lables_template($tid, $colg_id, $findline='')
    {
        $arguments = array($tid);
        $this->debug_obj->WriteDebug($class="TemplateFunctions.class", $function="get_home_lables_template", $file=$findline['file'], $this->debug_obj->FindFunctionCalledline('get_home_lables_template', $findline['file'], $findline['line']), $arguments);

        $args["tablename"]="tbl_template_captions";
        $args["whereCon"]="p1te_id =".$tid." and p1tc_type =1 and p1colg_id = ".$colg_id;
        $debug = array('file'=>'templatefunctions.class.php', 'line'=>'getall_homelables');
        $return_var['allcaptions'] = $this->templates_obj->get_all($args,$debug);/*getall_homelables*/

        if(count($return_var['allcaptions']) == 0) {
            header("location:index.php?page=templates");
            exit;
        }

        foreach($return_var['allcaptions'] as $caps){
            $list_det[$caps['p1tc_title']]=$caps['p1tc_id'];
        }

        if($_SESSION['d_type'] == "smart")
        {
            $return_var['defaultcaps'] = array(
                'Home Phone' => array(
                    $list_det['Select an item:']=>'Select an item:',
                    $list_det['Home Phone Number']=>'Home Phone Number',
                    $list_det['Please enter your home phone number']=>'Please enter your home phone number',
                    $list_det['Continue']=>'Continue',
                    $list_det['Remember My Number']=>'Remember My Number',
                    $list_det['Private and secure payment']=>'Private and secure payment',
                    $list_det['Verifying phone number.']=>'Verifying phone number.',
                    $list_det['I am confirming the following purchase:']=>'I am confirming the following purchase:',
                    $list_det['Item:']=>'Item:',
                    $list_det['Price:']=>'Price:',
                    $list_det['Billing:']=>'Billing:',
                    $list_det['Confirm']=>'Confirm',
                    $list_det['Display Home Number']=>'Display Home Number',
                    $list_det['Done']=>'Done'
                ),
                'IVS' => array(
                    $list_det['Sorry, we can not bill the home phone number [phonenumber] you provided. Please try again.']=>'Sorry, we can not bill the home phone number [phonenumber] you provided. Please try again.',
                    $list_det['Sorry, we are unable to verify the information you provided. Please try our mobile phone number option.']=>'Sorry, we are unable to verify the information you provided. Please try our mobile phone number option.',
                    $list_det['To complete your transaction, please provide the following details.']=>'To complete your transaction, please provide the following details.',
                    $list_det['First Name']=>'First Name',
                    $list_det['Last Name']=>'Last Name',
                    $list_det['Address']=>'Address',
                    $list_det['Zip Code']=>'Zip Code',
                    $list_det['Month/Year of Birth']=>'Month/Year of Birth',
                    $list_det['I Agree to Terms of Service']=>'I Agree to Terms of Service',
                    $list_det['Terms of Service']=>'Terms of Service',
                    $list_det['Terms Content (QWEST)']=>'Terms Content (QWEST)',
                    $list_det['Terms Content (NON QWEST)']=>'Terms Content (NON QWEST)',
                    $list_det['Authorization.']=>'Authorization',
                    $list_det['Processing order.']=>'Processing order.'
                ),
                'IVR' => array(
                    $list_det['City']=>'City',
                    $list_det['State']=>'State',
                    $list_det['IVR (Please Call)']=>'IVR (Please Call)',
                    $list_det['IVR (Follow Instructions)']=>'IVR (Follow Instructions)',
                    $list_det['Time remaining to call']=>'Time remaining to call'
                ),
                'Success' => array(
                    $list_det['Your purchase was successfully completed.']=>'Your purchase was successfully completed.',
                    $list_det['Post Sale Disclosure Notice']=>'Post Sale Disclosure Notice',
                    $list_det['Post Sale Disclosure Notice for AT&T Customers']=>'Post Sale Disclosure Notice for AT&T Customers',
                    $list_det['Post Sale Disclosure Notice for Qwest Customers']=>'Post Sale Disclosure Notice for Qwest Customers',
                    $list_det['Post Sale Disclosure Notice for state of Minnesota only']=>'Post Sale Disclosure Notice for state of Minnesota only'
                ),
                'Failure' => array(
                    $list_det['Transaction Cancelled.']=>'Transaction Cancelled.',
                    $list_det['Sorry, this transaction has been cancelled as you have not completed the transaction within the 10 minute time window.']=>'Sorry, this transaction has been cancelled as you have not completed the transaction within the 10 minute time window.'
                )
            );
        }
        else
        {
            $return_var['defaultcaps'] = array(
                'Header' => array(
                    $list_det['How it Works?']=>'How it Works?'
                ),
                'Home Phone' => array(
                    $list_det['Select an item:']=>'Select an item:',
                    $list_det['Home Phone Number']=>'Home Phone Number',
                    $list_det['Please enter your home phone number']=>'Please enter your home phone number',
                    $list_det['Continue']=>'Continue',
                    $list_det['Remember My Number']=>'Remember My Number',
                    $list_det['Private and secure payment']=>'Private and secure payment',
                    $list_det['Verifying phone number.']=>'Verifying phone number.',
                    $list_det['Pay with your Home Phone']=>'Pay with your Home Phone',
                    $list_det['I am confirming the following purchase:']=>'I am confirming the following purchase:',
                    $list_det['Item:']=>'Item:',
                    $list_det['Price:']=>'Price:',
                    $list_det['Billing:']=>'Billing:',
                    $list_det['Confirm']=>'Confirm',
                    $list_det['Display Home Number']=>'Display Home Number',
                    $list_det['Done']=>'Done'
                ),
                'IVS' => array(
                    $list_det['Sorry, we can not bill the home phone number [phonenumber] you provided. Please try again.']=>'Sorry, we can not bill the home phone number [phonenumber] you provided. Please try again.',
                    $list_det['Sorry, we are unable to verify the information you provided. Please try our mobile phone number option.']=>'Sorry, we are unable to verify the information you provided. Please try our mobile phone number option.',
                    $list_det['To complete your transaction, please provide the following details.']=>'To complete your transaction, please provide the following details.',
                    $list_det['First Name']=>'First Name',
                    $list_det['Last Name']=>'Last Name',
                    $list_det['Address']=>'Address',
                    $list_det['Zip Code']=>'Zip Code',
                    $list_det['Month/Year of Birth']=>'Month/Year of Birth',
                    $list_det['I Agree to Terms of Service']=>'I Agree to Terms of Service',
                    $list_det['Terms of Service']=>'Terms of Service',
                    $list_det['Terms Content (QWEST)']=>'Terms Content (QWEST)',
                    $list_det['Terms Content (NON QWEST)']=>'Terms Content (NON QWEST)',
                    $list_det['Authorization.']=>'Authorization',
                    $list_det['Processing order.']=>'Processing order.',
                    $list_det['Invalid Zip Code']=>'Invalid Zip Code'
                ),
                'IVR' => array(
                    $list_det['City']=>'City',
                    $list_det['State']=>'State',
                    $list_det['IVR (Please Call)']=>'IVR (Please Call)',
                    $list_det['IVR (Follow Instructions)']=>'IVR (Follow Instructions)',
                    $list_det['Time remaining to call']=>'Time remaining to call',
                    $list_det['IVR Per Call (Please Call)']=>'IVR Per Call (Please Call)',
                    $list_det['IVR Per Call (Time remaining to call)']=>'IVR Per Call (Time remaining to call)',
                    $list_det['IVR Per Call (Follow Instructions)']=>'IVR Per Call (Follow Instructions)',
                    $list_det['IVR Per Minute (Please Call)']=>'IVR Per Minute (Please Call)',
                    $list_det['IVR Per Minute (Follow Instructions)']=>'IVR Per Minute (Follow Instructions)',
                    $list_det['IVR Per Minute (Confirmation message)']=>'IVR Per Minute (Confirmation message)'
                    //$list_det['Legal Disclaimer']=>'Legal Disclaimer'
                ),
                'Success' => array(
                    $list_det['Purchase Confirmed']=>'Purchase Confirmed',
                    $list_det['Your purchase was successfully completed.']=>'Your purchase was successfully completed.',
                    $list_det['Post Sale Disclosure Notice']=>'Post Sale Disclosure Notice',
                    $list_det['Post Sale Disclosure Notice for AT&T Customers']=>'Post Sale Disclosure Notice for AT&T Customers',
                    $list_det['Post Sale Disclosure Notice for Qwest Customers']=>'Post Sale Disclosure Notice for Qwest Customers',
                    $list_det['Post Sale Disclosure Notice for state of Minnesota only']=>'Post Sale Disclosure Notice for state of Minnesota only'
                ),
                'Failure' => array(
                    $list_det['Transaction Cancelled.']=>'Transaction Cancelled.',
                    $list_det['Sorry, this transaction has been cancelled as you have not completed the transaction within the 10 minute time window.']=>'Sorry, this transaction has been cancelled as you have not completed the transaction within the 10 minute time window.',
                    $list_det['Product Not Supported']=>'Product Not Supported',
                    $list_det['Products not available.']=>'Products not available.'
                )
            );
        }
        return $return_var;
    }

    //-------------------------------------------------------------------------------------------------------------
    // function: get_mobile_lables_template ( -- arguments -- )
    //-------------------------------------------------------------------------------------------------------------
    // purpose:	 get mobile lables template
    // arguments:	 $tid
    //-------------------------------------------------------------------------------------------------------------
    function get_mobile_lables_template($tid, $colg_id, $findline='')
    {
        $arguments = array($tid);
        $this->debug_obj->WriteDebug($class="TemplateFunctions.class", $function="get_mobile_lables_template", $file=$findline['file'], $this->debug_obj->FindFunctionCalledline('get_mobile_lables_template', $findline['file'], $findline['line']), $arguments);	

        $args["tablename"]="tbl_template_captions";
        $args["whereCon"]="p1te_id =".$tid." and p1tc_type = 2 and p1colg_id = ".$colg_id;
        $debug = array('file'=>'templatefunctions.class.php', 'line'=>'getall_mobilelables');
        $return_var['allcaptions'] = $this->templates_obj->get_all($args,$debug);/*getall_mobilelables*/

        if(count($return_var['allcaptions']) == 0) {
            header("location:index.php?page=templates");
            exit;
        }

        foreach($return_var['allcaptions'] as $caps){
            $list_det[$caps['p1tc_title']]=$caps['p1tc_id'];
        }

        if($_SESSION['d_type'] == "smart")
        {
            $return_var['defaultcaps'] = array(
                'Mobile Phone' => array(
                    $list_det['Select an item:']=>'Select an item:',
                    $list_det['Please enter your mobile number']=>'Please enter your mobile number',
                    $list_det['Mobile Number']=>'Mobile Number',
                    $list_det['Continue']=>'Continue',
                    $list_det['Remember My Number']=>'Remember My Number',
                    $list_det['Private and secure payment']=>'Private and secure payment',
                    $list_det['Legal Disclaimer']=>'Legal Disclaimer',
                    $list_det['Sending PIN Code to your phone']=>'Sending PIN Code to your phone',
                    $list_det['Display Mobile Number']=>'Display Mobile Number',
                    $list_det['A text message containing a PIN code was sent to your phone.']=>'A text message containing a PIN code was sent to your phone.',
                    $list_det['Please Enter PIN Code']=>'Please Enter PIN Code',
                    $list_det['Completing Transaction']=>'Completing Transaction',
                    $list_det['Processing Order']=>'Processing Order',
                    $list_det['Done']=>'Done'
                ),
                'Success' => array(
                    $list_det['Your purchase was successfully completed.']=>'Your purchase was successfully completed.',
                    $list_det['Success, Please check your merchant website within 24 hours.']=>'Success, Please check your merchant website within 24 hours.'
                ),
                'Failure' => array(
                    $list_det['Transaction Cancelled.']=>'Transaction Cancelled.',
                    $list_det['Sorry, there was a problem with your purchase.']=>'Sorry, there was a problem with your purchase.',
                    $list_det['Exceeded PIN number entry attempts']=>'Exceeded PIN number entry attempts'
                )
            );
        }
        else
        {
            $return_var['defaultcaps'] = array(
                'Header' => array(
                    $list_det['How it Works?']=>'How it Works?'
                ),
                'Mobile Phone' => array(
                    $list_det['Select an item:']=>'Select an item:',
                    $list_det['Please enter your mobile number']=>'Please enter your mobile number',
                    $list_det['Mobile Number']=>'Mobile Number',
                    $list_det['Continue']=>'Continue',
                    $list_det['Remember My Number']=>'Remember My Number',
                    $list_det['Private and secure payment']=>'Private and secure payment',
                    $list_det['Legal Disclaimer']=>'Legal Disclaimer',
                    $list_det['Pay with your Mobile Phone']=>'Pay with your Mobile Phone',
                    $list_det['Sending PIN Code to your phone']=>'Sending PIN Code to your phone',
                    $list_det['Verifying phone number.']=>'Verifying phone number.',
                    $list_det['Display Mobile Number']=>'Display Mobile Number',
                    $list_det['A text message containing a PIN code was sent to your phone.']=>'A text message containing a PIN code was sent to your phone.',
                    $list_det['Please Enter PIN Code']=>'Please Enter PIN Code',
                    $list_det['Completing Transaction']=>'Completing Transaction',
                    $list_det['Processing Order']=>'Processing Order',
                    $list_det['MO Method']=>'MO Method',
                    $list_det['MT Method']=>'MT Method',
                    $list_det['Time remaining to send SMS']=>'Time remaining to send SMS',
                    $list_det['MO/MT failure to iLEC']=>'MO/MT failure to iLEC',
                    $list_det['Try again']=>'Try again',
                    $list_det['Try Web Billing']=>'Try Web Billing',
                    $list_det['Done']=>'Done'
                ),
                'Success' => array(
                    $list_det['Purchase Confirmed']=>'Purchase Confirmed',
                    $list_det['Your purchase was successfully completed.']=>'Your purchase was successfully completed.',
                    $list_det['Success, Please check your merchant website within 24 hours.']=>'Success, Please check your merchant website within 24 hours.'
                ),
                'Failure' => array(
                    $list_det['Transaction Cancelled.']=>'Transaction Cancelled.',
                    $list_det['Sorry, there was a problem with your purchase.']=>'Sorry, there was a problem with your purchase.',
                    $list_det['Exceeded PIN number entry attempts']=>'Exceeded PIN number entry attempts',
                    $list_det['Carrier Not Supported']=>'Carrier Not Supported',
                    $list_det['Product Not Supported']=>'Product Not Supported',
                    $list_det['Operator ID Mismatch']=>'Operator ID Mismatch',
                    $list_det['Products not available.']=>'Products not available.'
                )
            );
        }
        return $return_var;
    }

    //-------------------------------------------------------------------------------------------------------------
    // function: get_anyphone_lables_template ( -- arguments -- )
    //-------------------------------------------------------------------------------------------------------------
    // purpose:	 get anyphone lables template
    // arguments:	 $tid
    //-------------------------------------------------------------------------------------------------------------
    function get_anyphone_lables_template($tid, $colg_id, $findline='')
    {
        $arguments = array($tid);
        $this->debug_obj->WriteDebug($class="TemplateFunctions.class", $function="get_anyphone_lables_template", $file=$findline['file'], $this->debug_obj->FindFunctionCalledline('get_anyphone_lables_template', $findline['file'], $findline['line']), $arguments);

        $args["tablename"]="tbl_template_captions";
        $args["whereCon"]="p1te_id =".$tid." and p1tc_type = 3 and p1colg_id = ".$colg_id;
        $debug = array('file'=>'templatefunctions.class.php', 'line'=>'getall_homelables');
        $return_var['allcaptions'] = $this->templates_obj->get_all($args,$debug);/*getall_homelables*/

         if(count($return_var['allcaptions']) == 0) {
            header("location:index.php?page=templates");
            exit;
        }

        foreach($return_var['allcaptions'] as $caps){
            $list_det[$caps['p1tc_title']]=$caps['p1tc_id'];
        }

        if($_SESSION['d_type'] == "smart")
        {
            $return_var['defaultcaps'] = array(
                'Anyphone'=>array(
                    $list_det['Pay with any phone']=>'Pay with any phone',
                    $list_det['Please enter your mobile or home number']=>'Please enter your mobile or home number',
                    $list_det['Phone number']=>'Phone number',
                    $list_det['Remember My Number']=>'Remember My Number',
                    $list_det['Continue']=>'Continue',
                    $list_det['Private and secure payment']=>'Private and secure payment',
                    $list_det['Legal Disclaimer']=>'Legal Disclaimer',
                    $list_det['Phone number verification']=>'Phone number verification',
                    $list_det['Select a Method to Pay']=>'Select a Method to Pay',
                    $list_det['Pay With Your Home Phone']=>'Pay With Your Home Phone',
                    $list_det['Pay With Your Mobile Phone']=>'Pay With Your Mobile Phone'
                ),
                'Failure' => array(
                    $list_det['Error number not recognized']=>'Error number not recognized',
                    $list_det['Error transaction could not be processed']=>'Error transaction could not be processed'
                )
            );
        }
        else
        {
            $return_var['defaultcaps'] = array(
                'Header' => array(
                    $list_det['How it Works?']=>'How it Works?'
                ),
                'Anyphone'=>array(
                    $list_det['Pay with any phone']=>'Pay with any phone',
                    $list_det['Please enter your mobile or home number']=>'Please enter your mobile or home number',
                    $list_det['Phone number']=>'Phone number',
                    $list_det['Remember My Number']=>'Remember My Number',
                    $list_det['Continue']=>'Continue',
                    $list_det['Private and secure payment']=>'Private and secure payment',
                    $list_det['Legal Disclaimer']=>'Legal Disclaimer',
                    $list_det['Phone number verification']=>'Phone number verification',
                    $list_det['Select a Method to Pay']=>'Select a Method to Pay',
                    $list_det['Pay With Your Home Phone']=>'Pay With Your Home Phone',
                    $list_det['Pay With Your Mobile Phone']=>'Pay With Your Mobile Phone',
                    $list_det['Select an item:']=>'Select an item:',
                    $list_det['Processing Order']=>'Processing Order',
                    $list_det['Product Not Supported']=>'Product Not Supported',
                    $list_det['Products not available.']=>'Products not available.'
                ),
                'Failure' => array(
                    $list_det['Error number not recognized']=>'Error number not recognized',
                    $list_det['Error transaction could not be processed']=>'Error transaction could not be processed'
                )
            );
        }
        return $return_var;
    }

    //-------------------------------------------------------------------------------------------------------------
    // function: restore_default_lables_values ( -- arguments -- )
    //-------------------------------------------------------------------------------------------------------------
    // purpose:	 restore default lables values
    // arguments:	 $lid,$tid,$currenttemplate,$getcaps,$defaultcapids,$sub
    //-------------------------------------------------------------------------------------------------------------
    function restore_default_lables_values($lid,$tid,$currenttemplate,$getcaps,$def_value,$sub,$coun_lang_id,$findline='')
    {
        global $validator_obj;

        $arguments = array($lid,$tid,$currenttemplate,$getcaps,$defaultcapids,$sub);
        $this->debug_obj->WriteDebug($class="TemplateFunctions.class", $function="restore_default_lables_values", $file=$findline['file'], $this->debug_obj->FindFunctionCalledline('restore_default_lables_values', $findline['file'], $findline['line']), $arguments);

        $def_value = trim($def_value[0]['p1tc_value']);
        $def_value = $validator_obj->escape_string($def_value);
        $args["tablename"] = "tbl_template_captions";
        $args["fieldname"] = "p1tc_value = '".addslashes($def_value)."'";
        $args["whereCon"] = 'p1te_id ='.$tid." and p1tc_id = '".$lid."'";
        $debug = array('file'=>'templatefunctions.class.php', 'line'=>'updatestyle_restorelabel');
        $template_update =$this->templates_obj->update_styles($args,$debug);/*updatestyle_restorelabel*/
        $debug = array('file'=>'templatefunctions.class.php', 'line'=>'generatexml');
        $this->generate_label_xml($tid,$currenttemplate,$coun_lang_id,$debug);/*generatexml*/
        header("location:editor.php?option=labels&lid=$lid&tid=$tid&err=2&sub=$sub#$lid");
        exit;
    }

    //-------------------------------------------------------------------------------------------------------------
    // function: generate_label_xml( -- arguments -- )
    //-------------------------------------------------------------------------------------------------------------
    // purpose:	 generate label xml
    // arguments:	$tid,$currenttemplate
    //-------------------------------------------------------------------------------------------------------------
    function generate_label_xml($tid, $currenttemplate, $coun_lang_id, $findline='')
    {
        $arguments = array($tid,$currenttemplate,$coun_lang_id);
        $this->debug_obj->WriteDebug($class="TemplateFunctions.class", $function="generate_label_xml", $file=$findline['file'], $this->debug_obj->FindFunctionCalledline('generate_label_xml', $findline['file'], $findline['line']), $arguments);

        //Get all home captions.
        $args["tablename"]="tbl_template_captions";
        $args["whereCon"]='p1te_id ='.$tid.' and p1tc_type = 1 and p1colg_id = '.$coun_lang_id.'  order by  p1tc_id asc';
        $debug = array('file'=>'templatefunctions.class.php', 'line'=>'gethomelabels');
        $homeallcaptions = $this->templates_obj->get_all($args,$debug);/*gethomelabels*/

        //Get all mobile captions
        $args1["tablename"]="tbl_template_captions";
        $args1["whereCon"]='p1te_id ='.$tid.' and p1tc_type = 2 and p1colg_id = '.$coun_lang_id.'  order by  p1tc_id asc';
        $debug = array('file'=>'templatefunctions.class.php', 'line'=>'getmobilelabels');
        $moballcaptions = $this->templates_obj->get_all($args1,$debug);/*getmobilelabels*/

        //Get all anyphone captions
        $args1["tablename"]="tbl_template_captions";
        $args1["whereCon"]='p1te_id ='.$tid.' and p1tc_type = 3 and p1colg_id = '.$coun_lang_id.'  order by  p1tc_id asc';
        $debug = array('file'=>'templatefunctions.class.php', 'line'=>'getanyphonelabels');
        $anyphonecaptions = $this->templates_obj->get_all($args1,$debug);/*getanyphonelabels*/

        $dom = new DOMDocument("1.0");//Coding for xml file creation
        $dom->encoding = "utf-8";
        $gendetails = $dom->createComment('Generated by PaymentOne.com');// Create a comment
        $dom->appendChild($gendetails);
        //$tempdetails = $dom->createComment('label details for '.$currenttemplate->get_Name().' template');
        $tempdetails = $dom->createComment('label details for PaymentOne Template');
        $dom->appendChild($tempdetails);

        $root1 = $dom->createElement("PaymentOne");
        $attrb = $dom->createAttribute("country_code");// create country node
        $root1->appendChild($attrb);
        $value = $dom->createTextNode($_SESSION['temp_country_code']);
        $attrb->appendChild($value);
        $attrb = $dom->createAttribute("language_code");// create language node
        $root1->appendChild($attrb);
        $value = $dom->createTextNode($_SESSION['temp_language_code']);
        $attrb->appendChild($value);
        $dom->appendChild($root1);
        $dom->formatOutput=true;

        $root = $dom->createElement("Home");
        $dom->appendChild($root);
        $dom->formatOutput=true;
        $root1->appendChild( $root );
        foreach( $homeallcaptions as $hlabels ) {
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

        $root = $dom->createElement("Mobile");
        $dom->appendChild($root);
        $dom->formatOutput=true;
        $root1->appendChild( $root );
        foreach( $moballcaptions as $mlabels ) {
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

        $root = $dom->createElement("Anyphone");
        $dom->appendChild($root);
        $dom->formatOutput=true;
        $root1->appendChild( $root );
        foreach( $anyphonecaptions as $anylabels ) {
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

        $filename = "../data/xml/templates/template_".$tid."/";
        $country_path = $filename.$_SESSION['temp_country_code']."/";
        $template_path = $country_path.'labels_'.$_SESSION['temp_language_code'].'.xml';
        if(file_exists($country_path)) {
            chmod($country_path, 0777);
            if(file_exists($template_path)){
                $dom->save($template_path);// save tree to file
                chmod($template_path, 0777);
            }
            else {
                fopen($template_path, "w");
                chmod($template_path, 0777);
                $dom->save($template_path);// save tree to file
                chmod($template_path, 0777);
            }
        }
        else {
            mkdir($country_path, 0777);
            chmod($country_path, 0777);
            fopen($template_path, "w");
            chmod($template_path, 0777);
            $dom->save($template_path);// save tree to file
            chmod($template_path, 0777);
        }
    }

    //-------------------------------------------------------------------------------------------------------------
    // function: get services for template ( -- arguments -- )
    //-------------------------------------------------------------------------------------------------------------
    // purpose:	get services for template
    // arguments:	$merchantid
    //-------------------------------------------------------------------------------------------------------------
    function get_services_for_template($merchantid,$fileline='')
    {
        $arguments = array($merchantid);
        $this->debug_obj->WriteDebug($class="TemplateFunctions.class", $function="get_services_for_template", $file=$findline['file'], $this->debug_obj->FindFunctionCalledline('get_services_for_template', $findline['file'], $findline['line']), $arguments);

        $get_cats["tablename"] = "tbl_categories";
        $get_cats["fieldname"] = "p1ca_id, p1ca_name";
        $get_cats["whereCon"] = "p1me_id = ".$merchantid." or p1me_id IS NULL and p1ca_stflag = 1";
        $debug = array('file'=>'templatefunctions.class.php', 'line'=>'catservice_cat');
        $cat_services = $this->templates_obj->get_category_services($get_cats,$debug);/*catservice_cat*/
        foreach( $cat_services as $key => $val){
            $get_services["tablename"] = "tbl_merchant_services";
            $get_services["fieldname"] = "p1ms_id, p1ms_name, p1te_id, p1te_spid";
            $get_services["whereCon"] = "p1ca_id = ".$val['p1ca_id']." and p1me_id = ".$merchantid." and p1ms_stflag !=0 and p1ms_stflag !=3";
            $debug = array('file'=>'templatefunctions.class.php', 'line'=>'catservice_merchant');
            $cat_services[$key][] = $this->templates_obj->get_category_services($get_services,$debug);/*catservice_merchant*/
        }
        $return_var['cat_services']=$cat_services;
        $get_service["tablename"] = "tbl_merchant_services";
        $get_service["fieldname"] = "p1ms_id, p1ms_name, p1te_id";
        $get_service["whereCon"] = "p1me_id = ".$_SESSION['merchant_id']." and p1ms_stflag !=0 and p1ms_stflag !=3";
        $debug = array('file'=>'templatefunctions.class.php', 'line'=>'catservice_merchant_1');
        $return_var['services']= $this->templates_obj->get_category_services($get_service,$debug);/*catservice_merchant_1*/
        return $return_var;
    }

    //-------------------------------------------------------------------------------------------------------------
    // function: get_template_count ( -- arguments -- )
    //-------------------------------------------------------------------------------------------------------------
    // purpose:	get template count
    // arguments:	$type,$merchantid=''
    //-------------------------------------------------------------------------------------------------------------
    function get_template_count($type,$merchantid='',$findline='')
    {
        $arguments = array($type,$merchantid);
        $this->debug_obj->WriteDebug($class="TemplateFunctions.class", $function="get_template_count", $file=$findline['file'], $this->debug_obj->FindFunctionCalledline('get_template_count', $findline['file'], $findline['line']), $arguments);

        if($_SESSION['d_type']=="smart"){
            $d_where="and p1te_device_flag =2";
        }
        else{
            $d_where="and p1te_device_flag =1";
        }

        if($type="admin"){
            $args["whereCon"]="p1ad_id = 1 and p1me_id IS NULL and p1te_stflag = 1 $d_where order by p1te_added desc";
            $args["start_lim"] = 0;
            $args["lim"] = 0;
            $debug = array('file'=>'templatefunctions.class.php', 'line'=>'templatelist_admin');
            $tmpslistcnt = $this->templates_obj->get_all_templatelist($args,$debug);/*templatelist_admin*/
        }
        else if ($type="merchant"){
            $args["whereCon"]="p1me_id = $merchantid and p1te_stflag = 1 $d_where order by p1te_added desc";
            $args["start_lim"] = 0;
            $args["lim"] = 0;
            $debug = array('file'=>'templatefunctions.class.php', 'line'=>'templatelist_merchant');
            $tmpslistcnt = $this->templates_obj->get_all_templatelist($args,$debug);/*templatelist_merchant*/
        }
        return count($tmpslistcnt);
    }

    //-------------------------------------------------------------------------------------------------------------
    // function: apply_service_to_template ( -- arguments -- )
    //-------------------------------------------------------------------------------------------------------------
    // purpose:	apply service to template
    // arguments:	$request_data,$tid
    //-------------------------------------------------------------------------------------------------------------
    function apply_service_to_template($request_data,$tid,$findline='')
    {
        $arguments = array($request_data,$tid);
        $this->debug_obj->WriteDebug($class="TemplateFunctions.class", $function="apply_service_to_template", $file=$findline['file'], $this->debug_obj->FindFunctionCalledline('apply_service_to_template', $findline['file'], $findline['line']), $arguments);

        $update_count=0;
        $args1["tablename"] = "tbl_merchant_services";
        $args1["fieldname"] = "p1ms_id,p1te_id,p1te_spid";
        $args1["whereCon"] = " p1me_id = ".$_SESSION['merchant_id'];
        $debug = array('file'=>'templatefunctions.class.php', 'line'=>'cat_applyservice');
        $services = $this->templates_obj->get_category_services($args1,$debug);/*cat_applyservice*/
        foreach( $services  as $k => $v)
        {
            if(($v['p1te_id'] == "$tid" && $_SESSION['d_type']=="desktop") || ($v['p1te_spid'] == "$tid" && $_SESSION['d_type']=="smart"))
            {
                $flag='1';
                foreach( $request_data  as $key => $val){
                    if($v['p1ms_id'] == "$key"){
                        $flag='2';
                        break;
                    }
                }
                if($flag=='1'){
                    $app["tablename"] = "tbl_merchant_services";
                    if($_SESSION['d_type']=="smart"){
                        $sp_tid=$this->get_smartphone_default_id();
                        $app["fieldname"] = "p1te_spid = '$sp_tid'";
                    }
                    else{
                        $app["fieldname"] = "p1te_id = 1";
                    }
                    $app["whereCon"] = 'p1ms_id ='.$v['p1ms_id'];
                    $debug = array('file'=>'templatefunctions.class.php', 'line'=>'updatestyles_applyservice');
                    $services_update =$this->templates_obj->update_styles($app,$debug);/*updatestyles_applyservice*/
                    $update_count++;
                    $flag='1';
                }
            }
            else
            {
                foreach( $request_data  as $key => $val){
                    if($v['p1ms_id'] == "$key"){
                        $app["tablename"] = "tbl_merchant_services";
                        if($_SESSION['d_type']=="smart"){
                            $app["fieldname"] = "p1te_spid = '$tid'";
                        }
                        else{
                            $app["fieldname"] = "p1te_id = '$tid'";
                        }
                        $app["whereCon"] = 'p1ms_id ='.$v['p1ms_id'];
                        $debug = array('file'=>'templatefunctions.class.php', 'line'=>'updatestyles_applyservice_tid');
                        $services_update =$this->templates_obj->update_styles($app,$debug);/*updatestyles_applyservice_tid*/
                        $update_count++;
                    }
                }
            }
        }
        if($update_count>0){
            return "updated";
        }
        else{
            return "no updates";
        }
    }

    //-------------------------------------------------------------------------------------------------------------
    // function: delete_admin_template ( -- arguments -- )
    //-------------------------------------------------------------------------------------------------------------
    // purpose:	delete admin template
    // arguments:	$tid
    //-------------------------------------------------------------------------------------------------------------
    function delete_admin_template($tid,$findline='')
    {
        $arguments = array($tid);
        $this->debug_obj->WriteDebug($class="TemplateFunctions.class", $function="delete_admin_template", $file=$findline['file'], $this->debug_obj->FindFunctionCalledline('delete_admin_template', $findline['file'], $findline['line']), $arguments);

        if($_SESSION['d_type']=="smart"){
            $page_qs="&device=smart";
        }
        else{
            $page_qs="&device=desktop";
        }

        //Code for merchants checking while deleting
        $args1["tablename"] = "tbl_templates";
        $args1["fieldname"] = "p1te_refid";
        $args1["whereCon"] = "p1te_id = ".$_REQUEST['tid'];
        $debug = array('file'=>'templatefunctions.class.php', 'line'=>'catservice_admindelete');
        $addedlist = $this->templates_obj->get_category_services($args1,$debug);/*catservice_admindelete*/
        if($addedlist[0]['p1te_refid'] == '0' ||  $addedlist[0]['p1te_refid'] == ''){
            $del["tablename"] = "tbl_templates";
            $del["fieldname"] = "p1te_stflag = 0";
            $del["whereCon"] = 'p1te_id ='.$_REQUEST['tid'];
            $debug = array('file'=>'templatefunctions.class.php', 'line'=>'updatestyles_admindelete');
            $temp_update =$this->templates_obj->update_styles($del,$debug);/*updatestyles_admindelete*/
            $debug = array('file'=>'templatefunctions.class.php', 'line'=>'remove_template_files');
            $this->remove_template_files($tid);/*remove_template_files*/
            header("location:index.php?page=templates&err=1$page_qs&did=".$tid);
        }
        else{
            header("location:index.php?page=templates&err=4$page_qs&did=".$tid);
        }
    }

    //-------------------------------------------------------------------------------------------------------------
    // function: merchant_delete_template ( -- arguments -- )
    //-------------------------------------------------------------------------------------------------------------
    // purpose:	merchant delete template
    // arguments:	$tid
    //-------------------------------------------------------------------------------------------------------------
    function merchant_delete_template($tid, $sid='', $findline="")
    {
        $arguments = array($tid);
        $this->debug_obj->WriteDebug($class="TemplateFunctions.class", $function="merchant_delete_template", $file=$findline['file'], $this->debug_obj->FindFunctionCalledline('merchant_delete_template', $findline['file'], $findline['line']), $arguments);

        if($_SESSION['d_type']=="smart") {
            $page_qs = "&device=smart";
        }
        else {
            $page_qs = "&device=desktop";
        }

        //Code for checking if service is assigned to the selected template
        $args1["tablename"] = "tbl_merchant_services";
        $args1["fieldname"] = "p1ms_id";
        $args1["whereCon"] = "p1te_id = ".$tid." and p1ms_stflag = 1";
        $debug = array('file'=>'templatefunctions.class.php', 'line'=>'catservice_merchantdelete');
        $addedlist = $this->templates_obj->get_category_services($args1,$debug);/*catservice_merchantdelete*/
        //to update the service table to diplsy the service at top in the list
        if($sid != '') {
            $args3["tablename"] = "tbl_merchant_services";
            $args3["whereCon"] = " p1ms_id = ".$sid;
            $args3["fieldname"] = 'p1ms_updated=now()';
            $debug = array('file'=>'templatefunctions.class.php', 'line'=>'updateservice_position');
            $service_update =$this->templates_obj->update_styles($args3,$debug);/*updateservice_position*/
        }
        if(count($addedlist) == 0) {
            $del["tablename"] = "tbl_templates";
            $del["fieldname"] = "p1te_stflag = 0";
            $del["whereCon"] = 'p1te_id ='.$tid;
            $debug = array('file'=>'templatefunctions.class.php', 'line'=>'updatestyles_admindelete');
            $temp_update =$this->templates_obj->update_styles($del,$debug);/*updatestyles_admindelete*/
            $debug = array('file'=>'templatefunctions.class.php', 'line'=>'remove_template_files_merch');
            $this->remove_template_files($tid);/*remove_template_files_merch*/
            if($sid=='') {
                header("location: merchant.php?page=templates&err=1$page_qs&did=".$tid);
            }
            else {
                header("location: merchant.php?page=templates&err=suc&sid=".$sid."&assign=template&did=".$tid);
            }
        }
        else{
            if($sid==''){
                header("location:merchant.php?page=templates&err=3$page_qs&did=".$tid);
            }
            else{
                header("location: merchant.php?page=templates&err=fail&sid=".$sid."&assign=template&did=".$tid);
            }
        }
    }

    //-------------------------------------------------------------------------------------------------------------
    // function: get_smartphone_default_id ( -- arguments -- )
    //-------------------------------------------------------------------------------------------------------------
    // purpose:	To fetch the default template id of the smartphone
    // arguments:
    //-------------------------------------------------------------------------------------------------------------
    function get_smartphone_default_id($findline="")
    {
        $args_smart["whereCon"]="p1ad_id = 1 and p1me_id IS NULL and p1te_stflag = 1 and p1te_device_flag =2 order by p1te_id asc";
        $args_smart["start_lim"] = 0;
        $args_smart["lim"] = 1;
        $ref_smart = $this->templates_obj->get_all_templatelist($args_smart,$debug);
        $sp_tid=$ref_smart[0]['p1te_id'];
        return $sp_tid;
    }

    //-------------------------------------------------------------------------------------------------------------
    // function: apply_all_templates ( -- arguments -- )
    //-------------------------------------------------------------------------------------------------------------
    // purpose: To apply the default templates changes to all desktop/smartphone templates
    // arguments:  $template_id, $label_id, $template_type, $label_type, $value, $findline=""
    //-------------------------------------------------------------------------------------------------------------
    function apply_all_templates($template_id, $label_id, $template_type, $label_type, $value, $findline="")
    {
        global $validator_obj;

        $arguments = array($template_id, $label_id, $template_type, $label_type, $value);
        $this->debug_obj->WriteDebug($class="templatefunctions.class", $function="apply_all_templates", $file=$findline['file'], $this->debug_obj->FindFunctionCalledline('apply_all_templates', $findline['file'], $findline['line']), $arguments);

        $coun_lang_id = $_SESSION['temp_coun_lang_id'];

        if($template_type == 'desktop') { //insert record for desktop template
            $flag = 1;
        }
        else { //insert record for smartphone template
            $flag = 2;
        }

        if($label_type == 'home') { //insert record for home label
            $type = 1;
        }
        else if($label_type == 'mobile') { //insert record for mobile label
            $type = 2;
        }
        else if($label_type == 'anyphone') { //insert record for mobile label
            $type = 3;
        }

        $debug = array('file'=>'templatefunctions.class', 'line'=>'escapestring');
        $value = $validator_obj->escape_string($value, $debug);/*escapestring*/

        $get_labels["tablename"] = "tbl_template_captions";
        $get_labels["fieldname"] = 'p1tc_title';
        $get_labels["whereCon"] = 'p1te_id = '.$template_id.' and p1tc_id = '.$label_id;
        $debug = array('file'=>'templatefunctions.class', 'line'=>'getlabel');
        $form_label = $this->templates_obj->get_component($get_labels, $debug);/*getlabel*/
        $title = $form_label[0]['p1tc_title'];

        $args["tablename"] = "tbl_templates";
        $args["whereCon"] = 'p1te_device_flag = '.$flag.' and p1te_stflag = 1';
        $debug = array('file'=>'templatefunctions.class', 'line'=>'gettotaltemplates');
        $templates = $this->templates_obj->get_all($args, $debug);/*gettotaltemplates*/
        $count = 1;
        for($i=0;$i<count($templates);$i++){
            $id = $templates[$i]['p1te_id'];
            $update_caption["tablename"] = "tbl_template_captions";
            $update_caption["fieldname"] = "p1tc_value = '".addslashes($value)."'";
            $update_caption["whereCon"] = "p1te_id =".$id." and p1tc_type = ".$type." and p1colg_id = ".$coun_lang_id." and p1tc_title = '".$title."'";
            $debug = array('file'=>'templatefunctions.class', 'line'=>'update');
            $label_update = $this->templates_obj->update_styles($update_caption, $debug);/*update*/
            $debug = array('file'=>'templatefunctions.class', 'line'=>'generatexml');
            $this->generate_label_xml($id, 'name', $coun_lang_id, $debug);/*generatexml*/
            $count++;
            if($count == 100){
                sleep(5);
                $count = 1;
            }
        }
        return 1;
    }

    //-------------------------------------------------------------------------------------------------------------
    // function: apply_all_templates_styles ( -- arguments -- )
    //-------------------------------------------------------------------------------------------------------------
    // purpose: To apply the default templates changes to all desktop/smartphone templates
    // arguments:  $template_id, $style_id, $template_type, $style_title, $style_value, $component, $findline=""
    //-------------------------------------------------------------------------------------------------------------
    function apply_all_templates_styles($template_id, $style_id, $template_type, $style_title, $style_value, $component, $findline="")
    {
        $arguments = array($template_id, $style_id, $template_type, $style_title, $style_value, $component);
        $this->debug_obj->WriteDebug($class="templatefunctions.class", $function="apply_all_templates_styles", $file=$findline['file'], $this->debug_obj->FindFunctionCalledline('apply_all_templates_styles', $findline['file'], $findline['line']), $arguments);

        if($template_type == 'desktop'){
            $flag = 1;//insert record for desktop template
        }
        else{
            $flag = 2;//insert record for smartphone template
        }

        $style_title = substr($style_title, 0, -1);
        $style_title = explode('@', $style_title);

        $style_value = substr($style_value, 0, -1);
        $style_value = explode('@', $style_value);

        $args["tablename"] = "tbl_templates";
        $args["whereCon"] = 'p1te_device_flag = '.$flag.' and p1te_stflag = 1';
        $debug = array('file'=>'templatefunctions.class', 'line'=>'gettotaltemplates');
        $templates = $this->templates_obj->get_all($args, $debug);/*gettotaltemplates*/
        $template_count = count($templates);
        $count = 1;
        for($j=0;$j<$template_count;$j++)
        {
            $id = $templates[$j]['p1te_id'];
            $get_styles["tablename"] = "tbl_template_vars";
            $get_styles["fieldname"] = "p1tv_id, p1tv_name";
            $get_styles["whereCon"] = "p1te_id = ".$id." and p1tv_value = '$component'";
            $debug = array('file'=>'templatefunctions.class', 'line'=>'getlabel');
            $form_styles = $this->templates_obj->get_component($get_styles, $debug);/*get_styles*/
            $component_id[] = $form_styles[0]['p1tv_id'];

            $get_component["tablename"] = "tbl_template_attribs";
            $get_component["fieldname"] = 'p1ta_id, p1ta_title, p1ta_value';
            $get_component["whereCon"] = 'p1tv_id = '.$component_id[$j];
            $debug = array('file'=>'templatefunctions.class', 'line'=>'getlabel');
            $form_components = $this->templates_obj->get_component($get_component, $debug);/*get_component*/
            foreach ($form_components as $key => $value){
                $title[] = $value['p1ta_title'];
            }
            for($i=0; $i<count($style_title); $i++){
                if($style_title[$i] == $title[$i]){
                    $update["tablename"] = "tbl_template_attribs";
                    $update["fieldname"] = "p1ta_value = '".addslashes($style_value[$i])."'";
                    $update["whereCon"] = "p1tv_id =".$component_id[$j]." and p1ta_title = '$style_title[$i]'";
                    $debug = array('file'=>'templatefunctions.class', 'line'=>'update');
                    $style_update = $this->templates_obj->update_styles($update, $debug);/*update*/
                }
            }
            $get_components["tablename"]="tbl_template_vars";
            $get_components["whereCon"]="p1te_id =".$id;
            $debug = array('file'=>'autosave.php', 'line'=>'fetchstyles');
            $allcomponents = $this->templates_obj->get_all($get_components, $debug);/*fetchstyles*/
            $valu = "";
            foreach( $allcomponents as $key => $value){
                $get_components = '';
                $get_components["tablename"]="tbl_template_attribs";
                $get_components["whereCon"]= "p1tv_id = ".$value['p1tv_id'];
                $debug = array('file'=>'autosave.php', 'line'=>'fetchselectstyles');
                $listallattributes = $this->templates_obj->get_all($get_components, $debug);/*fetchselectstyles*/
                $valu .= $value['p1tv_value'];
                $valu .= " { ";
                foreach( $listallattributes as $key => $style){
                    $valu .= $style['p1ta_title'];
                    $valu .= ": ";
                    $valu .= $style['p1ta_value'];
                    $valu .= "; ";
                }
                $valu .= " }\n";
                $filename = (FULL_PATH."/data/styles/templates/template_".$id);
                $debug = array('file'=>'templatefunctions.class.php', 'line'=>'updatecss');
                $this->update_template_css($filename, $valu, $id, $debug);/*updatecss*/
                $count++;
                if($count == 100){
                    sleep(5);
                    $count = 1;
                }
            }
        }
        return 1;
    }

    //-------------------------------------------------------------------------------------------------------------
    // function: remove_template_files( -- arguments -- )
    //-------------------------------------------------------------------------------------------------------------
    // purpose: To remove the css and xml file specific to a template while deleting the template
    // arguments: $tid, $findline=""
    //-------------------------------------------------------------------------------------------------------------
    function remove_template_files($tid, $findline="")
    {
        $arguments = array($tid);
        $this->debug_obj->WriteDebug($class="templatefunctions.class", $function="remove_template_files", $file=$findline['file'], $this->debug_obj->FindFunctionCalledline('remove_template_files', $findline['file'], $findline['line']), $arguments);

        $css_file = FULL_PATH."/data/styles/templates/template_".$tid."/";
        $debug = array('file'=>'templatefunctions.class.php', 'line'=>'remove_css_files');
        $this->removedir($css_file, $debug);/*remove_css_files*/

        $xml_file = FULL_PATH."/data/xml/templates/template_".$tid."/";
        $debug = array('file'=>'templatefunctions.class.php', 'line'=>'remove_xml_files');
        $this->removedir($xml_file, $debug);/*remove_xml_files*/
   }

    //-------------------------------------------------------------------------------------------------------------
    // function: removedir( -- arguments -- )
    //-------------------------------------------------------------------------------------------------------------
    // purpose: To remove the css and xml files specific to a template
    // arguments: $dir, $findline=""
    //-------------------------------------------------------------------------------------------------------------
    function removedir($dir, $findline="")
    {
        $arguments = array($dir);
        $this->debug_obj->WriteDebug($class="templatefunctions.class", $function="removedir", $file=$findline['file'], $this->debug_obj->FindFunctionCalledline('removedir', $findline['file'], $findline['line']), $arguments);

        if(is_dir($dir))
        {
            $objects = scandir($dir, 1);
            foreach ($objects as $obj)
            {
                if($obj != "." && $obj != "..")
                {
                    if(filetype($dir."/".$obj) == "dir") {
                        $debug = array('file'=>'templatefunctions.class.php', 'line'=>'remove_files');
                        $this->removedir($dir."/".$obj, $debug);/*remove_files*/
                    }
                    else {
                        unlink($dir."/".$obj);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    // ---------------------------------------------------------------------------------------------------------------
    // function: search_html_tags ( -- arguments -- )
    // ---------------------------------------------------------------------------------------------------------------
    // Purpose: search html tags present in the string 
    // Parameters: $string
    // Returns: 1 or 0
    // ---------------------------------------------------------------------------------------------------------------
    function search_html_tags($string)
    {
        if (preg_match("/</", $string)){
            return 1;
        }
        else{
            return 0;
        }
    }

    // ---------------------------------------------------------------------------------------------------------------
    // function: check_style_changes ( -- arguments -- )
    // ---------------------------------------------------------------------------------------------------------------
    // Purpose: check style changes done
    // Parameters: $cid, $findline=""
    // ---------------------------------------------------------------------------------------------------------------
    function check_style_changes($cid, $findline="")
    {
        $arguments = array($cid);
        $this->debug_obj->WriteDebug($class="templatefunctions.class", $function="check_style_changes", $file=$findline['file'], $this->debug_obj->FindFunctionCalledline('check_style_changes', $findline['file'], $findline['line']), $arguments);

        $get_style["tablename"] = "tbl_template_attribs";
        $get_style["fieldname"] = "p1ta_title, p1ta_value";
        $get_style["whereCon"] = "p1tv_id = ".$cid;
        $debug = array('file'=>'templatefunctions.class', 'line'=>'getalllabels');
        $form_style = $this->templates_obj->get_component($get_style, $debug);/*getalllabels*/
        foreach($form_style as $key => $value){
            $default_title .= $value['p1ta_title'].'@';
            $default_value .= $value['p1ta_value'].'@';
        }
        $default_title = substr($default_title, 0, -1);
        $default_value = substr($default_value, 0, -1);
        return $default_title.'---'.$default_value;
    }

    //-------------------------------------------------------------------------------------------------------------
    // function: affect_all_template_labels ( -- arguments -- )
    //-------------------------------------------------------------------------------------------------------------
    // purpose: To apply the default template xml data to all desktop/smartphone templates
    // arguments:  $template_id, $label_id, $template_type, $label_type, $value, $findline=""
    //-------------------------------------------------------------------------------------------------------------
    function affect_all_template_labels($template_id, $label_id, $template_type, $label_type, $value, $findline="")
    {
        global $validator_obj;

        $arguments = array($template_id, $label_id, $template_type, $label_type, $value);
        $this->debug_obj->WriteDebug($class="templatefunctions.class", $function="affect_all_template_labels", $file=$findline['file'], $this->debug_obj->FindFunctionCalledline('affect_all_template_labels', $findline['file'], $findline['line']), $arguments);

        $coun_lang_id = $_SESSION['temp_coun_lang_id'];

        if($template_type == 'desktop') { //insert record for desktop template
            $flag = 1;
        }
        else { //insert record for smartphone template
            $flag = 2;
        }

        if($label_type == 'home') { //insert record for home label
            $type = 1;
        }
        else if($label_type == 'mobile') { //insert record for mobile label
            $type = 2;
        }
        else if($label_type == 'anyphone') { //insert record for mobile label
            $type = 3;
        }

        $debug = array('file'=>'templatefunctions.class', 'line'=>'escapestring');
        $value = $validator_obj->escape_string($value, $debug);/*escapestring*/

        $this_update["tablename"] = "tbl_template_captions";
        $this_update["fieldname"] = "p1tc_value = '".addslashes($value)."'";
        $this_update["whereCon"] = "p1te_id =".$template_id." and p1tc_type = ".$type." and p1tc_id = ".$label_id;
        $debug = array('file'=>'templatefunctions.class', 'line'=>'updatethistemplate');
        $label_update = $this->templates_obj->update_styles($this_update, $debug);/*updatethistemplate*/

        $default_data["tablename"] = "tbl_template_captions";
        $default_data["fieldname"] = "p1tc_title, p1tc_value, p1tc_type, p1colg_id";
        $default_data["whereCon"] = "p1te_id = ".$template_id." and p1colg_id = ".$coun_lang_id." order by  p1tc_id asc";
        $debug = array('file'=>'templatefunctions.class', 'line'=>'getdefaultdatathistemplate');
        $default_template_data = $this->templates_obj->get_component($default_data, $debug);/*getdefaultdatathistemplate*/

        $args["tablename"] = "tbl_templates";
        $args["fieldname"] = "p1te_id";
        $args["whereCon"] = "p1te_device_flag = ".$flag." and p1te_stflag = 1";
        $debug = array('file'=>'templatefunctions.class', 'line'=>'gettotaltemplates');
        $templates = $this->templates_obj->get_component($args, $debug);/*gettotaltemplates*/
        $count = 1;
        foreach($templates as $key){
            foreach($default_template_data as $keys => $datas){
                $debug = array('file'=>'templatefunctions.class', 'line'=>'escapestrings');
                $data_value = $validator_obj->escape_string($datas['p1tc_value'], $debug);/*escapestrings*/

                $update_captions["tablename"] = "tbl_template_captions";
                $update_captions["fieldname"] = "p1tc_value = '".addslashes($data_value)."'";
                $update_captions["whereCon"] = "p1te_id = ".$key['p1te_id']." and p1tc_type = ".$datas['p1tc_type']." and p1tc_title = '".$datas['p1tc_title']."' and p1colg_id = ".$datas['p1colg_id']."";
                $debug = array('file'=>'templatefunctions.class', 'line'=>'updatevalues');
                $label_update = $this->templates_obj->update_styles($update_captions, $debug);/*updatevalues*/
            }
            $debug = array('file'=>'templatefunctions.class', 'line'=>'regeneratexml');
            $this->generate_label_xml($key['p1te_id'], 'name', $coun_lang_id, $debug);/*regeneratexml*/
            $count++;
            if($count == 100){
                sleep(5);
                $count = 1;
            }
        }
        return $label_update;
    }

    //-------------------------------------------------------------------------------------------------------------
    // function: restor_default_template_data ( -- arguments -- )
    //-------------------------------------------------------------------------------------------------------------
    // purpose: To apply the default template xml data to all desktop/smartphone templates
    // arguments:  $template_id, $template_type, $type, $findline=""
    //-------------------------------------------------------------------------------------------------------------
    function restor_default_template_data($template_id, $template_type, $type, $findline="")
    {
        global $validator_obj;

        $arguments = array($template_id, $template_type, $type);
        $this->debug_obj->WriteDebug($class="templatefunctions.class", $function="restor_default_template_data", $file=$findline['file'], $this->debug_obj->FindFunctionCalledline('restor_default_template_data', $findline['file'], $findline['line']), $arguments);

        $coun_lang_id = $_SESSION['temp_coun_lang_id'];

        if($template_type == 'desktop'){
            $flag = 1;//update record for desktop template
        }
        else{
            $flag = 2;//update record for smartphone template
        }

        if($_SESSION['d_type'] == "smart"){
            $default_template_id = $this->get_smartphone_default_id();
        }
        else{
            $default_template_id = 1;
        }

        if($type == 'label')
        {
            $default_data["tablename"] = "tbl_template_captions";
            $default_data["fieldname"] = "p1tc_title, p1tc_value, p1tc_type, p1colg_id";
            $default_data["whereCon"] = "p1te_id = ".$default_template_id." and p1colg_id = ".$coun_lang_id." order by  p1tc_id asc";
            $debug = array('file'=>'templatefunctions.class', 'line'=>'getdefaulttemplatevalues');
            $default_template_data = $this->templates_obj->get_component($default_data, $debug);/*getdefaulttemplatevalues*/
            foreach($default_template_data as $keys => $datas){
                $debug = array('file'=>'templatefunctions.class', 'line'=>'escapestrings');
                $data_value = $validator_obj->escape_string($datas['p1tc_value'], $debug);/*escapestrings*/
                $update_captions["tablename"] = "tbl_template_captions";
                $update_captions["fieldname"] = "p1tc_value = '".addslashes($data_value)."'";
                $update_captions["whereCon"] = "p1te_id = ".$template_id." and p1tc_type = ".$datas['p1tc_type']." and p1tc_title = '".$datas['p1tc_title']."' and p1colg_id = ".$datas['p1colg_id']."";
                $debug = array('file'=>'templatefunctions.class', 'line'=>'update');
                $label_update = $this->templates_obj->update_styles($update_captions, $debug);/*update*/
            }
            $debug = array('file'=>'templatefunctions.class', 'line'=>'regeneratexml');
            $this->generate_label_xml($template_id, 'name', $coun_lang_id, $debug);/*regeneratexml*/
        }
        else
        {
            $debug = array('file'=>'templatefunctions.class', 'line'=>'getdefaultstylevalues');
            $default_data = $this->fetch_style_values($default_template_id);/*getdefaultstylevalues*/
            foreach($default_data as $keys){
                $default_value[] = $keys['p1ta_value'];
            }
             //fetches data for this template - styles
            $this_vars["tablename"] = "tbl_template_attribs as attr LEFT JOIN tbl_template_vars as vars ON ( attr.p1tv_id = vars.p1tv_id )";
            $this_vars["fieldname"] = "attr.p1ta_id, attr.p1tv_id, attr.p1ta_title";
            $this_vars["whereCon"]= "vars.p1te_id = ".$template_id;
            $debug = array('file'=>'templatefunctions.class', 'line'=>'thisvars');
            $this_template_data = $this->templates_obj->get_component($this_vars, $debug);/*thisvars*/
            foreach($this_template_data as $key => $val){
                $this_attr["tablename"] = "tbl_template_attribs";
                $this_attr["fieldname"] = "p1ta_value = '".addslashes($default_value[$key])."'";
                $this_attr["whereCon"]= "p1tv_id =".$val['p1tv_id']." and p1ta_title = '".$val['p1ta_title']."'";
                $debug = array('file'=>'templatefunctions.class', 'line'=>'updateattr');
                $label_update = $this->templates_obj->update_styles($this_attr, $debug);/*updateattr*/
            }
            $debug = array('file'=>'templatefunctions.class', 'line'=>'generatecss');
            $this->generate_css($template_id, $debug);/*generatecss*/
        }
        return $label_update;
    }

     //-------------------------------------------------------------------------------------------------------------
    // function: generate_css ( -- arguments -- )
    //-------------------------------------------------------------------------------------------------------------
    // purpose: To regenerate css
    // arguments:  $template_id, $findline=""
    //-------------------------------------------------------------------------------------------------------------
    function generate_css($template_id, $findline="")
    {
        $arguments = array($template_id);
        $this->debug_obj->WriteDebug($class="templatefunctions.class", $function="generate_css", $file=$findline['file'], $this->debug_obj->FindFunctionCalledline('generate_css', $findline['file'], $findline['line']), $arguments);

        $get_components["tablename"]="tbl_template_vars";
        $get_components["whereCon"]="p1te_id =".$template_id;
        $debug = array('file'=>'autosave.php', 'line'=>'fetchstyles');
        $allcomponents = $this->templates_obj->get_all($get_components, $debug);/*fetchstyles*/
        $valu = "";
        foreach( $allcomponents as $key => $value){
            $get_components = '';
            $get_components["tablename"]="tbl_template_attribs";
            $get_components["whereCon"]= "p1tv_id = ".$value['p1tv_id'];
            $debug = array('file'=>'autosave.php', 'line'=>'fetchselectstyles');
            $listallattributes = $this->templates_obj->get_all($get_components, $debug);/*fetchselectstyles*/
            $valu .= $value['p1tv_value'];
            $valu .= " { ";
            foreach( $listallattributes as $key => $style){
                $valu .= $style['p1ta_title'];
                $valu .= ": ";
                $valu .= $style['p1ta_value'];
                $valu .= "; ";
            }
            $valu .= " }\n";
            $filename = (FULL_PATH."/data/styles/templates/template_".$template_id);
            $debug = array('file'=>'templatefunctions.class.php', 'line'=>'updatecss');
            $this->update_template_css($filename, $valu, $template_id, $debug);/*updatecss*/
        }
    }

    //-------------------------------------------------------------------------------------------------------------
    // function: check_for_restore_styles ( -- arguments -- )
    //-------------------------------------------------------------------------------------------------------------
    // purpose: To check and enable if restore to default  should be enabled
    // arguments:  $template_id, $findline=""
    //-------------------------------------------------------------------------------------------------------------
    function check_for_restore_styles($template_id, $findline="")
    {
        $arguments = array($template_id);
        $this->debug_obj->WriteDebug($class="templatefunctions.class", $function="check_for_restore_styles", $file=$findline['file'], $this->debug_obj->FindFunctionCalledline('check_for_restore_styles', $findline['file'], $findline['line']), $arguments);

         if($_SESSION['d_type'] == "smart"){
            $default_template_id = $this->get_smartphone_default_id();
        }
        else{
            $default_template_id = 1;
        }

        //fetches default data from default templates - styles
        $debug = array('file'=>'templatefunctions.class', 'line'=>'getdefaultstyle');
        $default_template_data = $this->fetch_style_values($default_template_id);/*getdefaultstyle*/
        foreach($default_template_data as $key => $dafault_style){
            $default_data[] = trim($dafault_style['p1ta_value']);
        }

        //fetches data for this template - styles
        $debug = array('file'=>'templatefunctions.class', 'line'=>'thisvars');
        $this_template_data = $this->fetch_style_values($template_id);/*thisvars*/
        foreach($this_template_data as $keys => $this_style){
            $this_data[] = trim($this_style['p1ta_value']);
        }

        $count = 0;
        for($i=0;$i<count($default_data);$i++){
            if($default_data[$i] != $this_data[$i]){
                $count++;
            }
        }
        return $count;
    }

    //-------------------------------------------------------------------------------------------------------------
    // function: fetch_style_values ( -- arguments -- )
    //-------------------------------------------------------------------------------------------------------------
    // purpose: To check and enable if restore to default  should be enabled
    // arguments:  $template_id, $findline=""
    //-------------------------------------------------------------------------------------------------------------
    function fetch_style_values($template_id, $findline="")
    {
        $arguments = array($template_id);
        $this->debug_obj->WriteDebug($class="templatefunctions.class", $function="fetch_style_values", $file=$findline['file'], $this->debug_obj->FindFunctionCalledline('fetch_style_values', $findline['file'], $findline['line']), $arguments);

        $vars["tablename"] = "tbl_template_attribs as attr LEFT JOIN tbl_template_vars as vars ON ( attr.p1tv_id = vars.p1tv_id )";
        $vars["fieldname"] = "attr.p1ta_id, attr.p1tv_id, attr.p1ta_title, attr.p1ta_value, vars.p1te_id";
        $vars["whereCon"]= "vars.p1te_id = ".$template_id;
        $debug = array('file'=>'templatefunctions.class', 'line'=>'getstyles');
        $style_values = $this->templates_obj->get_component($vars);/*getstyles*/
        return $style_values;
    }

    //-------------------------------------------------------------------------------------------------------------
    // function: check_for_restore_labs ( -- arguments -- )
    //-------------------------------------------------------------------------------------------------------------
    // purpose: To check and enable if restore to default  should be enabled
    // arguments:  $template_id, $findline=""
    //-------------------------------------------------------------------------------------------------------------
    function check_for_restore_labs($template_id, $findline="")
    {
        $arguments = array($template_id);
        $this->debug_obj->WriteDebug($class="templatefunctions.class", $function="check_for_restore_labs", $file=$findline['file'], $this->debug_obj->FindFunctionCalledline('check_for_restore_labs', $findline['file'], $findline['line']), $arguments);

         if($_SESSION['d_type'] == "smart"){
            $default_template_id = $this->get_smartphone_default_id();
        }
        else{
            $default_template_id = 1;
        }

         //fetches default data from default templates - styles
        $debug = array('file'=>'templatefunctions.class', 'line'=>'getdefaultlabel');
        $default_template_data = $this->fetch_label_values($default_template_id);/*getdefaultlabel*/
        foreach($default_template_data as $key => $dafault_style){
            $default_data[] = trim($dafault_style['p1tc_value']);
        }

        //fetches data for this template - styles
        $debug = array('file'=>'templatefunctions.class', 'line'=>'thislabs');
        $this_template_data = $this->fetch_label_values($template_id);/*thislabs*/
        foreach($this_template_data as $keys => $this_style){
            $this_data[] = trim($this_style['p1tc_value']);
        }

        $count = 0;
        for($i=0;$i<count($default_data);$i++){
            if($default_data[$i] != $this_data[$i]){
                $count++;
            }
        }
        return $count;
    }

    //-------------------------------------------------------------------------------------------------------------
    // function: fetch_label_values ( -- arguments -- )
    //-------------------------------------------------------------------------------------------------------------
    // purpose: To check and enable if restore to default  should be enabled
    // arguments:  $template_id, $findline=""
    //-------------------------------------------------------------------------------------------------------------
    function fetch_label_values($template_id, $findline="")
    {
        $arguments = array($template_id);
        $this->debug_obj->WriteDebug($class="templatefunctions.class", $function="fetch_label_values", $file=$findline['file'], $this->debug_obj->FindFunctionCalledline('fetch_label_values', $findline['file'], $findline['line']), $arguments);

        $vars["tablename"] = "tbl_template_captions as cap LEFT JOIN tbl_templates as temp ON ( cap.p1te_id = temp.p1te_id )";
        $vars["fieldname"] = "cap.p1tc_id, cap.p1tc_title, cap.p1tc_value, cap.p1te_id";
        $vars["whereCon"]= "temp.p1te_id = ".$template_id;
        $debug = array('file'=>'templatefunctions.class', 'line'=>'getstyles');
        $style_values = $this->templates_obj->get_component($vars);/*getstyles*/
        return $style_values;
    }

    //--------------------------------------------------------------------------------------------------------
    // function: fetch_country_list ( -- parameters -- )
    //---------------------------------------------------------------------------------------------------------
    // purpose:    To get country list
    // parameters:  $findline=""
    //-------------------------------------------------------------------------------------------------------
    function fetch_country_list($findline="")
    {
        $arguments = array();
        $this->debug_obj->WriteDebug($class="templatefunctions.class", $function="fetch_country_list", $findline['file'], $this->debug_obj->FindFunctionCalledline('fetch_country_list', $findline['file'], $findline['line']), $arguments);

        $args["tablename"] = "tbl_country";
        $args["fieldname"] = "p1co_id, p1co_name, p1co_code";
        $args["whereCon"] = "p1co_stflag =1 order by p1co_id ASC";
        $debug = array('file'=>'templatefunctions.class.php', 'line'=>'fetch_country_lists');
        $result = $this->templates_obj->get_component($args, $debug);/*fetch_country_lists*/
        return $result;
    }

    //--------------------------------------------------------------------------------------------------------
    // function: fetch_language_list ( -- parameters -- )
    //---------------------------------------------------------------------------------------------------------
    // purpose:    To get country list
    // parameters:  $country_language_id, $findline=""
    //-------------------------------------------------------------------------------------------------------
    function fetch_language_list($country_language_id, $findline="")
    {
        $arguments = array($country_language_id);
        $this->debug_obj->WriteDebug($class="templatefunctions.class", $function="fetch_language_list", $findline['file'], $this->debug_obj->FindFunctionCalledline('fetch_language_list', $findline['file'], $findline['line']), $arguments);

        $args["tablename"] = "tbl_languages";
        $args["fieldname"] = "p1lg_id, p1lg_name, p1lg_code";
        $args["whereCon"] = "p1lg_stflag =1 and p1lg_id IN (".$country_language_id.")";
        $debug = array('file'=>'templatefunctions.class.php', 'line'=>'fetch_countrylang_lists');
        $result = $this->templates_obj->get_component($args, $debug);/*fetch_countrylang_lists*/
        return $result;
    }

    //--------------------------------------------------------------------------------------------------------
    // function: fetch_country_language_list ( -- parameters -- )
    //---------------------------------------------------------------------------------------------------------
    // purpose:    To get country languages list
    // parameters:  $country_id, $findline=""
    //-------------------------------------------------------------------------------------------------------
    function fetch_country_language_list($country_id, $findline="")
    {
        $arguments = array($country_id);
        $this->debug_obj->WriteDebug($class="templatefunctions.class", $function="fetch_country_language_list", $findline['file'], $this->debug_obj->FindFunctionCalledline('fetch_country_language_list', $findline['file'], $findline['line']), $arguments);

        $args["tablename"] = "tbl_country_language_reference";
        $args["fieldname"] = "p1colg_id, p1co_id, p1lg_id, p1colg_official";
        $args["whereCon"] = "p1colg_stflag =1 and p1co_id = ".$country_id;
        $debug = array('file'=>'templatefunctions.class.php', 'line'=>'fetch_country_language_lists');
        $result = $this->templates_obj->get_component($args, $debug);/*fetch_country_language_lists*/
        return $result;
    }

    //--------------------------------------------------------------------------------------------------------
    // function: fetch_labels_list ( -- parameters -- )
    //---------------------------------------------------------------------------------------------------------
    // purpose:    To get country labels list
    // parameters:  $country_id, $language_id, $findline=""
    //-------------------------------------------------------------------------------------------------------
    function fetch_labels_list($country_id, $language_id, $findline="")
    {
        $arguments = array($country_id);
        $this->debug_obj->WriteDebug($class="templatefunctions.class", $function="fetch_labels_list", $findline['file'], $this->debug_obj->FindFunctionCalledline('fetch_labels_list', $findline['file'], $findline['line']), $arguments);

        $args["tablename"] = "tbl_country_language_reference";
        $args["fieldname"] = "p1colg_id, p1co_id, p1lg_id, p1colg_official";
        $args["whereCon"] = "p1colg_stflag =1 and p1co_id = ".$country_id." and p1lg_id = ".$language_id;
        $debug = array('file'=>'templatefunctions.class.php', 'line'=>'fetch_country_language_lists');
        $result = $this->templates_obj->get_component($args, $debug);/*fetch_country_language_lists*/
        return $result;
    }

}//class ends

?>