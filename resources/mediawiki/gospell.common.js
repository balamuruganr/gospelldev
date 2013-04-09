/**
 * check user name exists and suggest uer name
 */
jQuery( function ( $ ) {   
    $('#user_search').keyup(function() {            
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
                res = res + '<li><a href="'+window.wgScript+'?title=user:'+field+'">'+field+'</a>';     
            });    
            res = res + '</ul>';
            $('#js_user_search_result').html(res);                  
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
} );

function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
    return pattern.test(emailAddress);
};

jQuery("#wpRetype").change(function() {
    if($('#wpPassword2').val() != $('#wpRetype').val()){
        $('#wpRetype').focus();
        $("#errretype").text("password must match");
        return false;
    }
    else{
        $("#errretype").text("");
        return true;
    }        
});
    
function IsEnoughLength(str,length) { 
	if ((str == null) || isNaN(length))
		return false;
	else if (str.length < length)
		return false;
	return true;
	
}

function HasMixedCase(passwd) {    
	if(passwd.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/))
		return true;
	else
		return false;
}

function HasNumeral(passwd) {
	if(passwd.match(/[0-9]/))
		return true;
	else
		return false;
}

function HasSpecialChars(passwd) {    
	if(passwd.match(/.[!,@,#,$,%,^,&,*,?,_,~]/))
		return true;
	else
		return false;
}


function CheckPasswordStrength(pwd) {
    var pass_strength;
	if (IsEnoughLength(pwd,10) && HasMixedCase(pwd) && HasNumeral(pwd) && HasSpecialChars(pwd)) {
		pass_strength = "<font style='color:olive'>Very strong</font>";
    }
	else if (IsEnoughLength(pwd,8) && HasMixedCase(pwd) && (HasNumeral(pwd) || HasSpecialChars(pwd))) {
		pass_strength = "<font style='color:Blue'>Strong</font>";
    }
	else if (IsEnoughLength(pwd,6) && HasNumeral(pwd)){
		pass_strength = "<font style='color:Green'>Good</font>";
    }
	else if ($('#wpPassword2').val().length < 6){
		pass_strength = "<font style='color:red'>password length minimum 6</font>";
    }    
	else {
	   	pass_strength = "<font style='color:red'>Weak</font>";
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

function chkValidEmail(email) {
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;        
    if( !emailReg.test( email ) ) {
        $("#erremail").text("Please enter valid email");
        return false;
    } else {        
        return true;
    }    
}
function chkValidPassword() {       
    if($('#wpPassword2').val().length < 6){
        $("#errpassword2").text("password length minimum 6");
        return false;
    }
    if($('#wpPassword2').val() != $('#wpRetype').val()){
        $('#wpRetype').focus();
        $("#errretype").text("password must match");
        return false;
    }
    else{
        $("#errretype").text("");
        return true;
    }       
}
$('#userlogin2').submit(function() {    
    var userAgent = navigator.userAgent.toLowerCase();    
    if (/msie/.test(userAgent) && 
        parseFloat((userAgent.match(/.*(?:rv|ie)[\/: ](.+?)([ \);]|$)/) || [])[1]) <= 9) { 
            if($('#wpFirstName2').val() == ''){
                $('#wpFirstName2').focus();
                $("#errfirstname2").text("required");         
                return false;
            }
            if($('#wpLastName2').val() == ''){   
                $('#wpLastName2').focus();
                $("#errlastname2").text("required");
                return false;
            }
            if($('#wpGender2').val() == ''){   
                $('#wpGender2').focus();
                $("#errgender").text("required");
                return false;
            }
            if($('#birthday').val() == ''){   
                $('#birthday').focus();
                $("#errbirthday").text("required");
                return false;
            }                                          
            if($('#wpName2').val() == ''){   
                $('#wpName2').focus();
                $("#errname2").text("required");
                return false;
            }
            if($('#wpPassword2').val() == ''){
                $('#wpPassword2').focus();
                $("#errpassword2").text("required");
                return false;
            }            
            if($('#wpPassword2').length() < 6){
                $('#wpPassword2').focus();
                $("#errpassword2").text("password length is minimum 6");
                return false;
            }                    
            if($('#wpPassword2').val() != $('#wpRetype').val()){
                $('#wpRetype').focus();
                $("#errretype").text("password must match");
                return false;
            }    
            if($('#wpEmail').val() == ''){  
                $('#wpEmail').focus();
                $("#erremail").text("required");
                return false;
            }     
            if($('#hometown_country').val() == ''){  
                $('#hometown_country').focus();
                $("#errhomecountry").text("required");
                return false;
            }                                     
            if(!isValidEmailAddress($('#wpEmail').val())){
                $("#erremail").text("Please enter Valid email");
                return false;
            }   
            if($('#aboutme').val() == ''){  
                $('#aboutme').focus();
                $("#erraboutme").text("required");
                return false;
            }
            return true;                
    }
    if(chkValidEmail($('#wpEmail').val()) && chkValidPassword()) {        
        return true
    } else {
        return false;
    }                
});