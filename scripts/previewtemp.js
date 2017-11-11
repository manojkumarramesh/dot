$(document).ready(function() {

    $(".previewsmart").colorbox({innerWidth:"320", innerHeight:"510", iframe:true, overlayClose:false});
    $(".example7").colorbox({innerWidth:"660", innerHeight:"510", iframe:true, overlayClose:false});
    $(".clickable").click( function(event) { window.location = $(this).attr("src"); event.preventDefault(); });
    $('#cboxClose').css('outline','none');
    $('#cboxClose').focus(function(){$(this).css('background-position','0 -26px'); });
    $('#cboxClose').blur(function(){$(this).css('background-position',''); });
    $('#cboxClose').attr('tabindex','0');
    $('#cboxClose').keypress(function(event) {
        if (event.keyCode == '13') {
            //event.preventDefault();
            $(this).colorbox.close();
        }
    });

    if($("#shall_temp_all").val() != null){
        $('#loading').show();
        var perSelectval=$("#shall_temp_all").val();
        var tmpVal=$("#hid_temp_id").val();
        $.post("editorajax.php",{tid:tmpVal,action:'preSelect'},function(result) {
            if(result == 0){
                $("#show_contents2").hide();    $("#show_contents").hide();     $("#show_contents3").hide();    $("#show_contents4").hide();
                $("#shall_temp_all").attr('checked','checked');
                $('#loading').hide();
            }else{
                $("#show_contents2").show();    $("#show_contents").show();     $("#show_contents3").show();        $("#show_contents4").show();
                $('#shall_temp_selected').attr('checked','checked');
                $('#loading').hide();
            }
        });
    }

    $('#shall_temp_all').live('click',function(){
        $('#loading').show();
        $("#show_contents2").hide();    $("#show_contents").hide();     $("#show_contents3").hide();        $("#show_contents4").hide();
        $('#loading').hide();
    });

    $('#shall_temp_selected').live('click',function(){
        $('#loading').show();
        $("#show_contents2").show();    $("#show_contents").show();     $("#show_contents3").show();        $("#show_contents4").show();
        $('#loading').hide();
    });

    function trim(stringToTrim) {
        return stringToTrim.replace(/^\s+|\s+$/g,"");
    }

    function getuimerchants()
    {
        var filterVal = $("#filter").val();
        var filterVal = trim(filterVal);
        var tmpVal = $("#hid_temp_id").val();
        var a='';
        $("#destination option").each(function(){
            a = $(this).val()+","+a;    // add $(this).val() to your list
        });
        if(filterVal.length > 0){
            $("#awaiting").css('display',"inline-block");
            $.post("editorajax.php",{filterval:filterVal,tid:tmpVal,action:'getfilter',filterids:a},function(result) {
                $("#showchanged").html(result);
                $("#awaiting").hide();
                $("#merchants_assign_error").hide();
            });
        }else{
            $(".success").hide();
            $("#merchants_assign_error").fadeIn();
        }
    }

    $("#filter").keypress(function(event){
        if (event.keyCode == '13'){
            event.preventDefault();
            getuimerchants();
        }
    });

    $("#save_merchants").click(function(event){
        event.preventDefault();
        $('#loading').show();
        for (var i=0; i < document.appmerc.temp_all.length; i++){
            if (document.appmerc.temp_all[i].checked){
                var rad_val = document.appmerc.temp_all[i].value;
            }
        }
        var id = $("#refid").val();
        if(rad_val == 'no'){
            var selectedArray = '';
            var selObj = document.getElementById('destination');
            var i;
            var count = 0;
            for (i=0; i<selObj.options.length; i++){
                selectedArray += selObj.options[i].value+',';
                count++;
            }
            var rightArray = '';
            var rightObj = document.getElementById('showchanged');
            var j;
            var counts = 0;
            for (j=0; j<rightObj.options.length; j++){
                rightArray += rightObj.options[j].value+',';
                counts++;
            }
            $.post("fileupload.php",{actions:'apply_to_merchants',temp_all:'no',id:id,te_refid:selectedArray},function(result) {
                location.href = 'editor.php?tid='+id+'&apply=merchants&err=2&option='+trim(result);
            });
        }
        else{
            $.post("fileupload.php",{actions:'apply_to_merchants',temp_all:'yes',id:id},function(result) {
                location.href = 'editor.php?tid='+id+'&apply=merchants&err=2&option='+trim(result);
            });
        }
    });

    $("#merchants_search").click(function(){
        getuimerchants();
    });

    $("#move_right").click(function(){ changeevent('showchanged','destination',' :selected'); });
    $("#move_left").click(function(){ changeevent('destination','showchanged',' :selected'); });
    $("#move_right_all").click(function(){ changeevent('showchanged','destination',' option'); });
    $("#move_left_all").click(function(){ changeevent('destination','showchanged',' option'); });

    function changeevent(srcid,desid,flg){
        $('#'+srcid+flg).each(function(i, selected){
            if($(selected).val() != '' && $(selected).val() != '-99'){
                if($(selected).val()!=''){
                    $('#'+desid).append('<option value="'+$(selected).val()+'">'+$(selected).text()+'</option>');
                    $("#"+srcid+"  option[value="+$(selected).val()+"]").remove();
                    $("#"+desid+"  option[value=-99]").remove();
                }
            }
        });
        $("#"+desid).each(function(){
            $(this).html($("option", $(this)).sort(function(a,b){return a.text==b.text?0:a.text < b.text?-1:1 }));
        });
    }

    // show delete confirmation
    $(".del_mov_img").live('click',function(){
        var del_id = $(this).attr("name").substring(4); // this is to take id
        $("#mov_del"+del_id).hide();
        $("#del_mov_confirm"+del_id).fadeIn();
        $("#del_mov_yes_main"+del_id).focus();
    });

    //show delete link
    $(".del_mov_no").live('click',function(){
        var msg_id = $(this).attr("id").substring(10); // this is to take id
        $("#del_mov_confirm"+msg_id).hide();
        $("#mov_del"+msg_id).fadeIn();
    });

    $(".del_mov_yes_main").live('click',function(){
        var del_id = $(this).attr("id").substring(16);
        window.location="index.php?tid="+ del_id +"&do=del&page=templates";
    });

    $(".del_mov_yes_merch").live('click',function(){
        var sid=$("#sid").val();
        var del_id = $(this).attr("id").substring(17);
        if(sid==''){
            window.location = "merchant.php?tid="+ del_id +"&do=del&page=templates";
        }else{
            window.location = "merchant.php?tid="+ del_id +"&do=del&page=templates&ser_id="+sid;
        }
    });

    // For confirmation message
    $(".checkselect_val").live('click',function(){
        var sel_id = $(this).attr("id"); // this is to take id 
        var opt = document.getElementById(sel_id);
        opt.checked = false;
        $(".show_instruction").hide();
        $(".del_sel_confirm").hide();
        $("#show_in_"+sel_id).fadeIn();
        $("#del_sel_confirm_"+sel_id).fadeIn();
    });

    $(".del_sel_no").live('click',function(){
        var msg_id = $(this).attr("id").substring(11); // this is to take id
        var opt = document.getElementById(msg_id);
        opt.checked = false;
        $("#del_mov_confirm"+msg_id).hide();
        $("#show_in_"+msg_id).hide(); 
    });

    $(".del_sel_yes_main").live('click',function(){
        var chk_id = $(this).attr("id").substring(17);	
        var opt = document.getElementById(chk_id);
        opt.checked = true;
        $("#del_mov_confirm"+chk_id).hide();
        $("#show_in_"+chk_id).hide();
    });

    $("#styleblkid").live('click',function(){
        $("#styleblk").hide();
        $.post("editorajax.php",{filterval:filterVal,tid:tmpVal,action:'getfilter'},function(result) {
            $("#showchanged").html(result);
        });
    });

    $("#show_iframe").click(function(){
        var hname = $(".hname").val() ;
        //var hname = document.location.host;
        var comp='', i;
        var now = new Date();
        var newurl = '';
        comp += $("#classname").val() + "{ ";
        for(i=0; i<8; i++){
                if($(".comp"+ i).val() != null){
                var comp0=$(".comp"+ i).val();
                var comp0id=$(".comp"+ i).attr('id');
                comp +=  $(".comp"+ i).attr('id') + ":" + $(".comp"+ i).val() +";";
                }
        }
        comp += "}";
        var oldurl = $('#oldurl').val();
        var newurl = oldurl + "&chk=preview&comp=" + escape(comp)+'time='+now;
        $('#show_it').attr('href',''); 
        $('#show_it').attr('href', hname +'/' + newurl);
        var newurl = hname +'/' + newurl;
        $('#show_it').colorbox({show_it:true, href: newurl});
        $('#show_it').trigger('click');
    });

    //
    $('#upload_apply_css').live('click',function(){

        $('#upload_css').attr('disabled', true);
        var template_id = parent.$('#template_id').val();
        $('#loading').show();
        $.post('fileupload.php',{actions:'apply_uploaded_file', ids: template_id, file: 'css'},function(result) {
            if(result == 401){
                window.location.href = 'index.php?do=login';
                return false;
            }
            $('#loading').hide();
            var result = result.split('@');
            if(result[0] == 1) {
                $('#message_css').attr('class', 'success');
                $('#message_css').html('Style Sheet applied successfully.');
                $('#apply_css').hide();
                $('#revert_css').show();
            }
            else {
                $('#message_css').attr('class', 'error');
                $('#message_css').html('Style Sheet apply failed.');
                $('#uploadcssfile').hide();
            }
            $('#upload_css').attr('disabled', false);
        });
    });

    //
    $('#upload_revert_css').live('click',function(){

        $('#upload_css').attr('disabled', true);
        var template_id = parent.$('#template_id').val();
        $('#loading').show();
        $.post('fileupload.php',{actions:'revert_uploaded_file', ids: template_id, file: 'css'},function(result) {
            if(result == 401){
                window.location.href = 'index.php?do=login';
                return false;
            }
            $('#loading').hide();
            var result = result.split('@');
            if(result[0] == 1) {
                $('#message_css').attr('class', 'success');
                $('#message_css').html('Style Sheet reverted successfully.');
            }
            else {
                $('#message_css').attr('class', 'error');
                $('#message_css').html('Style Sheet revert failed.');
            }
            $('#apply_css').hide();
            $('#revert_css').hide();
            $('#upload_css').attr('disabled', false);
        });
    });

    //
    $('#upload_apply_xml').live('click',function(){

        var template_id = parent.$('#template_id').val();
        var cou_id = $('input[name=xml_country_id]').val();
        var cou_code = $('input[name=xml_country_code]').val();
        var lang_id = $('input[name=xml_language_id]').val();
        var lang_code = $('input[name=xml_language_code]').val();
        $('#loading').show();
        $.post('fileupload.php',{actions:'apply_uploaded_file', ids: template_id, file: 'xml', country_id:cou_id, country_code:cou_code, language_id:lang_id, language_code:lang_code},function(result) {
            if(result == 401){
                window.location.href = 'index.php?do=login';
                return false;
            }
            $('#loading').hide();
            var result = result.split('@');
            if(result[0] == 1) {
                $('#message_xml').attr('class', 'success');
                $('#message_xml').html('XML file applied successfully.');
                $('#apply_xml').hide();
                $('#revert_xml').show();
            }
            else {
                $('#message_xml').attr('class', 'error');
                $('#message_xml').html('XML file apply failed.');
                $('#uploadxmlfile').hide();
            }
            $('#upload_xml').attr('disabled', false);
        });
    });

    //
    $('#upload_revert_xml').live('click',function(){

        $('#upload_xml').attr('disabled', true);
        var template_id = parent.$('#template_id').val();
        var cou_id = $('input[name=xml_country_id]').val();
        var cou_code = $('input[name=xml_country_code]').val();
        var lang_id = $('input[name=xml_language_id]').val();
        var lang_code = $('input[name=xml_language_code]').val();
        $('#loading').show();
        $.post('fileupload.php',{actions:'revert_uploaded_file', ids: template_id, file: 'xml', country_id:cou_id, country_code:cou_code, language_id:lang_id, language_code:lang_code},function(result) {
            if(result == 401){
                window.location.href = 'index.php?do=login';
                return false;
            }
            $('#loading').hide();
            var result = result.split('@');
            if(result[0] == 1) {
                $('#message_xml').attr('class', 'success');
                $('#message_xml').html('XML file reverted successfully.');
            }
            else {
                $('#message_xml').attr('class', 'error');
                $('#message_xml').html('XML file revert failed.');
            }
            $('#apply_xml').hide();
            $('#revert_xml').hide();
            $('#upload_xml').attr('disabled', false);
        });
    });

    //
    $('#applytoalltemplates').live('click',function(){

        var template_id = parent.$('#tid').val();
        var label_id = parent.$('#label_id').val();
        var template_type = parent.$('#templat_type').val();
        var temp_type = parent.$('#temp_type').val();
        var caption = jQuery.trim($('#caption').val());
        if(caption != '') {
            var r = confirm("This will apply current label value to all the existing templates in all merchants, Do you wish to continue ?");
            if(r == true) {
                $('#loading_image').show();
                $.post('fileupload.php',{actions:'apply_to_all_templates', tid: template_id, lid: label_id, type: template_type, sub: temp_type, val: caption},function(result) {
                    $('#loading_image').hide();
                    //$('#all_templates').attr('class', 'success'); $('#all_templates').html(result); $('#all_templates').show(); return false;
                    if(result == 1) {
                        $('.success').hide();
                        $('#all_templates').attr('class', 'success');
                        $('#all_templates').html('Current Label Name applied to all Template(s).');
                        $('#all_templates').show();
                    }
                    else if(result == 401){
                        window.location.href = 'index.php?do=login';
                        return false;
                    }
                });
            }
        }
        else {
            $('.success').hide();
            $('#all_templates').attr('class', 'error');
            $('#all_templates').html('Enter the UI template Current Label Name.');
            $('#all_templates').show();
        }
    });

    //
    $('#affectalltemplatelabels').live('click',function(){

        var template_id = parent.$('#tid').val();
        var label_id = parent.$('#label_id').val();
        var template_type = parent.$('#templat_type').val();
        var temp_type = parent.$('#temp_type').val();
        var caption = jQuery.trim($('#caption').val());
        if(caption != '') {
            var r = confirm("This will apply default label values to all the existing templates in all merchants, Do you wish to continue ?");
            if(r == true) {
                $('#loading_image').show();
                $.post('fileupload.php',{actions:'affect_all_template_labels', tid: template_id, lid: label_id, type: template_type, sub: temp_type, val: caption},function(result) {
                    $('#loading_image').hide();
                    //$('#all_templates').attr('class', 'success'); $('#all_templates').html(result); $('#all_templates').show(); return false;
                    if(result == 1) {
                        $('.success').hide();
                        $('#all_templates').attr('class', 'success');
                        $('#all_templates').html('Applied to all template(s).');
                        $('#all_templates').show();
                    }
                    else if(result == 401){
                        window.location.href = 'index.php?do=login';
                        return false;
                    }
                    else {
                        $('.success').hide();
                        $('#all_templates').attr('class', 'error');
                        $('#all_templates').html('Apply to all Template(s) failed.');
                        $('#all_templates').show();
                    }
                });
            }
        }
        else {
            $('.success').hide();
            $('#all_templates').attr('class', 'error');
            $('#all_templates').html('Enter the UI template current label name.');
            $('#all_templates').show();
        }
    });

    //
    $('#restoredefaultlabels').live('click',function(){

        var template_id = parent.$('#tid').val();
        var template_type = parent.$('#templat_type').val();
        var temp_type = parent.$('#temp_type').val();
        var type = parent.$('#type').val();
        if(template_id != '') {
            var r = confirm("This will restore to Default Template value(s). All changes made will be lost permanently, Do you wish to continue ?");
            if(r == true) {
                $('#loading_image').show();
                $.post('fileupload.php',{actions:'restore_default_template_data', tid: template_id, type: template_type, typ: type},function(result) {
                    $('#loading_image').hide();
                    //$('#all_templates').attr('class', 'success'); $('#all_templates').html(result); $('#all_templates').show(); return false;
                    if(result == 1) {
                        var error = '&err=20';
                    }
                    else if(result == 401){
                        window.location.href = 'index.php?do=login';
                        return false;
                    }
                    else {
                        var error = '&err=21';
                    }
                    if(type == 'label') {
                        var lid = parent.$('#labid').val();
                        var url = 'editor.php?option=labels&lid='+lid+'&tid='+template_id+'&sub='+temp_type;
                    }
                    else {
                        var cid = parent.$('#cid').val();
                        var url = 'editor.php?option=styles&cid='+cid+'&tid='+template_id;
                    }
                    window.location.href = url+error;
                });
            }
        }
    });

    //
    $('#applytoalltemplatestyles').live('click',function(){

        var template_id = parent.$('#tid').val();
        var style_id = parent.$('#cid').val();
        var template_type = parent.$('#templat_type').val();
        var r = confirm("This will affect all the existing templates in all merchants, Do you wish to continue ?");
        if(r == true) {
            $('#loading_image').show();
            var style_label = '';
            var style_value = '';
            for(i=0; i<8; i++) {
                if($(".comp"+ i).val() != null) {
                    style_label += $(".comp"+ i).attr('id')+'@';
                    style_value += $(".comp"+ i).val()+'@';
                }
            }
            var component = $("#classname").val();
            $.post('fileupload.php',{actions:'apply_to_all_templates_styles', tid: template_id, cid: style_id, type: template_type, label: style_label, value: style_value, comp: component},function(result) {
                $('#loading_image').hide();
                if(result == 1) {
                    $('.success').show();
                }
                else if(result == 401){
                    window.location.href = 'index.php?do=login';
                }
            });
        }
    });

    //
    $('#preview_button').live('click',function(){

        var valuess = jQuery.trim($('#caption').val());
        var hrefs = jQuery.trim(parent.$('#temp_href').val());
        $.post('fileupload.php',{actions:'encode_value', values: valuess},function(result) {
            if(result != '') {
                var attr = hrefs+'&preview_text='+result;
                var templat_type = jQuery.trim($("#templat_type").val());
                if(templat_type == 'smart') {
                    $('#preview_texts').colorbox({innerWidth:"320", innerHeight:"510", inline:false, iframe:true, overlayClose:false, href:attr, fastIframe:false, open:true, returnFocus:true});
                }
                else {
                    $('#preview_texts').colorbox({innerWidth:"660", innerHeight:"510", inline:false, iframe:true, overlayClose:false, href:attr, fastIframe:false, open:true, returnFocus:true});
                }
                $('#preview_texts').removeClass('cboxElement');
            }
        });
    });

    //
    $('.select_country').click(function() {

        $('#select_language').hide();
        var id = $(this).attr('value');
        var code = $('#country_code_'+id).val();
        var label_for = $('#temp_type').val();
        var tid = $('#tid').val();
        $('#loading').show();
        $.post('fileupload.php',{actions:'temp_select_language', countryid: id, countrycode: code},function(result) {
            if(result == 0){
                $('#loading').hide();
                $('#all_templates').show();
                $('#all_templates').attr('class', 'error');
                $('#all_templates').html('No Language(s) Found.');
            }
            else{
                window.location.href = 'editor.php?option=labels&sub='+label_for+'&tid='+tid;
            }
        });
    });

    //to fetch language in download xml
    $('.download_country_xml').click(function() {

        $('#download_language_xml').hide();
        var id = $(this).attr('value');
        var code = $(this).attr('name');
        $('#download_xml_file').removeAttr('href');
        $('#loading').show();
        $.post('fileupload.php',{actions:'temp_fetch_language', countryid:id, countrycode:code, page:'download_xml'},function(result) {
            $('#loading').hide();
            if(result == 0){
                $('#message_xml').show();
                $('#message_xml').attr('class', 'error');
                $('#message_xml').html('No Language(s) Found.');
            }
            else if(result == 401){
                window.location.href = 'index.php?do=login';
            }
            else{
                $('#download_language_xml').html(result);
                var lang_code = $("#language").parent().next().find("li #defaultSelected").attr('value');
                downloadxml(code, lang_code);
            }
        });
    });

     //
    $('.upload_country_xml').click(function() {

        $('#upload_language_xml').hide();
        var id = $(this).attr('value');
        var code = $(this).attr('name');
        $('input[name=xml_country_code]').val(code);
        $('input[name=xml_country_id]').val(id);
        $('input[name=xml_language_code]').val('');
        $('input[name=xml_language_id]').val('');
        $('#message_xml').hide();   $('#apply_xml').hide();     $('#revert_xml').hide();
        $('#loading').show();
        $.post('fileupload.php',{actions:'temp_fetch_language', countryid: id, countrycode: code, page: 'upload_xml'},function(result) {
            $('#loading').hide();        //alert(result);   return false;
            if(result == 0){
                $('#message_xml').show();
                $('#message_xml').attr('class', 'error');
                $('#message_xml').html('No Language(s) Found.');
            }
            else if(result == 401){
                window.location.href = 'index.php?do=login';
            }
            else{
                $('#upload_language_xml').html('');
                $('#upload_language_xml').html(result);
                var lang_id = $("#language_d").parent().next().find("li #defaultSelected").attr('class');
                var lang_code = $("#language_d").parent().next().find("li #defaultSelected").attr('value');
                uploadxml(lang_id, lang_code);
            }
        });
    });

    //
    $('#upload_preview_xml').click(function() {

        var templat_type = jQuery.trim($('#templat_type').val());
        var hrefs = jQuery.trim($('#preview_href').val());
        var coid = jQuery.trim($('input[name=xml_country_id]').val());
        var cocode = jQuery.trim($('input[name=xml_country_code]').val());
        var lgcode = jQuery.trim($('input[name=xml_language_code]').val());
        var attr = hrefs+'&country_id='+coid+'&country_code='+cocode+'&language_code='+lgcode;
        if(templat_type == 'smart') {
            $('#upload_preview_xml').colorbox({innerWidth:"320", innerHeight:"510", inline:false, iframe:true, overlayClose:false, href:attr, fastIframe:false, open:true, returnFocus:true});
        }
        else {
            $('#upload_preview_xml').colorbox({innerWidth:"660", innerHeight:"510", inline:false, iframe:true, overlayClose:false, href:attr, fastIframe:false, open:true, returnFocus:true});
        }
        $('#upload_preview_xml').removeClass('cboxElement');
    });

    $('.mer_adm_temp').click(function(){
        $('#loading').show();
    });

});

//
function downloadxml(countrycode, languagecode)
{
    var template_id = parent.$('#template_id').val();
    var hrefs = "download.php?tid="+template_id+"&file=xml&country_code="+countrycode+"&language_code="+languagecode;
    $('#download_xml_file').attr('href', hrefs);
}

//
function uploadxmlfiles(obj)
{
    $('#loading').show();
    var rand = new Date().getTime();
    $('#theuploadxmlform').attr("action", "fileupload.php?actions=upload_xml_files&id="+rand);
    $('#theuploadxmlform').submit();
    $('#postframe').load(function(){
        //iframeContents = $('iframe')[0].contentDocument.body.innerHTML;
        iframeContents = $('#postframe').contents().find('body').html();
        if(iframeContents == 401){
            window.location.href = 'index.php?do=login';
            return false;
        }
        $('#message_xml').attr('class', '');
        $('#message_xml').html(iframeContents);
        $('#message_xml').show();
        uploadedxml();
    });
    //return false;
}

//
function uploadedxml()
{
    var res = $('#message_xml').find('div').attr('id');
    if(res == 1) {
        $('#apply_xml').show();
        $('#revert_xml').hide();
    }
    else {
        $('#apply_xml').hide();
        $('#revert_xml').hide();
    }
    var txt = "<input type='file' class='file' id='xmlfile' name='xmlfile' onchange='return uploadxmlfiles(this);' />";
    $('#filexml').html(txt);
    $('#loading').hide();
}

//
function selectlanguage(languageid, languagecode)
{
    $('#loading').show();
    var countryid = $('#country_id').val();
    var countrycode = $('#country_code').val();
    var label_for = $('#temp_type').val();
    var tid = $('#tid').val();
    $.post('fileupload.php',{actions:'set_language', country_id: countryid, country_code: countrycode, language_id: languageid, language_code: languagecode},function(result) {
        window.location.href = 'editor.php?option=labels&sub='+label_for+'&tid='+tid;
    });
}

//
function uploadxml(languageid, languagecode)
{
    $('input[name=xml_language_id]').val(languageid);
    $('input[name=xml_language_code]').val(languagecode);
}

//
function upload_css_file()
{
    var template_id = parent.$('#template_id').val();
        var btnUpload=$('#uploadcss');
        new AjaxUpload(btnUpload, {
            action: '../admin/fileupload.php',
            name: 'uploadfile',
            data: {actions: 'upload', id: template_id},
            onSubmit: function(file, ext){
                if (! (ext && /^(css)$/.test(ext))) // extension is not allowed 
                {
                    $('#message_css').attr('class', 'error');
                    $('#message_css').html('Select valid CSS file.');
                    $('#apply_css').hide();
                    return false;
                }
                else
                {
                    $('#message_css').attr('class', '');
                    $('#message_css').html('');
                    $('#loading').show();
                }
            },
            onComplete: function(file, response){
                $('#loading').hide();
                if(response == 401){
                    window.location.href = 'index.php?do=login';
                    return false;
                }
                var result = response.split('@');
                if(result[0] == 1)
                {
                    $('#message_css').attr('class', 'success');
                    $('#apply_css').show();
                    $('#revert_css').hide();
                }
                else
                {
                    $('#message_css').attr('class', 'error');
                    $('#apply_css').hide();
                    $('#revert_css').hide();
                }
                $('#message_css').html(result[1]);
                $('#message_css').show();
            }
        });
}

//
function autosave(id,sid,subtype)
{
    $(document).ready(function(){
        $(".clickable").click( function(event) { window.location = $(this).attr("src"); event.preventDefault(); });
        $('#loading').show();
        var nameval = $("#type").val();
        var tid = $("#tid").val();
        var nextid = id;
        var rtnid = sid;
        var stype = subtype;

        if(nameval == 'label')
        {
            var original_caption = jQuery.trim($("#original_value").val());
            var caption = jQuery.trim($("#caption").val());
            var lid = $("#labid").val();
            if (caption.length > 0)
            {
                $.post("autosave.php",{caps:caption, tid:tid, lid:lid, stype:stype, types:nameval, nextid:nextid, orgvalue:original_caption},function(result) {

                    //$('.success').html(result);    $('.success').show();  return false;
                    if(result=="updated"){
                        var error = '&err=1';
                    }
                    else{
                        $(".success").hide();
                        var error = '';
                    }
                    if(nextid == rtnid)
                    {
                        $("#caption").val(caption);
                        $('#loading').hide();
                        var querystring = window.location.search.substring(1);
                        var pos=querystring.indexOf("err=1");
                        if (pos>=0){
                            location.reload();
                        }
                        else{
                            window.location.href = 'editor.php?option=labels&lid='+nextid+error+'&sub='+stype+'&tid='+tid+'&auto='+ nameval+'#'+nextid ;
                        }
                    }
                    else{
                        window.location.href = 'editor.php?option=labels&lid='+nextid+error+'&sub='+stype+'&tid='+tid+'&auto='+ nameval+'#'+nextid ;
                    }
                });
            }
            else
            {
                var querystring = window.location.search.substring(1);
                var pos=querystring.indexOf("err=3");
                if(pos>=0){
                    $('#loading').hide();
                }
                else{
                    window.location.href = 'editor.php?option=labels&lid='+rtnid+'&sub='+stype+'&tid='+tid+'&auto='+nameval+'&err=3'+'#'+rtnid;
                }
            }
        }

        if (nameval == 'style')
        {
            var cid = $("#cid").val();

            if($("#font-family").val() == null)
                var ff = "";
            else
                var ff = $("#font-family").val();

            if($("#background-color").val() == null)
                var bg = "";
            else
                var bg = $("#background-color").val();

            if($("#font-size").val() == null)
                var fs = "";
            else
                var fs = $("#font-size").val();

            if($("#color").val() == null)
                var cl = "";
            else
                var cl = $("#color").val();

            if($("#text-decoration").val() == null)
                var td = "";
            else
                var td = $("#text-decoration").val();

            if($("#text-align").val() == null)
                var ta = "";
            else
                var ta = $("#text-align").val();

            if($("#font-weight").val() == null)
                var fw = "";
            else
                var fw = $("#font-weight").val();

            if($("#font-style").val() == null)
                var fst = "";
            else
                var fst = $("#font-style").val();

            if($("#cursor").val() == null)
                var cr = "";
            else
                var cr = $("#cursor").val();

            if($("#background").val() == null)
                var bk = "";
            else
                var bk = $("#background").val();

            if($("#border-color").val() == null)
                var bc = "";
            else
                var bc = $("#border-color").val();

            if($("#border-bottom-color").val() == null)
                var bbc = "";
            else
                var bbc = $("#border-bottom-color").val();

            if($("#border-left-color").val() == null)
                var blc = "";
            else
                var blc = $("#border-left-color").val();

            if($("#border-top-color").val() == null)
                var btc = "";
            else
                var btc = $("#border-top-color").val();

            $.post("autosave.php",{tid:tid, cid:cid, types:nameval, nextid:nextid, FontFamily:ff, BackgroundColor:bg, Color:cl, FontSize:fs, TextDecoration:td, TextAlign:ta, FontWeight:fw, FontStyle:fst, Cursor:cr, Background:bk, BorderColor:bc, BorderBottomColor:bbc, BorderLeftColor:blc, BorderTopColor:btc},function(result) {
                var res = result.split('---');
                var style_title = res[0];
                var style_value = res[1];
                var titles = style_title.split('@');
                var org_value = style_value.split('@');
                var count = 0;
                for(i=0; i<titles.length; i++)
                {
                    if($('#'+titles[i]).val() != org_value[i])
                    {
                        count++;
                    }
                }
                if(count > 0){
                    window.location.href = 'editor.php?option=styles&cid='+nextid+'&tid='+tid+'&auto='+nameval+'#'+nextid;
                }
                else{
                    window.location.href = 'editor.php?option=styles&cid='+nextid+'&tid='+tid+'#'+nextid;
                }
            });
        }
    });
}

function goFun(id){
    var answer = confirm("Are you sure want to delete this template?");
    var tid = id;
    if (answer){
        window.location = "index.php?tid="+ tid +"&do=del&page=templates";
    }
}

function goFunM(id){
    var answer = confirm("Are you sure want to delete this template?");
    var tid = id;
    if (answer){
        window.location = "merchant.php?tid="+ tid +"&do=del&page=templates";
    }
}

function selBox(id){
    var opt = document.getElementById(id);
    if(opt.checked == true){
        var answer = confirm("Are you sure to replace the exisiting template for this service?");
        if (answer){
            opt.checked = true;
        }else{
            opt.checked = false;
        }
    }
}