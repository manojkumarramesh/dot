<?php
ob_start();
//-------------------------------------------------------------------------------------------------------------------
// File name   : templates.class.php
// Description : Handles templates related tasks
//
// © 2010-2011 PaymentOne Corportation. All rights reserved.
//
// Created date : 16-02-2010
// Modified date: 31-10-2011
// ------------------------------------------------------------------------------------------------------------------
class templates extends ClassGeneral {
    var $db_connect;
    var $tbl_name;
    var $_resarr;
    var $debug_obj;

    // initializing db connect
    function templates($args=array()){
        global $glb_obj_genral,$debug_obj;
        $this->db_connect = $glb_obj_genral->db_connect;
        $this->_resarr = $args;
        $this->debug_obj=$debug_obj;
    }

    // get the name of the curent template	
    function get_Name(){
        global $glb_obj_genral;
        return $this->_resarr['p1te_name'];
    }

    // Get the values of the UI template fields	
    function get_Field($field){
        global $glb_obj_genral;
        return $this->_resarr[$field];
    }

    // ---------------------------------------------------------------------------------------------------------------
    // function: get_all ( -- arguments -- )
    // ---------------------------------------------------------------------------------------------------------------
    // purpose:	get the all details from the tables
    // arguments:	$tablename, $whereCon
    // ---------------------------------------------------------------------------------------------------------------
    function get_all($args=array(),$findline='')
    {
        $arguments = array($args);
        $this->debug_obj->WriteDebug($class="templates.class", $function="get_all", $file=$findline['file'], $this->debug_obj->FindFunctionCalledline('get_all', $findline['file'], $findline['line']), $arguments);

        extract($args);
        $get_all = $this->db_connect->querySelect("CALL uspGet_all(\"$tablename\", \"$whereCon\")");
        $this->db_connect->closedb();
        return $get_all;
    }

    // ---------------------------------------------------------------------------------------------------------------
    // function: join_group_by ( -- arguments -- )
    // ---------------------------------------------------------------------------------------------------------------
    // purpose:	get the all details from more then one table using group by
    // arguments:	$tablename, $fieldname, $whereCon, $groupby
    // ---------------------------------------------------------------------------------------------------------------
    function join_group_by($tablename="",$fieldname="",$whereCon="", $groupby="",$findline='')
    {
        $arguments = array($tablename,$fieldname,$whereCon, $groupby);
        $this->debug_obj->WriteDebug($class="templates.class", $function="join_group_by", $file=$findline['file'], $this->debug_obj->FindFunctionCalledline('join_group_by', $findline['file'], $findline['line']), $arguments);

        $get_all_styles = $this->db_connect->querySelect("CALL uspGet_join_group_by(\"$tablename\", \"$fieldname\", \"$whereCon\", \"$groupby\")");
        $this->db_connect->closedb();
        return $get_all_styles;
    }

    // ---------------------------------------------------------------------------------------------------------------
    // function: template_insert ( -- arguments -- )
    // ---------------------------------------------------------------------------------------------------------------
    // purpose:	Insert the value in to the tables 
    // arguments:	$tablename, $fieldname, $fieldval
    // ---------------------------------------------------------------------------------------------------------------
    function template_insert($args=array(),$findline='')
    {
        $arguments = array($args);
        $this->debug_obj->WriteDebug($class="templates.class", $function="template_insert", $file=$findline['file'], $this->debug_obj->FindFunctionCalledline('template_insert', $findline['file'], $findline['line']), $arguments);

        extract($args);
        $insertres = $this->db_connect->query("CALL uspInsert_template(\"$tablename\", \"$fieldname\", \"$fieldval\")");
        $this->db_connect->closedb();
        if($insertres)
            return 1;
        else
            return 0;
    }

    // ---------------------------------------------------------------------------------------------------------------
    // function: get_maxid ( -- arguments -- )
    // ---------------------------------------------------------------------------------------------------------------
    // purpose:	get the all details from more then one table using group by
    // arguments:	$tablename, $fieldname, $whereCon
    // ---------------------------------------------------------------------------------------------------------------
    function get_maxid($args=array(),$findline='')
    {
        $arguments = array($args);
        $this->debug_obj->WriteDebug($class="templates.class", $function="get_maxid", $file=$findline['file'], $this->debug_obj->FindFunctionCalledline('get_maxid', $findline['file'], $findline['line']), $arguments);

        extract($args);
        $get_maxid = $this->db_connect->querySelect("CALL uspGet_join_using_where(\"$tablename\", \"$fieldname\", \"$whereCon\")");
        $this->db_connect->closedb();
        return $get_maxid;	
    }

    // ---------------------------------------------------------------------------------------------------------------
    // function: update_styles ( -- arguments -- )
    // ---------------------------------------------------------------------------------------------------------------
    // purpose:	update the values into the exact tables.
    // arguments:	$tablename, $fieldname, $whereCon
    // ---------------------------------------------------------------------------------------------------------------
    function update_styles($args=array(),$findline='')
    {
        $arguments = array($args);
        $this->debug_obj->WriteDebug($class="templates.class", $function="update_styles", $file=$findline['file'], $this->debug_obj->FindFunctionCalledline('update_styles', $findline['file'], $findline['line']), $arguments);

        extract($args);
        $update_styles = $this->db_connect->query("CALL uspUpdate_template(\"$tablename\", \"$fieldname\", \"$whereCon\")");
        $this->db_connect->closedb();
        if($update_styles)
            return 1;
        else
            return 0;
    }

    // ---------------------------------------------------------------------------------------------------------------
    // function: get_component ( -- arguments -- )
    // ---------------------------------------------------------------------------------------------------------------
    // purpose:	Get the all compoent details from more then one table using group by
    // arguments:	$tablename, $fieldname, $whereCon
    // ---------------------------------------------------------------------------------------------------------------
    function get_component($args=array(),$findline='')
    {
        $arguments = array($args);
        $this->debug_obj->WriteDebug($class="templates.class", $function="get_component", $file=$findline['file'], $this->debug_obj->FindFunctionCalledline('get_component', $findline['file'], $findline['line']), $arguments);

        extract($args);
        $get_component = $this->db_connect->querySelect("CALL uspGet_join_using_where(\"$tablename\", \"$fieldname\", \"$whereCon\")");
        $this->db_connect->closedb();
        return $get_component;
    }

    // ---------------------------------------------------------------------------------------------------------------
    // function: get_component_attr ( -- arguments -- )
    // ---------------------------------------------------------------------------------------------------------------
    // purpose:	Get the all details from more then one table using group by
    // arguments:	$tablename, $fieldname, $whereCon
    // ---------------------------------------------------------------------------------------------------------------
    function get_component_attr($tablename="",$fieldname="",$whereCon="",$findline='')
    {
        $arguments = array($tablename,$fieldname,$whereCon);
        $this->debug_obj->WriteDebug($class="templates.class", $function="get_component_attr", $file=$findline['file'], $this->debug_obj->FindFunctionCalledline('get_component_attr', $findline['file'], $findline['line']), $arguments);

        $get_component_attr = $this->db_connect->querySelect("CALL uspGet_join_using_where(\"$tablename\", \"$fieldname\", \"$whereCon\")");
        $this->db_connect->closedb();
        return $get_component_attr;
    }

    // ---------------------------------------------------------------------------------------------------------------
    // function: get_template_all_list ( -- arguments -- )
    // ---------------------------------------------------------------------------------------------------------------
    // purpose:	get the all template list details
    // arguments:	$whereCon, $lim, $start_lim
    // ---------------------------------------------------------------------------------------------------------------
    function get_all_templatelist($args=array(),$findline='')
    {
        $arguments = array($args);
        $this->debug_obj->WriteDebug($class="templates.class", $function="get_all_templatelist", $file=$findline['file'], $this->debug_obj->FindFunctionCalledline('get_all_templatelist', $findline['file'], $findline['line']), $arguments);

        extract($args);
        $template_list_all = $this->db_connect->querySelect("CALL uspGet_template_all_list(\"$whereCon\",\"$lim\",\"$start_lim\")");
        $this->db_connect->closedb();
        return $template_list_all;
    }

    // ---------------------------------------------------------------------------------------------------------------
    // function: get_total_services ( -- arguments -- )
    // ---------------------------------------------------------------------------------------------------------------
    // purpose:	get the all servce list details
    // arguments:	$whereCon
    // ---------------------------------------------------------------------------------------------------------------
    function get_total_services($whereCon="",$findline='')
    {
        $arguments = array($whereCon);
        $this->debug_obj->WriteDebug($class="templates.class", $function="get_total_services", $file=$findline['file'], $this->debug_obj->FindFunctionCalledline('get_total_services', $findline['file'], $findline['line']), $arguments);

        $template_services = $this->db_connect->querySelect("CALL uspGet_total_services(\"$whereCon\")");
        $this->db_connect->closedb();
        return $template_services;
    }

    // ---------------------------------------------------------------------------------------------------------------
    // function: get_category_services ( -- arguments -- )
    // ---------------------------------------------------------------------------------------------------------------
    // purpose:	get the all category and services list details
    // arguments:	$tablename, $fieldname, $whereCon
    // ---------------------------------------------------------------------------------------------------------------
    function get_category_services($args=array(),$findline='')
    {
        $arguments = array($args);
        $this->debug_obj->WriteDebug($class="templates.class", $function="get_category_services", $file=$findline['file'], $this->debug_obj->FindFunctionCalledline('get_category_services', $findline['file'], $findline['line']), $arguments);

        extract($args);
        $all_category_services = $this->db_connect->querySelect("CALL uspGet_all_category_services(\"$tablename\",\"$fieldname\",\"$whereCon\")");
        $this->db_connect->closedb();
        return $all_category_services;
    }

    // ---------------------------------------------------------------------------------------------------------------
    // function: temp_delete ( -- arguments -- )
    // ---------------------------------------------------------------------------------------------------------------
    // purpose:		Delete the details
    // arguments:		$tablename, $whereCon
    // ---------------------------------------------------------------------------------------------------------------
    function temp_delete($args=array(),$findline='')
    {
        $arguments = array($args);
        $this->debug_obj->WriteDebug($class="templates.class", $function="temp_delete", $file=$findline['file'], $this->debug_obj->FindFunctionCalledline('temp_delete', $findline['file'], $findline['line']), $arguments);

        extract($args);
        $seleItem = $this->db_connect->query("CALL uspDelete_all(\"$tabname\",\"$whereCon\")");
        $this->db_connect->closedb();
        return $seleItem;
    }

}
?>