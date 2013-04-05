var UserBoard = {
	posted: 0,

	sendMessage: function( perPage ) {
		if( !perPage ) {
			perPage = 25;
		}
		var message = document.getElementById( 'message' ).value;
		var recipient = document.getElementById( 'user_name_to' ).value;
		var sender = document.getElementById( 'user_name_from' ).value;
		if( message && !UserBoard.posted ) {
			UserBoard.posted = 1;
			var encodedName = encodeURIComponent( recipient );
			var encodedMsg = encodeURIComponent( message );
			var messageType = document.getElementById( 'message_type' ).value;
			sajax_request_type = 'POST';
			sajax_do_call(
				'wfSendBoardMessage',
				[ encodedName, encodedMsg, messageType, perPage ],
				function( request ) {
					UserBoard.posted = 0;
					var user_1, user_2;
					if( sender ) { // it's a board to board
						user_1 = sender;
						user_2 = recipient;
					} else {
						user_1 = recipient;
						user_2 = '';
					}
				    UserBoard.sendMessageFilesAlso(encodedName, user_1, user_2, request);	
				}
			);
		}
	},
    sendMessageFilesAlso: function( userTo, user_1, user_2, request ){
       
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
                      
                      var params = ( user_2 ) ? '&conv=' + user_2 : '';
                      var url = wgScriptPath + '/index.php?title=Special:UserBoard&user=' + user_1 + params;
                      window.location = url;
                      	
    				}
    			});
    	 } 
      
        
        
    },
	deleteMessage: function( id ) {
		if( confirm( _DELETE_CONFIRM ) ) {
			sajax_request_type = 'POST';
			sajax_do_call( 'wfDeleteBoardMessage', [ id ], function( request ) {
				window.location.reload();
			});
		}
	}
};
