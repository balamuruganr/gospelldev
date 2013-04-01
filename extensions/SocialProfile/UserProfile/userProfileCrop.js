
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
        jcrop_api.animateTo([0,0,160,160]);
        $('#can_click,#can_move,#can_size').attr('checked','checked');
        $('#ar_lock,#size_lock,#bg_swap').attr('checked',false);
        $('.requiresjcrop').show();
    
      });
        jcrop_api.setOptions({ allowResize: false });
        jcrop_api.setOptions({ allowSelect: false });
    };    
    function getRandom() {
      var dim = jcrop_api.getBounds();
      return [
        Math.round(Math.random() * dim[0]),
        Math.round(Math.random() * dim[1]),
        Math.round(Math.random() * dim[0]),
        Math.round(Math.random() * dim[1])
      ];
    };
    function showThumbnail(e){        
        $('#x1').val(e.x);
        $('#y1').val(e.y);
        $('#x2').val(e.x2);
        $('#y2').val(e.y2);
    }

    $('#user_avatar_sub').click(function() {        
        var param = 'x1='+$('#x1').val()+'&y1='+$('#y1').val();
        $.ajax({
        url: '?'+param ,
        cache: false
        }).done(function( html ) {            
            var userExist = html.split('||');
            if($.trim(userExist[0]) == 1) {
                $("#usrAvatar_l").attr("src",userExist[1]+'l.jpg');
                $("#usrAvatar_ml").attr("src",userExist[1]+'ml.jpg');
                $("#usrAvatar_m").attr("src",userExist[1]+'m.jpg');
                $("#usrAvatar_s").attr("src",userExist[1]+'s.jpg');               
            }
        });    
    });           
});
