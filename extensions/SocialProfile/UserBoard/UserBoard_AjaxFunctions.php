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
function wfDeleteBoardMessage( $ub_id ) {
	global $wgUser;

	$b = new UserBoard();
	if (
		$b->doesUserOwnMessage( $wgUser->getID(), $ub_id ) ||
		$wgUser->isAllowed( 'userboard-delete' )
	) {
		$b->deleteMessage( $ub_id );
	}
	return 'ok';
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

	return $b->displayWalls( $user_name, $user_id_to, 0, $count );
}

$wgAjaxExportList[] = 'wfDisplayAutoWallPost';
function wfDisplayAutoWallPost($user_name, $count) {
	global $wgUser;
	$user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name );
	$user_id_to = User::idFromName( $user_name );
	$b = new UserBoard();
        	
	return $b->displayWalls( $user_name, $user_id_to, 0, $count );
}

$wgAjaxExportList[] = 'wfSendWallComment';
function wfSendWallComment( $user_name, $message_id, $comment ) {
	global $wgUser;
    $user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name );
    $message_id = stripslashes( $message_id );
	$b = new UserBoard();
	$m = $b->sendWallComment($message_id,
		$wgUser->getID(), $wgUser->getName(),
        urldecode( $comment )
	);

	return $b->displayWallcomments( $user_name, $message_id );
}
//
$wgAjaxExportList[] = 'wfSendEditWallComment';
function wfSendEditWallComment( $user_name, $uwc_id, $message_id, $comment ) {
	global $wgUser;
    $user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name );
    $message_id = stripslashes( $message_id );
    $uwc_id = stripslashes( $uwc_id );
    
	$b = new UserBoard();
	$m = $b->sendEditWallComment( $uwc_id, $message_id, urldecode( $comment ) );

	return $b->displayWallcomments( $user_name, $message_id );
}

$wgAjaxExportList[] = 'wfDisplayAutoWallComment';
function wfDisplayAutoWallComment($user_name, $message_id) {
	global $wgUser;  
    $user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name );              
	$b = new UserBoard();
	return $b->displayWallcomments( $user_name, $message_id );
}

$wgAjaxExportList[] = 'wfDeleteWallComment';
function wfDeleteWallComment( $uwc_id, $ub_id ) {
	global $wgUser;

	$b = new UserBoard();    
	//if ( $b->doesUserOwnMessage( $wgUser->getID(), $ub_id ) || $wgUser->isAllowed( 'userboard-delete' ) ) {
	//	$b->deleteWallComment( $uwc_id, $ub_id );
	//}
    $b->deleteWallComment( $uwc_id, $ub_id );
	return 'ok';
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
	return $b->displayWalls( $user_name, $user_id_to );
}

$wgAjaxExportList[] = 'wfSendWallUnLike';
function wfSendWallUnLike( $user_name, $ub_id ) {
	global $wgUser;
    
    $user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name );
	$user_id_to = User::idFromName( $user_name );
     
	$b = new UserBoard();
	$b->sendWallUnLike( $ub_id, $wgUser->getID() );
	return $b->displayWalls( $user_name, $user_id_to );
} 

$wgAjaxExportList[] = 'wfSendWallCommentLike';
function wfSendWallCommentLike( $user_name, $ub_id, $uwc_id ) {
	global $wgUser;
    
    $user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name );
    
	$b = new UserBoard();
	$b->sendWallCommentLike( $uwc_id, $wgUser->getID(), $wgUser->getName() );
	return $b->displayWallcomments( $user_name, $ub_id );
}

$wgAjaxExportList[] = 'wfSendWallCommentUnLike';
function wfSendWallCommentUnLike( $user_name, $ub_id, $uwc_id ) {
	global $wgUser;
    
    $user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name );
    
	$b = new UserBoard();
	$b->sendWallCommentUnLike( $uwc_id, $wgUser->getID() );
	return $b->displayWallcomments( $user_name, $ub_id );
}

$wgAjaxExportList[] = 'wfTestMathi';
function wfTestMathi( $fd ) {
	global $wgUser;
    print_r($fd);
    
	 //$b->displayWallcomments( $user_name, $ub_id );
}
