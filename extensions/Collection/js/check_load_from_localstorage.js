(function($) {

var script_url = wgServer + ((wgScript == null) ? (wgScriptPath + "/index.php") : wgScript);

function count_articles(items) {
	var count = 0;
	return count;
}


/*$(function() {
	var c = $.jStorage.get('collection');
	if (c) {
		var num_pages = 0;
		for (var i = 0; i < c.items.length; i++) {
			if (c.items[i]['type'] == 'article') {
				num_pages++;
			}
		}
		if (num_pages) {
			var txt = collection_dialogtxt;
			txt = txt.replace(/%TITLE%/, c.title ? '("' + c.title + '")' : '');
			txt = txt.replace(/%NUMPAGES%/, num_pages);
			if (confirm(txt)) {
				$.post(script_url, {
					'action': 'ajax',
					'rs': 'wfAjaxPostCollection',
					'rsargs[]': [JSON.stringify(c)]
				}, function(result) {
					window.location.href = result.redirect_url;
				}, 'json');
			}
		}
	}
}); */

//=================================================================
    $('select[name="book_type"]').change(function(){
       if( $('#book_name').val() !=='' ){
        
        var bk_type = $(this).val();
        var bk_name = $('#book_name').val();
        var bk_sbtitle = $('#sub_title').val();
        
        var str ="";
        var x; 
        x = $('#pre_creater_link').val().split("&book_type=");
        str += x[0] + '&book_type=' + bk_type;
        str +='&book_name=' + bk_name;
        str +='&sub_title=' + bk_sbtitle;
        
        $('#create-button').children("a").attr('href', str);
         
       } else {
        alert("Please enter Book Title.");
        $('#book_name').focus();
       }
        
    });
    
   $('#create-button').children("a").css('cursor',"pointer");
   
   $('#create-button').children("a").click(function(){
    
     if( $(this).attr("href") ){
        
        $('#create-button').children("a").attr('href', $(this).attr("href"));
        return true;
        
     } else {
        
        if( $('#book_name').val() !=='' ){
            
           var bk_type = $('select[name="book_type"]').val();
           var bk_name = $('#book_name').val();
           var bk_sbtitle = $('#sub_title').val();            
           var str ="";
           var x;
           
           x = $('#pre_creater_link').val().split("&book_type=");
           str += x[0] + '&book_type=' + bk_type;
           str +='&book_name=' + bk_name;
           str +='&sub_title=' + bk_sbtitle;           
           $('#create-button').children("a").attr('href', str);
           return true;
            
        } else {
            
          alert("Please enter Book Title.");
          $('#book_name').focus(); 
          return false;
            
        }       
     }
     
   });
   
})(jQuery);

/*
function set_crater_link(){
        var bk_type = $('select[name="book_type"]').val();
        var str ="";
        var x; 
        x = $('#create-button').children("a").attr("href").split("&book_type=");
        str += x[0] + '&book_type=' + bk_type;
        str +='&book_name=' + bk_name;
        $('#create-button').children("a").attr('href', str);
}
//////////////// Remove the book ////////////////////
    function remove_this_book( bookid ){
        
        alert( "TEST" +bookid );
    }
*/