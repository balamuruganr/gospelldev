<?php
/**
 * AJAX functions used by UserBoard.
 */
$wgAjaxExportList[] = 'wfSendBoardMessage';
function wfSendBoardMessage( $user_name, $message, $message_type, $parse, $count ) {
	global $wgUser;
	$user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name );
	$user_id_to = User::idFromName( $user_name );
	$b = new UserBoard();

	$m = $b->sendBoardMessage(
		$wgUser->getID(), $wgUser->getName(), $user_id_to, $user_name,
		urldecode( $message ), $message_type, $parse
	);
 
	return $b->displayMessages( $user_id_to, 0, $count );
}

$wgAjaxExportList[] = 'wfDisplayBoardMessage';
function wfDisplayBoardMessage( $user_name, $count ) {
	global $wgUser;
	$user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name );
	$user_id_to = User::idFromName( $user_name );
	$b = new UserBoard();

	return $b->displayMessages( $user_id_to, 0, $count );
}

$wgAjaxExportList[] = 'wfAutoDisplayBoardMessage';
function wfAutoDisplayBoardMessage( $last_id, $user_name ) {
	global $wgUser;
	$user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name );
	$user_id_to = User::idFromName( $user_name );
	$b = new UserBoard();

	return $b->autoDisplayMessages( $last_id, $user_id_to, 0 );
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
function wfDeleteWallMessage( $wall_id, $user_name, $ub_id ) {
	global $wgUser;
    
    $user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name );
	$user_id_to = User::idFromName( $user_name );
    
    $wall_id = stripslashes( $wall_id );
    $wall_id = urldecode( $wall_id );
	$b = new UserBoard();
	//if ($b->doesUserOwnMessage( $wgUser->getID(), $ub_id ) || $wgUser->isAllowed( 'userboard-delete' ) ) { }
    $b->deleteMessage( $ub_id );
    
 return $b->displayWallPosts( $wall_id, $user_name, $user_id_to, 0, 10 );
}


$wgAjaxExportList[] = 'wfSendBoardMessageWall';
function wfSendBoardMessageWall( $currnt_wall_id, $user_name, $message, $message_type, $count ) {
	global $wgUser;
	$user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name );
	$user_id_to = User::idFromName( $user_name );
    $currnt_wall_id = stripslashes( $currnt_wall_id );
    $currnt_wall_id = urldecode( $currnt_wall_id );
	$b = new UserBoard();

	$m = $b->sendWallPost(
		$currnt_wall_id, $wgUser->getID(), $wgUser->getName(), $user_id_to, $user_name,
		urldecode( $message ), $message_type
	);  
       
	return $b->displayWallPosts( $currnt_wall_id, $user_name, $user_id_to, 0, $count );
}

$wgAjaxExportList[] = 'wfDisplayWallPost';
function wfDisplayWallPost( $currnt_wall_id, $user_name, $count ) {
	global $wgUser;
	$user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name );
	$user_id_to = User::idFromName( $user_name );
    $currnt_wall_id = stripslashes( $currnt_wall_id );
    $currnt_wall_id = urldecode( $currnt_wall_id );        
	$b = new UserBoard();
    
  return $b->displayWallPosts( $currnt_wall_id, $user_name, $user_id_to, 0, $count );
}

$wgAjaxExportList[] = 'wfDisplayAutoWallPost';
function wfDisplayAutoWallPost( $currnt_wall_id, $last_post_id, $user_name ) {
	global $wgUser;
	$user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name );
	$user_id_to = User::idFromName( $user_name );
    $currnt_wall_id = stripslashes( $currnt_wall_id );
    $currnt_wall_id = urldecode( $currnt_wall_id );        
	$b = new UserBoard();
    //echo $currnt_wall_id."<==>".$last_post_id; die;
  return $b->displayAutoWallPosts( $currnt_wall_id, $last_post_id, $user_name, $user_id_to );
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

$wgAjaxExportList[] = 'wfDisplayWallComment';
function wfDisplayWallComment($user_name, $message_id) {
	global $wgUser;  
    $user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name );              
	$b = new UserBoard();
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

$wgAjaxExportList[] = 'wfSetPinnedPost';
function wfSetPinnedPost( $wall_id, $user_name, $ub_id ) {
	global $wgUser;
    
    $user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name );
	$user_id_to = User::idFromName( $user_name );
    
    $wall_id = stripslashes( $wall_id );
    $wall_id = urldecode( $wall_id );
    
	$b = new UserBoard();
	$b->setPinnedWallPost( $ub_id );
    
   return $b->displayWallPosts( $wall_id, $user_name, $user_id_to );
}

$wgAjaxExportList[] = 'wfUnSetPinnedPost';
function wfUnSetPinnedPost( $wall_id, $user_name, $ub_id ) {
	global $wgUser;
    
    $user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name );
	$user_id_to = User::idFromName( $user_name );
    
    $wall_id = stripslashes( $wall_id );
    $wall_id = urldecode( $wall_id );
    
	$b = new UserBoard();
	$b->unSetPinnedWallPost( $ub_id );
    
   return $b->displayWallPosts( $wall_id, $user_name, $user_id_to );
}



$wgAjaxExportList[] = 'wfSendWallLike';
function wfSendWallLike( $wall_id, $user_name, $ub_id ) {
	global $wgUser;
     
    $user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name );
	$user_id_to = User::idFromName( $user_name );
    
    $wall_id = stripslashes( $wall_id );
    $wall_id = urldecode( $wall_id );
     
	$b = new UserBoard();
	$b->sendWallLike( $ub_id, $wgUser->getID(), $wgUser->getName() );
    
  return $b->displayWallPosts( $wall_id, $user_name, $user_id_to );
}

$wgAjaxExportList[] = 'wfSendWallUnLike';
function wfSendWallUnLike( $wall_id, $user_name, $ub_id ) {
	global $wgUser;
    
    $user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name );
	$user_id_to = User::idFromName( $user_name );
     
    $wall_id = stripslashes( $wall_id );
    $wall_id = urldecode( $wall_id );
     
	$b = new UserBoard();
	$b->sendWallUnLike( $ub_id, $wgUser->getID() );
     
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

$wgAjaxExportList[] = 'wfUpdateWall';
function wfUpdateWall( $user_name, $wall_id, $wall_name ) {
	global $wgUser;
    
    $user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name );
	$user_id_to = User::idFromName( $user_name );
    
    $wall_id = stripslashes( $wall_id );
	$wall_name = stripslashes( $wall_name );
    
	$b = new UserBoard();
    
	$m = $b->sendEditWall( 
		urldecode( $wall_id ),  urldecode( $wall_name ) 
        );
 
 return $b->displayWalls( $user_name, $user_id_to, 0, 10 );
}

$wgAjaxExportList[] = 'wfDeleteWall';
function wfDeleteWall( $user_name, $wall_id ) {
	global $wgUser;
    
    $user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name );
	$user_id_to = User::idFromName( $user_name );
        
    $wall_id = stripslashes( $wall_id );
    
	$b = new UserBoard();
    
	$m = $b->sendDeleteWall( $wall_id );
 
 return $b->displayWalls( $user_name, $user_id_to, 0, 10 );
}

$wgAjaxExportList[] = 'wfAutoBookList';
function wfAutoBookList( $user_name ) {
	global $wgUser;
    
    $user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name );
    $user_id = User::idFromName( $user_name );
    
    //$profile = new UserProfilePage( $user_name );
    
    //"<span id=\"user-book-{$book['book_id']}\"><a href=\"javascript:void(0);\" onclick=\"javascript:goto_this_bookset('{$book['book_id']}','".$url.$book['book_id']."');\">{$book['book_name']}</a></span>";
	
    
 
 return "";//$profile->getUserProfileBookList( $user_name );
}

$wgAjaxExportList[] = 'wfAutoFetchUrl';
function wfAutoFetchUrl( $uri ) {
  
  return gospellCommonFunctions::featch_url( $uri );       
}

$wgAjaxExportList[] = 'wfSetLastPostId';
function wfSetLastPostId($wall_id, $user_name){
   
    $user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name );
    $user_id = User::idFromName( $user_name );
    
    $b = new UserBoard();
    
   return $b->getLastPostId( $wall_id, $user_name, $user_id );
}

$wgAjaxExportList[] = 'wfSetLastMessageId';
function wfSetLastMessageId($user_name){
   
    $user_name = stripslashes( $user_name );
	$user_name = urldecode( $user_name );
    $user_id = User::idFromName( $user_name );
    
    $b = new UserBoard();
    
   return $b->getLastMessageId( $user_id, 0 );
}


$wgAjaxExportList[] = 'wfTestfunc';
function wfTestMathi() {
	global $wgUser;
      
 $b->displayWallPostComments( $user_name, $ub_id );
}
