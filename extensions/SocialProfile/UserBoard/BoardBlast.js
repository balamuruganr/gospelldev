var BoardBlast = {
	submitted: 0,

	toggleUser: function( user_id ) {
		var elem = jQuery( '#user-' + user_id );
		if( elem.hasClass( 'blast-friend-selected' ) ) {
			elem.removeClass( 'blast-friend-selected' )
				.addClass( 'blast-friend-unselected' );
		} else if( elem.hasClass( 'blast-friend-unselected' ) ) {
			elem.removeClass( 'blast-friend-unselected' )
				.addClass( 'blast-friend-selected' );
		}

		if( elem.hasClass( 'blast-foe-selected' ) ) {
			elem.removeClass( 'blast-foe-selected' )
				.addClass( 'blast-foe-unselected' );
		} else if( elem.hasClass( 'blast-foe-unselected' ) ) {
			elem.removeClass( 'blast-foe-unselected' )
				.addClass( 'blast-foe-selected' );
		}
	},

	toggleType: function( method, on, off ) {
		var list = jQuery( '#blast-friends-list div.' + ( ( method == 1 ) ? off : on ) );

		for( var x = 0; x <= list.length - 1; x++ ) {
			var el = list[x];
			if( jQuery( el ).hasClass( on ) || jQuery( el ).hasClass( off ) ) {
				if( method == 1 ) {
					jQuery( el ).removeClass( off ).addClass( on );
				} else {
					jQuery( el ).removeClass( on ).addClass( off );
				}
			}
		}
	},

	toggleFriends: function( method ) {
		BoardBlast.toggleType(
			method,
			'blast-friend-selected',
			'blast-friend-unselected'
		);
	},

	toggleFoes: function( method ) {
		BoardBlast.toggleType(
			method,
			'blast-foe-selected',
			'blast-foe-unselected'
		);
	},

	selectAll: function() {
		BoardBlast.toggleFriends( 1 );
		BoardBlast.toggleFoes( 1 );
	},

	unselectAll: function() {
		BoardBlast.toggleFriends( 0 );
		BoardBlast.toggleFoes( 0 );
	},

	sendMessages: function() {
		if( BoardBlast.submitted == 1 ) {
			return 0;
		}

		BoardBlast.submitted = 1;
		var selected = 0;
		var user_ids_to = '';

		/*var list = jQuery( '#blast-friends-list div.blast-friend-selected' );
		var el, user_id;
		for( var x = 0; x <= list.length - 1; x++ ) {
			el = list[x];
			selected++;
			user_id = el.id.replace( 'user-', '' );
			user_ids_to += ( ( user_ids_to ) ? ',' : '' ) + user_id;
		}

		list = jQuery( '#blast-friends-list div.blast-foe-selected' );
		for( x = 0; x <= list.length - 1; x++ ) {
			el = list[x];
			selected++;
			user_id = el.id.replace( 'user-', '' );
			user_ids_to += ( ( user_ids_to ) ? ',' : '' ) + user_id;
		} */
        
        if(document.getElementById( 'friend_name' ).value !== ''){
          selected ++;  
        }
        
		if( selected === 0 ) {
			alert( 'Please enter a friend name' );
			BoardBlast.submitted = 0;
			return 0;
		}

		if( !document.getElementById( 'message' ).value ) {
			alert( 'Please enter a message' );
			BoardBlast.submitted = 0;
			return 0;
		} 
        

		//document.getElementById( 'ids' ).value = user_ids_to;

		document.blast.message.style.color = '#ccc';
		document.blast.message.readOnly = true;
		document.getElementById( 'blast-friends-list' ).innerHTML = 'Sending messages...';
		document.blast.submit();
        
	}
};

function friend_selected( friend_user_id, friend_name ){
  document.getElementById( 'ids' ).value = friend_user_id;
  document.getElementById( 'friend_name' ).value =  friend_name;
  $('#user_names_messages').html("");  
}
function getFriendsNames( user_id ){
        var encFriendName = encodeURIComponent( document.getElementById( 'friend_name' ).value );
        var value_posted = 0;        
        $.ajax({
        url: mw.util.wikiScript( 'index.php?title=Special:SendBoardBlast&msg_from_user_id=' + user_id + '&friend_name=' + encFriendName),//'index.php?title=Special:UserLogin&user_search='+uname
        cache: false
        }).done(function( html ) {              
            var res = '<ul>';
            $.each(html, function(i, field){        
                res = res + '<li><a href="javascript:void(0);" onclick="friend_selected(\'' + field.user_id + '\',\'' + field.user_name + '\');">'+ field.user_name +'</a></li>';     
            });    
            res = res + '</ul>';
            $('#user_names_messages').html(res);      
        });   
}