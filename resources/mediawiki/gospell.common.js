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
    return true;
});        
