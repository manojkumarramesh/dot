<?php
// -------------------------------------------------------------------------------------------------------------------
// File name   : general.class.php 
// Description : class to handle informations
//
// © 2010-2011 PaymentOne Corportation. All rights reserved.
// 
// Created date : 10-01-2011
// Modified date : 31-10-2011
// -------------------------------------------------------------------------------------------------------------------

class ClassGeneral{
	
	// ---------------------------------------------------------------------------------------------------------------
	// member declaration
	// ---------------------------------------------------------------------------------------------------------------
		
	var $tbl_admin = "";
	
	// internal use only  
	var $db_hostname = "localhost";
	var $db_username = "";
	var $db_password = "";
	var $db_name = "";
	var $db_connect = "";
	
	// ---------------------------------------------------------------------------------------------------------------
	// Genral Functions
	// ---------------------------------------------------------------------------------------------------------------
		
		// ---------------------------------------------------------------------------------------------------------------
		// function: dateFormat ( -- arguments -- )
		// ---------------------------------------------------------------------------------------------------------------
		// purpose:			method to the database.
		// arguments:		$datetime, $showtime=true
		// returns/assigns:	Success: Date Format
		//            		Error  : none
		// ---------------------------------------------------------------------------------------------------------------
		
		function dateFormat( $datetime, $showtime=true ) {
			$arr_datetime = explode( " ", $datetime );
			$arr_date = explode( "-", $arr_datetime[0] );
			if( intval($arr_date[0])>0){
				$arr_return_format['day'] = $this->DDtoDay(intval($arr_date[2]));
				$arr_return_format['month'] = $this->MMtoMonth(intval($arr_date[1]));
				$arr_return_format['year'] = $arr_date[0];
				if(!empty($arr_datetime[1]) && $showtime ){
					$arr_time = explode( ":", $arr_datetime[1] );
					if( (intval($arr_time[0])>0) || (intval($arr_time[1])>0) ){
						$arr_return_format['time'] = (($arr_time[0]>12)?($arr_time[0]%12):$arr_time[0]).":".$arr_time[1];
						$arr_return_format['daynight'] = ($arr_time[0]>=12)?"PM":"AM";
					}
				}
				return ( implode(" ", $arr_return_format) );
			}else{
				return "Null";
			}
		}
		
		// ---------------------------------------------------------------------------------------------------------------
		// function: dateYMD ( -- arguments -- )
		// ---------------------------------------------------------------------------------------------------------------
		// purpose:			method to the database.
		// arguments:		$datetime
		// returns/assigns:	Success: arr_return_format
		//            		Error  : none
		// ---------------------------------------------------------------------------------------------------------------
		
		function dateYMD( $datetime ) {
			$arr_return_format="";
			if(strstr($datetime,'/'))
				$arr_date = explode( "/", $datetime );
			else 
				$arr_date = explode( "-", $datetime );
			if( count($arr_date)>0 ){
				$arr_return_format = $arr_date[2]."-".$arr_date[1]."-".$arr_date[0];
				
				return ( $arr_return_format);
			}else{
				return "Null";
			}
		}
		
		// ---------------------------------------------------------------------------------------------------------------
		// function: dateDYM ( -- arguments -- )
		// ---------------------------------------------------------------------------------------------------------------
		// purpose:			method to the database.
		// arguments:		$datetime
		// returns/assigns:	Success: arr_return_format
		//            		Error  : none
		// ---------------------------------------------------------------------------------------------------------------
		
		function dateDMY( $datetime ) {
			$arr_return_format="";
			$arr_date = explode( "-", $datetime );
			
			if( count($arr_date)>0 ){
				$arr_return_format = $arr_date[2]."-".$arr_date[1]."-".$arr_date[0];
				
				return ( $arr_return_format);
			}else{
				return "Null";
			}
		}
		
		// ---------------------------------------------------------------------------------------------------------------
		// function: isLeapYear ( -- arguments -- )
		// ---------------------------------------------------------------------------------------------------------------
		// purpose:			method to the database.
		// arguments:		$thisyear
		// returns/assigns:	Success: true
		//            		Error  : false
		// ---------------------------------------------------------------------------------------------------------------
		
		function isLeapYear( $thisyear ) {
			if ($thisyear % 4 == 0){
				return true;
			}
				return false;
		}
		
		// ---------------------------------------------------------------------------------------------------------------
		// function: daysinmonth ( -- arguments -- )
		// ---------------------------------------------------------------------------------------------------------------
		// purpose:			method to the database.
		// arguments:		$inmon, $inyr
		// returns/assigns:	Success: Valid days
		// ---------------------------------------------------------------------------------------------------------------
		
		function daysinmonth( $inmon, $inyr ) { 
			$monthString = array(1=>31,28,31,30,31,30,31,31,30,31,30,31); 
			$monthString[2] = $this->isLeapYear($inyr)?29:28; 
			$mm = intval($inmon);
			return $monthString[$mm];
		}	
		
		// ---------------------------------------------------------------------------------------------------------------
		// function: DDtoDay ( -- arguments -- )
		// ---------------------------------------------------------------------------------------------------------------
		// purpose:			method to the database.
		// arguments:		$inputDate
		// returns/assigns:	Success: Day string
		// ---------------------------------------------------------------------------------------------------------------
		
		function DDtoDay( $inputDate ) {
		  $dateString = array(0 =>'','st','nd','rd','th','th','th','th','th','th','th','th','th','th','th','th','th','th','th','th','th','st','nd','rd','th','th','th','th','th','th','th','st'); 
			$returnDate = ''; 
			$tempDate = intval($inputDate); 
				if  ($tempDate >= 1  &&  $tempDate <= 31){
				  $returnDate = $inputDate . $dateString[$tempDate];
			}
			return $returnDate;
		}
		
		// ---------------------------------------------------------------------------------------------------------------
		// function: MMtoMonth ( -- arguments -- )
		// ---------------------------------------------------------------------------------------------------------------
		// purpose:			method to the database.
		// arguments:		$inputMonth
		// returns/assigns:	Success: month string
		// ---------------------------------------------------------------------------------------------------------------
		
		function MMtoMonth( $inputMonth ) {
			$arr_month = array( '', 'Jan', 'Feb', 'March', 'April', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec' );
			return $arr_month[intval($inputMonth)];
		}
		
		// ---------------------------------------------------------------------------------------------------------------
		// function: GetTargetDate ( -- arguments -- )
		// ---------------------------------------------------------------------------------------------------------------
		// purpose:			method to the database.
		// arguments:		$inputday, $inputmonth, $inputyear, $plus
		// returns/assigns:	Success: Target date
		// ---------------------------------------------------------------------------------------------------------------
		
		function GetTargetDate( $inputday, $inputmonth, $inputyear, $plus, $format=true ) { 
			$tempMonth = intval($inputmonth); 
			$validdate = $this->daysinmonth( $tempMonth, $inputyear ); 
			$addd = $inputday + $plus; 
			if($addd <= $validdate){ 
				$newdate = $addd; 
				$newmonth = $inputmonth; 
				$newyear =  $inputyear; 
			}else{ 
				$newdate = $addd%$validdate; 
				$newmonth1 = $inputmonth + (intval($addd/$validdate)); 
				if($newmonth1 > 12){
					$newmonth = ($newmonth1%12); 
					$newyear = $inputyear+(intval($newmonth1/12)); 
				}else{
					$newmonth = $newmonth1;
					$newyear = $inputyear;  
				}
			}  
			if($format)
				return $newyear."-".((intval($newmonth)<10)?"0".intval($newmonth):intval($newmonth))."-".((intval($newdate)<10)?"0".intval($newdate):intval($newdate));
			else
				return $newyear.((intval($newmonth)<10)?"0".intval($newmonth):intval($newmonth)).((intval($newdate)<10)?"0".intval($newdate):intval($newdate));
		}
		
	// ---------------------------------------------------------------------------------------------------------------
	// Database initialisation
	// ---------------------------------------------------------------------------------------------------------------
		
		// ---------------------------------------------------------------------------------------------------------------
		// function: initdb ( -- arguments -- )
		// ---------------------------------------------------------------------------------------------------------------
		// purpose:			method to initialize database connection.
		// arguments:		$username, $pwd, $db, $host
		// returns/assigns:	none
		// ---------------------------------------------------------------------------------------------------------------
		
		function initdb( $username, $pwd, $db, $host='localhost' ) {
			
			$this->db_hostname = $host;
			$this->db_username = $username;
			$this->db_password = $pwd;
			$this->db_name = $db;
			
			$this->db_connect = new database( 
				$this->db_username, 
				$this->db_password, 
				$this->db_name, 
				$this->db_hostname
			);
			// echo $this->db_connect."..";  
			return $this->db_connect;
		}

   		 // ---------------------------------------------------------------------------------------------------------------
		// function: createThumbnail ( -- arguments -- )
		// ---------------------------------------------------------------------------------------------------------------
		// purpose:			create Thumbnail image.
		// arguments:		$imageDirectory, $imageName, $thumbDirectory,$thumbheight,$thumbWidth
		// returns/assigns:	None
		// ---------------------------------------------------------------------------------------------------------------
	 
		function createThumbnail($imageDirectory, $imageName, $thumbDirectory,$thumbheight,$thumbWidth)
		{
		if (!file_exists( $thumbDirectory."/".$imageName))
			{
			$img="$imageDirectory/$imageName";
			$srcImg = imagecreatefromjpeg("$imageDirectory/$imageName");
			$srcsize = getimagesize($img);
			$origWidth = imagesx($srcImg);
			$origHeight = imagesy($srcImg);
			$dest_x = $thumbWidth;
			$dest_y=$thumbheight;
			$dst_img = imagecreatetruecolor($dest_x, $dest_y);
			imagecopyresampled($dst_img, $srcImg, 0, 0, 0, 0, $dest_x, $dest_y, $srcsize[0], $srcsize[1]);
			imagejpeg($dst_img, "$thumbDirectory/$imageName");
			}
		}

        // ---------------------------------------------------------------------------------------------------------------
		// function: pagination ( -- arguments -- )
		// ---------------------------------------------------------------------------------------------------------------
		// purpose:			pagination.
		// arguments:		$total_records,$limit,$targetpage,$page,$start,$type
		// returns/assigns:	pagination
		// ---------------------------------------------------------------------------------------------------------------

        function pagination($total_records,$limit,$targetpage,$page,$start,$type)
        {
            $adjacents = 2;
            if ($page == 0) $page = 1;
            $prev = $page - 1;
            $next = $page + 1;
            $lastpage = ceil($total_records/$limit);
            $lpm1 = $lastpage - 1;
            $pagination = "";
            $start1=($start+1);
	    // $remaining=$total_records-$start1;
            $remaining=$start1+$limit-1;

            if($lastpage > 1)
            {
                // first
              $pagination1=$start1."&nbsp;-&nbsp;".$remaining."&nbsp;of&nbsp;".$total_records."&nbsp;&nbsp;";

                if ($page==1)
                    $pagination.="
                    ".$pagination1.
                    "<strong>&laquo;</strong>&nbsp;first&nbsp;&nbsp;";

                else
                    $pagination.="".$pagination1."<strong>&laquo;</strong>&nbsp;<a href='$targetpage?page=1&type=$type'>first</a>&nbsp;&nbsp;";


                if ($lastpage < 7 + ($adjacents * 2))
                {
                    for ($counter = 1; $counter <= $lastpage; $counter++)
                    {
                        if ($counter == $page)
                            $pagination.= "$counter";
                        else
                            $pagination.= "<a href='$targetpage?page=$counter&type=$type'>  $counter  </a>";
                    }
                }
                elseif($lastpage > 5 + ($adjacents * 2))
                {

                    if($page < 1 + ($adjacents * 2))
                    {
                        for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
                        {
                            if ($counter == $page)
                                $pagination.= "$counter";
                            else
                                $pagination.= "<a href='$targetpage?page=$counter&type=$type'>  $counter  </a>";
                        }
                        $pagination.= "...";
                        $pagination.= "<a href='$targetpage?page=$lpm1&type=$type'>  $lpm1  </a>";
                        $pagination.= "<a href='$targetpage?page=$lastpage&type=$type'>  $lastpage  </a>";
                    }

                    elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
                    {
                        $pagination.= "<a href='$targetpage?page=1&type=$type'>  1  </a>";
                        $pagination.= "<a href='$targetpage?page=2&type=$type'>  2  </a>";
                        $pagination.= "...";
                        for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
                        {
                            if ($counter == $page)
                                $pagination.= "$counter";
                            else
                                $pagination.= "<a href='$targetpage?page=$counter&type=$type'>  $counter  </a>";
                        }
                        $pagination.= "...";
                        $pagination.= "<a href='$targetpage?page=$lpm1&type=$type'>  $lpm1  </a>";
                        $pagination.= "<a href='$targetpage?page=$lastpage&type=$type'>  $lastpage  </a>";
                    }
                    //close to end; only hide early pages
                    else
                    {
                        $pagination.= "<a href='$targetpage?page=1&type=$type'>  1  </a>";
                        $pagination.= "<a href='$targetpage?page=2&type=$type'>  2  </a>";
                        $pagination.= "...";
                        for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
                        {
                            if ($counter == $page)
                                $pagination.= "$counter";
                            else
                                $pagination.= "<a href='$targetpage?page=$counter&type=$type'>  $counter  </a>";
                        }
                    }
                }


                // previous
                 if ($page > 1)
                    $pagination.="&nbsp;&nbsp;<strong>&#8249;</strong><a href='$targetpage?page=$prev&type=$type'>
                  previous</a>&nbsp;&nbsp;&nbsp;&nbsp;";
                 else
                    $pagination.= "&nbsp;&nbsp;<strong>&#8249;</strong>previous&nbsp;&nbsp;&nbsp;&nbsp;";


                //next button
                if ($page < $counter - 1)
                    $pagination.= "<a href='$targetpage?page=$next&type=$type'>next&nbsp;</a><strong>&#8250;</strong>&nbsp;&nbsp;";
                else
                    $pagination.= "next&nbsp;<strong>&#8250;</strong>&nbsp;&nbsp;";

                if ($page==$lastpage)
                    $pagination.= "last&nbsp;<strong>&raquo;</strong>&nbsp;&nbsp;&nbsp;&nbsp;";
                else
                    $pagination.= "<a href='$targetpage?page=$lastpage&type=$type'>last&nbsp;</a><strong>&raquo;</strong>&nbsp;&nbsp;&nbsp;&nbsp;";
                    $pagination="showing: ".$pagination;
            }
       return $pagination;
       }

        // ---------------------------------------------------------------------------------------------------------------
		// function: common_pagination ( -- arguments -- )
		// ---------------------------------------------------------------------------------------------------------------
		// purpose:			pagination.
		// arguments:		$total_pages,$limit,$targetpage,$page,$start,$pagecount,$type
		// returns/assigns:	pagination
		// ---------------------------------------------------------------------------------------------------------------

     function common_pagination($total_pages,$limit,$targetpage,$page,$start,$var_name,$tag_name="")
     {
       //echo $type;
       $adjacents = 1;
        if ($page == 0) $page = 1;
            $prev = $page - 1;
            $next = $page + 1;
            $lastpage = ceil($total_pages/$limit);
            $lpm1 = $lastpage - 1;
            $pagination = "";
            $start1=($start+1);
            //$remaining=$total_pages-$start1;
            $remaining=$start1+$limit-1;

            if($total_pages < $remaining)
            {
                $remaining=$total_pages;
            }
            if($lastpage >= 1)
            {
                // first
              $pagination1=$start1."&nbsp;-&nbsp;".$remaining."&nbsp;of&nbsp;".$total_pages."&nbsp;&nbsp;";

                if ($page==1)
                    $pagination.="
                    ".$pagination1.
                    "<strong>&laquo;</strong>&nbsp;first&nbsp;&nbsp;";

                else
                    $pagination.="".$pagination1."<strong>&laquo;</strong>&nbsp;<a href='$targetpage&$var_name=1$tag_name'>first</a>&nbsp;&nbsp;";


                if ($lastpage < 7 + ($adjacents * 2))
                {
                    for ($counter = 1; $counter <= $lastpage; $counter++)
                    {
                        if ($counter == $page)
                            $pagination.= "$counter";
                        else
                            $pagination.= "<a href='$targetpage&$var_name=$counter$tag_name'>  $counter  </a>";
                    }
                }
                elseif($lastpage > 5 + ($adjacents * 2))
                {

                    if($page < 1 + ($adjacents * 2))
                    {
                        for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
                        {
                            if ($counter == $page)
                                $pagination.= "$counter";
                            else
                                $pagination.= "<a href='$targetpage&$var_name=$counter$tag_name'>  $counter  </a>";
                        }
                        $pagination.= "...";
                        $pagination.= "<a href='$targetpage&$var_name=$lpm1$tag_name'>  $lpm1  </a>";
                        $pagination.= "<a href='$targetpage&$var_name=$lastpage$tag_name'>  $lastpage  </a>";
                    }

                    elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
                    {
                        $pagination.= "<a href='$targetpage&$var_name=1$tag_name'>  1  </a>";
                        $pagination.= "<a href='$targetpage&$var_name=2$tag_name'>  2  </a>";
                        $pagination.= "...";
                        for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
                        {
                            if ($counter == $page)
                                $pagination.= "$counter";
                            else
                                $pagination.= "<a href='$targetpage&$var_name=$counter$tag_name'>  $counter  </a>";
                        }
                        $pagination.= "...";
                        $pagination.= "<a href='$targetpage&$var_name=$lpm1$tag_name'>  $lpm1  </a>";
                        $pagination.= "<a href='$targetpage&$var_name=$lastpage$tag_name'>  $lastpage  </a>";
                    }
                    //close to end; only hide early pages
                    else
                    {
                        $pagination.= "<a href='$targetpage&$var_name=1$tag_name'>  1  </a>";
                        $pagination.= "<a href='$targetpage&$var_name=2$tag_name'>  2  </a>";
                        $pagination.= "...";
                        for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
                        {
                            if ($counter == $page)
                                $pagination.= "$counter";
                            else
                                $pagination.= "<a href='$targetpage&$var_name=$counter$tag_name'>  $counter  </a>";
                        }
                    }
                }


                // previous
                 if ($page > 1)
                    $pagination.="&nbsp;&nbsp;<strong>&#8249;</strong><a href='$targetpage&$var_name=$prev$tag_name'>
                  previous</a>&nbsp;&nbsp;&nbsp;&nbsp;";
                 else
                    $pagination.= "&nbsp;&nbsp;<strong>&#8249;</strong> previous&nbsp;&nbsp;&nbsp;&nbsp;";


                //next button
                if ($page < $counter - 1)
                    $pagination.= "<a href='$targetpage&$var_name=$next$tag_name'>next&nbsp;</a><strong>&#8250;</strong>&nbsp;&nbsp;";
                else
                    $pagination.= "next&nbsp;<strong>&#8250;</strong>&nbsp;&nbsp;";

                if ($page==$lastpage)
                    $pagination.= "last&nbsp;<strong>&raquo;</strong>&nbsp;&nbsp;&nbsp;&nbsp;";
                else
                    $pagination.= "<a href=$targetpage&$var_name=$lastpage$tag_name>last&nbsp;</a><strong>&raquo;</strong>&nbsp;&nbsp;&nbsp;&nbsp;";


            $pagination="showing: ".$pagination;
            }

       return $pagination;
     }

	// ---------------------------------------------------------------------------------------------------------------
	// function: paging_url ( -- arguments -- )
	// ---------------------------------------------------------------------------------------------------------------		
	// purpose:			pagination.
	// arguments:		$total_records,$limit,$targetpage,$page,$start
	// returns/assigns:	 Pagination for
	//                   News, Reviews, Reviews
	// ---------------------------------------------------------------------------------------------------------------

	function paging_url($total_records,$limit,$targetpage,$page,$start,$type)
	{
		$adjacents = 1;
		if ($page == 0) $page = 1;
		$prev = $page - 1;
		$next = $page + 1;
		$lastpage = ceil($total_records/$limit);
		$lpm1 = $lastpage - 1;
		$pagination = "";
		$start1=($start+1);
		$remaining=$start1+$limit-1;
	
		if($lastpage > 1)
		{
		$pagination1=$start1."&nbsp;-&nbsp;".$remaining."&nbsp;of&nbsp;".$total_records."&nbsp;&nbsp;";
	
		if ($page==1)
			$pagination.="".$pagination1."<strong>&laquo;</strong>&nbsp;first&nbsp;&nbsp;";
		else
			$pagination.="".$pagination1."<strong>&laquo;</strong>&nbsp;<a href='$targetpage&page=1'>first</a>&nbsp;&nbsp;";
	
		if ($lastpage < 7 + ($adjacents * 2))
		{
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
			if ($counter == $page)
				$pagination.= "$counter";
			else
				$pagination.= "<a href='$targetpage&page=$counter'>  $counter  </a>";
			}
		}elseif($lastpage > 5 + ($adjacents * 2))
		{
			if($page < 1 + ($adjacents * 2))
			{
			for($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
			{
				if ($counter == $page)
				$pagination.= "$counter";
				else 
				$pagination.= "<a href='$targetpage&page=$counter'>  $counter  </a>";
			}
			$pagination.= "...";
			$pagination.= "<a href='$targetpage&page=$lpm1'>  $lpm1  </a>";
			$pagination.= "<a href='$targetpage&page=$lastpage'>  $lastpage  </a>";
			}elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
			{
			$pagination.= "<a href='$targetpage&page=1'>  1  </a>";
			$pagination.= "<a href='$targetpage&page=2'>  2  </a>";
			$pagination.= "...";
			for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
			{
				if ($counter == $page)
				$pagination.= "$counter";
				else
					$pagination.= "<a href='$targetpage&page=$counter'>  $counter  </a>";
			}
			$pagination.= "...";
			$pagination.= "<a href='$targetpage&page=$lpm1'>  $lpm1  </a>";
			$pagination.= "<a href='$targetpage&page=$lastpage'>  $lastpage  </a>";
			}else
			{
			$pagination.= "<a href='$targetpage&page=1'>  1  </a>";
			$pagination.= "<a href='$targetpage&page=2'>  2  </a>";
			$pagination.= "...";
			for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
			{
				if ($counter == $page)
				$pagination.= "$counter";
				else
				$pagination.= "<a href='$targetpage&page=$counter'>  $counter  </a>";
			}
			}
		}
	
		if ($page > 1)
			$pagination.="&nbsp;&nbsp;<strong>&#8249;</strong><a href='$targetpage&page=$prev'>
					previous</a>&nbsp;&nbsp;&nbsp;&nbsp;";
		else
			$pagination.= "&nbsp;&nbsp;<strong>&#8249;</strong>previous&nbsp;&nbsp;&nbsp;&nbsp;";
	
		if ($page < $counter - 1)
			$pagination.= "<a href='$targetpage&page=$next'>next&nbsp;</a><strong>&#8250;</strong>&nbsp;&nbsp;";
		else
			$pagination.= "next&nbsp;<strong>&#8250;</strong>&nbsp;&nbsp;";
	
		if ($page==$lastpage)
			$pagination.= "last&nbsp;<strong>&raquo;</strong>&nbsp;&nbsp;&nbsp;&nbsp;";
		else
			$pagination.= "<a href='$targetpage&page=$lastpage'>last&nbsp;</a><strong>&raquo;</strong>&nbsp;&nbsp;&nbsp;&nbsp;";
	
		$pagination="showing: ".$pagination;
		}
	
		return $pagination;
	}	

        // ---------------------------------------------------------------------------------------------------------------
	// function: date_incrementer ( -- arguments -- )
	// ---------------------------------------------------------------------------------------------------------------
	// purpose:			method to the database.
	// arguments:       $input
	// returns/assigns:	Success: return date
	// ---------------------------------------------------------------------------------------------------------------

	function date_incrementer($input)
	{
		$output=date("l",strtotime($input)+(3600*24*1));
		return $output;
	}

        // ---------------------------------------------------------------------------------------------------------------
	// function: date_order ( -- arguments -- )
	// ---------------------------------------------------------------------------------------------------------------
	// purpose:			method to the database.
	// arguments:       $day
	// returns/assigns:	Success: return date
	// ---------------------------------------------------------------------------------------------------------------


        function date_order($day)
	{
            if(isset($day) && ($day=='2'))
            {
                $date = "yesterday";
            }
            elseif(isset($day) && ($day=='3'))
            {
                $date = "week";
            }
            elseif(isset($day) && ($day=='4'))
            {
                $date = "month";
            }
            elseif(isset($day) && ($day=='5'))
            {
                $date = "year";
            }
            elseif(isset($day) && ($day=='1'))
            {
                $date=1;
            }
            elseif(isset($day) && ($day=='all'))
            {
                $date='all';
            }
            return $date;
	}

}

?>