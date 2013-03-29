<?php
/*
 * Copyright © 2010-2012 Garrett Brown <http://www.mediawiki.org/wiki/User:Gbruin>
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along
 * with this program. If not, see <http://www.gnu.org/licenses/>.
 */


/*
 * Not a valid entry point, skip unless MEDIAWIKI is defined.
 */
if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}


/**
 * Class FacebookDB
 * 
 * This class abstracts the manipulation of the custom table used by this
 * extension. If $wgDBprefix is set, this class will pull from the translated
 * tables. If the table 'users_fbconnect' does not exist in your database
 * you will receive errors like this:
 * 
 * Database error from within function "FacebookDB::getUser". Database
 * returned error "Table 'user_fbconnect' doesn't exist".
 * 
 * In this case, you will need to fix this by running the MW updater:
 * >php maintenance/update.php
 */
class FacebookDB {    
	/**
	 * Find the Facebook IDs of the given user, if any, using the database
	 * connection provided.
	 * 
	 * If $user is not specified, the ID of the logged in user will be used.
	 */
	public static function getFacebookIDs( $user = NULL, $db = DB_SLAVE  ) {
		global $wgMemc;
		// Connect to the database
		$dbr = wfGetDB( $db, array(), self::sharedDB() );
		$fbid = array();
		if ( empty( $user) || !($user instanceof User) || $user->getId() == 0 ) {
			global $wgUser;
			$user = $wgUser;
		}
		if ( $user->getId() != 0 ) {
			// Try memcached to avoid hitting the database
			$memkey = wfMemcKey( 'fb_user_id', $user->getId() );
			$val = $wgMemc->get( $memkey );
			if ( ( is_array( $val ) ) &&  ( $db == DB_SLAVE ) ){
				return $val;
			}
			// Query the database
			$prefix = self::getPrefix();
			$res = $dbr->select(
				array( "{$prefix}user_fbconnect" ),
				array( 'user_fbid' ),
				array( 'user_id' => $user->getId() ),
				__METHOD__
			);
			// $res might be null if the table user_fbconnect wasn't created
			if ( $res ) {
				foreach( $res as $row ) {
					$fbid[] = $row->user_fbid;
				}
				$res->free();
				$wgMemc->set( $memkey, $fbid );
			}
			
		}
		return $fbid;
	}
	
	/**
	 * Find the user by their Facebook ID.
	 * If there is no user found for the given id, returns null.
	 */
	public static function getUser( $fbid ) {
		$prefix = self::getPrefix();
		
		// NOTE: Do not just pass this dbr into getUserByDB since that function prevents
		// rewriting of the database name for shared tables.
		$dbr = wfGetDB( DB_SLAVE, array(), self::sharedDB() );
		
		$id = $dbr->selectField(
			array( "{$prefix}user_fbconnect" ),
			array( 'user_id' ),
			array( 'user_fbid' => $fbid ),
			__METHOD__
		);
		if ( $id ) {
			/* Wikia change - begin */
			global $wgExternalAuthType;
			
			$user = User::newFromId( $id );
			if ( $wgExternalAuthType ) {
				$user->load();
				if ( $user->getId() == 0 ) {
					$mExtUser = ExternalUser::newFromId( $id );
					if ( is_object( $mExtUser ) && ( $mExtUser->getId() != 0 ) ) {
						$mExtUser->linkToLocal( $mExtUser->getId() );
						$user->setId( $id );
					}
				}
			}
			
			return $user;
			/* Wikia change - end */
		} else {
			return null;
		}
	}
	
	/**
	 * Given a facebook id and database connection with read permission,
	 * finds the Facebook user by their id.
	 * If there is no user found for the given id, returns null.
	 */
	public static function getUserByDB( $fbid, $dbr ){
		$prefix = self::getPrefix();
		$id = $dbr->selectField(
			"`{$prefix}user_fbconnect`",
			'user_id',
			array( 'user_fbid' => $fbid ),
			__METHOD__
		);
		if ( $id ) {
			return User::newFromId( $id );
		} else {
			return null;
		}
	}
	
	/**
	 * Add a User <-> Facebook ID association to the database.
     * $fb_userinfo it has facebook information details edited by gospelldev rajaraman
	 */
	public static function addFacebookID( $user, $fbid, $fb_userinfo = array() ) {
		global $wgMemc,$wgUploadDirectory,$wgDBname;
		wfProfileIn( __METHOD__ );
		
		$memkey = wfMemcKey( 'fb_user_id', $user->getId() );
			
		if ( $user->getId() == 0 ) {
			wfDebug("Facebook: tried to store a mapping from fbid \"$fbid\" to a user with no id (ie: not logged in).\n");
		} else {
			$prefix = self::getPrefix();
			$dbw = wfGetDB( DB_MASTER, array(), self::sharedDB() );
			$dbw->insert(
				"{$prefix}user_fbconnect",
				array(
					'user_id' => $user->getId(),
					'user_fbid' => $fbid
				),
				__METHOD__,
				array( 'IGNORE' )
			);
			$dbw->commit();
            //gospelldev
            if(!empty($fb_userinfo)) {
                if($fb_userinfo['birthday']) {                    
                    $expl_bday = explode('/',$fb_userinfo['birthday']); 
                    $new_bday = $expl_bday[2].'-'.$expl_bday[0].'-'.$expl_bday[1];                    
    				$dbw->insert(
    					"{$prefix}user_profile",
    					array(
    						'up_user_id' => $user->getId(),
    						'up_birthday' => $new_bday
    					),
    					__METHOD__
    				);
                }
            
                $fb_profile_img_url = self::getFacebookProfliePicture("http://graph.facebook.com/$fbid/picture?width=160&height=160");
                $temp_file = $fbid.'.jpg';
                $temp_path = $wgUploadDirectory.'/temp/'.$temp_file;
                file_put_contents($temp_path, file_get_contents($fb_profile_img_url));

    			if ( is_file( $temp_path ) ) {				
            		$imageInfo = getimagesize( $temp_path );
            		switch ( $imageInfo[2] ) {
            			case 1:
            				$ext = 'gif';
            				break;
            			case 2:
            				$ext = 'jpg';
            				break;
            			case 3:
            				$ext = 'png';
            				break;
            			default:
            				break;
            		}                    
                    self::createThumbnail( $temp_path, $imageInfo, $wgDBname . '_' . $user->getId() . '_l', 75 );    
                    self::createThumbnail( $temp_path, $imageInfo, $wgDBname . '_' . $user->getId() . '_ml', 50 );
                    self::createThumbnail( $temp_path, $imageInfo, $wgDBname . '_' . $user->getId() . '_m', 30 );
                    self::createThumbnail( $temp_path, $imageInfo, $wgDBname . '_' . $user->getId() . '_s', 16 ); 
                    unlink( $temp_path ); //delete temp file                                  
    			}
                                                
            }
            //gospelldev
		}
		
		$wgMemc->set( $memkey, self::getFacebookIDs( $user, DB_MASTER ) );
		
		wfProfileOut( __METHOD__ );
	}
	
	/**
	 * Remove a User <-> Facebook ID association from the database.
	 */
	public static function removeFacebookID( $user ) {
		global $wgMemc; 
		$prefix = self::getPrefix();
		if ( $user instanceof User && $user->getId() != 0 ) {
			$dbw = wfGetDB( DB_MASTER, array(), self::sharedDB() );
			$memkey = wfMemcKey( 'fb_user_id', $user->getId() );
			$dbw->delete(
				"{$prefix}user_fbconnect",
				array(
					'user_id' => $user->getId(),
				),
				__METHOD__
			); 
			$dbw->commit();
	 		$wgMemc->set( $memkey, self::getFacebookIDs( $user, DB_MASTER ) );
	 		return (bool) $dbw->affectedRows();
		}
		return 0;
	}
	
	/**
	 * Estimates the total number of User <-> Facebook ID associations in the
	 * database. If there are no users, then the estimate will probably be 1.
	 */
	public static function countUsers() {
		$prefix = self::getPrefix();
		$dbr = wfGetDB( DB_SLAVE, array(), self::sharedDB() );
		// An estimate is good enough for choosing a unique nickname
		$count = $dbr->estimateRowCount( "{$prefix}user_fbconnect" );
		// Avoid returning 0 or -1
		return $count >= 1 ? $count : 1;
	}
	
	/**
	 * Returns the name of the shared database, if one is in use for the Facebook
	 * Connect users table. Note that 'user_fbconnect' (without respecting
	 * $wgSharedPrefix) is added to $wgSharedTables in FacebookInit::init() by
	 * default. This function can also be used as a test for whether a shared
	 * database for Facebook users is in use.
	 * 
	 * See also <http://www.mediawiki.org/wiki/Manual:Shared_database>
	 */
	public static function sharedDB() {
		global $wgExternalSharedDB;
		if ( !empty( $wgExternalSharedDB ) ) {
			return $wgExternalSharedDB;
		}
		return false;
	}
	
	/**
	 * Returns the table prefix name, either $wgDBprefix, $wgSharedPrefix
	 * depending on whether a shared database is in use.
	 */
	private static function getPrefix() {
		global $wgDBprefix, $wgSharedPrefix;
		return self::sharedDB() ? $wgSharedPrefix : ""; // bugfix for $wgDBprefix;
	}
    /**
     * Getting facebook profile picture url by curl gospelldev
    */
    public static function getFacebookProfliePicture($url) {
        $ch = curl_init();        
        curl_setopt($ch, CURLOPT_URL, $url);        
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch);        
        $url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);        
        curl_close($ch);
        return $url;        
    }    
	public static function createThumbnail( $imageSrc, $imageInfo, $imgDest, $thumbWidth ) {
		global $wgUseImageMagick, $wgImageMagickConvertCommand, $wgUploadDirectory;        
        $avatar_upload_dir = $wgUploadDirectory.'/avatars';
        
		if ( $wgUseImageMagick ) { // ImageMagick is enabled
			list( $origWidth, $origHeight, $typeCode ) = $imageInfo;

			if ( $origWidth < $thumbWidth ) {
				$thumbWidth = $origWidth;
			}
			$thumbHeight = ( $thumbWidth * $origHeight / $origWidth );
			$border = ' -bordercolor white  -border  0x';
			if ( $thumbHeight < $thumbWidth ) {
				$border = ' -bordercolor white  -border  0x' . ( ( $thumbWidth - $thumbHeight ) / 2 );
			}
			if ( $typeCode == 2 ) {
				exec(
					$wgImageMagickConvertCommand . ' -size ' . $thumbWidth . 'x' . $thumbWidth .
					' -resize ' . $thumbWidth . ' -crop ' . $thumbWidth . 'x' .
					$thumbWidth . '+0+0   -quality 100 ' . $border . ' ' .
					$imageSrc . ' ' . $avatar_upload_dir . '/' . $imgDest . '.jpg'
				);
			}
			if ( $typeCode == 1 ) {
				exec(
					$wgImageMagickConvertCommand . ' -size ' . $thumbWidth . 'x' . $thumbWidth .
					' -resize ' . $thumbWidth . ' -crop ' . $thumbWidth . 'x' .
					$thumbWidth . '+0+0 ' . $imageSrc . ' ' . $border . ' ' .
					$avatar_upload_dir . '/' . $imgDest . '.gif'
				);
			}
			if ( $typeCode == 3 ) {
				exec(
					$wgImageMagickConvertCommand . ' -size ' . $thumbWidth . 'x' . $thumbWidth .
					' -resize ' . $thumbWidth . ' -crop ' . $thumbWidth . 'x' .
					$thumbWidth . '+0+0 ' . $imageSrc . ' ' .
					$avatar_upload_dir . '/' . $imgDest . '.png'
				);
			}
		} else { // ImageMagick is not enabled, so fall back to PHP's GD library
			// Get the image size, used in calculations later.
			list( $origWidth, $origHeight, $typeCode ) = getimagesize( $imageSrc );

			switch( $typeCode ) {
				case '1':
					$fullImage = imagecreatefromgif( $imageSrc );
					$ext = 'gif';
					break;
				case '2':
					$fullImage = imagecreatefromjpeg( $imageSrc );
					$ext = 'jpg';
					break;
				case '3':
					$fullImage = imagecreatefrompng( $imageSrc );
					$ext = 'png';
					break;
			}

			$scale = ( $thumbWidth / $origWidth );

			// Create our thumbnail size, so we can resize to this, and save it.
			$tnImage = imagecreatetruecolor(
				$origWidth * $scale,
				$origHeight * $scale
			);

			// Resize the image.
			imagecopyresampled(
				$tnImage,
				$fullImage,
				0, 0, 0, 0,
				$origWidth * $scale,
				$origHeight * $scale,
				$origWidth,
				$origHeight
			);

			// Create a new image thumbnail.
			if ( $typeCode == 1 ) {
				imagegif( $tnImage, $imageSrc );
			} elseif ( $typeCode == 2 ) {
				imagejpeg( $tnImage, $imageSrc );
			} elseif ( $typeCode == 3 ) {
				imagepng( $tnImage, $imageSrc );
			}

			// Clean up.
			imagedestroy( $fullImage );
			imagedestroy( $tnImage );

			// Copy the thumb
			copy(
				$imageSrc,
				$avatar_upload_dir . '/' . $imgDest . '.' . $ext
			);
		}
	}        
}
