/**
 * JavaScript functions used by UserProfile
 */
var posted = 0;
function send_message() {
	var userTo = decodeURIComponent( wgTitle ); //document.getElementById( 'user_name_to' ).value;
	var encMsg = encodeURIComponent( document.getElementById( 'message' ).value );
    var p = 0;
     if($('#featch_url_content_block').is(":visible")){        
         var cont = encodeURIComponent( $('#featch_url_content_block').html() );
         encMsg = encMsg + cont;
         p = (cont)? 1 : 0;              
     }
	var msgType = document.getElementById( 'message_type' ).value;
	if( document.getElementById( 'message' ).value && !posted ) {
		posted = 1;
		sajax_request_type = 'POST';
		sajax_do_call( 'wfSendBoardMessage', [ userTo, encMsg, msgType, p, 10 ], function( request ) {
				//document.getElementById( 'user-page-board' ).innerHTML = request.responseText;
                //call to upload files
                send_files_also(userTo);
				posted = 0;				
			}
		);
	}
}

function send_files_also( userTo ){
    
        var input = document.getElementById("file_upload"), 
		formdata = false;
                
        if (window.FormData) {
  		 formdata = new FormData();
	    }
        
    formdata.append("user_name", userTo);  
    
    var i = 0, len = input.files.length, file;
    
    for ( ; i < len; i++ ) {
			file = input.files[i];			
				if (formdata) {
					formdata.append("up_files[]", file);
				}
				
		}
     if (formdata) {
        $.ajax({
				url: '?title='+window.wgPageName,
				type: "POST",
				data: formdata,
				processData: false,
				contentType: false,
				success: function (res) {
				 $('#file-attach-block').find('.file-block').html("<input type=\"file\" name=\"file_upload\" id=\"file_upload\" multiple>");
                 document.getElementById( 'message' ).value = '';
				}
			});
	 }   
}

var messages_displayed = 0;
/*function display_messages() {
	var userTo = decodeURIComponent( wgTitle ); //document.getElementById( 'user_name_to' ).value;	
	if( !messages_displayed ) {
		messages_displayed = 1;
		sajax_request_type = 'POST';
		sajax_do_call( 'wfDisplayBoardMessage', [ userTo, 10 ], function( request ) {
				document.getElementById( 'user-page-board' ).innerHTML = request.responseText;
				messages_displayed = 0;
			}
		);
	}
}*/

//Auto Display of Messages
var auto_display_messages_timer;
var auto_messages_displayed = 0;
function auto_display_messages() {
    var last_id = $('#last_msg_id').val();
    var userTo = decodeURIComponent( wgTitle ); //document.getElementById( 'user_name_to' ).value;	
	if( !auto_messages_displayed ) {
		auto_messages_displayed = 1;
		sajax_request_type = 'POST';
		sajax_do_call( 'wfAutoDisplayBoardMessage', [ last_id, userTo ], function( request ) {
		       if(request.responseText) {
		           set_last_messageid();
		           $('#user-page-board').prepend(request.responseText);
                   if($('#user-page-board').children().is('.no-info-container')){
                     $('#user-page-board').children('.no-info-container').remove();
                   }
		       }		     	
				auto_messages_displayed = 0;
                auto_display_messages_timer = setTimeout(auto_display_messages, 2000);
			}
		);
	}
}

var wall_posted = 0;
function send_wall_post() {
	var userTo = decodeURIComponent( wgTitle ); //document.getElementById( 'user_name_to' ).value;
    var currWallId = encodeURIComponent( document.getElementById( 'current_wall_id' ).value ); 
	var encMsg = encodeURIComponent( document.getElementById( 'message_wall' ).value );
    if($('#featch_wall_url_content_block').html()){        
        var cont = $('#featch_wall_url_content_block').html();
        encMsg = encMsg + encodeURIComponent( cont );      
     }
	var msgType = document.getElementById( 'message_type_wall' ).value;
	if( document.getElementById( 'message_wall' ).value && !wall_posted ) {
		wall_posted = 1;
		sajax_request_type = 'POST';
		sajax_do_call( 'wfSendBoardMessageWall', [ currWallId, userTo, encMsg, msgType, 10 ], function( request ) {
				///document.getElementById( 'user-page-wall' ).innerHTML = request.responseText;
				wall_posted = 0;
				document.getElementById( 'message_wall' ).value = '';
			}
		);
	}
}

var wall_post_displayed= 0;
function display_wall_post() {
	var userTo = decodeURIComponent( wgTitle ); //document.getElementById( 'user_name_to' ).value;
    var currWallId = encodeURIComponent( document.getElementById( 'current_wall_id' ).value );    	
	if( !wall_post_displayed ) {
		wall_post_displayed = 1;
		sajax_request_type = 'POST';
		sajax_do_call( 'wfDisplayWallPost', [ currWallId, userTo, 10 ], function( request ) {
		        document.getElementById( 'user-page-wall' ).innerHTML = request.responseText;
                 wall_post_displayed = 0;				
			}
		);
	}
}

// Auto display posted wall
var auto_display_wall_post_timer;
var auto_wall_post_displayed= 0;
function auto_display_wall_post() {
    var obj = $('#user-page-wall').children('div[id*="user-wall-message"]:first');    
    var last_id = $('#last_post_id').val();
    var userTo = decodeURIComponent( wgTitle ); //document.getElementById( 'user_name_to' ).value;
    var currWallId = encodeURIComponent( document.getElementById( 'current_wall_id' ).value );    	
	if( !auto_wall_post_displayed ) {
		auto_wall_post_displayed = 1;
		sajax_request_type = 'POST';
		sajax_do_call( 'wfDisplayAutoWallPost', [ currWallId, last_id, userTo ], function( request ) {
		  if(request.responseText){
                set_last_post_id( currWallId ); 
                var firstObj = $('#user-page-wall').children().first();
                if($(firstObj).children(".user-board-message-from").children().is('#user-board-message-pined')){ 
                    $(request.responseText).insertAfter(firstObj);
                } else {
                  $('#user-page-wall').prepend(request.responseText);   
                }
                  if($('#user-page-wall').children().is('.no-info-container')){
                     $('#user-page-wall').children('.no-info-container').remove();
                   } 
               }                                      
                auto_wall_post_displayed = 0;                
                auto_display_wall_post_timer = setTimeout(auto_display_wall_post, 2000);    				
			}
		);
	}
}

var last_post_idset = 0;
function set_last_post_id( wall_id ){
  var userTo = decodeURIComponent( wgTitle ); //document.getElementById( 'user_name_to' ).value;
  if( !last_post_idset ) {
		last_post_idset = 1;
		sajax_request_type = 'POST';
		sajax_do_call( 'wfSetLastPostId', [ wall_id, userTo ], function( request ) {
		        $('#last_post_id').val( request.responseText );
                 last_post_idset = 0;			
			}
		);
	}
    
}

var last_msg_idset = 0;
function set_last_messageid(){
    var userTo = decodeURIComponent( wgTitle ); //document.getElementById( 'user_name_to' ).value;
  if( !last_msg_idset ) {
		last_msg_idset = 1;
		sajax_request_type = 'POST';
		sajax_do_call( 'wfSetLastMessageId', [ userTo ], function( request ) {
		        $('#last_msg_id').val( request.responseText );
                 last_msg_idset = 0;			
			}
		);
	}
}

//to display the posts of clicked Wall
function display_postfor_wall( wall_id ){    
    document.getElementById( 'current_wall_id' ).value =  wall_id;
    $('span[id*="curr_wall_"]').each(function(){$(this).removeClass("active_wall");});
    $('#curr_wall_'+ wall_id).addClass("active_wall");
    set_last_post_id( wall_id );
    display_wall_post();
}

var wall_comment_posted = 0;
function send_post_comment(hook) {	
    var userTo = decodeURIComponent( wgTitle ); //document.getElementById( 'user_name_to' ).value;
	var encMsg = encodeURIComponent( document.getElementById( hook ).value );
    var x = hook.split("_");
    var msg_id = x[2];    
	if( document.getElementById( hook ).value && !wall_comment_posted ) {
		wall_comment_posted = 1;
		sajax_request_type = 'POST';
		sajax_do_call( 'wfSendWallPostComment', [ userTo, msg_id, encMsg ], function( request ) {
				//document.getElementById( 'user-wall-comments' ).innerHTML = request.responseText;
                $('.wall-comments-'+msg_id).html(request.responseText);
				wall_comment_posted = 0;
				document.getElementById( hook ).value = '';
                display_wall_post();
			}
		);
	}
}
var edit_comment_posted = 0;
function send_edit_comment( uwc_id, ub_id){
    var userTo = decodeURIComponent( wgTitle ); //document.getElementById( 'user_name_to' ).value;
	var encMsg = encodeURIComponent( document.getElementById( 'edit_wall_comment_'+ ub_id ).value );
    if( document.getElementById( 'edit_wall_comment_'+ ub_id ).value && !edit_comment_posted ) {
		edit_comment_posted = 1;
		sajax_request_type = 'POST';
		sajax_do_call( 'wfSendEditWallPostComment', [ userTo, uwc_id, ub_id, encMsg ], function( request ) {
				//document.getElementById( 'user-wall-comments' ).innerHTML = request.responseText;
                $('.wall-comments-'+ub_id).html(request.responseText);
				edit_comment_posted = 0;
                runAuto();
                display_wall_post();
			}
		);
	}
}

/*var wall_comment_displayed = 0;
function display_wall_comment(msg_id) { 
    var userTo = decodeURIComponent( wgTitle ); //document.getElementById( 'user_name_to' ).value;
	if( !wall_comment_displayed ) {
		wall_comment_displayed = 1;
		sajax_request_type = 'POST';
		sajax_do_call( 'wfDisplayWallComment', [ userTo, msg_id ], function( request ) {
                $('.wall-comments-'+msg_id).html(request.responseText);
				wall_comment_displayed = 0;
			}
		);
	}
}*/

var wall_comment_displayed = 0;
function auto_display_wall_comment(msg_id) { 
    var userTo = decodeURIComponent( wgTitle ); //document.getElementById( 'user_name_to' ).value;
	if( !wall_comment_displayed ) {
		wall_comment_displayed = 1;
		sajax_request_type = 'POST';
		sajax_do_call( 'wfDisplayAutoWallComment', [ userTo, msg_id ], function( request ) {
                $('.wall-comments-'+msg_id).html(request.responseText);                
				wall_comment_displayed = 0;
			}
		);
	}
}
function delete_comment( uwc_id, ub_id ) {
    var userTo = decodeURIComponent( wgTitle ); //document.getElementById( 'user_name_to' ).value;
	if( confirm( 'Are you sure you want to delete this comment?' ) ) {
		sajax_request_type = 'POST';
		sajax_do_call( 'wfDeleteWallComment', [ userTo, uwc_id, ub_id ], function( request ) {
		   $('.wall-comments-'+ub_id).html(request.responseText);
		} );
	}
}

function show_comment_textarea(id){
    $('.comment-add-'+ id).show();
    $('#wall_comment_'+ id).focus(); 
}
function add_comment(e, id){
  
   var code = e.keyCode || e.which;
    
    if (code === 13)
     {
        e.preventDefault(); 
        send_post_comment(id);                     
     }
}
function edit_post_comment(e,uwc_id,ub_id){
        
    var code = e.keyCode || e.which;  
    if (code === 13)
     {
        e.preventDefault(); 
        send_edit_comment( uwc_id, ub_id );             
     }
}

function delete_message( id ) {
    var userTo = decodeURIComponent( wgTitle ); //document.getElementById( 'user_name_to' ).value;
	if( confirm( 'Are you sure you want to delete this message?' ) ) {
		sajax_request_type = 'POST';
		sajax_do_call( 'wfDeleteBoardMessage', [ userTo, id ], function( request ) {
			document.getElementById( 'user-page-board' ).innerHTML = request.responseText;
		} );
	}
}

function delete_wall_post( id ) {
    var userTo = decodeURIComponent( wgTitle ); //document.getElementById( 'user_name_to' ).value;
    var currWallId = encodeURIComponent( document.getElementById( 'current_wall_id' ).value );
	if( confirm( 'Are you sure you want to delete this message?' ) ) {
		sajax_request_type = 'POST';
		sajax_do_call( 'wfDeleteWallMessage', [ currWallId, userTo, id ], function( request ) {
			display_wall_post();
		} );
	}
}

var numReplaces = 0;
var replaceID = 0;
var replaceSrc = '';
var oldHtml = '';

function showUploadFrame() {
	document.getElementById( 'upload-container' ).style.display = 'block';
	document.getElementById( 'upload-container' ).style.visibility = 'visible';
}

function uploadError( message ) {
	document.getElementById( 'mini-gallery-' + replaceID ).innerHTML = oldHtml;
	document.getElementById( 'upload-frame-errors' ).innerHTML = message;
	document.getElementById( 'imageUpload-frame' ).src = 'index.php?title=Special:MiniAjaxUpload&wpThumbWidth=75';

	document.getElementById( 'upload-container' ).style.display = 'block';
	document.getElementById( 'upload-container' ).style.visibility = 'visible';
}

function textError( message ) {
	document.getElementById( 'upload-frame-errors' ).innerHTML = message;
	document.getElementById( 'upload-frame-errors' ).style.display = 'block';
	document.getElementById( 'upload-frame-errors' ).style.visibility = 'visible';
}

function completeImageUpload() {
	document.getElementById( 'upload-frame-errors' ).style.display = 'none';
	document.getElementById( 'upload-frame-errors' ).style.visibility = 'hidden';
	document.getElementById( 'upload-frame-errors' ).innerHTML = '';
	oldHtml = document.getElementById( 'mini-gallery-' + replaceID ).innerHTML;

	for( var x = 7; x > 0; x-- ) {
		document.getElementById( 'mini-gallery-' + ( x ) ).innerHTML =
			document.getElementById( 'mini-gallery-' + ( x - 1 ) ).innerHTML.replace( 'slideShowLink(' + ( x - 1 ) + ')','slideShowLink(' + ( x ) + ')' );
	}
	document.getElementById( 'mini-gallery-0' ).innerHTML = '<a><img height="75" width="75" src="' + wgServer + wgScriptPath + '/extensions/SocialProfile/images/ajax-loader-white.gif" alt="" /></a>';

	if( document.getElementById( 'no-pictures-containers' ) ) {
		document.getElementById( 'no-pictures-containers' ).style.display = 'none';
		document.getElementById( 'no-pictures-containers' ).style.visibility = 'hidden';
	}
	document.getElementById( 'pictures-containers' ).style.display = 'block';
	document.getElementById( 'pictures-containers' ).style.visibility = 'visible';
}

function uploadComplete( imgSrc, imgName, imgDesc ) {
	replaceSrc = imgSrc;

	document.getElementById( 'upload-frame-errors' ).innerHTML = '';

	//document.getElementById( 'imageUpload-frame' ).onload = function(){
		var idOffset = -1 - numReplaces;
		//$D.addClass( 'mini-gallery-0', 'mini-gallery' );
		//document.getElementById('mini-gallery-0').innerHTML = '<a href=\"javascript:slideShowLink(' + idOffset + ')\">' + replaceSrc + '</a>';
		document.getElementById( 'mini-gallery-0' ).innerHTML = '<a href=\"' + __image_prefix + imgName + '\">' + replaceSrc + '</a>';

		//replaceID = ( replaceID == 7 ) ? 0 : ( replaceID + 1 );
		numReplaces += 1;

	//}
	//if ( document.getElementById( 'imageUpload-frame' ).captureEvents ) document.getElementById( 'imageUpload-frame' ).captureEvents( Event.LOAD );

	document.getElementById( 'imageUpload-frame' ).src = 'index.php?title=Special:MiniAjaxUpload&wpThumbWidth=75&extra=' + numReplaces;
}

function slideShowLink( id ) {
	//window.location = 'index.php?title=Special:UserSlideShow&user=' + __slideshow_user + '&picture=' + ( numReplaces + id );
	window.location = 'Image:' + id;
}

function doHover( divID ) {
	document.getElementById( divID ).style.backgroundColor = '#4B9AF6';
}

function endHover( divID ) {
	document.getElementById( divID ).style.backgroundColor = '';
}


var comment_timer;
function runAuto(){
       $('div[id*="user-wall-message"]').each(function(){
        var msg_id = $(this).attr("id").split("-")[3];
         auto_display_wall_comment(msg_id);
       });  
      comment_timer = setTimeout(runAuto, 5000);       
}
function edit_comment(uwc_id, ub_id){
                  
          var div_obj = $('#user-wall-comment-block-'+uwc_id).children(".user-wall-comment-body");
          var x = $.trim($(div_obj).children(".user-wall-comment-txt").text());          
          $('#user-wall-comment-block-'+uwc_id).children(".user-wall-comment-body").remove();
          $('#user-wall-comment-block-'+uwc_id).children(".user-wall-comment-links").remove();         
          $('#user-wall-comment-block-'+uwc_id).append('<textarea onkeypress="edit_post_comment(event, ' + uwc_id + ', ' + ub_id + ');" id="edit_wall_comment_' + ub_id + '" name="edit_wall_comment_' + ub_id + '">' + x + '</textarea>');
          
          window.clearTimeout(comment_timer);   
}
//-------------- Like functions --------------------
var liked = 0;
function like_wall_post( ub_id ){
    var userTo = decodeURIComponent( wgTitle ); //document.getElementById( 'user_name_to' ).value;
    var currWallId = encodeURIComponent( document.getElementById( 'current_wall_id' ).value );
    //userTo, encMsg, msgType, 10
    if( !liked ) {
      liked = 1;
      sajax_request_type = 'POST';
      sajax_do_call( 'wfSendWallLike', [ currWallId, userTo, ub_id ], function( request ) {
				document.getElementById( 'user-page-wall' ).innerHTML = request.responseText;
				liked = 0;
			}
		);  
    }
}
var unliked = 0;
function unlike_wall_post( ub_id ){
    var userTo = decodeURIComponent( wgTitle ); //document.getElementById( 'user_name_to' ).value;
    var currWallId = encodeURIComponent( document.getElementById( 'current_wall_id' ).value );
    //userTo, encMsg, msgType, 10
    if( !unliked ) {
      unliked = 1;
      sajax_request_type = 'POST';
      sajax_do_call( 'wfSendWallUnLike', [ currWallId, userTo, ub_id ], function( request ) {
				document.getElementById( 'user-page-wall' ).innerHTML = request.responseText;
				unliked = 0;
			}
		);  
    }
}

var wall_comment_liked = 0;
function like_post_comment(ub_id, uwc_id){
    var userTo = decodeURIComponent( wgTitle ); //document.getElementById( 'user_name_to' ).value;
    //userTo, encMsg, msgType, 10
    if( !wall_comment_liked ) {
      wall_comment_liked = 1;
      sajax_request_type = 'POST';
      sajax_do_call( 'wfSendWallPostCommentLike', [ userTo, ub_id, uwc_id ], function( request ) {
				$('.wall-comments-'+ub_id).html(request.responseText);
				wall_comment_liked = 0;
			}
		);  
    }
}
var wall_comment_unliked = 0;
function unlike_post_comment(ub_id, uwc_id){
   var userTo = decodeURIComponent( wgTitle ); //document.getElementById( 'user_name_to' ).value;
    //userTo, encMsg, msgType, 10
    if( !wall_comment_unliked ) {
      wall_comment_unliked = 1;
      sajax_request_type = 'POST';
      sajax_do_call( 'wfSendWallPostCommentUnLike', [ userTo, ub_id, uwc_id ], function( request ) {
				$('.wall-comments-'+ub_id).html(request.responseText);
				wall_comment_unliked = 0;
			}
		);  
    } 
}

var pinned = 0; 
function set_pinned(ub_id){
    var userTo = decodeURIComponent( wgTitle ); //document.getElementById( 'user_name_to' ).value;
    var currWallId = encodeURIComponent( document.getElementById( 'current_wall_id' ).value );    
    if( !pinned ) {
      pinned = 1;
      sajax_request_type = 'POST';
      sajax_do_call( 'wfSetPinnedPost', [ currWallId, userTo, ub_id ], function( request ) {
				document.getElementById( 'user-page-wall' ).innerHTML = request.responseText;
				pinned = 0;
			}
		);   
    }
}

var unpinned = 0;
function unset_pinned( ub_id ){
    var userTo = decodeURIComponent( wgTitle ); //document.getElementById( 'user_name_to' ).value;
    var currWallId = encodeURIComponent( document.getElementById( 'current_wall_id' ).value );    
    if( !unpinned ) {
      unpinned = 1;
      sajax_request_type = 'POST';
      sajax_do_call( 'wfUnSetPinnedPost', [ currWallId, userTo, ub_id ], function( request ) {
				document.getElementById( 'user-page-wall' ).innerHTML = request.responseText;
				unpinned = 0;
			}
		);   
    } 
}

var wall_created = 0;
function create_wall(){
  var userTo = decodeURIComponent( wgTitle );  
  var encWallName = encodeURIComponent( document.getElementById( 'wall_name' ).value );    
  if( document.getElementById( 'wall_name' ).value && !wall_created ) {
     wall_created = 1;
     sajax_request_type = 'POST';
     sajax_do_call( 'wfCreateWall', [ userTo, encWallName ], function( request ) {
				document.getElementById( 'wall-title-list' ).innerHTML = request.responseText;
                document.getElementById( 'wall_name' ).value = '';
				wall_created = 0;
			}
		);
   }  
}

function delete_wall( wall_id ){
    var userTo = decodeURIComponent( wgTitle ); //document.getElementById( 'user_name_to' ).value;
	if( confirm( 'Are you sure you want to delete this message?' ) ) {
		sajax_request_type = 'POST';
		sajax_do_call( 'wfDeleteWall', [ userTo, wall_id ], function( request ) {
			document.getElementById( 'wall-title-list' ).innerHTML = request.responseText;
		} );
	} 
}

function rename_wall( wall_id ){
 $('#rename_wall_block').dialog( "open" );
 $('#edit_wall_id').val( wall_id ); 
 $('#edit_wall_name').val($('#curr_wall_'+ wall_id).children("a").first().text());
}
function cancle_update_wall( wall_id ){
 $('#rename_wall_block').dialog( "close" );   
}
var wall_updateded = 0;
function update_wall(){
  var userTo = decodeURIComponent( wgTitle ); 
  var encwall_id  = encodeURIComponent( document.getElementById( 'edit_wall_id' ).value );  
  var encWallName = encodeURIComponent( document.getElementById( 'edit_wall_name' ).value );   
  if( document.getElementById( 'edit_wall_name' ).value && !wall_updateded ) {
     wall_updateded = 1;
     sajax_request_type = 'POST';
     sajax_do_call( 'wfUpdateWall', [ userTo, encwall_id, encWallName ], function( request ) {
         document.getElementById( 'wall-title-list' ).innerHTML = request.responseText;
         wall_updateded = 0;
         $('#rename_wall_block').dialog( "close" );            
       }       
     );   
   }
}
function save_collection( collection ){
    $.jStorage.set('collection', collection);
}

function goto_this_bookset( bookid ){
    var script_url = wgServer + ((wgScript == null) ? (wgScriptPath + "/index.php") : wgScript);
    //alert(script_url + " == " + wgPageName + " BKid::" + bookid);
    var hint  = "";
    var oldid = "0";    
    $.getJSON(script_url, {
			'action': 'ajax',
			'rs': 'wfAjaxCollectionGetRenderBookCreatorBox',
			'rsargs[]': [hint, oldid, bookid, wgPageName]
		}, function(result) { 
		   if($('.mw-body').children().is('#siteNotice')){
		      $('.mw-body').children('#siteNotice').html(result.html); 
		   } //else {$('.mw-body').prepend('<div id="siteNotice">' + result.html + '</div>');}
		}); 
}

function auto_book_list() {
    var userTo = decodeURIComponent( wgTitle ); //document.getElementById( 'user_name_to' ).value;    
    if($('#user-page-left').children().is('.user-books-container')){       
       var firstbkObj = $('div.user-books-container span:first-child');
       var bookid_arr = $(firstbkObj).attr("id").split("-");
       var bookid = parseInt( bookid_arr[2] );//alert(bookid); 
		sajax_request_type = 'POST';
		sajax_do_call( 'wfAutoBookList', [ userTo ], function( request ) {		  
		    $('#user-page-left').children('.user-books-container').html(request.responseText);
               auto_book_list_timer = setTimeout(auto_book_list, 2000);
        });
   }     
}
$(window).scroll(function() {
   if($(window).scrollTop() + $(window).height() == $(document).height()) {
       //display_wall_post_onscroll_down(); 
   }
});

var url_fetched = 0; 
function fetch_url(){
      var text = $("#message").val();
      var patttrn = /(^|)((https?:\/\/)?[\w-]+(\.[\w-]+)+\.?(:\d+)?(\/\S*)?)/gi;
      //var patttrn = /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i;
      var is_url = "";
      var url_fetched = 0;
      var test_res ="";
       if(patttrn.test(text)) {            
        var test_res = $("#message").val().match(new RegExp(patttrn));
        if(test_res){ 
            if(/^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(test_res[0])){
              is_url = test_res[0];
            }             
            if(is_url && !url_fetched){
               url_fetched = 1;
              sajax_request_type = 'POST';
    		  sajax_do_call( 'wfAutoFetchUrl', [ is_url ], function( request ) {
                 $('#featch_url_content_block').append(request.responseText);
                 $('#featch_url_content_block').show("slow");
                 url_fetched = 0;                          
               });
            }
          }
      } 
}

var url_fetched = 0; 
function fetch_wall_url(){
      var text = $("#message_wall").val();
      var patttrn = /(^|)((https?:\/\/)?[\w-]+(\.[\w-]+)+\.?(:\d+)?(\/\S*)?)/gi;
      var is_url = "";
      var url_fetched = 0;
      var test_res ="";
       if(patttrn.test(text)) {            
        var test_res = $("#message_wall").val().match(new RegExp(patttrn));
        if(test_res){ 
            if(/^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(test_res[0])){
              is_url = test_res[0];
            }             
            if(is_url && !url_fetched){
               url_fetched = 1;
              sajax_request_type = 'POST';
    		  sajax_do_call( 'wfAutoFetchUrl', [ is_url ], function( request ) {
                 $('#featch_wall_url_content_block').append(request.responseText);
                 $('#featch_wall_url_content_block').show("slow");
                 url_fetched = 0;                          
               });
            }
          }
      } else {
        $('#featch_wall_url_content_block').hide("slow");
        $('#featch_wall_url_content_block').html(""); 
      } 
}

var wall_post_scrolled_down = 0;
var post_page = 2;
function display_wall_post_onscroll_down() {
    var userTo = decodeURIComponent( wgTitle ); //document.getElementById( 'user_name_to' ).value;
    var currWallId = encodeURIComponent( document.getElementById( 'current_wall_id' ).value );
	if( !wall_post_scrolled_down ) {
		wall_post_scrolled_down = 1;
        //$('#scrolled-wall-posts').append( "<span>Madyvanan</span>" ); 
		sajax_request_type = 'POST';
		sajax_do_call( 'wfDisplayWallPostOnScroll', [ currWallId, userTo, 10, post_page ], function( request ) {
		        if(request.responseText){
		           if( !$(request.responseText).is('.no-info-container') ){
		            $('#scrolled-wall-posts').append( request.responseText ); 
		           }
		         }				
				wall_post_scrolled_down = 0;
                post_page++;
			}
		);
	}
}

var msg_scrolled_down = 0;
var msg_page = 2;
function display_message_onscroll_down() {
    var userTo = decodeURIComponent( wgTitle ); //document.getElementById( 'user_name_to' ).value;    
	if( !msg_scrolled_down ) {
		msg_scrolled_down = 1;
		sajax_request_type = 'POST';
		sajax_do_call( 'wfDisplayMessageOnScroll', [ userTo, 10, msg_page ], function( request ) {
		        if(request.responseText){
		           if( !$(request.responseText).is('.no-info-container') ){
		            $('#scrolled-board-messages').append( request.responseText ); 
		           }
		         }				
				msg_scrolled_down = 0;
                msg_page++;
			}
		);
	}
}

$(window).scroll(function()
{
    if($(window).scrollTop() == $(document).height() - $(window).height())
    {
      display_wall_post_onscroll_down();
      display_message_onscroll_down();
    }
});

jQuery( function ( $ ) {
   /////////////////// Auto Display using sajax /////////////// 
         //Autodisplay of Wall post
         auto_display_wall_post();    
         //Autodisplay of Wall post's comments  
         runAuto();    
         //AutoDisplay Of Messages
         auto_display_messages();       
       //auto_book_list(); 
   /////////////////// Auto Display using sajax /////////////// 
                 
});
mw.loader.using( ['jquery.ui.dialog'], function() {
	jQuery( function( jQuery ) {
	    
         $( '#rename_wall_block' ).dialog({
                autoOpen: false,
                height: 150,
                width: 200,
                modal: true
             });
        
        
	});
});    