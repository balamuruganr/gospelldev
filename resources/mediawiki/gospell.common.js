/**
 * check user name exists and suggest uer name
 */
jQuery( function ( $ ) {
    
    if($('#wpTextbox1').length && wgNamespaceNumber != 500) {    

        var inaccurate_occurrence, i, match_string, sign_post_avail, patt;        
        var signpost_template_array = new Array();
        var signpost_id_array = new Array();
        
        signpost_template_array[0] = "/{{Inaccurate}}/gi";
        signpost_template_array[1] = "/{{Incomplete}}/gi";
        signpost_template_array[2] = "/{{Disputed}}/gi";
        signpost_template_array[3] = "/#REDIRECT(.*)]]/gi";
        signpost_template_array[4] = "/{{Disambiguation(.*)}}/gi";
        
        signpost_id_array[0] = "inaccurate";
        signpost_id_array[1] = "incomplete";
        signpost_id_array[2] = "disputeed";        
        signpost_id_array[3] = "redirect";
        signpost_id_array[4] = "disambiguation";
        
        var singpost_template_cnt = signpost_template_array.length;

        for( i=0; i<singpost_template_cnt; i++ ) {   
            sign_post_avail = ($("#wpTextbox1").val().match(new RegExp(eval(signpost_template_array[i])))) ? 1 : 0; 
            
            if(sign_post_avail) {                             
                $('#add'+signpost_id_array[i]).hide();
                $('#remove'+signpost_id_array[i]).show(); 
                if(signpost_id_array[i] == 'redirect') {
                    $('#removeredirect').show();
                    $('#redirect_signpost_container').hide();
                    $('#addredirect').hide();                    
                }                                 
            }           
        }                        
    };        
   $('#addinaccurate, #addincomplete, #adddisputeed, #adddisambiguation').click(function() {
    
        var txt = $("#wpTextbox1");                  
        var signpost_ary = new Array();
        var enable_id_ary = new Array();
        var add_signpost_msg_ary = new Array();        
        
        signpost_ary['addinaccurate'] = '{{Inaccurate}}';
        signpost_ary['addincomplete'] = '{{Incomplete}}';
        signpost_ary['adddisputeed']  = '{{Disputed}}';
        signpost_ary['adddisambiguation']  = '{{Disambiguation| [[Disambiguation: '+wgTitle+']]}}';
                
        add_signpost_msg_ary['addinaccurate']   = 'Inaccurate';
        add_signpost_msg_ary['addincomplete']   = 'Incomplete';
        add_signpost_msg_ary['adddisputeed']    = 'Disputed';  
        add_signpost_msg_ary['adddisambiguation']    = 'Disambiguation';              

        enable_id_ary['addinaccurate']  = 'removeinaccurate';
        enable_id_ary['addincomplete']  = 'removeincomplete';
        enable_id_ary['adddisputeed']   = 'removedisputeed';
        enable_id_ary['adddisambiguation']   = 'removedisambiguation';        
                    	
        var occurrence = (txt.val().match(new RegExp(eval('/'+signpost_ary[this.id]+'/gi')))) ? 1 : 0;  
        if(this.id == 'adddisambiguation') {
            var patt=/\{\{Disambiguation(.*)\}\}/gi;
            occurrence = (patt.test($("#wpTextbox1").val())) ? 1 : 0;                     
        }   
                                          
        if(occurrence == 0){
            txt.val(signpost_ary[this.id]+"\n" + txt.val() );
            checkSignpostRedirectAndAssignTop();                                    
            $('#'+this.id).hide();
            $('#'+enable_id_ary[this.id]).show();
            $('#signpost_msg_container').html('<font style="color:green">'+add_signpost_msg_ary[this.id]+' signpost added and please save the changes</font>');            
        }else{
            $('#signpost_msg_container').html('<font style="color:red">'+add_signpost_msg_ary[this.id]+' signpost is already added</font>');            
        }                    
    });
    
    $('#addredirect').click(function() {
            $('#redirect_signpost_container').show();
    });     
    $('#removeredirect').click(function() {
            var txt = $("#wpTextbox1"); 
            var occurrence = (txt.val().match(new RegExp(eval('/#REDIRECT(.*)]]/gi')))) ? 1 : 0; 
            if(occurrence){                
                var replace_val = txt.val().replace(eval('/#REDIRECT(.*)]]/gi'),'');
                txt.val(replace_val);
            }
            $('#removeredirect').hide();
            $('#redirect_signpost_container').hide();
            $('#addredirect').show();
    });           
    $('#btn_signpost_redirect_page').click(function() {        
        var redirect_page = $("#signpost_redirect_page").val();   
        if(redirect_page == ''){
            $('#signpost_msg_container').html('<font style="color:red">Please enter redirect page name</font>');
            return false;
        }
        var txt = $("#wpTextbox1");       
        var occurrence = (txt.val().match(new RegExp(eval('/#REDIRECT(.*)]]/gi')))) ? 1 : 0;         
        if(occurrence == 0) {            
            txt.val('#REDIRECT [['+redirect_page+']]\n' + txt.val() ); 
            $('#signpost_msg_container').html('<font style="color:green">Redirect signpost added and please save the changes</font>');                       
            $('#removeredirect').show();
            $('#addredirect').hide();
            $('#redirect_signpost_container').hide();            
        }else {
            $('#signpost_msg_container').html('<font style="color:red">Redirect signpost is already added</font>');
        }                    
    });     
                
    $('#removeinaccurate, #removeincomplete, #removedisputeed, #removedisambiguation').click(function() {    
        var txt = $("#wpTextbox1");  
        var enable_id_ary = new Array();
        var rm_signpost_ary = new Array();        
        var rm_signpost_msg_ary = new Array();
        
        rm_signpost_ary['removeinaccurate'] = '{{Inaccurate}}';
        rm_signpost_ary['removeincomplete'] = '{{Incomplete}}';
        rm_signpost_ary['removedisputeed'] = '{{Disputed}}';
        rm_signpost_ary['removedisambiguation'] = '{{Disambiguation(.*)}}';
        
        rm_signpost_msg_ary['removeinaccurate'] = 'Inaccurate';
        rm_signpost_msg_ary['removeincomplete'] = 'Incomplete';
        rm_signpost_msg_ary['removedisputeed'] = 'Disputed';
        rm_signpost_msg_ary['removedisambiguation'] = 'Disambiguation';

        enable_id_ary['removeinaccurate'] = 'addinaccurate';
        enable_id_ary['removeincomplete'] = 'addincomplete';
        enable_id_ary['removedisputeed'] = 'adddisputeed';
        enable_id_ary['removedisambiguation'] = 'adddisambiguation';
            	
        var occurrence = (txt.val().replace(new RegExp(eval('/'+rm_signpost_ary[this.id]+'/gi')))) ? 1 : 0;
        if(this.id == 'removedisambiguation') {
            var patt=/\{\{Disambiguation(.*)\}\}/gi;
            occurrence = (patt.test($("#wpTextbox1").val())) ? 1 : 0;              
        }   
        if(occurrence) {
            var replace_val = txt.val().replace(eval('/'+rm_signpost_ary[this.id]+'/gi'),'');
            txt.val(replace_val);
            $('#'+this.id).hide();
            $('#'+enable_id_ary[this.id]).show();
            $('#signpost_msg_container').html('<font style="color:green">'+rm_signpost_msg_ary[this.id]+' signpost removed and please save the changes</font>');
        }                    
    });        
    $('#wpSave').click(function() {
        if(wgNamespaceNumber != 500) {
            var inaccurate_occurrence, i, match_string, sign_post_avail;        
            var signpost_template_array = new Array();
            var signpost_template_name_array = new Array();
            
            signpost_template_array[0] = "/{{Inaccurate}}/gi";
            signpost_template_array[1] = "/{{Incomplete}}/gi";
            signpost_template_array[2] = "/{{Disputed}}/gi";
            signpost_template_array[3] = "/#REDIRECT(.*)]]/gi";
            signpost_template_array[4] = "/{{Disambiguation(.*)}}/gi";
            
            signpost_template_name_array[0] = "Inaccurate";
            signpost_template_name_array[1] = "Incomplete";
            signpost_template_name_array[2] = "Disputed";
            signpost_template_name_array[3] = "Redirect";
            signpost_template_name_array[4] = "Disambiguation";
            
            var singpost_template_cnt = signpost_template_array.length;
            for( i=0; i<singpost_template_cnt; i++ ) {   
                sign_post_avail = ($("#wpTextbox1").val().match(new RegExp(eval(signpost_template_array[i])))) ? 1 : 0;
                if(sign_post_avail) {            
                    inaccurate_occurrence = $("#wpTextbox1").val().match(new RegExp(eval(signpost_template_array[i]))).length;
                    if(inaccurate_occurrence > 1) {
                        $('#signpost_msg_container').html('<font style="color:red">'+signpost_template_name_array[i]+' signpost is added more than one time. Please add it one time and save the changes</font>');
                        return false;
                    } 
                }           
            } 
        }       
    });     
    
    mw.loader.using( ['jquery.typewatch'], function() {    
    	jQuery( function( jQuery ) { 
    		$('#user_search').typeWatch({
    			callback: searchUserName,
                captureLength:1
    		});
    		$('#disambiguation_src_txt_bx').typeWatch({
    			callback: getPageTitlesForDisambiguation,
                captureLength:1
    		});
            
    	});
    });
    $('#wpName2').change(function() {        
        var fname = $.trim($('#wpFirstName2').val());
        var lname = $.trim($('#wpLastName2').val());
        var uname = $.trim($('#wpName2').val());        
        $('#uNameExists').html('');
        if(uname == ''){
            return false;
        }
        uname = uname+'|||'+fname+lname        
        $.ajax({
        url: mw.util.wikiScript( 'index.php?title=Special:UserLogin&checkuser=1&uname='+uname ),
        cache: false,
        success: function (html) {
            var userExist = html.split('||');
            if(userExist[0] == 0){
                $('#uNameExists').html('');
            }                        
            if(userExist[0] == 1){                
                $('#uNameExists').html('Someone already has that username. Try another? <br> Available : '+userExist[1]);
            }                        
        }                
        }).done();    
    });
   ////////////////////////////////
   set_default_book();
   ///////////////////////////////     
    $('#sub_disambig_src_txt_bx').click(function() {         
        var title = $('#disambiguation_src_txt_bx').val();
        if(title === ''){
            return false;
        }
        
        title_pat = "/"+title+"]]/gi";
        title_occur = ($("#wpTextbox1").val().match(new RegExp(eval(title_pat)))) ? 1 : 0;
        if(title_occur){
            $('#disambiguation_msg_container').html('<p><font style="color:red">This page is already added</font></p>');
            return false;
        }
                                  
        $.ajax({
		url: mw.util.wikiScript( 'api' ),
		data: {
            action: 'query',
            titles: title,
            prop: 'revisions',
            rvprop: 'content',
            format: 'json',
            limit: 1
		},
        cache: false,
        success: function ( data ) {            
            var tmp_ary = data['query']['pages'];
            $('#disambiguation_msg_container').html('');
            $.each(tmp_ary, function(i, field) {                                 
                if ( typeof field['pageid'] !== 'undefined' ) {
                    var disamb_page_desc, tmp_var, res_title, full_desc;
                    
                    tmp_var = tmp_ary[field['pageid']];  
                    res_title = tmp_var['title'];                         
                    full_desc = tmp_var['revisions'][0]['*'];

                    full_desc = full_desc.replace(/(\{\{(.*)\}\})/gm,"").replace(/(\[\[File:(.*)\]\])/gm,"");//remove templates                                        
                    full_desc = full_desc.replace(/&/g, "&amp;").replace(/>/g, "&gt;").replace(/</g, "&lt;").replace(/"/g, "&quot;");//remove html special char
                    full_desc = full_desc.replace(/(\r\n|\n|\r)/gm,"");//remove tab
                    full_desc = full_desc.replace(/[^\w\s]/gi, '');//remove special char
                    disamb_page_desc = '# '+'[['+res_title+']]<p>'+full_desc.substring(0,140)+'...</p>'; 
                                           
                    $("#wpTextbox1").val( disamb_page_desc+"\n" + $("#wpTextbox1").val() );
                    $('#disambiguation_msg_container').html('<p><font style="color:green">Page added and please save the changes</font></p>');
                  }
                  else {
                    $('#disambiguation_msg_container').html('<p><font style="color:red">No Results Found</font></p>');  
                  }                   
                });    
        }                
        }).done();    
    });       
});

mw.loader.using( ['jquery.highlight'], function() {
    jQuery( function ( $ ) {
        $('#gsFindWord_btn').click(function() {
            var highlight_val = $("#gsFindWord_txt_bx").val();             
            $('#mw-content-text').removeHighlight().highlight(highlight_val);
        });
    });
});            

function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
    return pattern.test(emailAddress);
};

jQuery("#wpRetype").change(function() {
    if($('#wpPassword2').val() != $('#wpRetype').val()){
        $('#wpRetype').focus();
        $("#errretype").text("password must match");
        return false;
    }else{
        $("#errretype").text("");        
    }        
});

function CheckPasswordStrength(pwd) {
    var pass_strength = '';
	if (pwd.length > 10 && pwd.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/) && pwd.match(/[0-9]/) && pwd.match(/.[!,@,#,$,%,^,&,*,?,_,~]/)) {
		pass_strength = "<font style='color:olive'>Very strong</font>";
        $('#errpassword2').html(pass_strength);
        return false;
    }
	else if (pwd.length > 8 && pwd.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/) && (pwd.match(/[0-9]/) || pwd.match(/.[!,@,#,$,%,^,&,*,?,_,~]/))) {
		pass_strength = "<font style='color:Blue'>Strong</font>";
        $('#errpassword2').html(pass_strength);
        return false;
    }
	else if (pwd.length > 6 && pwd.match(/[0-9]/)){
		pass_strength = "<font style='color:Green'>Good</font>";
        $('#errpassword2').html(pass_strength);
        return false;        
    }
	else if (pwd.length < 6){
		pass_strength = "<font style='color:red'>The password should be a minimum of 6 characters long</font>";
        $('#errpassword2').html(pass_strength);
        return false;        
    }    
	else {
	   	pass_strength = "<font style='color:red'>Weak</font>";
        $('#errpassword2').html(pass_strength);
        return false;        
    }
    $('#errpassword2').html(pass_strength);
}

jQuery("#wpPassword2").change(function() {
    CheckPasswordStrength($("#wpPassword2").val());
});  

mw.loader.using( ['jquery.validate','jquery.ui.datepicker'], function() {    
	jQuery( function( jQuery ) { 
		jQuery( '#birthday' ).datepicker({
			changeYear: true,
            changeMonth: true,
			 yearRange: '-100:'+((-1)*13), //yearRange: '2001:c',  
			dateFormat: jQuery( '#birthday' ).hasClass( 'long-birthday' ) ? 'mm/dd/yy' : 'mm/dd'
		});	 
          
        jQuery( 'form[name="userlogin2"]' ).validate();                
	});
});

function chkValidPassword() {       
    if($('#wpPassword2').val().length < 6){
        $('#wpPassword2').focus();
        $("#errpassword2").text("The password should be a minimum of 6 characters long");
        return false;
    }else{
        $("#errpassword2").text("");
    }
    if($('#wpPassword2').val() != $('#wpRetype').val()){
        $('#wpRetype').focus();
        $("#errretype").text("password must match");
        return false;
    }else{
        $("#errretype").text("");             
    } 
    return true;      
}

$('#userlogin2').submit(function() {        
    var userAgent = navigator.userAgent.toLowerCase();    
    if (/msie/.test(userAgent) && 
        parseFloat((userAgent.match(/.*(?:rv|ie)[\/: ](.+?)([ \);]|$)/) || [])[1]) <= 9) { 
            if($('#wpFirstName2').val() == ''){
                $('#wpFirstName2').focus();
                $("#errfirstname2").text("required");         
                return false;
            }else{
                $("#errfirstname2").text("");
            }
            if($('#wpLastName2').val() == ''){   
                $('#wpLastName2').focus();
                $("#errlastname2").text("required");
                return false;
            }else{
                $("#errlastname2").text("");
            }
            if($('#wpGender2').val() == ''){   
                $('#wpGender2').focus();
                $("#errgender").text("required");
                return false;
            }else{
                $("#errgender").text("");
            }
            if($('#birthday').val() == ''){   
                $('#birthday').focus();
                $("#errbirthday").text("required");
                return false;
            }else{
                $("#errbirthday").text("");
            }                                          
            if($('#wpName2').val() == ''){   
                $('#wpName2').focus();
                $("#errname2").text("required");
                return false;
            }else{
                $("#errname2").text("");
            }
            if($('#wpPassword2').val() == ''){
                $('#wpPassword2').focus();
                $("#errpassword2").text("required");
                return false;
            }else{
                $("#errpassword2").text("");
            }            
            if($('#wpPassword2').val().length < 6){                
                $('#wpPassword2').focus();
                $("#errpassword2").text("The password should be a minimum of 6 characters long");
                return false;
            }else{
                $("#errpassword2").text("");
            }                    
            if($('#wpPassword2').val() != $('#wpRetype').val()){
                $('#wpRetype').focus();
                $("#errretype").text("password must match");
                return false;
            }else{
                $("#errretype").text("");
            }    
            if($('#wpEmail').val() == ''){  
                $('#wpEmail').focus();
                $("#erremail").text("required");
                return false;
            }else{
                $("#erremail").text("");
            }
            if(!isValidEmailAddress($('#wpEmail').val())){
                $('#wpEmail').focus();
                $("#erremail").text("Please enter Valid email");
                return false;
            }else{
                $("#erremail").text("");
            }                    
            if($('#hometown_country').val() == ''){  
                $('#hometown_country').focus();
                $("#errhomecountry").text("required");
                return false;
            }else{
                $("#errhomecountry").text("");
            }                                     
            if($('#aboutme').val() == ''){  
                $('#aboutme').focus();
                $("#erraboutme").text("required");
                return false;
            }else{
                $("#erraboutme").text("");
            }            
            return true;
    }
    
    if(!isValidEmailAddress($('#wpEmail').val())){
        $('#wpEmail').focus();
        $("#erremail").text("Please enter Valid email");
        return false;        
    }else{
        $("#erremail").text("");
    }        
    if(!chkValidPassword()) {        
        return false;
    }        
    return true;        
});

///////////////////////////// Updated for Default book ///////////////////////////
function goto_default_bookset( bookid ){
    var script_url = wgServer + ((wgScript == null) ? (wgScriptPath + "/index.php") : wgScript);
    //var blokedPages = Array("Book","UserLogout","UserLogout");   
    var hint  = "";
    var oldid = "0";
    $.getJSON(script_url, {
			'action': 'ajax',
			'rs': 'wfAjaxCollectionGetRenderBookCreatorBox',
			'rsargs[]': [hint, oldid, bookid, wgPageName]
		}, function(result) {  //$('#siteNotice').html(result.html);//save_collection(result.collection); 
                    
		   if($('.mw-body').children().is('#siteNotice')){
		      $('.mw-body').children('#siteNotice').html(result.html); 
		   } //else { $('.mw-body').prepend('<div id="siteNotice">' + result.html + '</div>');} 
                      
		});
        
        
}
function set_default_book(){
     var script_url = wgServer + ((wgScript == null) ? (wgScriptPath + "/index.php") : wgScript);
     var userTo = decodeURIComponent( wgTitle );
      
    if( wgTitle !=='Book' ){ 
        
     $.getJSON(script_url, {
			'action': 'ajax',
			'rs': 'wfAjaxSetDefaultBookSettings',
			'rsargs[]': []
		}, function(result) {
		    if(result.html > 0 ){
		      goto_default_bookset( result.html ); 
		    } 		    
		});
        
    }    
}
///////////////////////////// Updated for Default book ///////////////////////////

function checkSignpostRedirectAndAssignTop() {
    var txt = $("#wpTextbox1"); 
    var redirect_occurrence = (txt.val().match(new RegExp(eval('/#REDIRECT(.*)]]/gi')))) ? 1 : 0; 
    if(redirect_occurrence){                
        var tmp_redirect_str = txt.val().match(new RegExp(eval('/#REDIRECT(.*)]]/gi')));        
        var replace_val = txt.val().replace(eval('/#REDIRECT(.*)]]/gi'),'');  
        txt.val(replace_val);                              
        txt.val(tmp_redirect_str[0]+'\n' + txt.val() );                
    }
}

function searchUserName(){
    var uname = $.trim($('#user_search').val());                
    if(uname == ''){
        return false;
    }            
    $.ajax({
    url: mw.util.wikiScript( 'index.php?title=Special:UserLogin&user_search='+uname ),
    cache: false
    }).done(function( html ) {              
        var res = '<ul>';
        $.each(html, function(i, field){            
            var split_username = field.split('||');
            res = res + '<li><a href="'+window.wgScript+'?title=user:'+split_username[0]+'">'+split_username[1]+'</a>';     
        });    
        res = res + '</ul>';
        $('#js_user_search_result').html(res);                  
    });     
}

function getPageTitlesForDisambiguation(){
    var that = this;
    var title = $(this).val();    
    var request = $.ajax( {
    					url: mw.util.wikiScript( 'api' ),
    					data: {
                            action: 'opensearch',
                            search: title,
                            namespace: 0,
                            suggest: '',
                            format: 'json'
    					},
    					dataType: 'json',
                        cache: false,
    					success: function ( data ) {    					       					   
    						$(that).suggestions( 'suggestions', data[1] );
    					}
    				});
}