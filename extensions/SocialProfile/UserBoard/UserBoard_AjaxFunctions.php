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

	return $b->displayWalls( $user_id_to, 0, $count );
}

$wgAjaxExportList[] = 'wfSendWallComment';
function wfSendWallComment( $message_id, $comment ) {
	global $wgUser;
    $message_id = stripslashes( $message_id );
	$b = new UserBoard();
	$m = $b->sendWallComment($message_id,
		$wgUser->getID(), $wgUser->getName(),
        urldecode( $comment )
	);

	return $b->displayWallcommands( $message_id );
}

$wgAjaxExportList[] = 'wfDisplayAutoWallPost';
function wfDisplayAutoWallPost() {
	global $wgUser;
	$user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name );
	$user_id_to = User::idFromName( $user_name );
	$b = new UserBoard();
    	
	return $b->displayWalls( $user_id_to, 0, $count );
}

$wgAjaxExportList[] = 'wfDisplayAutoWallComment';
function wfDisplayAutoWallComment($message_id) {
	global $wgUser;                
	$b = new UserBoard();
	return $b->displayWallcommands( $message_id );
}
