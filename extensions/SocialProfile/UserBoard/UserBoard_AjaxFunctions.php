<?php
/**
 * AJAX functions used by UserBoard.
 */
$wgAjaxExportList[] = 'wfSendBoardMessage';
function wfSendBoardMessage( $user_name, $message, $message_type, $count ) {
	global $wgUser;
	$user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name );
	$user_id_to = User::idFromName( $user_name );
	$b = new UserBoard();

	$m = $b->sendBoardMessage(
		$wgUser->getID(), $wgUser->getName(), $user_id_to, $user_name,
		urldecode( $message ), $message_type
	);
 
	return $b->displayMessages( $user_id_to, 0, $count );
}

$wgAjaxExportList[] = 'wfDisplayAutoBoardMessage';
function wfDisplayAutoBoardMessage( $user_name, $count ) {
	global $wgUser;
	$user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name );
	$user_id_to = User::idFromName( $user_name );
	$b = new UserBoard();

	return $b->displayMessages( $user_id_to, 0, $count );
}

$wgAjaxExportList[] = 'wfDeleteBoardMessage';
function wfDeleteBoardMessage( $user_name, $ub_id ) {
	global $wgUser;
    
    $user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name );
	$user_id_to = User::idFromName( $user_name );

	$b = new UserBoard();
	if (
		$b->doesUserOwnMessage( $wgUser->getID(), $ub_id ) ||
		$wgUser->isAllowed( 'userboard-delete' )
	) {
		$b->deleteMessage( $ub_id );
	}
    
 return $b->displayMessages( $user_id_to, 0, 10 );
}

$wgAjaxExportList[] = 'wfDeleteWallMessage';
function wfDeleteWallMessage( $user_name, $ub_id ) {
	global $wgUser;
    
    $user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name );
	$user_id_to = User::idFromName( $user_name );

	$b = new UserBoard();
	//if ($b->doesUserOwnMessage( $wgUser->getID(), $ub_id ) || $wgUser->isAllowed( 'userboard-delete' ) ) { }
    $b->deleteMessage( $ub_id );
    $wall_id = $b->getFirstWallID($user_name, $user_id_to);
 return $b->displayWallPosts( $wall_id, $user_name, $user_id_to, 0, 10 );
}


$wgAjaxExportList[] = 'wfSendBoardMessageWall';
function wfSendBoardMessageWall( $user_name, $message, $message_type, $count ) {
	global $wgUser;
	$user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name );
	$user_id_to = User::idFromName( $user_name );
	$b = new UserBoard();

	$m = $b->sendBoardMessage(
		$wgUser->getID(), $wgUser->getName(), $user_id_to, $user_name,
		urldecode( $message ), $message_type
	);
    $wall_id = $b->getFirstWallID($user_name, $user_id_to); 
	return $b->displayWallPosts( $wall_id, $user_name, $user_id_to, 0, $count );
}

$wgAjaxExportList[] = 'wfDisplayAutoWallPost';
function wfDisplayAutoWallPost($user_name, $count) {
	global $wgUser;
	$user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name );
	$user_id_to = User::idFromName( $user_name );
	$b = new UserBoard();
    $wall_id = $b->getFirstWallID($user_name, $user_id_to);    	
	return $b->displayWallPosts( $wall_id, $user_name, $user_id_to, 0, $count );
}

$wgAjaxExportList[] = 'wfSendWallPostComment';
function wfSendWallPostComment( $user_name, $message_id, $comment ) {
	global $wgUser;
    $user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name );
    $message_id = stripslashes( $message_id );
	$b = new UserBoard();
	$m = $b->SendWallPostComment($message_id,
		$wgUser->getID(), $wgUser->getName(),
        urldecode( $comment )
	);

	return $b->displayWallPostComments( $user_name, $message_id );
}
//
$wgAjaxExportList[] = 'wfSendEditWallPostComment';
function wfSendEditWallPostComment( $user_name, $uwc_id, $message_id, $comment ) {
	global $wgUser;
    $user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name );
    $message_id = stripslashes( $message_id );
    $uwc_id = stripslashes( $uwc_id );
    
	$b = new UserBoard();
	$m = $b->SendEditWallPostComment( $uwc_id, $message_id, urldecode( $comment ) );

	return $b->displayWallPostComments( $user_name, $message_id );
}

$wgAjaxExportList[] = 'wfDisplayAutoWallComment';
function wfDisplayAutoWallComment($user_name, $message_id) {
	global $wgUser;  
    $user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name );              
	$b = new UserBoard();
	return $b->displayWallPostComments( $user_name, $message_id );
}

$wgAjaxExportList[] = 'wfDeleteWallComment';
function wfDeleteWallComment( $user_name, $uwc_id, $ub_id ) {
	global $wgUser;
    
    $user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name ); 
    
	$b = new UserBoard();    
	//if ( $b->doesUserOwnMessage( $wgUser->getID(), $ub_id ) || $wgUser->isAllowed( 'userboard-delete' ) ) {
	//	$b->deleteWallComment( $uwc_id, $ub_id );
	//}
    $b->deleteWallComment( $uwc_id, $ub_id );
	return $b->displayWallPostComments( $user_name, $ub_id );
}

$wgAjaxExportList[] = 'wfSetPinnedWall';
function wfSetPinnedWall( $ub_id ) {
	global $wgUser;

	$b = new UserBoard();
	$b->setPinnedWall( $ub_id );
	return 'ok';
}

$wgAjaxExportList[] = 'wfSendWallLike';
function wfSendWallLike( $user_name, $ub_id ) {
	global $wgUser;
     
    $user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name );
	$user_id_to = User::idFromName( $user_name );
     
	$b = new UserBoard();
	$b->sendWallLike( $ub_id, $wgUser->getID(), $wgUser->getName() );
    $wall_id = $b->getFirstWallID($user_name, $user_id_to); 
	return $b->displayWallPosts( $wall_id, $user_name, $user_id_to );
}

$wgAjaxExportList[] = 'wfSendWallUnLike';
function wfSendWallUnLike( $user_name, $ub_id ) {
	global $wgUser;
    
    $user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name );
	$user_id_to = User::idFromName( $user_name );
     
	$b = new UserBoard();
	$b->sendWallUnLike( $ub_id, $wgUser->getID() );
    
    $wall_id = $b->getFirstWallID($user_name, $user_id_to);
     
	return $b->displayWallPosts( $wall_id, $user_name, $user_id_to );
} 

$wgAjaxExportList[] = 'wfSendWallPostCommentLike';
function wfSendWallPostCommentLike( $user_name, $ub_id, $uwc_id ) {
	global $wgUser;
    
    $user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name );
    
	$b = new UserBoard();
	$b->SendWallPostCommentLike( $uwc_id, $wgUser->getID(), $wgUser->getName() );
	return $b->displayWallPostComments( $user_name, $ub_id );
}

$wgAjaxExportList[] = 'wfSendWallPostCommentUnLike';
function wfSendWallPostCommentUnLike( $user_name, $ub_id, $uwc_id ) {
	global $wgUser;
    
    $user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name );
    
	$b = new UserBoard();
	$b->SendWallPostCommentUnLike( $uwc_id, $wgUser->getID() );
	return $b->displayWallPostComments( $user_name, $ub_id );
}

$wgAjaxExportList[] = 'wfCreateWall';
function wfCreateWall( $user_name, $wall_name ) {
	global $wgUser;
    
    $user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name );
	$user_id_to = User::idFromName( $user_name );
	$wall_name = stripslashes( $wall_name );
    
	$b = new UserBoard();
    
	$m = $b->sendCreateWall( 
		$wgUser->getID(), $wgUser->getName(), $user_id_to, $user_name,  urldecode( $wall_name ) 
        );
 
 return $b->displayWalls( $user_name, $user_id_to, 0, 10 );
}

$wgAjaxExportList[] = 'wfTestMathi';
function wfTestMathi( $fd ) {
	global $wgUser;
    print_r($fd);    
	 //$b->displayWallPostComments( $user_name, $ub_id );
}
