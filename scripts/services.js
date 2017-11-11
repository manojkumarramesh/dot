$(document).ready(function(){

 	    $(".dragHandle").mousedown(function(event) {
 		
		//$(this).parent().find("td:first a").trigger('click');
		$('.product-view').each(function(index) {

			if($(this).html()=='Hide product details')
				$(this).html('Show product details').removeClass("selected");
			
			var pid=$(this).attr("name");
			var trd=$("#tr"+pid).html();

			if($("#remove"+pid).length)
			{
				$("#remove"+pid).remove();
			}
    
		});
     });


    var jspage=jQuery.trim($("#page_action").val());
    if(jspage=="add")
    {
        var hidcatVal=$("#catValhid").val();
        var catAuthhid=$("#catAuthhid").val();
        var catlisthid=$("#catlisthid").val();
        $("#serviceName").focus();
        if(hidcatVal=="others" || catlisthid=="0")
        {
            $("#txt_otr_cat").show();
        }	
        else
        {
             $("#txt_otr_cat").hide();
        }
         //To start category other	
        $(".category").change(function(){			
        var catVal=$(".category").val();
        if(catVal=='others')
        {				
            $("#txt_otr_cat").show();
	    $("#txt_otr_cat").focus();
        }
        else
        { 
            $("#txt_otr_cat").hide();
        }			
    });
      //To end category other

    // cancel function
    $("#edit_cancel").live('click',function(){			
	window.location.href="services.php?action=listing";
    });

    // clicks the add button
         $("#add_serv").live('click',function(){
	      var actionVal=$("#actionVal").val();		 
	      var sid=$("#service_id").val();	
	      if(this.value=="Save And Continue")
	      {
	           document.formName.action="services.php?action=add";
	      }
	      document.formName.target="";
	      document.getElementById("formName").setAttribute("enctype", "");			
	      document.formName.submit();
         });

        
     // check box check and uncheck code start


    //clicking the parent checkbox should check or uncheck all child checkboxes
    $(".parentCheckBox").click(
	function() {
	$(this).parents('fieldset:eq(0)').find('.childCheckBox').attr('checked', this.checked);
	if(this.checked==true)
	{
            fetchpayonebutton(1);
	    $("#ivs").attr("checked", true);
            $("#recognition").show();
	}
	else{
           $("input[name=home]").attr("checked", false);
           $("#recognition").hide();
	   fetchpayonebutton('');
	}
	}
    );
    //clicking the last unchecked or checked checkbox should check or uncheck the parent checkbox
    $('.childCheckBox').click(
	function() {
	if ($(this).parents('fieldset:eq(0)').find('.parentCheckBox').attr('checked') == true && this.checked == false)
	{
		$(this).parents('fieldset:eq(0)').find('.parentCheckBox').attr('checked', false);
		fetchpayonebutton($('input[name=payment_type[]]:checked').val());
		if($('input[name=payment_type[]]:checked').val() == 1)
	             $("#recognition").show();
	        else
	             $("#recognition").hide();
	}
	
	if (this.checked == true) {
		var flag = true;
		$(this).parents('fieldset:eq(0)').find('.childCheckBox').each(
			function() {
			if (this.checked == false)
				flag = false;
			}
		);
		$(this).parents('fieldset:eq(0)').find('.parentCheckBox').attr('checked', flag);
		if(flag==true)
		{
			fetchpayonebutton(1);
			$("#recognition").show();
		}
		else{
                        var id = trim($('input[name=payment_type[]]:checked').val());
			fetchpayonebutton(id);
			if(id == 1)
	                $("#recognition").show();
	                else
	                $("#recognition").hide();
		}
	}
    if($('input[name=payment_type]').attr('checked', true) && this.checked == false)
    {
	if(!($('.childCheckBox').is(':checked')))
	{
	    fetchpayonebutton('');
	    $("#recognition").hide();
	}else{
	fetchpayonebutton($('input[name=payment_type[]]:checked').val());
	    if($('input[name=payment_type[]]:checked').val() == 1)
	    $("#recognition").show();
	    else
	    $("#recognition").hide();
	}
    }


    });
// check box check and uncheck code ends


    }
    else if(jspage=="edit")
    {
        $("#add_serv").live('click',function(){
	     var actionVal=$("#actionVal").val();		 
	     var sid=$("#service_id").val();	
	     document.formName.action="services.php?action=edit&serid="+sid;
	     document.formName.target="";
	     document.getElementById("formName").setAttribute("enctype", "");			
	     document.formName.submit();
        });

	var hidcatVal=$("#catValhid").val();
        if(hidcatVal=="others")
        {
            $("#txt_otr_cat").show();
        }	
        else
        {
             $("#txt_otr_cat").hide();
        }
         //To start category other	
        $(".category").change(function(){			
		var catVal=$(".category").val();
		if(catVal=='others')
		{				
		$("#txt_otr_cat").show();
		$("#txt_otr_cat").focus();
		}
		else
		{ 
		$("#txt_otr_cat").hide();
		}			
	});
	//To end category other
    }
    else if(jspage=="products")
    {	
	//change country in product add/update
	$(".change_country").live('click',function() {	
		var country= $(this).attr('name');
		$("#product_country").val(country);
		var editid=$("#save_product_price").attr("name");
		$("#loading_home").css('display','block');
		$("#output_fail, #output_suc,#notices").hide();
		$.ajax({
			type: "POST",
			url: "serviceajax.php",
			async: true,
			data: "action=show_product_price&editid="+editid+"&country_code="+country,
			success: function(result){
	
				var resultdata=jQuery.trim(result);
				$("#loading_home").hide();
				var response=resultdata.split("||");
				
				if(response[0]=="dberror")
				{
					$("#output_fail").html("<div class='error'><ul><li><strong>Oops! Something Went Wrong</strong><br/>We are aware of the problem and actively working to fix it. <i>Please try again later</i>.</ul></li></div>");
					$("#output_fail").show();
					$('html, body').animate({scrollTop:100}, 200);
				}
				else if(response[0]=="error")
				{
					$("#output_fail").html("<div class='error'><ul><li><strong>Oops! Something Went Wrong</strong><br/>We are aware of the problem and actively working to fix it. <i>Please try again later</i>.</ul></li></div>");
					$("#output_fail").show();
					$('html, body').animate({scrollTop:100}, 200);		   	
				}
				else if(response[0]=="session_expire")
				{
					$("#output_fail").html("<div class='error'><ul><li><strong>Your session has expired. Please log in again.</i></ul></li></div>");
					$("#output_fail").show();
					$('html, body').animate({scrollTop:100}, 200);
				}
				else if(response[0]=="success")
				{
					//$("#price_detail").html(response[1]);
					document.getElementById("price_detail").innerHTML=response[1];	
				}
			}
		});
		
	});

         $(".add-item").click( function(event){
         
		
		$("#loading_home").css('display','block');
		$("#fail").hide();
		setTimeout( function() {
		var pay_id=$("#payment_type").val();
		var ser_id=$("#service_id").val();
		
			$.ajax({
			type: "POST",
			url: "serviceajax.php",
			async: false,
			data: "action=add_product&pay_id="+pay_id+"&sid="+ser_id,
			success: function(result){
			var resultdata=jQuery.trim(result);
			$("#loading_home").hide();
			if(resultdata=="dberror")
			{
			resultdata="<div class='error'><ul><li><strong>Oops! Something Went Wrong</strong><br/>We are aware of the problem and actively working to fix it. <i>Please try again later</i>.</ul></li></div>";
			}
			else if(resultdata=="error")
			{
			resultdata="<div class='error'><ul><li><strong>Oops! Something Went Wrong</strong><br/>We are aware of the problem and actively working to fix it. <i>Please try again later</i>.</ul></li></div>";		   	
			}
			else if(resultdata=="session_expire")
			{
			resultdata="<div class='error'><ul><li><strong>Your session has expired. Please log in again.</i></ul></li></div>";
			}
		
			$("#item-table, #item-add2, #item-view, .success, #iframeDisplay").hide();
			$("#item-add").html(resultdata);
			$("#item-add").fadeIn();
			
			}
		});
		}, 1000 );
               //return false;
			   event.preventDefault();
      });

	//update product price
	$("#update_product_price").live('click',function(){

 		var price=new Array();
		var price_id=new Array();
		var editid=$(this).attr("name");
		var des_id=$("#des_id").val();
		var des_val=$("#des").val();
		var des_sms=$("#des_sms").length==0?'NULL':$("#des_sms").val();
		var country_code=$("#product_country").val();
		
		/*var flag=1;
		$('input[name="update_price"]').each(function(){
			
			if(parseFloat($(this).parent().prev().prev().text()) > parseFloat($(this).val()))
			{
				$("#output_fail, #output_suc,#notices").hide();
				$("#output_fail").show();
				$("#output_fail").html("Product Price should not be lesser than Min price.");
				$('html, body').animate({scrollTop:100}, 200);
				flag=2;
				return false;
			}			
		});
		
		if(flag==1)
		{*/
			$('input[name="update_price"], select[name="update_price"]').each(function(){
				price.push($(this).val());
				price_id.push($(this).attr('id'));
			});
			$("#loading_home").css('display','block');
	
			$.post("serviceajax.php",{'action':'update_price','prices[]':price,'price_id[]':price_id,'editid':editid,'desid':des_id,'des':des_val,'sms_des':des_sms,'country':country_code },function(resultdata) {
					$("#loading_home, #output_fail, #output_suc,#notices").hide();
					var response=resultdata.split("||");
	
					if(response[0]=="success")
					{
// 						$("#output_suc").html("Product has been successfully updated.");
// 						$("#output_suc").show();
// 						$('html, body').animate({scrollTop:100}, 200);
// 					
						$("#item-add, #item-table, .success,#output_fail,#fail").hide();
						$("#item-add2").fadeIn();
						$('html, body').animate({scrollTop:100}, 200);	
						document.getElementById("item-add2").innerHTML=response[1];
						$("#output_suc").show();
						$("#output_suc").html("Product has been successfully updated.");
						
					}
					else if(response[0]=="already")
					{
						if(response[1]!='')
						{
						var emsg=response[1].split("|");
						$("#output_fail").show();
						$("#output_fail").html(emsg[1]);
						$('html, body').animate({scrollTop:100}, 200);	
						}
					}
					else{
						$("#output_fail").show();
						if(resultdata=="error" )
						{
						$("#output_fail").html('<ul><li><strong>Oops! Something Went Wrong</strong><br/>We are aware of the problem and actively working to fix it. <i>Please try again later</i>.</ul></li>');
						$('html, body').animate({scrollTop:100}, 200);
						}		
						else if(resultdata=="session_expire")
						{
						$("#output_fail").html("<ul><li><strong>Your session has expired. Please log in again.</li></ul>");
						$('html, body').animate({scrollTop:100}, 200);	
						}
						else
						{
							$("#output_fail").html(resultdata);		
							$('html, body').animate({scrollTop:100}, 200);
						}
					}
				});
		//}
	});	


	//save product price
	$("#save_product_price").live('click',function(){

		$("#loading_home").show();	
 		var price=new Array();
		var price_id=new Array();
		var editid=$(this).attr("name");
		var des_id=$("#des_id").val();
		var des_val=$("#des").val();
		var des_sms=$("#des_sms").length==0?'NULL':$("#des_sms").val();
		var country_code=$("#product_country").val();
		var ser_id=$("#service_id").val();
		var func_type=$(this).attr("rel");
		if(func_type=="insert")
		{
			var success_msg="Product has been successfully added.";
		}
		else
		{
			var success_msg="Product has been successfully updated.";
		}
		
			$('input[name="update_price"], select[name="update_price"]').each(function(){
				price.push($(this).val());
				price_id.push($(this).attr('id'));
			});
	
			$.post("serviceajax.php", {'action':'update_price','prices[]':price,'price_id[]':price_id,'editid':editid,'desid':des_id,'des':des_val,'sms_des':des_sms,'co_code':'US','ser_id':ser_id,'country':country_code},function(resultdata) {
					$("#loading_home, #output_fail, #output_suc,#notices").hide();
					var response=resultdata.split("||");
					if(response[0]=="success")
					{
						$("#item-add, #item-add2, .success,#output_fail").hide();
						$("#target_add").html(response[1]);
						$("#item-table, #iframeDisplay").fadeIn();
						$('html, body').animate({scrollTop:0}, 'slow');		
						draging();
						$(".product-detail").hide();
						$("#output_suc").show();
						$("#output_suc").html();					
						
						$("#country").html($('.dropdown ul li:last a').html());
						$(".dropdown ul li #defaultSelected").removeAttr('id');
						$('.dropdown ul li:last a').attr('id','defaultSelected');
								
					}
					else if(response[0]=="already")
					{
						if(response[1]!='')
						{
						var emsg=response[1].split("|");
						$("#output_fail").show();
						$("#output_fail").html(emsg[1]);
						$('html, body').animate({scrollTop:100}, 500);	
						}
					}
					else{
						$("#output_fail").show();
						if(resultdata=="error")
						{
						$("#output_fail").html('<ul><li><strong>Oops! Something Went Wrong</strong><br/>We are aware of the problem and actively working to fix it. <i>Please try again later</i>.</ul></li>');
						$('html, body').animate({scrollTop:100}, 500);
						}		
						else if(resultdata=="session_expire")
						{
						$("#output_fail").html("<ul><li><strong>Your session has expired. Please log in again.</li></ul>");
						$('html, body').animate({scrollTop:100}, 500);	
						}
						else
						{
							$("#output_fail").html(resultdata);		
							$('html, body').animate({scrollTop:100}, 500);
						}				
					}
				
				});

	});	

	

	$("#available_country").live('click',function(){
		if($("#available_country option:selected").length > 0)
		{	
		$("#multi-select input[type='button']:first").removeAttr("disabled");
		} 
	});

	$("#selected_country").live('click',function(){
		if($("#selected_country option:selected").length > 0)
		{	
		$("#multi-select input[type='button']:last").removeAttr("disabled");
		} 
	});
	//START multi-select for styles country
			$("#multi-select input[type='button']").live('click',function(){
				var arr = $(this).attr("name").split("2");
				var from = arr[0];
				var to = arr[1];
				$("#" + from + " option:selected").each(function(){
				  $("#" + to).append($(this).clone());
				  $(this).remove();
				});
				$(this).attr('disabled','disabled');
			  }); 
			//END multi-select for styles country

      //cancel product add 
	$("#but_cancel").live('click', function(){
		   $("#item-add").hide();
		   $("#item-table, #iframeDisplay").fadeIn();
		   $('html, body').animate({scrollTop:0}, 'slow');
		   return false;
		   })

	$("#but_edit_cancel").live('click', function(){
		   	
			$("#loading_home").css('display','block');				
			var ser_id=$("#service_id").val();	
			//var contry_code=$("#country").attr('name');
			$.ajax({
					type: "POST",
					url: "serviceajax.php",
					async: false,
					data: "action=view_product&serid="+ser_id, //+"&co_code="+contry_code,
					success: function(result){

						var resultdata=jQuery.trim(result);
						$("#loading_home").hide();
						if(resultdata=="dberror")
						{
						resultdata="<div class='error'><ul><li><strong>Oops! Something Went Wrong</strong><br/>We are aware of the problem and actively working to fix it. <i>Please try again later</i>.</ul></li></div>";
						}
						else if(resultdata=="error")
						{
						resultdata="<div class='error'><ul><li><strong>Oops! Something Went Wrong</strong><br/>We are aware of the problem and actively working to fix it. <i>Please try again later</i>.</ul></li></div>";		   	
						}
						else if(resultdata=="session_expire")
						{
						resultdata="<div class='error'><ul><li><strong>Your session has expired. Please log in again.</i></ul></li></div>";
						}
					
						$("#item-add, #item-add2, .success,#output_fail,#output_suc").hide();
						$("#target_add").html(resultdata);
						$("#item-table, #iframeDisplay").fadeIn();
						$('html, body').animate({scrollTop:0}, 'slow');
					
						$("#country").html($('.dropdown ul li:last a').html());
						$(".dropdown ul li #defaultSelected").removeAttr('id');
						$('.dropdown ul li:last a').attr('id','defaultSelected');
					}
				});
			draging();
			$(".product-detail").hide(); //Hide all content
			return false;
		   })	
		
     // show edit product detail
     $(".del_edi_img").live('click',function(){
	 // openColorbox();
	 $("#loading_home").css('display','block');
         var edit_id = $(this).attr("name").substring(5); // this is to take id
         var ser_id=$("#service_id").val();
	 $.ajax({
		type: "POST",
		url: "serviceajax.php",
		async: true,
		data: "action=edit_tmpItem&editid="+edit_id+"&sid="+ser_id,
		success: function(result){
		$("#loading_home").hide();
		var resultdata=jQuery.trim(result);
		if(resultdata=="dberror")
	 	{
                    resultdata="<div class='error'><ul><li><strong>Oops! Something Went Wrong</strong><br/>We are aware of the problem and actively working to fix it. <i>Please try again later</i>.</ul></li></div>";
	        }
		else if(resultdata=="error")
		{
		    resultdata="<div class='error'><ul><li><strong>Oops! Something Went Wrong</strong><br/>We are aware of the problem and actively working to fix it. <i>Please try again later</i>.</ul></li></div>";
		}
		else if(resultdata=="session_expire")
		{
		    $("#output_fail").hide();
		    resultdata="<div class='error'>Your session has expired. Please log in again.</div>";
		}
		//$("#ajax_content").html(resultdata);
		$("#item-table, #item-add2, #item-view, .success, #iframeDisplay").hide();
		$("#item-add").html(resultdata);
		$("#item-add").fadeIn();
		showChecked();
		}
	   });
           return false;
     });

  
$(".apply_to").live('click', function() { 
			
			showChecked(); 
		});

// add product
$("#additem").live('click',function(e){
	
    e.preventDefault();
    var sid=jQuery.trim($("#service_id").val());
    var pay_id=jQuery.trim($("#pay_type").val());
    var sku=jQuery.trim($("#txt_sku").val());
    var desc=jQuery.trim($("#txt_desc").val());
    var planid=jQuery.trim($("#txt_plan").val());
    var purchase_type=jQuery.trim($("#purchase").val());
    var category=jQuery.trim($("#category").val());
    var minprice = jQuery.trim($("#min_price").val());
    var rprice = jQuery.trim($("#txt_maxprice").val());
    var fprice = jQuery.trim($("#fixed_price").val());
    var default_country = jQuery.trim($("#default_country").val());
    var short = jQuery.trim($("#txt_shortcode").val());
    var smsval=jQuery.trim($("#txt_sms").val());    
    var ccheck=($("#chk_dmb").is(":checked")==true)?1:"";
    var mcheck=($("#chk_mobile").is(":checked")==true)?1:"";
    var hcheck=($("#chk_home").is(":checked")==true)?1:"";
    var editid=$("#edit_target").val()	
    var country='';

	$("#selected_country option").each(function () {
                country += $(this).val() + ",";
              });
     country=country.slice(0,-1);

    $("#loading_home").show();
    $.post("serviceajax.php",{serid:sid,sku_val:sku,desc_val:desc,plan_id:planid,purchase_id:purchase_type,pay_type:pay_id,max_price:rprice,min_price:minprice,fixed_price:fprice,default_country:default_country,shortcode:short,sms:smsval,cat_id:category,home_chk:hcheck,mobile_chk:mcheck,dmb_chk:ccheck,country:country,action:'insert_product'},function(result) {
	var resultdata=jQuery.trim(result);
	 if(resultdata=="dberror")
	 {
              window.location.href="erroraccess.php?page_err=yes";
	 }
	 else
	{
		var response=resultdata.split("||");	
		if(response[0]=="success")
		{
			$("#item-add, #item-table, .success,#output_fail,#fail").hide();
			$("#item-add2").fadeIn();
			$('html, body').animate({scrollTop:0}, 'slow');	
			$("#output_suc").show();
			$("#output_suc").html("Product has been successfully added.");
			document.getElementById("item-add2").innerHTML=response[1];
		}
		else if(response[0]=="already")
		{
			if(response[1]!='')
			{
			var emsg=response[1].split("|");
			$("#itemerror").show();
			$("#itemerror").html(emsg[1]);
			$('html, body').animate({scrollTop:200}, 500);	
			}
		}
		else{
			$("#itemerror").show();
			if(resultdata=="error")
			{
			$("#itemerror").html('<ul><li><strong>Oops! Something Went Wrong</strong><br/>We are aware of the problem and actively working to fix it. <i>Please try again later</i>.</ul></li>');
			$('html, body').animate({scrollTop:200}, 500);
			}		
			else if(resultdata=="session_expire")
			{
			$("#itemerror").html("<ul><li><strong>Your session has expired. Please log in again.</li></ul>");
			$('html, body').animate({scrollTop:200}, 500);	
			}
			else{
			$("#itemerror").html(resultdata);		
			$('html, body').animate({scrollTop:200}, 500);
			}
			// function called for tow times to avoid scroll in I.E and Opera
			//$.fn.colorbox.resize();
			//setTimeout ('$.fn.colorbox.resize()', 200);
		//draging();
		}
	}
	$("#loading_home").hide();
    });
	
 });

// save product
$("#saveitem").live('click',function(e){
    e.preventDefault();
    var sid=jQuery.trim($("#service_id").val());
    var pay_id=jQuery.trim($("#pay_type").val());
    var sku=jQuery.trim($("#txt_sku").val());
    var desc=jQuery.trim($("#txt_desc").val());
    var planid=jQuery.trim($("#txt_plan").val());
    var purchase_type=jQuery.trim($("#purchase").val());
    var category=jQuery.trim($("#category").val());
    var minprice = jQuery.trim($("#min_price").val());
    var rprice = jQuery.trim($("#txt_maxprice").val());
    var fprice = jQuery.trim($("#fixed_price").val());
    var smsval=jQuery.trim($("#txt_sms").val());
    var ccheck=($("#chk_dmb").is(":checked")==true)?1:"";
    var mcheck=($("#chk_mobile").is(":checked")==true)?1:"";
    var hcheck=($("#chk_home").is(":checked")==true)?1:"";
    var edit_type = jQuery.trim($("#edit_type").val());
    var editid = jQuery.trim($("#edit_target").val());
    var country='';

	$("#selected_country option").each(function () {
                country += $(this).val() + ",";
              });
     country=country.slice(0,-1);

    $("#loading_home").show();
    $.post("serviceajax.php",{serid:sid,sku_val:sku,desc_val:desc,plan_id:planid,purchase_id:purchase_type,pay_type:pay_id,max_price:rprice,min_price:minprice,fixed_price:fprice,sms:smsval,cat_id:category,home_chk:hcheck,mobile_chk:mcheck,dmb_chk:ccheck,edittype:edit_type,editid:editid,country:country,action:'update_product'},function(result) {

	var resultdata=jQuery.trim(result);
	 if(resultdata=="dberror")
	 {
              window.location.href="erroraccess.php?page_err=yes";
	 }
	 else{
	     	var response=resultdata.split("||");

		if(response[0]=="success")
		{
			alert("Changes to price, SMS description and  product description will not affect the products.");			
			$("#item-add, #item-table, .success,#output_fail,#fail").hide();
			$("#item-add2").fadeIn();
			$('html, body').animate({scrollTop:0}, 'slow');		
			document.getElementById("item-add2").innerHTML=response[1];
 			$("#output_suc").show();
 			$("#output_suc").html("Product has been successfully updated.");
		}
		else if(response[0]=="already")
		{
			if(response[1]!='')
			{
			var emsg=response[1].split("|");
			$("#itemerror").show();
			$("#itemerror").html(emsg[1]);
			$('html, body').animate({scrollTop:200}, 500);	
			}
		}
		else{
			
			$("#itemerror").show();
			if(resultdata=="error")
			{
			$("#itemerror").html('<ul><li><strong>Oops! Something Went Wrong</strong><br/>We are aware of the problem and actively working to fix it. <i>Please try again later</i>.</ul></li>');
			$('html, body').animate({scrollTop:200}, 500);
			}		
			else if(resultdata=="session_expire")
			{
			$("#itemerror").html("<ul><li><strong>Your session has expired. Please log in again.</li></ul>");
				$('html, body').animate({scrollTop:200}, 500);
			}
			else{
			$("#itemerror").html(resultdata);
			$('html, body').animate({scrollTop:200}, 500);
			}
			//$.fn.colorbox.resize();
			//setTimeout ('$.fn.colorbox.resize()', 200);		
		}
	}
	$("#loading_home").hide();
    });
	
 });
 // show delete confirmation
    $(".del_mov_img").live('click',function(e){
	e.preventDefault();
	 $("#output_suc").hide();
	 $("#output_fail").hide();
	 $("#itemerror").hide();
        var del_id = $(this).attr("name").substring(4); // this is to take id
        $("#mov_del"+del_id).hide();
        $("#del_mov_confirm"+del_id).fadeIn();
    });
   //show delete link
    $(".del_mov_no").live('click',function(e){
	e.preventDefault();
         var msg_id = $(this).attr("id").substring(10); // this is to take id                            
         $("#del_mov_confirm"+msg_id).hide();
         $("#mov_del"+msg_id).fadeIn();
    });

   // delete product
    $(".del_mov_yes").live('click',function(e){
	e.preventDefault();
	$("#loading_home").show();
         var del_id = $(this).attr("id").substring(11); // this is to take id
	 var actionVal=$("#actionVal").val();
         var serviceId=$("#service_id").val();
	 var country_code=$("#country").attr('name');
         $.post("serviceajax.php",{delid:del_id,serid:serviceId,action:'del_tmpItem',co_code:country_code},function(result) {

	  $("#itemlogo").show();
	     var resultdata=jQuery.trim(result);
	     if(resultdata=="dberror")
	     {
		  window.location.href="erroraccess.php?page_err=yes";
	     }
	     else{

		//$("#itemlogo").show();
		var response=resultdata.split("||");

		if(response[0]=="success")
		{	//$('#but_edit_cancel').trigger('click');		
			$("#item-add, #item-add2, .success,#output_fail").hide();
			$("#output_suc").show();
 			$("#output_suc").html("Product has been successfully removed.");	
			$("#target_add").html(response[1]);
			$("#item-table, #iframeDisplay").fadeIn();
			$(".product-detail").css("display","none");
			$('html, body').animate({scrollTop:0}, 'slow');			
		}
		else if(response[0]=="session_expire")
		{	
			$("#item-add, #item-add2, .success,#output_suc").hide();
			$("#output_fail").show();
			var errorval = 'Your session has expired. Please log in again.';
			$("#output_fail").html(errorval);
			$(".product-detail").css("display","none");
			$('html, body').animate({scrollTop:0}, 'slow');
		}
		else{	
			$("#item-add, #item-add2, .success,#output_suc").hide();
			$("#output_fail").show();
			var errorval = '<strong>Oops! Something Went Wrong</strong><br/>We are aware of the problem and actively working to fix it. <i>Please try again later</i>.';
			$("#output_fail").html(errorval);			
			$("#target_add").html(response[1]);
			$(".product-detail").css("display","none");
			$('html, body').animate({scrollTop:0}, 'slow');
		}
		draging();
	     }
		$("#loading_home").hide();
	 }); 
     });
    
   } // end of product listing
       else if (jspage=="listing")
       {
            $(".del_mov_yes_main").live('click',function(){
	    var del_id = $(this).attr("id").substring(16);
	    var pageval= $('#pageval').val();
	    var actflag=$('#actflag').val();
	    if(actflag==0)
	    {
	         document.myservice.action="services.php?action=delete&serid="+del_id+"&page_val="+pageval;
	    }
	    else{
	        document.myservice.action="services.php?action=delete&st="+actflag+"&serid="+del_id+"&page_val="+pageval;
	   }
	   document.myservice.submit();
           });
           $("#add_new_ser").live('click',function(){
	        document.myservice.action="services.php?action=add";
	        document.myservice.submit();
           });

	// show delete confirmation
    $(".del_mov_img").live('click',function(e){
	e.preventDefault();
        var del_id = $(this).attr("name").substring(4); // this is to take id	
        $("#mov_del"+del_id).hide();
        $("#del_mov_confirm"+del_id).fadeIn();
    });
   //show delete link
    $(".del_mov_no").live('click',function(e){	
	e.preventDefault();
         var msg_id = $(this).attr("id").substring(10); // this is to take id	 
         $("#del_mov_confirm"+msg_id).hide();
         $("#mov_del"+msg_id).fadeIn();
    });

        }
	else if(jspage=="view")
	{
            $(".paymentonescript").live('click',function(){			
	         $(".paymentonescript").select();
            });
	    $("#paymentoneURL").live('click',function(){
		$(this).select();
	    });
	    $(".p1iframe").live('click',function(){
	         $(".p1iframe").select();
            });	
        }
}); 

// service listing
function sendit(v)
{
    document.myservice.hid_selectCat.value=document.myservice.selectCat.value;
    document.myservice.action="services.php?action=listing&st="+v;
    document.myservice.submit();
}
//start upload function
function startUpload(){
    document.getElementById('f1_upload_process').style.visibility = 'visible';
    document.getElementById('f1_upload_form').style.visibility = 'hidden';
    return true;
}

// stop upload function
function stopUpload(success)
{
    var result = '';
    if (success == 1){
        result = '<span class="msg">The file was uploaded successfully!<\/span><br/><br/>';
    }
    else{
        result = '<span class="emsg">There was an error during file upload!<\/span><br/><br/>';
    }
      document.getElementById('f1_upload_process').style.visibility = 'hidden';
      document.getElementById('f1_upload_form').innerHTML = result + '<label>File: <input name="myfile" type="file" size="30" /><\/label><label><input type="submit" name="submitBtn" class="sbtn" value="Upload" /><\/label>';
      document.getElementById('f1_upload_form').style.visibility = 'visible';      
      return true;   
}

// check the number key code
function isNumberKey(evt)
{
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
    return false;
    return true;
}

//to close the iframe
function closeoverlay()
{
	parent.$('.change_button').colorbox.close();
}

// trim the string
function trim(stringToTrim) {
        return stringToTrim.replace(/^\s+|\s+$/g,"");
}

// code to delete Payone Button - starts
    //code when delete is clicked
    $(".payone_delete").live('click',function(){
        var delete_id = $(this).attr("name").substring(14);// this is to take id
        $("#payone_delete_"+delete_id).hide();
        $("#payone_delete_confirm"+delete_id).fadeIn();
    });

    //code when N is clicked
    $(".payone_delete_confirm_no").live('click',function(){
        var id = $(this).attr("id").substring(25); // this is to take id
        $("#payone_delete_confirm"+id).hide();
        $("#payone_delete_"+id).fadeIn();
    });

    //code when Y is clicked
    $(".payone_delete_confirm_yes").live('click',function(){
        var id = $(this).attr("id").substring(26); // this is to take id
        var pages = $("#page").val();
        window.location = "p1buttons.php?action=delete&id="+id+"&page="+pages;
    });
// code to delete Payone Button - ends

// code to Service show Payone Button - starts
function fetchpayonebutton(id)
{
   $('#add_serv').attr('disabled','disabled');	

    if (id != '')
    {
        $.post("selectp1buttons.php",{actions:'fetch_default_button_image', id:id},function(result) {
		
            $("#show_default").fadeIn();
            $("#show_default_image").fadeIn();
            var attr = 'selectp1buttons.php?payment_id='+id+'&actions=fetch_all_buttons_for';
            var res = result.split(',');
            if(res[0] != '')
            {
                $('#image_id').val(trim(res[0]));
                var txt = '<img src=../data/images/original/'+res[1]+'>';
                $('#show_default_image').html(txt);
                if(res[2] == 1)
                {
                    parent.$('#default_buttons').html('Using Default Button');
                    parent.$('#chn_button').html(' | Select Different Button');
                    $('.change-button').colorbox({innerWidth:"660", innerHeight:"350", inline:false, iframe:true, overlayClose:false, href:attr});
			setTimeout(enablebutton(),'4000');	
                }
                else
                {
                    var txt = "<a href=#p1btn-preview id=applydefaultbutton>Apply Default Button</a>";
                    parent.$('#default_buttons').html(txt);
                    parent.$('#chn_button').html(' | Select Different Button');
			setTimeout(enablebutton(),'4000');
                }
		
            }
            else
            {
                $('#image_id').val('0');
                var img = '<img src="../images/noimage.jpeg" title="No image"/>';
                $('#show_default_image').html(img);
		setTimeout(enablebutton(),'4000');
            }
	    //$('#add_serv').removeAttr('disabled');	
        });
    }
    else
    {
        $.post("selectp1buttons.php",{actions:'clear_image'},function(result) {
		
            $("#show_default").hide();
            $("#show_default_image").hide();
            $('#image_id').val('0');
	    setTimeout(enablebutton(),'3000');
	    //$('#add_serv').removeAttr('disabled');	
        });
    }
}
function enablebutton()
{
	$('#add_serv').removeAttr('disabled');
}
// home paymentment option
$(':radio').live('click',function(){
$("#home").attr("checked",true);
    var checkflag = true;
    $(this).parents('fieldset:eq(0)').find('.childCheckBox').each(
    function() {
        if (this.checked == false)
	    checkflag = false;
	}
);
$(this).parents('fieldset:eq(0)').find('.parentCheckBox').attr('checked', checkflag);
	if(checkflag=="true")
	{
	    fetchpayonebutton(1);
	    $("#recognition").show();
	}
	else{
	     if($('input[name=payment_type[]]:checked').val() == 1)
	     $("#recognition").show();
	     else
	     $("#recognition").hide();
	    fetchpayonebutton($('input[name=payment_type[]]:checked').val());
	}

});

//to make the selected button image as the default
function selectthisbutton(image_id)
{
    $.post("selectp1buttons.php",{actions:'select_this_button', image_id:image_id},function(result) {
        var res = result.split(',');
        if(res[0] != '')
        {
            parent.$('#image_id').val(trim(res[0]));
            var txt = '<img src=../data/images/original/'+res[1]+'>';
            parent.$('#show_default_image').html(txt);
            if(res[2] == 1)
            {
                parent.$('#default_buttons').html('Using Default Button');
                closeoverlay();
            }
            else
            {
                var txt = "<a href=#p1btn-preview id=applydefaultbutton >Apply Default Button</a>";
                parent.$('#default_buttons').html(txt);
                closeoverlay();
            }
        }
        else
        {
            parent.$('#image_id').val('0');
            parent.$('#default_buttons').html('<img src="../images/noimage.jpeg" title="No image"/>');
            closeoverlay();
        }
    });
}

// show selected button
$("#selected_button").live('click',function(){
    var image_id = $(this).attr('value');
    selectthisbutton(image_id);
});

// apply default button
$("#applydefaultbutton").live('click',function(){
    var value = $("input:checked").attr('value');
    fetchpayonebutton(value);
});
// code to Service show Payone Button - ends

// open colorbox
function openColorbox()
{
    $.fn.colorbox({onOpen: function() {$('#cboxClose').remove(); showChecked();},onClosed:function() {$.fn.colorbox.init(); },height:400, overlayClose:false, inline:true, href:"#ajax_content"});
}

// load ajax content
function ajaxContent(rval)
{
    var pay_id=$("#payment_type").val();
    var ser_id=$("#service_id").val();
    $("#ajax_content").load("products.php?action=add&pay_id="+pay_id+"&sid="+ser_id);
    //$("#ajax_content").load("../templates/default/admin/service-add-product-items.html "+rval);
}
	
// check the payment type
function showChecked()
{

// 		$(".apply-to").each(function(){
// 			if($(this).is(":checked"))
// 			{
// 				$(this).parent().next('div').show();
// 			}
// 			else
// 			{
// 				$(this).parent().next('div').hide();
// 			}
// 		});	
        if($("#chk_dmb").is(':checked') || $("#chk_mobile").is(':checked'))
        {
// 		  $("input[name$='apply-to']:checked").parent().next('div').show();
        $('#psms_dmb').show();
        }
        else
        {
            $('#psms_dmb').hide();
        
      /*  $("input[name$='apply-to']:not(:checked)").parent().next('div').hide();*/	
        
        }
        if($("#chk_home").is(':checked'))
        {
            $('#home').show();
        }
        else
        {
            $('#home').hide();
        }

        if(!$("#chk_mobile").is(':checked') && !$("#chk_home").is(':checked'))
	{
		$('#fixedprice').hide();
	} 
	else
	{
			$('#fixedprice').show();
	}


			if($("#chk_dmb").is(':checked') || $("#chk_home").is(':checked'))
			{
				$('#retailprice').show();
				$('#minimumprice').show();
			} 

			if(!$("#chk_dmb").is(':checked') && !$("#chk_home").is(':checked'))//if($("#chk_dmb").not(':checked') && $("#chk_home").not(':checked'))
			{
				$('#retailprice').hide();
				$('#minimumprice').hide();
			}		

}
// drag and drop functionality
function draging()
{ 
    $('#table-5').tableDnD({
        onDrop: function(table, row) { 
            var ser_id=$("#service_id").val();
	    var country=$("#country").attr('name'); 	
	    var param1 = $('#table-5').tableDnDSerialize();
	    if(param1=='')
	    {return false;}
            var param = $('#table-5').tableDnDSerialize() +'&action=update_order&sid='+ser_id+'&country='+country;		
	    $.post("serviceajax.php", param, function(results){	
		if(results=="success")
		{
		     $("#output_fail").hide();
		     $("#output_suc").html("Order has been successfully updated.");
		     $("#output_suc").show();
		}
		else if(results=="session_expire")
		{
			$("#output_fail").html("Your session has expired. Please log in again.");
			$("#output_fail").show();
			$('html, body').animate({scrollTop:100}, 200);
		}
		else{
		    $("#output_suc").hide();
		    $("#output_fail").html("<strong>Oops! Something Went Wrong</strong><br/>We are aware of the problem and actively working to fix it. <i>Please try again later</i>.");
		    $("#output_fail").show();
		}
	    });
        },
        dragHandle: "dragHandle"
    });
}

function merchantDropdown(co_code)
{	
	$(".res_loading").show();
	$("#country").attr("name",co_code);
	var serid=$("#htn_service_id").val();
	var page_action=$("#htn_action").val();	

	$.ajax({
		type: "POST",
		url: "serviceajax.php",
		async: true,
		data: "action=view_product&serid="+serid+"&co_code="+co_code+"&page_action="+page_action,
		success: function(result){
			$(".res_loading").hide();
			var resultdata=jQuery.trim(result);
			$("#loading_home").hide();
			if(resultdata=="dberror")
			{
			resultdata="<div class='error'><ul><li><strong>Oops! Something Went Wrong</strong><br/>We are aware of the problem and actively working to fix it. <i>Please try again later</i>.</ul></li></div>";
			}
			else if(resultdata=="error")
			{
			resultdata="<div class='error'><ul><li><strong>Oops! Something Went Wrong</strong><br/>We are aware of the problem and actively working to fix it. <i>Please try again later</i>.</ul></li></div>";		   	
			}
			else if(resultdata=="session_expire")
			{
			resultdata="<div class='error'><ul><li><strong>Your session has expired. Please log in again.</i></ul></li></div>";
			}
		
			$("#item-add, #item-add2, .success,#output_fail,#output_suc").hide();
			$("#target_add").html(resultdata);
			$("#item-table, #iframeDisplay").fadeIn();
			$('html, body').animate({scrollTop:0}, 'slow');
			$(".product-detail").hide(); //Hide all content
			draging();
		}
	});
	return false;
}

function merchantHelptext(co_id)
{	
	$(".res_loading").show();
	var service_id = $("#htn_service_id").val();		
	$.ajax({
		type: "POST",
		url: "serviceajax_dulp.php",
		async: true,
		data: "action=edit_helptext&service_id="+service_id+"&co_id="+co_id,
		success: function(result){
			$(".res_loading").hide();
			$("#language_helptext").html(result);
		}
	});
	return false;
}