/**
 * check user name exists and suggest uer name
 */
jQuery( function ( $ ) {	
    $('#wpName2').change(function() {
        var uname = $('#wpName2').val();
        $('#uNameExists').html('');
        if(uname == ''){
            return false;
        }        
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
