var http=createRequestObject();
var uploader="";
var uploadDir="";
var dirname="";
var filename="";
var timeInterval="";
var idname="";
var uploaderId="";

function createRequestObject() {
    var obj;
    var browser = navigator.appName;
    if(browser == "Microsoft Internet Explorer"){
    	return new ActiveXObject("Microsoft.XMLHTTP");
    }
    else{
    	return new XMLHttpRequest();
    }   
}
function traceUpload() {
 
    	//document.getElementById(uploaderId).innerHTML="";
   http.onreadystatechange = handleResponse;
   http.open("GET", 'imageupload.php?uploadDir='+uploadDir+'&dirname='+dirname+'&filename='+filename+'&uploader='+uploader); 
   http.send(null);   
}
function handleResponse() { 
//alert(http.readyState);

	if(http.readyState == 4){
		var response=http.responseText; 
		
		if(response.indexOf("File uploaded") != -1){
			clearInterval(timeInterval);
			//document.getElementById('loading'+idname).innerHTML="";
		}
        //document.getElementById(uploaderId).innerHTML=response;
    }
    else {
    	//document.getElementById(uploaderId).innerHTML="";
    }
}
function uploadFile(obj, dname) {
	uploadDir=obj.value;	
	idname=obj.name;	
	dirname=dname;	
	filename=uploadDir.substr(uploadDir.lastIndexOf('\\')+1);	
	uploaderId = 'uploader'+obj.name;	
	uploader = obj.name;
	document.getElementById("formName").target = "iframe";
	document.getElementById('formName').submit();
	traceUpload();
}