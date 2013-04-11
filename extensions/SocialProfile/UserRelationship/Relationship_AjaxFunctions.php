<?php
/**
 * AJAX functions used by UserRelationship extension.
 */
global $IP;
require_once("$IP/includes/gospellCommonClass.php"); 
$wgAjaxExportList[] = 'wfRelationshipRequestResponse';
function wfRelationshipRequestResponse( $response, $requestId ) {
	global $wgUser;
	$out = '';

	$rel = new UserRelationship( $wgUser->getName() );
	if ( $rel->verifyRelationshipRequest( $requestId ) == true ) {	    
		$request = $rel->getRequest( $requestId );
		$user_name_from = $request[0]['user_name_from'];
               $user_realname_from = gospellCommonFunctions::getUserRealNameByUserId($request[0]['user_id_from']);//getting user real name
		$user_id_from = User::idFromName( $user_name_from );
		$rel_type = strtolower( $request[0]['type'] );

		$response = ( isset( $_POST['response' ] ) ) ? $_POST['response'] : $response;
		$rel->updateRelationshipRequestStatus( $requestId, intval( $response ) );

		$avatar = new wAvatar( $user_id_from, 'l' );
		$avatar_img = $avatar->getAvatarURL();

		if ( $response == 1 ) {
			$rel->addRelationship( $requestId );
			$out .= "<div class=\"relationship-action red-text\">
				{$avatar_img}" .
					wfMsg( "ur-requests-added-message-{$rel_type}", $user_realname_from ) .
				'<div class="cleared"></div>
			</div>';
		} else {
			$out .= "<div class=\"relationship-action red-text\">
				{$avatar_img}" .
					wfMsg( "ur-requests-reject-message-{$rel_type}", $user_realname_from ) .
				'<div class="cleared"></div>
			</div>';
		}
		$rel->deleteRequest( $requestId );
	}

	return $out;
}
