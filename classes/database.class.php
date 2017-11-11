<?php
//-------------------------------------------------------------------------------------------------------------------
// file name   : database.class.php
// description : Handles database related tasks
//
// © 2010-2011 PaymentOne Corportation. All rights reserved.
//
// Created date : 30-01-2009
// Modified date: 31-10-2011
// -------------------------------------------------------------------------------------------------------------------

class database {
	
	public $mysqli = 0; //  database connection
	protected $recordsSelected = 0;
	protected $recordsUpdated = 0;
	protected $databaseResults = Array();	
	
	var $connected = false;
	var $queried = false;	
	var $results = array();
	var $rescount = 1;	
	var $insertIDs = array();	
	var $fmtDate = "'Y-m-d'";	
	var $dbuser, $dbpass, $dbname, $dbhost;
	var $socket;	
	var $queries;

  // ---------------------------------------------------------------------------------------------------------------
  // function: database ( -- arguments -- )
  // ---------------------------------------------------------------------------------------------------------------
  // purpose:			assgin db connect varible to class
  // arguments:		    $dbuser, $dbpass, $dbname, $dbhost='localhost'
  // returns/assigns:	none
  // ---------------------------------------------------------------------------------------------------------------


	function database( $dbuser, $dbpass, $dbname, $dbhost='localhost' ) {
		 
		$this->dbuser = $dbuser;
		$this->dbpass = $dbpass;
		$this->dbname = $dbname;
		$this->dbhost = $dbhost;
		error_reporting(1);
		
	}
  
  // ---------------------------------------------------------------------------------------------------------------
  // function: connect ( -- arguments -- )
  // ---------------------------------------------------------------------------------------------------------------
  // purpose:			check the connection
  // arguments:         None
  // returns/assigns:	Success: connected true
  // ---------------------------------------------------------------------------------------------------------------

	function connect() {
		//echo "Host:$this->dbhost User:$this->dbuser Pass: $this->dbpass DB: $this->dbname";
		
		$this->socket = new mysqli( $this->dbhost, $this->dbuser, $this->dbpass,$this->dbname );
		if ($this->socket->connect_errno >0) {

   			 $this->error( "Error connecting to database server - ", true );
		}		
		$this->connected = true;
	}

  // ---------------------------------------------------------------------------------------------------------------
  // function: closedb ( -- arguments -- )
  // ---------------------------------------------------------------------------------------------------------------
  // purpose:			close the connection
  // arguments:         None
  // returns/assigns:	Success: connected true
  // ---------------------------------------------------------------------------------------------------------------

	function closedb(){
		if($this->socket)
			
			mysqli_close($this->socket);
			$this->connected = false;
	}

   // ---------------------------------------------------------------------------------------------------------------
  // function: DBDate ( -- arguments -- )
  // ---------------------------------------------------------------------------------------------------------------
  // purpose:		fetch date 
  // arguments:         $d
  // returns/assigns:	Success: date
  // ---------------------------------------------------------------------------------------------------------------

	function DBDate($d)
	{
		// note that we are limited to 1970 to 2038
		return date($this->fmtDate,$d);
	}

  // ---------------------------------------------------------------------------------------------------------------
  // function: error ( -- arguments -- )
  // ---------------------------------------------------------------------------------------------------------------
  // purpose:			show the error 
  // arguments:         $text, $fatal
  // returns/assigns:	none
  // ---------------------------------------------------------------------------------------------------------------




	function error( $text, $fatal=true) {
		if(stristr($_SERVER['PHP_SELF'],'serviceajax.php'))
                {
                    echo "dberror";
		    exit;
                }
		else{
			if(function_exists(customError)){
			customError($this->socket->connect_errno,$text,$GLOBALS ['PHP_SELF'],"");
			}
			if( $fatal && $_GET['page_err']!='yes')
				{ 
				$re_direct=explode("/",$GLOBALS ['PHP_SELF']);
				
				if($re_direct[1]=='admin')
				{
						header( 'Location:'.DOMAIN_NAME.'admin/erroraccess.php?page_err=yes' );
				}
				else {
					header( 'Location: '.DOMAIN_NAME.'erroraccess.php?page_err=yes' );
					exit;
				}
				exit;
			};
		}
	
	}

  // ---------------------------------------------------------------------------------------------------------------
  // function: lastInsertId ( -- arguments -- )
  // ---------------------------------------------------------------------------------------------------------------
  // purpose:			get the last intserted id
  // arguments:         None
  // returns/assigns:	Success: insert id
  // ---------------------------------------------------------------------------------------------------------------

	function lastInsertId() 
	{
		  
		return $this->socket->insert_id;
		  
	}

  // ---------------------------------------------------------------------------------------------------------------
  // function: query ( -- arguments -- )
  // ---------------------------------------------------------------------------------------------------------------
  // purpose:			execute the query
  // arguments:         $sql
  // returns/assigns:	Success: array data
  // ---------------------------------------------------------------------------------------------------------------

	function query( $sql ) {
		
		$this->queries[] = $sql;
		
		if( !$this->connected ) 
			$this->connect();
		
		$result = $this->socket->query( $sql);
		if( $this->socket->errno){
			$this->error( $sql."Error querying database: ". $this->socket->error,false);
			return false;

		}

		if( !$result  ) 
		{
			return false;

		} else {
		
			$nr = $this->rescount;
			$this->results[$nr] = $result;
			$this->insertIDs[$nr] = $this->socket->insert_id;
			$this->rescount += 1;
			$this->queried = true;
			return( $nr );
		}
	
	}

   // ---------------------------------------------------------------------------------------------------------------
  // function: escape  ( gaaaa )
  // ---------------------------------------------------------------------------------------------------------------
  // purpose:			execute the query
  // arguments:         $sql
  // returns/assigns:	Success: array data
  // ---------------------------------------------------------------------------------------------------------------

	function escape( $val ) {

		if( !$this->connected )
			$this->connect();

        return $this->socket->real_escape_string($val);

	}

  // ---------------------------------------------------------------------------------------------------------------
  // function: getArray ( -- arguments -- )
  // ---------------------------------------------------------------------------------------------------------------
  // purpose:			get the array of data from query result
  // arguments:         $rID
  // returns/assigns:	Success: array of data
  // ---------------------------------------------------------------------------------------------------------------
	
  function getArray( $rID ) {
		
		if( !$this->queried ) {
			$this->error( "Database hasn't been queried yet" );
			return false;
		}
		$ret = array();
		while( $thing = mysqli_fetch_assoc( $this->results[ $rID ] ) )
			$ret[] = $thing;
			
		return $ret;
		
	}

  // ---------------------------------------------------------------------------------------------------------------
  // function: getOne ( -- arguments -- )
  // ---------------------------------------------------------------------------------------------------------------
  // purpose:			fetch one data from query result
  // arguments:         $rID
  // returns/assigns:	Success: array data
  // ---------------------------------------------------------------------------------------------------------------

	function getOne( $rID ) {
		
		if( !$this->queried ) {
			$this->error( "Database hasn't been queried yet" );
			return false;
		}

		$thing = mysqli_fetch_assoc( $this->results[ $rID ] );
		
		return $thing;
		
	}

  // ---------------------------------------------------------------------------------------------------------------
  // function: getArrayFromSQL ( -- arguments -- )
  // ---------------------------------------------------------------------------------------------------------------
  // purpose:			get the array of data from table
  // arguments:         $sql
  // returns/assigns:	Success: arra data
  // ---------------------------------------------------------------------------------------------------------------

	function getArrayFromSQL( $sql ) {
		if($resID = $this->query( $sql ))
			return $this->getArray( $resID );
		else
			return array();
	
	}

   // ---------------------------------------------------------------------------------------------------------------
  // function: getOneFromSQL ( -- arguments -- )
  // ---------------------------------------------------------------------------------------------------------------
  // purpose:			get the one row data from table
  // arguments:         $sql
  // returns/assigns:	Success: array of data
  // ---------------------------------------------------------------------------------------------------------------

	function getOneFromSQL( $sql ) {

		if( $resID = $this->query( $sql ) )
					return $this->getOne( $resID );
		else
			return false;
		
	}
	
  // ---------------------------------------------------------------------------------------------------------------
  // function: querySelect ( -- arguments -- )
  // ---------------------------------------------------------------------------------------------------------------
  // purpose:			fetch the array of data from table
  // arguments:         $query
  // returns/assigns:	Success: array of data
  // ---------------------------------------------------------------------------------------------------------------
	
	function querySelect($query) {
		
		if (strlen(trim($query)) < 0 ) {
			trigger_error("Database encountered empty query string in querySelect function",E_USER_ERROR);
			return false;
		}
		
		if( !$this->connected ) 
			$this->connect();
			
		if ($result = $this->socket->query($query)) {
			$this->recordsSelected = $result->num_rows;
			$this->databaseResults = $this->getData($result);
			$result->close();			
		}
		elseif($this->socket->errno!='')
		{
		
			$this->error( $sql."Error querying database: ". $this->socket->error,false);
			return false;

			}
		
		return $this->databaseResults;
	}

  // ---------------------------------------------------------------------------------------------------------------
  // function: queryExecute ( -- arguments -- )
  // ---------------------------------------------------------------------------------------------------------------
  // purpose:			execute the query
  // arguments:         $query
  // returns/assigns:	Success: query result
  // ---------------------------------------------------------------------------------------------------------------

	function queryExecute($query) {
		
		if (strlen(trim($query)) < 0 ) {
			trigger_error("Database encountered empty query string in queryExecute function",E_ERROR);
		}
		if( !$this->connected ) 
			$this->connect();
		
		if($this->socket->query($query) === true) {
			$this->recordsUpdated = $this->socket->affected_rows;
		}
	}

  // ---------------------------------------------------------------------------------------------------------------
  // function: getData ( -- arguments -- )
  // ---------------------------------------------------------------------------------------------------------------
  // purpose:			fetch the array of data from query result
  // arguments:         $result
  // returns/assigns:	Success: array of data
  // ---------------------------------------------------------------------------------------------------------------
	
	function getData($result) {
		$data = array();
		$i = 0;
		while ($row = $result->fetch_assoc()) {
			foreach ($row as $key => $value) {
				$data[$i][$key] = $value;		
			}
			$i++;
		}
		return $data;
	}

}
?>
