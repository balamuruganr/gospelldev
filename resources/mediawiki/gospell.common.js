/**
 * Vector-specific scripts
 */
jQuery( function ( $ ) {	
    $('#wpName2').change(function() {
        var uname = $('#wpName2').val();
        $('#uNameExists').text('');
        if(uname == ''){
            return false;
        }        
        $.ajax({
        url: mw.util.wikiScript( 'index.php?title=Special:UserLogin&checkuser=1&uname='+uname ),
        cache: false
        }).done(function( html ) {            
            if(html == 1){
                $('#uNameExists').text('User name already available. Please select another one.');
            }
        });    
    });
    
} );
