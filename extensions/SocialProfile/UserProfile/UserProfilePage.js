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
				document.getElementById( 'user-page-board' ).innerHTML = request.responseText;
				posted = 0;
				document.getElementById( 'message' ).value = '';
			}
		);
	}
}
var wall_posted = 0;
function send_wall() {
	var userTo = decodeURIComponent( wgTitle ); //document.getElementById( 'user_name_to' ).value;
	var encMsg = encodeURIComponent( document.getElementById( 'message_wall' ).value );
	var msgType = document.getElementById( 'message_type_wall' ).value;
	if( document.getElementById( 'message_wall' ).value && !wall_posted ) {
		wall_posted = 1;
		sajax_request_type = 'POST';
		sajax_do_call( 'wfSendBoardMessageWall', [ userTo, encMsg, msgType, 10 ], function( request ) {
				document.getElementById( 'user-page-wall' ).innerHTML = request.responseText;
				wall_posted = 0;
				document.getElementById( 'message_wall' ).value = '';
			}
		);
	}
}

var wall_comment_posted = 0;
function send_wall_comment(hook) {	
	var encMsg = encodeURIComponent( document.getElementById( hook ).value );
    var x = hook.split("_");
    var msg_id = x[2];    
	if( document.getElementById( hook ).value && !wall_comment_posted ) {
		wall_comment_posted = 1;
		sajax_request_type = 'POST';
		sajax_do_call( 'wfSendWallComment', [ msg_id, encMsg ], function( request ) {
				//document.getElementById( 'user-wall-comments' ).innerHTML = request.responseText;
                $('.wall-comments-'+msg_id).html(request.responseText);
				wall_comment_posted = 0;
				document.getElementById( hook ).value = '';
			}
		);
	}
}

var wall_comment_displayed = 0;
function display_wall_comment(msg_id) {    
	if( !wall_comment_displayed ) {
		wall_comment_displayed = 1;
		sajax_request_type = 'POST';
		sajax_do_call( 'wfDisplayAutoWallComment', [ msg_id ], function( request ) {
                $('.wall-comments-'+msg_id).html(request.responseText);
				wall_comment_displayed = 0;
			}
		);
	}
}

function show_comment_textarea(id){
    $('.comment-add-'+ id).show();
}
function add_comment(e, id){
   var code = e.keyCode || e.which;
  
    if (code === 13)
     {
        e.preventDefault(); 
        send_wall_comment(id);             
     }
}
function delete_message( id ) {
	if( confirm( 'Are you sure you want to delete this message?' ) ) {
		sajax_request_type = 'POST';
		sajax_do_call( 'wfDeleteBoardMessage', [ id ], function( request ) {
			window.location.reload();
		} );
	}
}
function like_wall( msg_id, user_id, like_state ) {
	if( confirm( 'Are you sure you want to delete this message?' ) ) {
		sajax_request_type = 'POST';
		sajax_do_call( 'wfDeleteBoardMessage', [ id ], function( request ) {
			window.location.reload();
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

(function() {
    function runAuto(){
       $('div[id*="user-wall-message"]').each(function(){
        var msg_id = $(this).attr("id").split("-")[3];
         display_wall_comment(msg_id);
       });  
     setTimeout(runAuto, 5000);     
    }
   runAuto();    
})();
    