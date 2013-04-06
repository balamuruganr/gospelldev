/**
 * JavaScript functions used by UserProfile
 */
var posted = 0;
function send_message() {
	var userTo = decodeURIComponent( wgTitle ); //document.getElementById( 'user_name_to' ).value;
	var encMsg = encodeURIComponent( document.getElementById( 'message' ).value );
	var msgType = document.getElementById( 'message_type' ).value;
	if( document.getElementById( 'message' ).value && !posted ) {
		posted = 1;
		sajax_request_type = 'POST';
		sajax_do_call( 'wfSendBoardMessage', [ userTo, encMsg, msgType, 10 ], function( request ) {
				//document.getElementById( 'user-page-board' ).innerHTML = request.responseText;
                //call to upload files
                send_files_also(userTo);
				posted = 0;				
			}
		);
	}
}
//sendMessageFiles
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
                 display_messages();	
				}
			});
	 }   
}
//Auto Display of Messages
var messages_displayed = 0;
function display_messages() {
	var userTo = decodeURIComponent( wgTitle ); //document.getElementById( 'user_name_to' ).value;	
	if( !messages_displayed ) {
		messages_displayed = 1;
		sajax_request_type = 'POST';
		sajax_do_call( 'wfDisplayAutoBoardMessage', [ userTo, 10 ], function( request ) {
				document.getElementById( 'user-page-board' ).innerHTML = request.responseText;
				messages_displayed = 0;
			}
		);
	}
 setTimeout(display_messages, 5000);   
}

var wall_posted = 0;
function send_wall_post() {
	var userTo = decodeURIComponent( wgTitle ); //document.getElementById( 'user_name_to' ).value;
    var currWallId = encodeURIComponent( document.getElementById( 'current_wall_id' ).value ); 
	var encMsg = encodeURIComponent( document.getElementById( 'message_wall' ).value );
	var msgType = document.getElementById( 'message_type_wall' ).value;
	if( document.getElementById( 'message_wall' ).value && !wall_posted ) {
		wall_posted = 1;
		sajax_request_type = 'POST';
		sajax_do_call( 'wfSendBoardMessageWall', [ currWallId, userTo, encMsg, msgType, 10 ], function( request ) {
				document.getElementById( 'user-page-wall' ).innerHTML = request.responseText;
				wall_posted = 0;
				document.getElementById( 'message_wall' ).value = '';
			}
		);
	}
}
// Auto display posted wall
var display_wall_post_timer;
var wall_post_displayed= 0;
function display_wall_post() {
	var userTo = decodeURIComponent( wgTitle ); //document.getElementById( 'user_name_to' ).value;
    var currWallId = encodeURIComponent( document.getElementById( 'current_wall_id' ).value );    	
	if( !wall_post_displayed ) {
		wall_post_displayed = 1;
		sajax_request_type = 'POST';
		sajax_do_call( 'wfDisplayAutoWallPost', [ currWallId, userTo, 10 ], function( request ) {
				document.getElementById( 'user-page-wall' ).innerHTML = request.responseText;
				wall_post_displayed = 0;
			}
		);
	}
 display_wall_post_timer = setTimeout(display_wall_post, 8000);        
}

//to display the posts of clicked Wall
function display_postfor_wall( wall_id ){    
    document.getElementById( 'current_wall_id' ).value =  wall_id;
    $('span[id*="curr_wall_"]').each(function(){$(this).removeClass("active_wall");});
    $('#curr_wall_'+ wall_id).addClass("active_wall");
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

var wall_comment_displayed = 0;
function display_wall_comment(msg_id) { 
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
    window.clearTimeout(display_wall_post_timer);   
}
function add_comment(e, id){
   window.clearTimeout(display_wall_post_timer); 
   var code = e.keyCode || e.which;
    
    if (code === 13)
     {
        e.preventDefault(); 
        send_post_comment(id);                     
     }
}
function edit_post_comment(e,uwc_id,ub_id){
    window.clearTimeout(display_wall_post_timer);
    
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
         display_wall_comment(msg_id);
       });  
      comment_timer = setTimeout(runAuto, 5000);       
}
function edit_comment(uwc_id, ub_id){
                  
          var div_obj = $('#user-wall-comment-block-'+uwc_id).children(".user-wall-comment-body");
          var x = $.trim($(div_obj).children(".user-wall-comment-txt").text());          
          $('#user-wall-comment-block-'+uwc_id).children(".user-wall-comment-body").remove();
          $('#user-wall-comment-block-'+uwc_id).children(".user-wall-comment-links").remove();         
          $('#user-wall-comment-block-'+uwc_id).append('<textarea onkeypress="edit_post_comment(event, ' + uwc_id + ', ' + ub_id + ');" id="edit_wall_comment_' + ub_id + '" name="edit_wall_comment_' + ub_id + '" onfocus="javascript:stop_auto_load();">' + x + '</textarea>');
          
          window.clearTimeout(comment_timer); 
          window.clearTimeout(display_wall_post_timer);        
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

function stop_auto_load(){
  window.clearTimeout(display_wall_post_timer);  
}

(function() { 
   /////////////////// Auto Display using sajax /////////////// 
        //Autodisplay of Wall post
        display_wall_post();      
        //Autodisplay of Wall post's comments  
        runAuto();    
        //AutoDisplay Of Messages
       display_messages(); 
   /////////////////// Auto Display using sajax /////////////// 
   
    /*$('.user-board-message-from').each(function(){
        $(this).hover(
          function(){ $(this).children("#user-board-message-pin").show(); },
          function(){ $(this).children("#user-board-message-pin").hide(); }
          );
    });*/    
              
})();    