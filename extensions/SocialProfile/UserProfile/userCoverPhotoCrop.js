
jQuery(function($){
    var jcrop_api;
    initJcrop();
    
    function initJcrop()
    {
      $('.requiresjcrop').hide();
      $('#target').Jcrop({        
        onChange: showThumbnail,
        onSelect: showThumbnail        
      },function(){
        jcrop_api = this;
        jcrop_api.animateTo([100,100,400,300]);
        $('#can_click,#can_move,#can_size').attr('checked','checked');
        $('#ar_lock,#size_lock,#bg_swap').attr('checked',false);
        $('.requiresjcrop').show();    
      });
    }; 
    function showThumbnail(e){        
        $('#x1').val(e.x);
        $('#y1').val(e.y);
        $('#x2').val(e.x2);
        $('#y2').val(e.y2);
        $('#w').val(e.w);
        $('#h').val(e.h);
    }
});

$('#user_avatar_sub').click(function() {          
    var param = '&x1='+$('#x1').val()+'&y1='+$('#y1').val()+'&h='+$('#h').val()+'&w='+$('#w').val();
    $.ajax({
    url: '?title='+window.wgPageName+param ,
    cache: false
    }).done(function( html ) {            
        var userExist = html.split('||');            
        if($.trim(userExist[0]) == 1) {                
            $("#user_cover_photo").attr("src",userExist[1]);   
            $('#img_cover_target_container').hide();
            $('#user_avatar_sub').remove();                                                      
        }
    });           
});  
    
