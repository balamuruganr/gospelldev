<?php
/**
 * Functions for managing user board data
 */
class UserBoard {

	/**
	 * Constructor
	 */
	public function __construct() {}
     
    public function sendCreateWall($user_id_from, $user_name_from, $user_id_to, $user_name_to, $wall_name){
        
        $dbw = wfGetDB( DB_MASTER );
        
        $user_name_from = stripslashes( $user_name_from );
		$user_name_to = stripslashes( $user_name_to );
        
        $dbw->insert(
			'user_walls',
			array(
				'uw_user_id' => $user_id_to,
				'uw_user_name' => $user_name_to,
                'uw_user_id_from' => $user_id_from,
                'uw_user_name_from' => $user_name_from,
				'uw_wall_name' => $wall_name,
				'uw_date' => date( 'Y-m-d H:i:s' ),
			),
			__METHOD__
		);
        
        /*/ Send e-mail notification (if user is not writing on own board)
		if ( $user_id_from != $user_id_to ) {
			$this->sendBoardNotificationEmail( $user_id_to, $user_name_from );
			$this->incNewMessageCount( $user_id_to );
		}

		$stats = new UserStatsTrack( $user_id_to, $user_name_to );
		if ( $message_type == 0 ) {
			// public message count
			$stats->incStatField( 'user_board_count' );
		} else {
			// private message count
			$stats->incStatField( 'user_board_count_priv' );
		}

		$stats = new UserStatsTrack( $user_id_from, $user_name_from );
		$stats->incStatField( 'user_board_sent' );
        */

		return $dbw->insertId();
        
    }
    
    public function sendEditWall( $wall_id, $wall_name ){
        $dbw = wfGetDB( DB_MASTER );
        
        $wall_id = stripslashes( $wall_id );
        $wall_name = stripslashes( $wall_name );
        
        $dbw->update( 'user_walls',
			array( 'uw_wall_name' => $wall_name ),
			array( 'uw_id' => $wall_id ),
			__METHOD__
		);
    } 
        
	/**
	 * Sends a user board message to another user.
	 * Performs the insertion to user_board table, sends e-mail notification
	 * (if appliable), and increases social statistics as appropriate.
	 *
	 * @param $user_id_from Integer: user ID of the sender
	 * @param $user_name_from Mixed: user name of the sender
	 * @param $user_id_to Integer: user ID of the reciever
	 * @param $user_name_to Mixed: user name of the reciever
	 * @param $message Mixed: message text
	 * @param $message_type Integer: 0 for public message
	 * @return Integer: the inserted value of ub_id row
	 */
	public function sendBoardMessage( $user_id_from, $user_name_from, $user_id_to, $user_name_to, $message, $message_type = 0, $parse = 0 ) {
		$dbw = wfGetDB( DB_MASTER );

		$user_name_from = stripslashes( $user_name_from );
		$user_name_to = stripslashes( $user_name_to );

		$dbw->insert(
			'user_board',
			array(
				'ub_user_id_from' => $user_id_from,
				'ub_user_name_from' => $user_name_from,
				'ub_user_id' => $user_id_to,
				'ub_user_name' => $user_name_to,
				'ub_message' => $message,
				'ub_type' => $message_type,
                'ub_no_parse' => $parse,
				'ub_date' => date( 'Y-m-d H:i:s' ),
			),
			__METHOD__
		);

		// Send e-mail notification (if user is not writing on own board)
		if ( $user_id_from != $user_id_to ) {
			$this->sendBoardNotificationEmail( $user_id_to, $user_name_from );
			$this->incNewMessageCount( $user_id_to );
		}

		$stats = new UserStatsTrack( $user_id_to, $user_name_to );
		if ( $message_type == 0 ) {
			// public message count
			$stats->incStatField( 'user_board_count' );
		} else {
			// private message count
			$stats->incStatField( 'user_board_count_priv' );
		}

		$stats = new UserStatsTrack( $user_id_from, $user_name_from );
		$stats->incStatField( 'user_board_sent' );

		return $dbw->insertId();
	}
    
    public function sendWallPost( $wall_id, $user_id_from, $user_name_from, $user_id_to, $user_name_to, $message, $message_type = 0 ) {
		$dbw = wfGetDB( DB_MASTER );

		$user_name_from = stripslashes( $user_name_from );
		$user_name_to = stripslashes( $user_name_to );
        $wall_id = stripslashes( $wall_id );
        
		$dbw->insert(
			'user_board',
			array(
                'ub_wall_id' => $wall_id,
				'ub_user_id_from' => $user_id_from,
				'ub_user_name_from' => $user_name_from,
				'ub_user_id' => $user_id_to,
				'ub_user_name' => $user_name_to,
				'ub_message' => $message,
				'ub_type' => $message_type,
				'ub_date' => date( 'Y-m-d H:i:s' ),
			),
			__METHOD__
		);

		// Send e-mail notification (if user is not writing on own board)
		if ( $user_id_from != $user_id_to ) {
			$this->sendBoardNotificationEmail( $user_id_to, $user_name_from );
			$this->incNewMessageCount( $user_id_to );
		}

		$stats = new UserStatsTrack( $user_id_to, $user_name_to );
		if ( $message_type == 0 ) {
			// public message count
			$stats->incStatField( 'user_board_count' );
		} else {
			// private message count
			$stats->incStatField( 'user_board_count_priv' );
		}

		$stats = new UserStatsTrack( $user_id_from, $user_name_from );
		$stats->incStatField( 'user_board_sent' );

		return $dbw->insertId();
	}
    
    public function getLastInsertedMessageId($user_id_from, $user_name_from, $user_id_to, $user_name_to){
        global $wgUser, $wgOut, $wgTitle;
        
		$dbr = wfGetDB( DB_SLAVE );
        
        $where = " ub_user_id_from={$user_id_from} AND ub_user_id={$user_id_to} AND ub_type=1";
        
        $limit_sql =" LIMIT 0, 1";
        
        $sql = "SELECT ub_id, ub_user_id_from, ub_user_name_from, ub_user_id, ub_user_name,
			ub_message,UNIX_TIMESTAMP(ub_date) AS unix_time,ub_type, ub_pinned
			FROM {$dbr->tableName( 'user_board' )} WHERE{$where} ORDER BY ub_id DESC{$limit_sql}";
            
		$res = $dbr->query( $sql, __METHOD__ );
		$messages = array();
		foreach ( $res as $row ) {
			$parser = new Parser();
			$message_text = $parser->parse( $row->ub_message, $wgTitle, $wgOut->parserOptions(), true );
			$message_text = $message_text->getText();

			$messages[] = array(
				'ub_id' => $row->ub_id,
				'timestamp' => ( $row->unix_time ),
				'user_id_from' => $row->ub_user_id_from,
				'user_name_from' => $row->ub_user_name_from,
				'user_id' => $row->ub_user_id,
				'user_name' => $row->ub_user_name,
				'message_text' => $message_text,
				'type' => $row->ub_type,
                'is_pinned' => $row->ub_pinned
                
			);
		}
        
     return $messages[0]['ub_id'];   
    }
    
    public function sendUploadUserBoardFiles($ub_id, $fname){
        $dbw = wfGetDB( DB_MASTER );

		$ub_id = stripslashes( $ub_id );
		$fname = stripslashes( $fname );

		$dbw->insert(
			'user_bord_files',
			array(
				'ubf_ub_id' => $ub_id,
				'ubf_file_name' => $fname,
			),
			__METHOD__
		);
       
       return $dbw->insertId();    
    }
    
    public function sendUserBoardMessageFiles() {
      global $wgUser, $wgRequest, $wgUploadDirectory, $wgFileExtensions, $wgGospellSettingsUserBordMessageFileSize;
      
        $user_name = stripslashes( $_POST['user_name'] );
	    $user_name = urldecode( $user_name );
	    $user_id_to = User::idFromName( $user_name );
        
        $ub_id = $this->getLastInsertedMessageId($wgUser->getID(), $wgUser->getName(), $user_id_to, $user_name);
        
        if (!empty($_FILES)) { //File Array Check
           foreach($_FILES["up_files"]["name"] as $key => $val){
            
            $fileParts  = pathinfo($_FILES['up_files']['name'][$key]);
            // Check if extension is valid
			if (in_array($fileParts['extension'],$wgFileExtensions)) {
			 // Check file size is valid
			  if($_FILES['up_files']['size'][$key] <= $wgGospellSettingsUserBordMessageFileSize){	     
			  
			  $fname = str_replace(".","",microtime(true)).".".$fileParts['extension'];
              $file_up = move_uploaded_file( $_FILES["up_files"]["tmp_name"][$key], $wgUploadDirectory."/user_files/" . $fname);
              if($file_up)
               {
                 $insert = $this->sendUploadUserBoardFiles($ub_id, $fname);
                 if(!$insert){
                  @unlink($wgUploadDirectory."/user_files/" . $fname);   
                 }                 
               }
               usleep(1000000);
               
              } // Check file size ends
              
			} //Check if extension ends //@unlink($_FILES["up_files"]["tmp_name"][$key]);
                           
           } //loop ends
            
        } //File Array Check ends
      //$b->displayMessages( $user_id_to, 0, $count )      
    }
    
    public function SendWallPostComment($message_id, $user_id, $user_name, $comment){
        $dbw = wfGetDB( DB_MASTER );
        
        $message_id = stripslashes( $message_id );
        $user_id = stripslashes( $user_id );
        $user_name = stripslashes( $user_name );
		$comment = stripslashes( $comment );
        
        $dbw->insert(
			'user_wall_comments',
			array(
				'uwc_wallmessage_id' => $message_id,
				'uwc_user_id' => $user_id,
				'uwc_user_name' => $user_name,
				'uwc_comment' => $comment,
				'uwc_date' => date( 'Y-m-d H:i:s' ),
			),
			__METHOD__
		);
        
        /*// Send e-mail notification (if user is not writing on own board)
		if ( $user_id_from != $user_id_to ) {
			$this->sendBoardNotificationEmail( $user_id_to, $user_name_from );
			$this->incNewMessageCount( $user_id_to );
		}

		$stats = new UserStatsTrack( $user_id_to, $user_name_to );
		if ( $message_type == 0 ) {
			// public message count
			$stats->incStatField( 'user_board_count' );
		} else {
			// private message count
			$stats->incStatField( 'user_board_count_priv' );
		}

		$stats = new UserStatsTrack( $user_id_from, $user_name_from );
		$stats->incStatField( 'user_board_sent' ); */
      
      return $dbw->insertId();  
    }
    
    public function SendEditWallPostComment($uwc_id, $message_id, $comment){
        $dbw = wfGetDB( DB_MASTER );
        
        $message_id = stripslashes( $message_id );
        $comment = stripslashes( $comment );
        
        $dbw->update( 'user_wall_comments',
			array( 'uwc_comment' => $comment, 'uwc_date' => date( 'Y-m-d H:i:s' ) ),
			array( 'uwc_id' => $uwc_id, 'uwc_wallmessage_id' => $message_id ),
			__METHOD__
		);
        
        /*// Send e-mail notification (if user is not writing on own board)
		if ( $user_id_from != $user_id_to ) {
			$this->sendBoardNotificationEmail( $user_id_to, $user_name_from );
			$this->incNewMessageCount( $user_id_to );
		}

		$stats = new UserStatsTrack( $user_id_to, $user_name_to );
		if ( $message_type == 0 ) {
			// public message count
			$stats->incStatField( 'user_board_count' );
		} else {
			// private message count
			$stats->incStatField( 'user_board_count_priv' );
		}

		$stats = new UserStatsTrack( $user_id_from, $user_name_from );
		$stats->incStatField( 'user_board_sent' ); */
             
    }
    
	/**
	 * Sends an email to a user if someone wrote on their board
	 *
	 * @param $user_id_to Integer: user ID of the reciever
	 * @param $user_from Mixed: the user name of the person who wrote the board message
	 */
	public function sendBoardNotificationEmail( $user_id_to, $user_from ) {
		$user = User::newFromId( $user_id_to );
		$user->loadFromId();

		// Send email if user's email is confirmed and s/he's opted in to recieving social notifications
		if ( $user->isEmailConfirmed() && $user->getIntOption( 'notifymessage', 1 ) ) {
			$board_link = SpecialPage::getTitleFor( 'UserBoard' );
			$update_profile_link = SpecialPage::getTitleFor( 'UpdateProfile' );
			$subject = wfMsgExt( 'message_received_subject', 'parsemag',
				$user_from
			);
			$body = wfMsgExt( 'message_received_body', 'parsemag',
				$user->getName(),
				$user_from,
				$board_link->escapeFullURL(),
				$update_profile_link->escapeFullURL()
			);
			$user->sendMail( $subject, $body );
		}
	}

	/**
	 * Increase the amount of new messages for $user_id
	 *
	 * @param $user_id Integer: user ID for the user whose message count we're
	 *							going to increase.
	 */
	public function incNewMessageCount( $user_id ) {
		global $wgMemc;
		$key = wfMemcKey( 'user', 'newboardmessage', $user_id );
		$wgMemc->incr( $key );
	}

	/**
	 * Clear the new board messages counter for the user with ID = $user_id.
	 * This is done by setting the value of the memcached key to 0.
	 *
	 * @param $user_id Integer: user ID for the user whose message count we're
	 *							going to clear.
	 */
	static function clearNewMessageCount( $user_id ) {
		global $wgMemc;
		$key = wfMemcKey( 'user', 'newboardmessage', $user_id );
		$wgMemc->set( $key, 0 );
	}

	/**
	 * Get the amount of new board messages for the user with ID = $user_id
	 * from memcached. If successful, returns the amount of new messages.
	 *
	 * @param $user_id Integer: user ID for the user whose messages we're going
	 *							to fetch.
	 * @return Integer: amount of new messages
	 */
	static function getNewMessageCountCache( $user_id ) {
		global $wgMemc;
		$key = wfMemcKey( 'user', 'newboardmessage', $user_id );
		$data = $wgMemc->get( $key );
		if ( $data != '' ) {
			wfDebug( "Got new message count of $data for id $user_id from cache\n" );
			return $data;
		}
	}

	/**
	 * Get the amount of new board messages for the user with ID = $user_id
	 * from the database.
	 *
	 * @param $user_id Integer: user ID for the user whose messages we're going
	 *							to fetch.
	 * @return Integer: amount of new messages
	 */
	static function getNewMessageCountDB( $user_id ) {
		global $wgMemc;

		wfDebug( "Got new message count for id $user_id from DB\n" );

		$key = wfMemcKey( 'user', 'newboardmessage', $user_id );
		$newCount = 0;
		/* 
		$dbw = wfGetDB( DB_MASTER );
		$s = $dbw->selectRow(
			'user_board',
			array( 'COUNT(*) AS count' ),
			array( 'ug_user_id_to' => $user_id, 'ug_status' => 1 ),
			__METHOD__
		);
		if ( $s !== false ) {
			$newCount = $s->count;
		}
		*/

		$wgMemc->set( $key, $newCount );

		return $newCount;
	}

	/**
	 * Get the amount of new board messages for the user with ID = $user_id.
	 * First tries cache (memcached) and if that succeeds, returns the cached
	 * data. If that fails, the count is fetched from the database.
	 * UserWelcome.php calls this function.
	 *
	 * @param $user_id Integer: user ID for the user whose messages we're going
	 *							to fetch.
	 * @return Integer: amount of new messages
	 */
	static function getNewMessageCount( $user_id ) {
		$data = self::getNewMessageCountCache( $user_id );

		if ( $data != '' ) {
			$count = $data;
		} else {
			$count = self::getNewMessageCountDB( $user_id );
		}
		return $count;
	}

	/**
	 * Checks if the user with ID number $user_id owns the board message with
	 * the ID number $ub_id.
	 *
	 * @param $user_id Integer: user ID number
	 * @param $ub_id Integer: user board message ID number
	 * @return Boolean: true if user owns the message, otherwise false
	 */
	public function doesUserOwnMessage( $user_id, $ub_id ) {
		$dbr = wfGetDB( DB_SLAVE );
		$s = $dbr->selectRow(
			'user_board',
			array( 'ub_user_id' ),
			array( 'ub_id' => $ub_id ),
			__METHOD__
		);
		if ( $s !== false ) {
			if ( $user_id == $s->ub_user_id ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Deletes a user board message from the database and decreases social
	 * statistics as appropriate (either 'user_board_count' or
	 * 'user_board_count_priv' is decreased by one).
	 *
	 * @param $ub_id Integer: ID number of the board message that we want to delete
	 */
	public function deleteMessage( $ub_id ) {
		if ( $ub_id ) {
			$dbw = wfGetDB( DB_MASTER );
			$s = $dbw->selectRow(
				'user_board',
				array( 'ub_user_id', 'ub_user_name', 'ub_type' ),
				array( 'ub_id' => $ub_id ),
				__METHOD__
			);
			if ( $s !== false ) {
				$dbw->delete(
					'user_board',
					array( 'ub_id' => $ub_id ),
					__METHOD__
				);

				$stats = new UserStatsTrack( $s->ub_user_id, $s->ub_user_name );
				if ( $s->ub_type == 0 ) {
					$stats->decStatField( 'user_board_count' );
				} else {
					$stats->decStatField( 'user_board_count_priv' );
				}
			}
		}
	}
    
    /**
	 * Deletes a user board message from the database and decreases social
	 * statistics as appropriate (either 'user_board_count' or
	 * 'user_board_count_priv' is decreased by one).
	 *
	 * @param $uwc_id Integer: ID number of the comment  that we want to delete
	 */
	public function deleteWallComment( $uwc_id, $ub_id ) {
		if ( $ub_id ) {
			$dbw = wfGetDB( DB_MASTER );
			$s = $dbw->selectRow(
				'user_wall_comments',
				array( 'uwc_user_id', 'uwc_user_name', 'uwc_status' ),
				array( 'uwc_id' => $uwc_id ),
				__METHOD__
			);
			if ( $s !== false ) {
				$dbw->delete(
					'user_wall_comments',
					array( 'uwc_id' => $uwc_id ),
					__METHOD__
				);

				/*$stats = new UserStatsTrack( $s->ub_user_id, $s->ub_user_name );
				if ( $s->ub_type == 0 ) {
					$stats->decStatField( 'user_board_count' );
				} else {
					$stats->decStatField( 'user_board_count_priv' );
				}*/
			}
		}
	}
    
    public function getUserWallsList( $user_id, $user_id_2 = 0, $limit = 0, $page = 0 ){        
        global $wgUser, $wgOut, $wgTitle;
		$dbr = wfGetDB( DB_SLAVE );
        
        if ( $limit > 0 ) {
			$limitvalue = 0;
			if ( $page ) {
				$limitvalue = $page * $limit - ( $limit );
			}
			$limit_sql = " LIMIT {$limitvalue},{$limit} ";
		}
        
        if ( $user_id_2 ) {
			$user_sql = "( (uw_user_id={$user_id} AND uw_user_id_from={$user_id_2}) OR (uw_user_id={$user_id_2} AND uw_user_id_from={$user_id}) )";
			
		} else {
			$user_sql = "uw_user_id = {$user_id}";
            
			if ( $wgUser->isLoggedIn() ) {
				$user_sql .= " OR (uw_user_id={$user_id} AND uw_user_id_from={$wgUser->getID() }" . " ) ";
			}
		}
        
        $sql = "SELECT uw_id, uw_user_id_from, uw_user_name_from, uw_user_id, uw_user_name,
			uw_wall_name, UNIX_TIMESTAMP(uw_date) AS unix_time 
            FROM {$dbr->tableName( 'user_walls' )} 
            WHERE {$user_sql} 
            ORDER BY uw_id ASC 
            {$limit_sql}";
        
        $res = $dbr->query( $sql, __METHOD__ );
		$walls = array();
		foreach ( $res as $row ) {
			//$parser = new Parser();
			//$message_text = $parser->parse( $row->ub_message, $wgTitle, $wgOut->parserOptions(), true );
			//$message_text = $message_text->getText();
            
			$walls[] = array(
				'wall_id' => $row->uw_id,
				'timestamp' => ( $row->unix_time ),
				'user_id_from' => $row->uw_user_id_from,
				'user_name_from' => $row->uw_user_name_from,
				'user_id' => $row->uw_user_id,
				'user_name' => $row->uw_user_name,
				'wall_name' => $row->uw_wall_name
			);
		}
        
	 return $walls;
             
    }
    
    public function sendDeleteWall( $wall_id ){
            
           if ( $wall_id ) {
            
			$dbw = wfGetDB( DB_MASTER );
			$s = $dbw->selectRow(
				'user_walls',
				array( 'uw_id', 'uw_user_id', 'uw_user_name', 'uw_wall_name', 'uw_user_id_from', 'uw_user_name_from' ),
				array( 'uw_id' => $wall_id ),
				__METHOD__
			);
			if ( $s !== false ) {
				$dbw->delete(
					'user_walls',
					array( 'uw_id' => $wall_id ),
					__METHOD__
				);

			}
		}
        
    }
    
	/**
	 * Get the user board messages for the user with the ID $user_id.
	 *
	 * @todo FIXME: rewrite this function to be compatible with non-MySQL DBMS
	 * @param $user_id Integer: user ID number
	 * @param $user_id_2 Integer: user ID number of the second user; only used
	 *                            in board-to-board stuff
	 * @param $limit Integer: used to build the LIMIT and OFFSET for the SQL
	 *                        query
	 * @param $page Integer: used to build the LIMIT and OFFSET for the SQL
	 *                       query
	 * @return Array: array of user board messages
	 */
	public function getUserBoardMessages( $user_id, $user_id_2 = 0, $limit = 0, $page = 0 ) {
		global $wgUser, $wgOut, $wgTitle;
		$dbr = wfGetDB( DB_SLAVE );
        
		if ( $limit > 0 ) {
			$limitvalue = 0;
			if ( $page ) {
				$limitvalue = $page * $limit - ( $limit );
			}
			$limit_sql = " LIMIT {$limitvalue},{$limit} ";
		}
        
		if ( $user_id_2 ) {
			$user_sql = "( (ub_user_id={$user_id} AND ub_user_id_from={$user_id_2} AND ub_type = 1) OR
					(ub_user_id={$user_id_2} AND ub_user_id_from={$user_id} AND ub_type = 1) )";
			
		} else {
		  
			$user_sql = "ub_user_id = {$user_id} AND ub_type = 1";            
			
			if ( $wgUser->isLoggedIn() ) {
				$user_sql .= " OR ( ub_user_id={$user_id} AND ub_user_id_from={$wgUser->getID() }" . " AND ub_type = 1 ) ";
			}
		}


		$sql = "SELECT ub_id, ub_wall_id, ub_user_id_from, ub_user_name_from, ub_user_id, ub_user_name,
			ub_message, ub_no_parse, UNIX_TIMESTAMP(ub_date) AS unix_time,ub_type, ub_pinned
			FROM {$dbr->tableName( 'user_board' )}
			WHERE {$user_sql}
			ORDER BY ub_id DESC
			{$limit_sql}";
		$res = $dbr->query( $sql, __METHOD__ );
		$messages = array();
		foreach ( $res as $row ) {
		    
		   if($row->ub_no_parse == 0 ){
			 $parser = new Parser();
			 $message_text = $parser->parse( $row->ub_message, $wgTitle, $wgOut->parserOptions(), true );
			 $message_text = $message_text->getText();
            } else {
              $message_text = $row->ub_message; 
            }
            
			$messages[] = array(
				'id' => $row->ub_id,
				'timestamp' => ( $row->unix_time ),
                'wall_id' => $row->ub_wall_id,
				'user_id_from' => $row->ub_user_id_from,
				'user_name_from' => $row->ub_user_name_from,
				'user_id' => $row->ub_user_id,
				'user_name' => $row->ub_user_name,
				'message_text' => $message_text,
				'type' => $row->ub_type,
                'no_parse' => $row->ub_no_parse,
                'is_pinned' => $row->ub_pinned
                
			);
		}
		return $messages;
	}
    
    public function getAutoUserBoardMessages( $last_id, $user_id, $user_id_2 = 0, $limit = 0, $page = 0 ) {
		global $wgUser, $wgOut, $wgTitle;
		$dbr = wfGetDB( DB_SLAVE );
        
        if($last_id == NULL){
            $last_id = 0;
        }
        
        $limit_sql ="";
		if ( $limit > 0 ) {
			$limitvalue = 0;
			if ( $page ) {
				$limitvalue = $page * $limit - ( $limit );
			}
			$limit_sql = " LIMIT {$limitvalue},{$limit} ";
		}
        
		if ( $user_id_2 ) {
		  
			$user_sql = "( (ub_user_id={$user_id} AND ub_user_id_from={$user_id_2} AND ub_type = 1 AND ub_id > {$last_id}) OR
					(ub_user_id={$user_id_2} AND ub_user_id_from={$user_id} AND ub_type = 1 AND ub_id > {$last_id}) )";
                    
		} else {
		  
			$user_sql = "ub_user_id = {$user_id} AND ub_type = 1 AND ub_id > {$last_id}";
            
			if ( $wgUser->isLoggedIn() ) {
				$user_sql .= " OR ( ub_user_id={$user_id} AND ub_user_id_from={$wgUser->getID() }" . "  AND ub_type = 1 AND ub_id > {$last_id} ) ";
			}
		}
        
		$sql = "SELECT ub_id, ub_wall_id, ub_user_id_from, ub_user_name_from, ub_user_id, ub_user_name,
			ub_message, ub_no_parse, UNIX_TIMESTAMP(ub_date) AS unix_time,ub_type, ub_pinned
			FROM {$dbr->tableName( 'user_board' )}
			WHERE {$user_sql}
			ORDER BY ub_id DESC
			{$limit_sql}";
		$res = $dbr->query( $sql, __METHOD__ );
		$messages = array();
		foreach ( $res as $row ) {
		  
			if($row->ub_no_parse == 0 ){
			 $parser = new Parser();
			 $message_text = $parser->parse( $row->ub_message, $wgTitle, $wgOut->parserOptions(), true );
			 $message_text = $message_text->getText();
            } else {
              $message_text = $row->ub_message; 
            }

			$messages[] = array(
				'id' => $row->ub_id,
				'timestamp' => ( $row->unix_time ),
                'wall_id' => $row->ub_wall_id,
				'user_id_from' => $row->ub_user_id_from,
				'user_name_from' => $row->ub_user_name_from,
				'user_id' => $row->ub_user_id,
				'user_name' => $row->ub_user_name,
				'message_text' => $message_text,
				'type' => $row->ub_type,
                'no_parse' => $row->ub_no_parse,
                'is_pinned' => $row->ub_pinned
                
			);
		}
		return $messages;
	}
    
    function getLastmessageId( $user_id, $user_id_2 = 0 ){
        global $wgUser, $wgOut, $wgTitle;
	  $dbr = wfGetDB( DB_SLAVE );
        
      if ( $user_id_2 ) {
			$user_sql = "( (ub_user_id={$user_id} AND ub_user_id_from={$user_id_2} AND ub_type = 1) OR
					(ub_user_id={$user_id_2} AND ub_user_id_from={$user_id} AND ub_type = 1) )";
			
		} else {
		  
			$user_sql = "ub_user_id = {$user_id} AND ub_type = 1";            
			
			if ( $wgUser->isLoggedIn() ) {
				$user_sql .= " OR ( ub_user_id={$user_id} AND ub_user_id_from={$wgUser->getID() }" . " AND ub_type = 1 ) ";
			}
		}


		$sql = "SELECT MAX( ub_id ) AS last_msg_id FROM {$dbr->tableName( 'user_board' )} 
            WHERE {$user_sql}";
        
        $res = $dbr->query( $sql, __METHOD__ );
	    $row = $dbr->fetchObject( $res );
                
     return (isset($row->last_msg_id))? $row->last_msg_id : '0'; 
    }
    
    /**
	 * Get the user board messages for the user with the ID $user_id.
	 *
	 * @todo FIXME: rewrite this function to be compatible with non-MySQL DBMS
	 * @param $user_id Integer: user ID number
	 * @param $user_id_2 Integer: user ID number of the second user; only used
	 *                            in board-to-board stuff
	 * @param $limit Integer: used to build the LIMIT and OFFSET for the SQL
	 *                        query
	 * @param $page Integer: used to build the LIMIT and OFFSET for the SQL
	 *                       query
	 * @return Array: array of user board messages
	 */
	public function getUserWallPosts( $wall_id, $user_id, $user_id_2 = 0, $limit = 0, $page = 0 ) {
		global $wgUser, $wgOut, $wgTitle;
		$dbr = wfGetDB( DB_SLAVE );
        $limit_sql='';
		if ( $limit > 0 ) {
			$limitvalue = 0;
			if ( $page ) {
				$limitvalue = $page * $limit - ( $limit );
			}
			$limit_sql = " LIMIT {$limitvalue},{$limit} ";
		}
                
		if ( $user_id_2 ) {
			$user_sql = "( (ub_wall_id={$wall_id} AND ub_user_id={$user_id} AND ub_user_id_from={$user_id_2} AND ub_type = 0 AND ub_pinned !=1) OR
					(ub_wall_id={$wall_id} AND ub_user_id={$user_id_2} AND ub_user_id_from={$user_id} AND ub_type = 0 AND ub_pinned !=1) )";
			
		} else {
			$user_sql = "ub_wall_id={$wall_id} AND ub_user_id = {$user_id} AND ub_type = 0 AND ub_pinned !=1";            
			
			if ( $wgUser->isLoggedIn() ) {
				$user_sql .= " OR ( ub_wall_id={$wall_id} AND ub_user_id={$user_id} AND ub_user_id_from={$wgUser->getID() }" . " AND ub_type = 0 AND ub_pinned !=1 ) ";
			}
		}
        //echo $limit_sql."<br />"; die;
        
		$sql = "SELECT ub_id, ub_wall_id, ub_user_id_from, ub_user_name_from, ub_user_id, ub_user_name,
			ub_message,UNIX_TIMESTAMP(ub_date) AS unix_time,ub_type, ub_pinned FROM {$dbr->tableName( 'user_board' )} 
            WHERE {$user_sql}
			ORDER BY ub_id DESC
			{$limit_sql}";
          //echo $sql;  
		$res = $dbr->query( $sql, __METHOD__ );
        $messages = array();        
		foreach ( $res as $row ) {
			$parser = new Parser();
			$message_text = $parser->parse( $row->ub_message, $wgTitle, $wgOut->parserOptions(), true );
			$message_text = $message_text->getText();
            
			$messages[] = array(
				'id' => $row->ub_id,
				'timestamp' => ( $row->unix_time ),
                'wall_id' => $row->ub_wall_id,
				'user_id_from' => $row->ub_user_id_from,
				'user_name_from' => $row->ub_user_name_from,
				'user_id' => $row->ub_user_id,
				'user_name' => $row->ub_user_name,
				'message_text' => $message_text,
				'type' => $row->ub_type,
                'is_pinned' => $row->ub_pinned
                
			); 
		}
        
		return $messages;
	}
    
    public function getUserWallNewPosts( $wall_id, $last_post_id, $user_id, $user_id_2 = 0 ) {
		global $wgUser, $wgOut, $wgTitle;
		$dbr = wfGetDB( DB_SLAVE );
		 
         if($last_post_id == NULL){
            $last_post_id = 0;
         }
                       
		if ( $user_id_2 ) {
			$user_sql = "( (ub_wall_id={$wall_id} AND ub_user_id={$user_id} AND ub_user_id_from={$user_id_2} AND ub_type = 0 AND ub_pinned !=1 AND ub_id > {$last_post_id}) OR
					(ub_wall_id={$wall_id} AND ub_user_id={$user_id_2} AND ub_user_id_from={$user_id} AND ub_type = 0 AND ub_pinned !=1 AND ub_id > {$last_post_id}) )";
			
		} else {
			$user_sql = "ub_wall_id={$wall_id} AND ub_user_id = {$user_id} AND ub_type = 0 AND ub_pinned !=1 AND ub_id > {$last_post_id}";            
			
			if ( $wgUser->isLoggedIn() ) {
				$user_sql .= " OR ( ub_wall_id={$wall_id} AND ub_user_id={$user_id} AND ub_user_id_from={$wgUser->getID() }" . " AND ub_type = 0 AND ub_pinned !=1 AND ub_id > {$last_post_id}) ";
			}
		}
        
		$sql = "SELECT ub_id, ub_wall_id, ub_user_id_from, ub_user_name_from, ub_user_id, ub_user_name,
			ub_message,UNIX_TIMESTAMP(ub_date) AS unix_time,ub_type, ub_pinned FROM {$dbr->tableName( 'user_board' )} 
            WHERE {$user_sql}
			ORDER BY ub_id DESC";
            //echo $sql; die; 
		$res = $dbr->query( $sql, __METHOD__ );
        $messages = array();        
		foreach ( $res as $row ) {
			$parser = new Parser();
			$message_text = $parser->parse( $row->ub_message, $wgTitle, $wgOut->parserOptions(), true );
			$message_text = $message_text->getText();
            
			$messages[] = array(
				'id' => $row->ub_id,
				'timestamp' => ( $row->unix_time ),
                'wall_id' => $row->ub_wall_id,
				'user_id_from' => $row->ub_user_id_from,
				'user_name_from' => $row->ub_user_name_from,
				'user_id' => $row->ub_user_id,
				'user_name' => $row->ub_user_name,
				'message_text' => $message_text,
				'type' => $row->ub_type,
                'is_pinned' => $row->ub_pinned
                
			); 
		}
        
		return $messages;
    }
    
    public function getUserWallPinnedPosts( $wall_id, $user_id, $user_id_2 = 0 ){
        global $wgUser, $wgOut, $wgTitle;
		$dbr = wfGetDB( DB_SLAVE );
                
		if ( $user_id_2 ) {
			$user_sql = "( (ub_wall_id={$wall_id} AND ub_user_id={$user_id} AND ub_user_id_from={$user_id_2} AND ub_type = 0 AND ub_pinned =1) OR
					(ub_wall_id={$wall_id} AND ub_user_id={$user_id_2} AND ub_user_id_from={$user_id} AND ub_type = 0 AND ub_pinned =1) )";
			
		} else {
			$user_sql = "ub_wall_id={$wall_id} AND ub_user_id = {$user_id} AND ub_type = 0 AND ub_pinned =1";            
			
			if ( $wgUser->isLoggedIn() ) {
				$user_sql .= " OR ( ub_wall_id={$wall_id} AND ub_user_id={$user_id} AND ub_user_id_from={$wgUser->getID() }" . " AND ub_type = 0 AND ub_pinned =1  ) ";
			}
		}

		$sql = "SELECT ub_id, ub_wall_id, ub_user_id_from, ub_user_name_from, ub_user_id, ub_user_name,
			ub_message,UNIX_TIMESTAMP(ub_date) AS unix_time,ub_type, ub_pinned FROM {$dbr->tableName( 'user_board' )} 
            WHERE {$user_sql}
			ORDER BY ub_pinned DESC";
		$res = $dbr->query( $sql, __METHOD__ );
        $messages = array();        
		foreach ( $res as $row ) {
			$parser = new Parser();
			$message_text = $parser->parse( $row->ub_message, $wgTitle, $wgOut->parserOptions(), true );
			$message_text = $message_text->getText();
            
			$messages[] = array(
				'id' => $row->ub_id,
				'timestamp' => ( $row->unix_time ),
                'wall_id' => $row->ub_wall_id,
				'user_id_from' => $row->ub_user_id_from,
				'user_name_from' => $row->ub_user_name_from,
				'user_id' => $row->ub_user_id,
				'user_name' => $row->ub_user_name,
				'message_text' => $message_text,
				'type' => $row->ub_type,
                'is_pinned' => $row->ub_pinned
                
			); 
		}
        
		return $messages;
    }
    
        
    public function getUserBoardMessageFiles( $ub_id, $limit = 0, $page = 0){
        global $wgUser, $wgOut, $wgTitle;
		$dbr = wfGetDB( DB_SLAVE );
        $limit_sql="";
        if ( $limit > 0 ) {
			$limitvalue = 0;
			if ( $page ) {
				$limitvalue = $page * $limit - ( $limit );
			}
			$limit_sql = " LIMIT {$limitvalue},{$limit} ";
		}
        
        if ( $ub_id ) {
           $ub_sql = "ubf_ub_id ={$ub_id}"; 
        }
        
        $sql = "SELECT ubf_file_id, ubf_ub_id, ubf_file_name, ubf_status FROM {$dbr->tableName( 'user_bord_files' )}
			WHERE {$ub_sql} ORDER BY ubf_file_id DESC {$limit_sql}";
        
        $res = $dbr->query( $sql, __METHOD__ );
		$files = array();
        
        foreach ( $res as $row ) {
			//$parser = new Parser();
			//$message_text = $parser->parse( $row->ubf_file_name, $wgTitle, $wgOut->parserOptions(), true );
			//$message_text = $message_text->getText();
            $files[] = array(
				'file_id' => $row->ubf_file_id,
				'ub_id' => $row->ubf_ub_id,
				'file_name' => $row->ubf_file_name                
			);
        }
     return $files;          
    }
    
    /**
	 * Get the user board messages for the user with the ID $user_id.
	 *
	 * @todo FIXME: rewrite this function to be compatible with non-MySQL DBMS
	 * @param $user_id Integer: user ID number
	 * @param $user_id_2 Integer: user ID number of the second user; only used
	 *                            in board-to-board stuff
	 * @param $limit Integer: used to build the LIMIT and OFFSET for the SQL
	 *                        query
	 * @param $page Integer: used to build the LIMIT and OFFSET for the SQL
	 *                       query
	 * @return Array: array of user board messages
	 */
	public function getUserWallMessages( $user_id, $user_id_2 = 0, $limit = 0, $page = 0 ) {
		global $wgUser, $wgOut, $wgTitle;
		$dbr = wfGetDB( DB_SLAVE );

		if ( $limit > 0 ) {
			$limitvalue = 0;
			if ( $page ) {
				$limitvalue = $page * $limit - ( $limit );
			}
			$limit_sql = " LIMIT {$limitvalue},{$limit} ";
		}

		if ( $user_id_2 ) {
			$user_sql = "( (ub_user_id={$user_id} AND ub_user_id_from={$user_id_2} AND ub_type = 0) OR
					(ub_user_id={$user_id_2} AND ub_user_id_from={$user_id} AND ub_type = 0) )";
			//if ( !( $user_id == $wgUser->getID() || $user_id_2 == $wgUser->getID() ) ) {
			//	$user_sql .= ' AND ub_type = 0 ';
			//}
		} else {
			$user_sql = "ub_user_id = {$user_id}";
            $user_sql .= ' AND ub_type = 0 ';
			//if ( $user_id != $wgUser->getID() ) {
			   
			//	$user_sql .= ' AND ub_type = 0 ';
			//}
			if ( $wgUser->isLoggedIn() ) {
				$user_sql .= " OR (ub_user_id={$user_id} AND ub_user_id_from={$wgUser->getID() }" . " AND ub_type = 0 ) ";
			}
		}


		$sql = "SELECT ub_id, ub_user_id_from, ub_user_name_from, ub_user_id, ub_user_name,
			ub_message,UNIX_TIMESTAMP(ub_date) AS unix_time,ub_type, ub_pinned
			FROM {$dbr->tableName( 'user_board' )}
			WHERE {$user_sql}
			ORDER BY ub_id, ub_pinned DESC
			{$limit_sql}";
		$res = $dbr->query( $sql, __METHOD__ );
		$messages = array();
		foreach ( $res as $row ) {
			$parser = new Parser();
			$message_text = $parser->parse( $row->ub_message, $wgTitle, $wgOut->parserOptions(), true );
			$message_text = $message_text->getText();

			$messages[] = array(
				'id' => $row->ub_id,
				'timestamp' => ( $row->unix_time ),
				'user_id_from' => $row->ub_user_id_from,
				'user_name_from' => $row->ub_user_name_from,
				'user_id' => $row->ub_user_id,
				'user_name' => $row->ub_user_name,
				'message_text' => $message_text,
				'type' => $row->ub_type,
                'is_pinned' => $row->ub_pinned
                
			);
		}
		return $messages;
	}
    
    public function getUserWallPostComments( $message_id, $limit = 0, $page = 0 ) {
		global $wgUser, $wgOut, $wgTitle;
		$dbr = wfGetDB( DB_SLAVE );

		if ( $limit > 0 ) {
			$limitvalue = 0;
			if ( $page ) {
				$limitvalue = $page * $limit - ( $limit );
			}
			$limit_sql = " LIMIT {$limitvalue},{$limit} ";
		}
        $comment_sql='';
        if ( $message_id ) {
          $comment_sql = "( uwc_wallmessage_id ={$message_id} )"; 
        }
		
		$sql = "SELECT uwc_id, uwc_wallmessage_id, uwc_user_id, uwc_user_name, uwc_comment,
			uwc_pinned,UNIX_TIMESTAMP(uwc_date) AS unix_datetime, uwc_status
			FROM {$dbr->tableName( 'user_wall_comments' )}
            WHERE {$comment_sql}
			ORDER BY uwc_id ASC
			{$limit_sql}";
           
		$res = $dbr->query( $sql, __METHOD__ );
		$comments = array();
		foreach ( $res as $row ) {
			$parser = new Parser();
			$comment_text = $parser->parse( $row->uwc_comment, $wgTitle, $wgOut->parserOptions(), true );
			$comment_text = $comment_text->getText();

			$comments[] = array(
				'comment_id' => $row->uwc_id,
                'wall_message_id' =>$row->uwc_wallmessage_id,
				'timestamp' => ( $row->unix_datetime ),
				'user_id_from' => $row->uwc_user_id,
				'user_name_from' => $row->uwc_user_name,
				'user_id' => $wgUser->getID(),
				'user_name' => $wgUser->getName(),
				'comment_text' => $comment_text,
				'status' => $row->uwc_status
			);
		}
		return $comments;
	}
	/**
	 * Get the amount of board-to-board messages sent between the users whose
	 * IDs are $user_id and $user_id_2.
	 *
	 * @todo FIXME: rewrite this function to be compatible with non-MySQL DBMS
	 * @param $user_id Integer: user ID of the first user
	 * @param $user_id_2 Integer: user ID of the second user
	 * @return Integer: the amount of board-to-board messages
	 */
	public function getUserBoardToBoardCount( $user_id, $user_id_2 ) {
		global $wgUser;

		$dbr = wfGetDB( DB_SLAVE );

		$user_sql = " ( (ub_user_id={$user_id} AND ub_user_id_from={$user_id_2}) OR
					(ub_user_id={$user_id_2} AND ub_user_id_from={$user_id}) )";

		if ( !( $user_id == $wgUser->getID() || $user_id_2 == $wgUser->getID() ) ) {
			$user_sql .= ' AND ub_type = 0 ';
		}
		$sql = "SELECT COUNT(*) AS the_count
			FROM {$dbr->tableName( 'user_board' )}
			WHERE {$user_sql}";

		$res = $dbr->query( $sql, __METHOD__ );
		$row = $dbr->fetchObject( $res );
		if ( $row ) {
			$count = $row->the_count;
		}
		return $count;
	}
    
    public function displayUserBoardMessageFiles( $ub_id ){      
      global $wgUser, $wgTitle, $wgScriptPath, $wgFileExtensions;  
      $output = ''; // Prevent E_NOTICE
      
      $files = $this->getUserBoardMessageFiles( $ub_id, 0 );
       
       //
      foreach ( $files as $file ) {
        
         $output .= "<div class=\"user-board-message-eachfile\">
                      <a href=\"$wgScriptPath/images/user_files/{$file['file_name']}\" target=\"_block\">{$file['file_name']}</a>                      
                     </div>";
       }
     return $output;  
    }

	public function displayMessages( $user_id, $user_id_2 = 0, $count = 10, $page = 0 ) {
		global $wgUser, $wgTitle;

		$output = ''; //Prevent E_NOTICE
        $isThereAnyMsg=0;
                        
		$messages = $this->getUserBoardMessages( $user_id, $user_id_2, $count, $page );
		if ( $messages ) {
		  
    		     foreach ( $messages as $message ) {
    		     
    			  if( $message['type'] == 1 ){
    			    $isThereAnyMsg = 1;
    				$user = Title::makeTitle( NS_USER, $message['user_name_from'] );
    				$avatar = new wAvatar( $message['user_id_from'], 'm' );
    
    				$board_to_board = '';
    				$board_link = '';
    				$message_type_label = '';
    				$delete_link = '';
    
    				if ( $wgUser->getName() != $message['user_name_from'] ) {
    					$board_to_board = '<a href="' . UserBoard::getUserBoardToBoardURL( $message['user_name'], $message['user_name_from'] ) . '">' .
    						wfMsgHtml( 'userboard_board-to-board' ) . '</a>';
    					$board_link = '<a href="' . UserBoard::getUserBoardURL( $message['user_name_from'] ) . '">' .
    						wfMsgHtml( 'userboard_sendmessage', $message['user_name_from'] ) . '</a>';
    				}
    				if ( $wgUser->getName() == $message['user_name'] || $wgUser->isAllowed( 'userboard-delete' ) ) {
    					$delete_link = "<span class=\"user-board-red\">
    							<a href=\"javascript:void(0);\" onclick=\"javascript:delete_message({$message['id']})\">" .
    								wfMsgHtml( 'userboard_delete' ) . '</a>
    						</span>';
    				}
                    
    				//if ( $message['type'] == 1 ) {
    				//	$message_type_label = '(' . wfMsgHtml( 'userboard_private' ) . ')';
    				//}
    
    				$message_text = $message['message_text'];
    				# $message_text = preg_replace_callback( "/(<a[^>]*>)(.*?)(<\/a>)/i", 'cut_link_text', $message['message_text'] );
    
    				$output .= "<div class=\"user-board-message\" id=\"user-board-message-{$message['id']}\">
    					<div class=\"user-board-message-from\">
    					 <a href=\"{$user->escapeFullURL()}\" title=\"{$message['user_name_from']}\">{$message['user_name_from']}</a>                         
    					</div>
    					<div class=\"user-board-message-time\">" .
    						wfMsgHtml( 'userboard_posted_ago', $this->getTimeAgo( $message['timestamp'] ) ) .
    					"</div>
    					<div class=\"user-board-message-content\">
    						<div class=\"user-board-message-image\">
    							<a href=\"{$user->escapeFullURL()}\" title=\"{$message['user_name_from']}\">{$avatar->getAvatarURL()}</a>
    						</div>
    						<div class=\"user-board-message-body\">
    						". $message_text ."
    						</div>
    						<div class=\"cleared\"></div>
                             <div class=\"user-board-message-files\">";
                 $output .= $this->displayUserBoardMessageFiles($message['id']);           
                 $output .= "</div>
    					</div>
    					<div class=\"user-board-message-links\">
    						{$board_link}
    						{$board_to_board}
    						{$delete_link}
    					</div>
    				</div>";
                  } //if ends here  
    			}
                
            $output .= '<div id="scrolled-board-messages"></div>';       
		  }
       if(!$isThereAnyMsg)
        { //if ( $wgUser->getName() == $wgTitle->getText() )
          $output .= '<div class="no-info-container">' .
				wfMsgHtml( 'userboard_nomessages' ) .
			'</div>';  
        } 
        
		return $output;
	}
    
    public function autoDisplayMessages( $last_id, $user_id, $user_id_2 = 0, $count = 10, $page = 0 ) {
		global $wgUser, $wgTitle;

		$output = ''; // Prevent E_NOTICE
        $isThereAnyMsg=0;
        
		$messages = $this->getAutoUserBoardMessages( $last_id, $user_id, $user_id_2, $count, $page );
		if ( $messages ) {
		  
    		     foreach ( $messages as $message ) {
    		     
    			  if( $message['type'] == 1 ){
    			    $isThereAnyMsg = 1;
    				$user = Title::makeTitle( NS_USER, $message['user_name_from'] );
    				$avatar = new wAvatar( $message['user_id_from'], 'm' );
    
    				$board_to_board = '';
    				$board_link = '';
    				$message_type_label = '';
    				$delete_link = '';
    
    				if ( $wgUser->getName() != $message['user_name_from'] ) {
    					$board_to_board = '<a href="' . UserBoard::getUserBoardToBoardURL( $message['user_name'], $message['user_name_from'] ) . '">' .
    						wfMsgHtml( 'userboard_board-to-board' ) . '</a>';
    					$board_link = '<a href="' . UserBoard::getUserBoardURL( $message['user_name_from'] ) . '">' .
    						wfMsgHtml( 'userboard_sendmessage', $message['user_name_from'] ) . '</a>';
    				}
    				if ( $wgUser->getName() == $message['user_name'] || $wgUser->isAllowed( 'userboard-delete' ) ) {
    					$delete_link = "<span class=\"user-board-red\">
    							<a href=\"javascript:void(0);\" onclick=\"javascript:delete_message({$message['id']})\">" .
    								wfMsgHtml( 'userboard_delete' ) . '</a>
    						</span>';
    				}
                    
    				//if ( $message['type'] == 1 ) {
    				//	$message_type_label = '(' . wfMsgHtml( 'userboard_private' ) . ')';
    				//}
    
    				$message_text = $message['message_text'];
    				# $message_text = preg_replace_callback( "/(<a[^>]*>)(.*?)(<\/a>)/i", 'cut_link_text', $message['message_text'] );
    
    				$output .= "<div class=\"user-board-message\" id=\"user-board-message-{$message['id']}\">
    					<div class=\"user-board-message-from\">
    					 <a href=\"{$user->escapeFullURL()}\" title=\"{$message['user_name_from']}\">{$message['user_name_from']}</a>                         
    					</div>
    					<div class=\"user-board-message-time\">" .
    						wfMsgHtml( 'userboard_posted_ago', $this->getTimeAgo( $message['timestamp'] ) ) .
    					"</div>
    					<div class=\"user-board-message-content\">
    						<div class=\"user-board-message-image\">
    							<a href=\"{$user->escapeFullURL()}\" title=\"{$message['user_name_from']}\">{$avatar->getAvatarURL()}</a>
    						</div>
    						<div class=\"user-board-message-body\">
    						". $message_text ."
    						</div>
    						<div class=\"cleared\"></div>
                             <div class=\"user-board-message-files\">";
                 $output .= $this->displayUserBoardMessageFiles($message['id']);           
                 $output .= "</div>
    					</div>
    					<div class=\"user-board-message-links\">
    						{$board_link}
    						{$board_to_board}
    						{$delete_link}
    					</div>
    				</div>";
                  } //if ends here  
    			}                
                 
		  }
        
        
		return $output;
	}
    
       
    public function getFirstWallID($user_name, $user_id, $user_id_2 = 0){        
        global $wgUser, $wgTitle, $wgStylePath;
        $dbr = wfGetDB( DB_SLAVE );
        
        
        if ( $user_id_2 ) {
			$user_sql = "( (uw_user_id={$user_id} AND uw_user_id_from={$user_id_2}) OR (uw_user_id={$user_id_2} AND uw_user_id_from={$user_id}) )";
			
		} else {
			$user_sql = "uw_user_id = {$user_id}";
            
			if ( $wgUser->isLoggedIn() ) {
				$user_sql .= " OR (uw_user_id={$user_id} AND uw_user_id_from={$wgUser->getID() }" . " ) ";
			}
		}
         
         $sql = "SELECT uw_id, uw_user_id_from, uw_user_name_from, uw_user_id, uw_user_name,
			uw_wall_name, UNIX_TIMESTAMP(uw_date) AS unix_time 
            FROM {$dbr->tableName( 'user_walls' )} 
            WHERE {$user_sql} 
            ORDER BY uw_id ASC LIMIT 0, 1";
        
        $res = $dbr->query( $sql, __METHOD__ );
		$walls = array();
		foreach ( $res as $row ) {
		  $walls[] = array(
				'wall_id' => $row->uw_id,
				'timestamp' => ( $row->unix_time ),
				'user_id_from' => $row->uw_user_id_from,
				'user_name_from' => $row->uw_user_name_from,
				'user_id' => $row->uw_user_id,
				'user_name' => $row->uw_user_name,
				'wall_name' => $row->uw_wall_name
			);
		}
                 
      return  (!empty($walls))?$walls[0]['wall_id']:0;   
    }
    
    public function displayWalls($user_name, $user_id, $user_id_2 = 0, $count = 10, $page = 0) {
        global $wgUser, $wgTitle, $wgStylePath;
        
        $output = '';
		$output = "<span id=\"curr_wall_0\" class=\"active_wall\" style=\"clear:both;\">
                   <a href=\"javascript:void(0);\" onclick=\"javascript:display_postfor_wall('0');\">Base wall</a>
                   </span><div class=\"cleared\"></div>"; // Prevent E_NOTICE
        
        $walls = $this->getUserWallsList(  $user_id, $user_id_2, $count, $page );
        if ( $walls ) {	
            //'<span><a>wall1</a></span><span><a>wall2</a></span><span><a>wall3</a></span>';
           foreach ( $walls as $wall ) {    
            $user = Title::makeTitle( NS_USER, $wall['user_name_from'] );
		    $avatar = new wAvatar( $wall['user_id_from'], 'm' );
                    
            $comment_user = Title::makeTitle( NS_USER, $wgUser->getName() );
            $avatar_comment = new wAvatar( $wgUser->getID(), 'm' );
            //if ( $wgUser->getName() === $user_name ) {}
            $output .= "<span id=\"curr_wall_{$wall['wall_id']}\">
                         <a href=\"javascript:void(0);\" onclick=\"javascript:display_postfor_wall('{$wall['wall_id']}');\">{$wall['wall_name']}</a>&nbsp;
                         [<a href=\"javascript:void(0);\" class=\"wall_rename_remove\" onclick=\"rename_wall('{$wall['wall_id']}');\">Rename this</a>]&nbsp;
                         [<a href=\"javascript:void(0);\" class=\"wall_rename_remove\" onclick=\"delete_wall('{$wall['wall_id']}');\">Remove this</a>]
                        </span><div class=\"cleared\"></div>";
            
           }         
        }
        
     return $output;   
    }
        
    public function getLastPostId($wall_id, $user_name, $user_id, $user_id_2 = 0){
      global $wgUser, $wgOut, $wgTitle;
	  $dbr = wfGetDB( DB_SLAVE );
        
      if ( $user_id_2 ) {
			$user_sql = "( (ub_wall_id={$wall_id} AND ub_user_id={$user_id} AND ub_user_id_from={$user_id_2} AND ub_type = 0 AND ub_pinned !=1) OR
					(ub_wall_id={$wall_id} AND ub_user_id={$user_id_2} AND ub_user_id_from={$user_id} AND ub_type = 0 AND ub_pinned !=1) )";
			
		} else {
			$user_sql = "ub_wall_id={$wall_id} AND ub_user_id = {$user_id} AND ub_type = 0 AND ub_pinned !=1";            
			
			if ( $wgUser->isLoggedIn() ) {
				$user_sql .= " OR ( ub_wall_id={$wall_id} AND ub_user_id={$user_id} AND ub_user_id_from={$wgUser->getID() }" . " AND ub_type = 0 AND ub_pinned !=1 ) ";
			}
		}
        
        $sql = "SELECT MAX( ub_id ) AS last_post_id FROM {$dbr->tableName( 'user_board' )} 
            WHERE {$user_sql}";
        
        $res = $dbr->query( $sql, __METHOD__ );
	    $row = $dbr->fetchObject( $res );
        
      return (isset($row->last_post_id))? $row->last_post_id : '0';          
    }
    
    
    
    public function displayWallPosts( $wall_id, $user_name, $user_id, $user_id_2 = 0, $count = 10, $page = 0) {
		global $wgUser, $wgTitle, $wgStylePath;

		$output = ''; // Prevent E_NOTICE
        $isThereAnyPost = 0; 
             
        if ( $wgUser->getName() === $user_name ) {
          $pinned_msg = $this->getUserWallPinnedPosts( $wall_id, $user_id, $user_id_2 );  
        }
        
		$messages = $this->getUserWallPosts( $wall_id, $user_id, $user_id_2, $count, $page );
        
        if ( isset($pinned_msg) && $pinned_msg ) {	
          $messages = array_merge($pinned_msg, $messages);
        }
               
		if ( $messages ) {		  
    		     foreach ( $messages as $message ) {    			 		     
    			 if( $message['type'] == 0 ){	
    			     $isThereAnyPost = 1;
    				$user = Title::makeTitle( NS_USER, $message['user_name_from'] );
    				$avatar = new wAvatar( $message['user_id_from'], 'm' );
                    
                    $comment_user = Title::makeTitle( NS_USER, $wgUser->getName() );
                    $avatar_comment = new wAvatar( $wgUser->getID(), 'm' );
                    
                    $like_link = ''; 
                    $unlike_link = '';
                    
                    $command_link = ''; 
                    
    				$board_to_board = '';
    				$board_link = '';
    				$message_type_label = '';
    				$delete_link = '';
                    $is_pinned = '';
                    $like_count = '';
                    
                    if ( $wgUser->getName() === $user_name ) {
                        if($message['is_pinned']== 1){
                          $is_pinned .= "<span id=\"user-board-message-pined\" onclick=\"unset_pinned('{$message['id']}');\"><img src=\"{$wgStylePath}/common/images/pined.png\" border=\"0\" title=\"This post has pinned\"></span>";  
                        } else {
                          $is_pinned .= "<span id=\"user-board-message-pin\" onclick=\"set_pinned('{$message['id']}');\"><img src=\"{$wgStylePath}/common/images/pin.png\" border=\"0\" title=\"Pin this post\"></span>";  
                        }                         
                    }                   
                                        
                    $like_link .='<span class="user-wall-message-links" id="wall-message-links-like-'.$message['id'].'" title="Like this post">
                       <a href="javascript:void(0);" onclick="javascript:like_wall_post('.$message['id'].');">'.
    								wfMsgHtml( 'user_wall_like' ) . '</a>
                       </span>';
                    
                    $unlike_link .='<span class="user-wall-message-links" id="wall-message-links-unlike-'.$message['id'].'" title="Unlike this post">
                       <a href="javascript:void(0);" onclick="javascript:unlike_wall_post('.$message['id'].');">'.
    								wfMsgHtml( 'user_wall_unlike' ) . '</a>
                       </span>';  
                       
                    $command_link .='<span class="user-wall-message-links">
                       <a href="javascript:void(0);" onclick="javascript:show_comment_textarea(\''.$message['id'].'\');">'.
    								wfMsgHtml( 'user_wall_comment' ) . '</a>
                       </span>'; 
    				if ( $wgUser->getName() != $message['user_name_from'] ) {
    					
    					$board_link = '<a href="' . UserBoard::getUserWallURL( $message['user_name_from'] ) . '">' .
    						wfMsgHtml( 'userwall_sendmessage', $message['user_name_from'] ) . '</a>';
    				}
    				if ( $wgUser->getName() == $message['user_name'] || $wgUser->getName() == $message['user_name_from'] || $wgUser->isAllowed( 'userboard-delete' ) ) {
    					$delete_link = "<span class=\"user-board-red\">
    							<a href=\"javascript:void(0);\" onclick=\"javascript:delete_wall_post({$message['id']})\">" .
    								wfMsgHtml( 'userboard_delete' ) . '</a>
    						</span>';
    				}
                    
                    /////////////////// Like Count /////////////////////////////////
                    $lk_count = $this->getWallPostLikeCount( $message['id'] );
                    //if($wgUser->getName() == $message['user_name'] && $wgUser->getName() == $message['user_name_from']){
                      //if( $lk_count > 0 ){
                       // $like_count .='<div id="user-wall-likescount" class="wall-likes-count-'.$message['id'].'">You like this</div>';
                       //} else {
                        //$like_count .='';
                       //}  
                    //} else {
                       if( $lk_count > 0 ){
                        $like_count .='<div id="user-wall-likescount" class="wall-likes-count-'.$message['id'].'">' . $lk_count . '&nbsp;like this</div>';   
                       }  
                    //}            
                    /////////////////// Like Count /////////////////////////////////
                                                                
    				$message_text = $message['message_text'];
    				# $message_text = preg_replace_callback( "/(<a[^>]*>)(.*?)(<\/a>)/i", 'cut_link_text', $message['message_text'] );
    
    				$output .= "<div class=\"user-board-message\" id=\"user-wall-message-{$message['id']}\">
    					<div class=\"user-board-message-from\">
    					 <a href=\"{$user->escapeFullURL()}\" title=\"{$message['user_name_from']}\">{$message['user_name_from']}&nbsp;{$message['id']}</a>
                         {$is_pinned}
    					</div>
    					<div class=\"user-board-message-time\">" .
    						wfMsgHtml( 'userwall_posted_ago', $this->getTimeAgo( $message['timestamp'] ) ) .
    					"</div>
    					<div class=\"user-board-message-content\">
    						<div class=\"user-board-message-image\">
    							<a href=\"{$user->escapeFullURL()}\" title=\"{$message['user_name_from']}\">{$avatar->getAvatarURL()}</a>
    						</div>
    						<div class=\"user-board-message-body\">
    							{$message_text}
    						</div>
    						<div class=\"cleared\"></div>
    					</div>
    					<div class=\"user-board-message-links\">";
                    
                    if($this->checkWallLike( $message['id'], $wgUser->getID() ))    
                      $output .= "{$like_link}";
                    else
                      $output .= "{$unlike_link}";  
                    
                    $output .= "
                            {$command_link}
                            {$board_link}
    						{$board_to_board}
    						{$delete_link}
    					</div>
                        $like_count";
                      
                        $is_comments_there = $this->displayWallPostComments($user_name, $message['id']); 
                        
                    $output .= '<div id="user-wall-comments" class="wall-comments-'.$message['id'].'"> ';
                      $output .= $is_comments_there;                    
                    $output .= '</div>';                     
                    
                    $output .='<div id="user-wall-comment-add"';
                        if( strlen(trim($is_comments_there)) <=0 ){
                          $output .=' style="display:none;"';   
                        }
                    $output .=" class=\"comment-add-{$message['id']}\">";
                       
                        $output .="<div class=\"user-wall-comment-block\">
                                    <div class=\"user-wall-comment-image\">
                                     <a href=\"{$comment_user->escapeFullURL()}\" title=\"{$message['user_name_from']}\">{$avatar_comment->getAvatarURL()}</a>
                                    </div>                                 
                                    <textarea name=\"wall_comment_{$message['id']}\" id=\"wall_comment_{$message['id']}\" placeholder=\"Write a comment...\" onkeypress=\"add_comment(event,'wall_comment_{$message['id']}');\" onfocus=\"javascript:stop_auto_load();\"></textarea>
                                   </div>";
                                                                                                          
                       $output .='</div>';                
                    
				   $output .= '</div>'; 
                   
                                   
                  } //if ends here... 
                  
                  
    			}
          $output .= '<div id="scrolled-wall-posts"></div>';                   		  
			
		}else { //if($wgUser->getName() == $wgTitle->getText()){
		   $output .= '<div class="no-info-container">' .
				wfMsgHtml( 'userwall_nomessages' ) .
			'</div>';
		}

		return $output;
	}
    
        
    public function displayAutoWallPosts( $wall_id, $last_post_id, $user_name, $user_id, $user_id_2 = 0 ) {
		global $wgUser, $wgTitle, $wgStylePath;

		$output = ''; // Prevent E_NOTICE
        $isThereAnyPost = 0; 
             
        //if ( $wgUser->getName() === $user_name ) {
        //  $pinned_msg = $this->getUserWallPinnedPosts( $wall_id, $user_id, $user_id_2 );  
        //}
        
		$new_posts = $this->getUserWallNewPosts( $wall_id, $last_post_id, $user_id, $user_id_2 );
        
        //if ( isset($pinned_msg) && $pinned_msg ) {	
        //  $new_posts = array_merge($pinned_msg, $new_posts);
        //}
        
		if ( $new_posts ) {		  
    		     foreach ( $new_posts as $message ) {    			 		     
    			 if( $message['type'] == 0 ){	
    			     $isThereAnyPost = 1;
    				$user = Title::makeTitle( NS_USER, $message['user_name_from'] );
    				$avatar = new wAvatar( $message['user_id_from'], 'm' );
                    
                    $comment_user = Title::makeTitle( NS_USER, $wgUser->getName() );
                    $avatar_comment = new wAvatar( $wgUser->getID(), 'm' );
                    
                    $like_link = ''; 
                    $unlike_link = '';
                    
                    $command_link = ''; 
                    
    				$board_to_board = '';
    				$board_link = '';
    				$message_type_label = '';
    				$delete_link = '';
                    $is_pinned = '';
                    $like_count = '';
                    
                    if ( $wgUser->getName() === $user_name ) {
                        if($message['is_pinned']== 1){
                          $is_pinned .= "<span id=\"user-board-message-pined\" onclick=\"unset_pinned('{$message['id']}');\"><img src=\"{$wgStylePath}/common/images/pined.png\" border=\"0\" title=\"This post has pinned\"></span>";  
                        } else {
                          $is_pinned .= "<span id=\"user-board-message-pin\" onclick=\"set_pinned('{$message['id']}');\"><img src=\"{$wgStylePath}/common/images/pin.png\" border=\"0\" title=\"Pin this post\"></span>";  
                        }                         
                    }                   
                                        
                    $like_link .='<span class="user-wall-message-links" id="wall-message-links-like-'.$message['id'].'" title="Like this post">
                       <a href="javascript:void(0);" onclick="javascript:like_wall_post('.$message['id'].');">'.
    								wfMsgHtml( 'user_wall_like' ) . '</a>
                       </span>';
                    
                    $unlike_link .='<span class="user-wall-message-links" id="wall-message-links-unlike-'.$message['id'].'" title="Unlike this post">
                       <a href="javascript:void(0);" onclick="javascript:unlike_wall_post('.$message['id'].');">'.
    								wfMsgHtml( 'user_wall_unlike' ) . '</a>
                       </span>';  
                       
                    $command_link .='<span class="user-wall-message-links">
                       <a href="javascript:void(0);" onclick="javascript:show_comment_textarea(\''.$message['id'].'\');">'.
    								wfMsgHtml( 'user_wall_comment' ) . '</a>
                       </span>'; 
    				if ( $wgUser->getName() != $message['user_name_from'] ) {
    					
    					$board_link = '<a href="' . UserBoard::getUserWallURL( $message['user_name_from'] ) . '">' .
    						wfMsgHtml( 'userwall_sendmessage', $message['user_name_from'] ) . '</a>';
    				}
    				if ( $wgUser->getName() == $message['user_name'] || $wgUser->getName() == $message['user_name_from'] || $wgUser->isAllowed( 'userboard-delete' ) ) {
    					$delete_link = "<span class=\"user-board-red\">
    							<a href=\"javascript:void(0);\" onclick=\"javascript:delete_wall_post({$message['id']})\">" .
    								wfMsgHtml( 'userboard_delete' ) . '</a>
    						</span>';
    				}
                    
                    /////////////////// Like Count /////////////////////////////////
                    $lk_count = $this->getWallPostLikeCount( $message['id'] );
                    
                       if( $lk_count > 0 ){
                        $like_count .='<div id="user-wall-likescount" class="wall-likes-count-'.$message['id'].'">' . $lk_count . '&nbsp;like this</div>';   
                       }  
                        
                    /////////////////// Like Count /////////////////////////////////                                                                
    				$message_text = $message['message_text'];
    				# $message_text = preg_replace_callback( "/(<a[^>]*>)(.*?)(<\/a>)/i", 'cut_link_text', $message['message_text'] );
    
    				$output .= "<div class=\"user-board-message\" id=\"user-wall-message-{$message['id']}\">
    					<div class=\"user-board-message-from\">
    					 <a href=\"{$user->escapeFullURL()}\" title=\"{$message['user_name_from']}\">{$message['user_name_from']}&nbsp;{$message['id']}</a>
                         {$is_pinned}
    					</div>
    					<div class=\"user-board-message-time\">" .
    						wfMsgHtml( 'userwall_posted_ago', $this->getTimeAgo( $message['timestamp'] ) ) .
    					"</div>
    					<div class=\"user-board-message-content\">
    						<div class=\"user-board-message-image\">
    							<a href=\"{$user->escapeFullURL()}\" title=\"{$message['user_name_from']}\">{$avatar->getAvatarURL()}</a>
    						</div>
    						<div class=\"user-board-message-body\">
    							{$message_text}
    						</div>
    						<div class=\"cleared\"></div>
    					</div>
    					<div class=\"user-board-message-links\">";
                    
                    if($this->checkWallLike( $message['id'], $wgUser->getID() ))    
                      $output .= "{$like_link}";
                    else
                      $output .= "{$unlike_link}";  
                    
                    $output .= "
                            {$command_link}
                            {$board_link}
    						{$board_to_board}
    						{$delete_link}
    					</div>
                        $like_count";
                      
                        $is_comments_there = $this->displayWallPostComments($user_name, $message['id']); 
                        
                    $output .= '<div id="user-wall-comments" class="wall-comments-'.$message['id'].'"> ';
                      $output .= $is_comments_there;                    
                    $output .= '</div>';                     
                    
                    $output .='<div id="user-wall-comment-add"';
                        if( strlen(trim($is_comments_there)) <=0 ){
                          $output .=' style="display:none;"';   
                        }
                    $output .=" class=\"comment-add-{$message['id']}\">";
                       
                        $output .="<div class=\"user-wall-comment-block\">
                                    <div class=\"user-wall-comment-image\">
                                     <a href=\"{$comment_user->escapeFullURL()}\" title=\"{$message['user_name_from']}\">{$avatar_comment->getAvatarURL()}</a>
                                    </div>                                 
                                    <textarea name=\"wall_comment_{$message['id']}\" id=\"wall_comment_{$message['id']}\" placeholder=\"Write a comment...\" onkeypress=\"add_comment(event,'wall_comment_{$message['id']}');\" onfocus=\"javascript:stop_auto_load();\"></textarea>
                                   </div>";
                                                                                                          
                       $output .='</div>';                
                    
				   $output .= '</div>';                   
                  } //if ends here... 
                  
                  
    			}		  
			
		}
        
		return $output;
    }
    
    public function setPinnedWallPost( $ub_id ){
       if ( $ub_id ) {
			$dbw = wfGetDB( DB_MASTER );
			$s = $dbw->selectRow(
				'user_board',
				array( 'ub_id', 'ub_wall_id', 'ub_user_id', 'ub_user_name' ),
				array( 'ub_id' => $ub_id, 'ub_type' => 0 ),
				__METHOD__
			);
			if ( $s !== false ) {
			    $dbw->update(
					'user_board',
                    array( 'ub_pinned' => 0 ),
					array( 'ub_type' => 0, 'ub_pinned' => 1 ),
					__METHOD__
				);
				$dbw->update(
					'user_board',
                    array( 'ub_pinned' => 1 ),
					array( 'ub_id' => $ub_id, 'ub_type' => 0 ),
					__METHOD__
				);
                			
			}
		} 
    }
    
    public function unSetPinnedWallPost( $ub_id ){
       if ( $ub_id ) {
			$dbw = wfGetDB( DB_MASTER );
			$s = $dbw->selectRow(
				'user_board',
				array( 'ub_id', 'ub_wall_id', 'ub_user_id', 'ub_user_name' ),
				array( 'ub_id' => $ub_id, 'ub_type' => 0, 'ub_pinned' => 1 ),
				__METHOD__
			);
			if ( $s !== false ) {
			    $dbw->update(
					'user_board',
                    array( 'ub_pinned' => 0 ),
					array( 'ub_id' => $ub_id, 'ub_type' => 0, 'ub_pinned' => 1 ),
					__METHOD__
				);				                			
			}
		} 
    }
    
    
    public function sendWallLike( $ub_id, $user_id, $user_name ){
        global $wgUser;
              
		$dbw = wfGetDB( DB_MASTER );
        
        $ub_id = stripslashes( $ub_id );
        $user_id = stripslashes( $user_id );
        $user_name = stripslashes( $user_name ); 
        
        $s = $dbw->selectRow(
				'user_likes',
				array( 'ul_ub_id', 'ul_user_id', 'ul_user_name', 'ul_like_state' ),
				array( 'ul_ub_id' => $ub_id, 'ul_user_id' => $user_id ),
				__METHOD__
			);
            
            if ($s !== false ){
                $dbw->update(
					'user_likes',
                    array( 'ul_like_state' => 1 ),
					array( 'ul_ub_id' => $ub_id, 'ul_user_id' => $user_id ),
					__METHOD__
				);
            } else {
              $dbw->insert(
        			'user_likes',
        			array(
        				'ul_ub_id' => $ub_id,
        				'ul_user_id' => $user_id,
        				'ul_user_name' => $user_name,
        				'ul_like_state' => 1,
        				'ul_date' => date( 'Y-m-d H:i:s' ),
        			),
        			__METHOD__
        		);  
           }
                           
        /*// Send e-mail notification (if user is not writing on own board)
		if ( $user_id_from != $user_id_to ) {
			$this->sendBoardNotificationEmail( $user_id_to, $user_name_from );
			$this->incNewMessageCount( $user_id_to );
		}

		$stats = new UserStatsTrack( $user_id_to, $user_name_to );
		if ( $message_type == 0 ) {
			// public message count
			$stats->incStatField( 'user_board_count' );
		} else {
			// private message count
			$stats->incStatField( 'user_board_count_priv' );
		}

		$stats = new UserStatsTrack( $user_id_from, $user_name_from );
		$stats->incStatField( 'user_board_sent' ); */
              
    }
    
    public function sendWallUnLike( $ub_id, $user_id ){
       if ( $ub_id ) {
          $dbw = wfGetDB( DB_MASTER );
          
            $s = $dbw->selectRow(
				'user_likes',
				array( 'ul_ub_id', 'ul_user_id', 'ul_user_name', 'ul_like_state' ),
				array( 'ul_ub_id' => $ub_id, 'ul_user_id' => $user_id, 'ul_like_state' => 1 ),
				__METHOD__
			);
            if ($s !== false ){
                $dbw->update(
					'user_likes',
                    array( 'ul_like_state' => 0 ),
					array( 'ul_ub_id' => $ub_id, 'ul_user_id' => $user_id, 'ul_like_state' => 1 ),
					__METHOD__
				);
            }
       }	       
    }
    
    public function checkWallLike( $ub_id, $user_id ){
        global $wgUser;
        
        if($ub_id){
            $dbw = wfGetDB( DB_MASTER );
            $s = $dbw->selectRow(
				'user_likes',
				array( 'ul_ub_id', 'ul_user_id', 'ul_user_name', 'ul_like_state' ),
				array( 'ul_ub_id' => $ub_id, 'ul_user_id' => $user_id, 'ul_like_state' => 1 ),
				__METHOD__
			);
                        
			if ($s !== false )
             return false;
            else
             return true;				
       }      
    }
    
    public function getWallPostLikeCount( $ub_id ){
        global $wgUser;
        
        if($ub_id){
            $dbw = wfGetDB( DB_MASTER );
            $sql = "SELECT ul_ub_id, ul_like_state, COUNT(ul_ub_id) AS like_count 
                    FROM user_likes 
                    WHERE ul_ub_id ={$ub_id} AND ul_like_state =1";
        
        $res = $dbw->query( $sql, __METHOD__ );
	    $row = $dbw->fetchObject( $res );                    				
       }
       
     return (isset($row->like_count))? $row->like_count : 0;        
    }
    
    public function getWallPostCommentLikeCount( $uwc_id ){
        global $wgUser;
        
        if($uwc_id){
            $dbw = wfGetDB( DB_MASTER );
            $sql = "SELECT ul_uwc_id, ul_like_state, COUNT(ul_uwc_id) AS like_count 
                    FROM user_likes 
                    WHERE ul_uwc_id ={$uwc_id} AND ul_like_state =1";
        
        $res = $dbw->query( $sql, __METHOD__ );
	    $row = $dbw->fetchObject( $res );                    				
       }
       
     return (isset($row->like_count))? $row->like_count : 0;        
    }
    
    public function SendWallPostCommentLike( $uwc_id, $user_id, $user_name ){
        global $wgUser;
              
		$dbw = wfGetDB( DB_MASTER );
        
        $uwc_id = stripslashes( $uwc_id );
        $user_id = stripslashes( $user_id );
        $user_name = stripslashes( $user_name ); 
        
        $s = $dbw->selectRow(
				'user_likes',
				array( 'ul_uwc_id', 'ul_user_id', 'ul_user_name', 'ul_like_state' ),
				array( 'ul_uwc_id' => $uwc_id, 'ul_user_id' => $user_id ),
				__METHOD__
			);
            
            if ($s !== false ){
                $dbw->update(
					'user_likes',
                    array( 'ul_like_state' => 1 ),
					array( 'ul_uwc_id' => $uwc_id, 'ul_user_id' => $user_id ),
					__METHOD__
				);
            } else {
              $dbw->insert(
        			'user_likes',
        			array(
        				'ul_uwc_id' => $uwc_id,
        				'ul_user_id' => $user_id,
        				'ul_user_name' => $user_name,
        				'ul_like_state' => 1,
        				'ul_date' => date( 'Y-m-d H:i:s' ),
        			),
        			__METHOD__
        		);  
           }
            
        
                
        /*// Send e-mail notification (if user is not writing on own board)
		if ( $user_id_from != $user_id_to ) {
			$this->sendBoardNotificationEmail( $user_id_to, $user_name_from );
			$this->incNewMessageCount( $user_id_to );
		}

		$stats = new UserStatsTrack( $user_id_to, $user_name_to );
		if ( $message_type == 0 ) {
			// public message count
			$stats->incStatField( 'user_board_count' );
		} else {
			// private message count
			$stats->incStatField( 'user_board_count_priv' );
		}

		$stats = new UserStatsTrack( $user_id_from, $user_name_from );
		$stats->incStatField( 'user_board_sent' ); */
              
    }
    
    public function SendWallPostCommentUnLike( $uwc_id, $user_id ){
       if ( $uwc_id ) {
          $dbw = wfGetDB( DB_MASTER );
          
            $s = $dbw->selectRow(
				'user_likes',
				array( 'ul_uwc_id', 'ul_user_id', 'ul_user_name', 'ul_like_state' ),
				array( 'ul_uwc_id' => $uwc_id, 'ul_user_id' => $user_id, 'ul_like_state' => 1 ),
				__METHOD__
			);
            if ($s !== false ){
                $dbw->update(
					'user_likes',
                    array( 'ul_like_state' => 0 ),
					array( 'ul_uwc_id' => $uwc_id, 'ul_user_id' => $user_id, 'ul_like_state' => 1 ),
					__METHOD__
				);
            }
       }	       
    }
    
    public function checkWallCommentLike( $uwc_id, $user_id ){
        global $wgUser;
        
        if($uwc_id){
            $dbw = wfGetDB( DB_MASTER );
            $s = $dbw->selectRow(
				'user_likes',
				array( 'ul_uwc_id', 'ul_user_id', 'ul_user_name', 'ul_like_state' ),
				array( 'ul_uwc_id' => $uwc_id, 'ul_user_id' => $user_id, 'ul_like_state' => 1 ),
				__METHOD__
			);
                        
			if ($s !== false )
             return false;
            else
             return true;				
       }      
    }
    
    public function displayWallPostComments($user_name, $message_id, $count = 10, $page = 0){
        global $wgUser, $wgTitle, $wgStylePath;

		$output = ''; // Prevent E_NOTICE
        
        $comments = $this->getUserWallPostComments( $message_id, $count, $page );
        
        $comment_user = Title::makeTitle( NS_USER, $wgUser->getName() );
        $avatar_comment = new wAvatar( $wgUser->getID(), 'm' );
        
        if ( $comments ) {		  
    		     foreach ( $comments as $comment ) {
    		        $user = Title::makeTitle( NS_USER, $comment['user_name_from'] );
    				$avatar = new wAvatar( $comment['user_id_from'], 'm' );
                    
                    $close_edit_link ='';                                        
                    $like_link = '';
                    $unlike_link = '';
                    $comment_time = '';
                    $like_count = '';
                    
                  $comment_text =   $comment['comment_text'];
                   
                  //$user_name
                  if ( $wgUser->getName() == $comment['user_name_from'] ) {
                   $close_edit_link .="<span id=\"wall_comment_delete\" onclick=\"delete_comment('{$comment['comment_id']}','{$message_id}');\"><img src=\"{$wgStylePath}/common/images/remove.png\" border=\"0\" title=\"Delete this comment\"></span>
                                       <span id=\"wall_comment_edit\" onclick=\"edit_comment('{$comment['comment_id']}','{$message_id}');\"><img src=\"{$wgStylePath}/common/images/edit.png\" border=\"0\" title=\"Edit this comment\"></span>";
                  } else {                   
                   $close_edit_link .="";
                   //<span id=\"wall_comment_delete\" onclick=\"delete_comment('{$comment['comment_id']}','{$message_id}');\"><img src=\"{$wgStylePath}/common/images/remove.png\" border=\"0\" title=\"Delete this comment\"></span>  
                  }
                 
                  $comment_time .="<span style=\"color:#949494\">" .
                                    wfMsgHtml( 'userwall_comment_posted_ago', $this->getTimeAgo( $comment['timestamp'] ) ) .
                                   "</span>";
                  
                  $like_link .='<span class="user-wall-message-links" id="wall-comment-links-like-'.$comment['comment_id'].'" title="Like this comment">
                       <a href="javascript:void(0);" onclick="javascript:like_post_comment('.$comment['wall_message_id'].', '.$comment['comment_id'].');">'.
    								wfMsgHtml( 'user_wall_like' ) . '</a>
                       </span>';
                    
                  $unlike_link .='<span class="user-wall-message-links" id="wall-comment-links-unlike-'.$comment['comment_id'].'" title="Unlike this comment">
                       <a href="javascript:void(0);" onclick="javascript:unlike_post_comment('.$comment['wall_message_id'].', '.$comment['comment_id'].');">'.
    								wfMsgHtml( 'user_wall_unlike' ) . '</a>
                       </span>';
                  
                  /////////////////// Like Count /////////////////////////////////
                    $lk_count = $this->getWallPostCommentLikeCount( $comment['comment_id'] );
                    if( $lk_count > 0 ){
                        $like_count .='<span class="wall-comment-like-count">' . $lk_count . '</span>';   
                       }  
                                
                    /////////////////// Like Count ///////////////////////////////////     
                                     
                  
                  $output .='<div class="user-wall-comment-block" id="user-wall-comment-block-' .$comment['comment_id']. '">';
                  
                   $output .="<div class=\"user-wall-comment-image\">
                                 <a href=\"{$user->escapeFullURL()}\" title=\"{$comment['user_name_from']}\">{$avatar->getAvatarURL()}</a>
                                 </div>";
                   $output .="<div class=\"user-wall-comment-body\">
                               <span class=\"user-wall-comment-user\"><strong>{$comment['user_name_from']}</strong></span><span class=\"user-wall-comment-txt\">{$comment_text}</span>
                              </div>";                                           
                   $output .="<div class=\"user-wall-comment-links\">
                              {$comment_time}
                              ";
                    
                    if($this->checkWallCommentLike( $comment['comment_id'], $wgUser->getID() ))    
                      $output .= "{$like_link}";
                    else
                      $output .= "{$unlike_link}";
                              
                   $output .="
                              {$like_count}
                              {$close_edit_link}
                              </div>";
                                                                                        
                  $output .='</div>';            
    		     }
        }
        
     return $output;           
    }
    
    public function autoDisplayWallPostComments( $user_name, $message_id, $count = 10, $page = 0){
        global $wgUser, $wgTitle, $wgStylePath;

		$output = ''; // Prevent E_NOTICE
        
        $comments = $this->getUserWallPostNewComments( $message_id, $count, $page );
        
        $comment_user = Title::makeTitle( NS_USER, $wgUser->getName() );
        $avatar_comment = new wAvatar( $wgUser->getID(), 'm' );
        
        if ( $comments ) {		  
    		     foreach ( $comments as $comment ) {
    		        $user = Title::makeTitle( NS_USER, $comment['user_name_from'] );
    				$avatar = new wAvatar( $comment['user_id_from'], 'm' );
                    
                    $close_edit_link ='';                                        
                    $like_link = '';
                    $unlike_link = '';
                    $comment_time = '';
                    $like_count = '';
                    
                  $comment_text =   $comment['comment_text'];
                   
                  //$user_name
                  if ( $wgUser->getName() == $comment['user_name_from'] ) {
                   $close_edit_link .="<span id=\"wall_comment_delete\" onclick=\"delete_comment('{$comment['comment_id']}','{$message_id}');\"><img src=\"{$wgStylePath}/common/images/remove.png\" border=\"0\" title=\"Delete this comment\"></span>
                                       <span id=\"wall_comment_edit\" onclick=\"edit_comment('{$comment['comment_id']}','{$message_id}');\"><img src=\"{$wgStylePath}/common/images/edit.png\" border=\"0\" title=\"Edit this comment\"></span>";
                  } else {                   
                   $close_edit_link .="";
                   //<span id=\"wall_comment_delete\" onclick=\"delete_comment('{$comment['comment_id']}','{$message_id}');\"><img src=\"{$wgStylePath}/common/images/remove.png\" border=\"0\" title=\"Delete this comment\"></span>  
                  }
                 
                  $comment_time .="<span style=\"color:#949494\">" .
                                    wfMsgHtml( 'userwall_comment_posted_ago', $this->getTimeAgo( $comment['timestamp'] ) ) .
                                   "</span>";
                  
                  $like_link .='<span class="user-wall-message-links" id="wall-comment-links-like-'.$comment['comment_id'].'" title="Like this comment">
                       <a href="javascript:void(0);" onclick="javascript:like_post_comment('.$comment['wall_message_id'].', '.$comment['comment_id'].');">'.
    								wfMsgHtml( 'user_wall_like' ) . '</a>
                       </span>';
                    
                  $unlike_link .='<span class="user-wall-message-links" id="wall-comment-links-unlike-'.$comment['comment_id'].'" title="Unlike this comment">
                       <a href="javascript:void(0);" onclick="javascript:unlike_post_comment('.$comment['wall_message_id'].', '.$comment['comment_id'].');">'.
    								wfMsgHtml( 'user_wall_unlike' ) . '</a>
                       </span>';
                  
                  /////////////////// Like Count /////////////////////////////////
                    $lk_count = $this->getWallPostCommentLikeCount( $comment['comment_id'] );
                    if( $lk_count > 0 ){
                        $like_count .='<span class="wall-comment-like-count">' . $lk_count . '</span>';   
                       }  
                                
                    /////////////////// Like Count ///////////////////////////////////     
                                     
                  
                  $output .='<div class="user-wall-comment-block" id="user-wall-comment-block-' .$comment['comment_id']. '">';
                  
                   $output .="<div class=\"user-wall-comment-image\">
                                 <a href=\"{$user->escapeFullURL()}\" title=\"{$comment['user_name_from']}\">{$avatar->getAvatarURL()}</a>
                                 </div>";
                   $output .="<div class=\"user-wall-comment-body\">
                               <span class=\"user-wall-comment-user\"><strong>{$comment['user_name_from']}</strong></span><span class=\"user-wall-comment-txt\">{$comment_text}</span>
                              </div>";                                           
                   $output .="<div class=\"user-wall-comment-links\">
                              {$comment_time}
                              ";
                    
                    if($this->checkWallCommentLike( $comment['comment_id'], $wgUser->getID() ))    
                      $output .= "{$like_link}";
                    else
                      $output .= "{$unlike_link}";
                              
                   $output .="
                              {$like_count}
                              {$close_edit_link}
                              </div>";
                                                                                        
                  $output .='</div>';            
    		     }
        }
        
     return $output;           
    }
    
	/**
	 * Get the escaped full URL to Special:SendBoardBlast.
	 * This is just a silly wrapper function.
	 *
	 * @return String: escaped full URL to Special:SendBoardBlast
	 */
	static function getBoardBlastURL() {
		$title = SpecialPage::getTitleFor( 'SendBoardBlast' );
		return $title->escapeFullURL();
	}
    
    //Mathi
    static function getWallBlastURL() {
		$title = SpecialPage::getTitleFor( 'SendBoardBlast' );
		//$user_name = str_replace( '&', '%26', $user_name );
		return $title->escapeFullURL( 'is_wall=1' );
	}

	/**
	 * Get the user board URL for $user_name.
	 *
	 * @param $user_name Mixed: name of the user whose user board URL we're
	 *							going to get.
	 * @return String: escaped full URL to the user board page
	 */
	static function getUserBoardURL( $user_name ) {
		$title = SpecialPage::getTitleFor( 'UserBoard' );
		$user_name = str_replace( '&', '%26', $user_name );
		return $title->escapeFullURL( 'user=' . $user_name );
	}
    
    static function getUserWallURL( $user_name ) {
		$title = SpecialPage::getTitleFor( 'UserBoard' );
		$user_name = str_replace( '&', '%26', $user_name );
		return $title->escapeFullURL( 'user=' . $user_name ).'&is_wall=1';
	}

	/**
	 * Get the board-to-board URL for the users $user_name_1 and $user_name_2.
	 *
	 * @param $user_name_1 Mixed: name of the first user
	 * @param $user_name_2 Mixed: name of the second user
	 * @return String: escaped full URL to the board-to-board conversation
	 */
	static function getUserBoardToBoardURL( $user_name_1, $user_name_2 ) {
		$title = SpecialPage::getTitleFor( 'UserBoard' );
		$user_name_1 = str_replace( '&', '%26', $user_name_1 );
		$user_name_2 = str_replace( '&', '%26', $user_name_2 );
		return $title->escapeFullURL( 'user=' . $user_name_1 . '&conv=' . $user_name_2 );
	}

	/**
	 * Gets the difference between two given dates
	 *
	 * @param $dt1 Mixed: current time, as returned by PHP's time() function
	 * @param $dt2 Mixed: date
	 * @return Difference between dates
	 */
	public function dateDiff( $date1, $date2 ) {
		$dtDiff = $date1 - $date2;

		$totalDays = intval( $dtDiff / ( 24 * 60 * 60 ) );
		$totalSecs = $dtDiff - ( $totalDays * 24 * 60 * 60 );
		$dif['w'] = intval( $totalDays / 7 );
		$dif['d'] = $totalDays;
		$dif['h'] = $h = intval( $totalSecs / ( 60 * 60 ) );
		$dif['m'] = $m = intval( ( $totalSecs - ( $h * 60 * 60 ) ) / 60 );
		$dif['s'] = $totalSecs - ( $h * 60 * 60 ) - ( $m * 60 );

		return $dif;
	}

	public function getTimeOffset( $time, $timeabrv, $timename ) {
		$timeStr = '';
		if ( $time[$timeabrv] > 0 ) {
			$timeStr = wfMsgExt( "userboard-time-{$timename}", 'parsemag', $time[$timeabrv] );
		}
		if ( $timeStr ) {
			$timeStr .= ' ';
		}
		return $timeStr;
	}

	/**
	 * Gets the time how long ago the given board message was posted
	 *
	 * @param $time
	 * @return $timeStr Mixed: time, such as "20 days" or "11 hours"
	 */
	public function getTimeAgo( $time ) {
		$timeArray = $this->dateDiff( time(), $time );
		$timeStr = '';
		$timeStrD = $this->getTimeOffset( $timeArray, 'd', 'days' );
		$timeStrH = $this->getTimeOffset( $timeArray, 'h', 'hours' );
		$timeStrM = $this->getTimeOffset( $timeArray, 'm', 'minutes' );
		$timeStrS = $this->getTimeOffset( $timeArray, 's', 'seconds' );
		$timeStr = $timeStrD;
		if ( $timeStr < 2 ) {
			$timeStr .= $timeStrH;
			$timeStr .= $timeStrM;
			if ( !$timeStr ) {
				$timeStr .= $timeStrS;
			}
		}
		if ( !$timeStr ) {
			$timeStr = wfMsgExt( 'userboard-time-seconds', 'parsemag', 1 );
		}
		return $timeStr;
	}
}
