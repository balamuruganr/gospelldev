/**
 * check user name exists and suggest uer name
 */
jQuery( function ( $ ) {	
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
        cache: false
        }).done(function( html ) {
            var userExist = html.split('||');
            if(userExist[0] == 0){
                $('#uNameExists').html('');
            }                        
            if(userExist[0] == 1){                
                $('#uNameExists').html('Someone already has that username. Try another? <br> Available : '+userExist[1]);
            }
        });    
    });    
} );

function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
    return pattern.test(emailAddress);
};

jQuery("#wpCreateaccount").click(function() {
    if($('#wpFirstName2').val() == ''){
         $("#errfirstname2").text("required");
        return false;
    }
    if($('#wpLastName2').val() == ''){   
        $("#errlastname2").text("required");
        return false;
    }
    if($('#wpName2').val() == ''){              
        $("#errname2").text("required");
        return false;
    }
    if($('#wpPassword2').val() == ''){        
        return false;
    }    
    if($('#wpPassword2').val() != $('#wpRetype').val()){
        $("#errretype").text("password must match");
        return false;
    }
    if($('#wpEmail').val() == ''){   
        $("#erremail").text("required");
        return false;
    }  
    if(!isValidEmailAddress($('#wpEmail').val())){
        $("#erremail").text("Please enter Valid email");
        return false;
    }          
    return true;
});        
