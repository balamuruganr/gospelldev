<?php
/**
 * A special page for uploading cover photo
 * Requirements: Need writable directory $wgUploadPath/cover_photos
 *
 */
 
global $IP;
require_once("$IP/includes/gospellCommonClass.php"); 
class SpecialUploadCoverPhoto extends SpecialUpload {
	var $avatarUploadDirectory;

	/**
	 * Constructor
	 */
	public function __construct( $request = null ) {
		SpecialPage::__construct( 'UploadCoverPhoto', 'upload', false/* listed? */ );
	}

	/**
	 * Let the parent handle most of the request, but specify the Upload
	 * class ourselves
	 */
	protected function loadRequest() {
		$request = $this->getRequest();
		parent::loadRequest( $request );
		$this->mUpload = new UploadCoverPhoto();
		$this->mUpload->initializeFromRequest( $request );
	}

	/**
	 * Show the special page. Let the parent handle most stuff, but handle a
	 * successful upload ourselves
	 *
	 * @param $params Mixed: parameter(s) passed to the page or null
	 */
	public function execute( $params ) {
		global $wgUserProfileScripts,$wgUploadDirectory,$wgDBname,$wgUploadPath;

        //ajax part
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $x1 = $_REQUEST['x1'];
            $y1 = $_REQUEST['y1'];
            $h = $_REQUEST['h'];
            $w = $_REQUEST['w'];
            $user_id = $this->getUser()->getId();
            $c_path = $wgUploadDirectory.'/cover_photos/'.$wgDBname.'_cover_'.$user_id.'.jpg';
            
            if(gospellCommonFunctions::cropUserAvatar($user_id,$w,$h,$x1,$y1,850,315,$c_path,$image_for = 'cover')) {
                @unlink($wgUploadDirectory . '/temp/usr_tmp_cover_photos_'.$user_id.'.jpg'); 
                echo '1||'.$wgUploadPath.'/cover_photos/'.$wgDBname.'_cover_'.$user_id.'.jpg';
                die();
            }
            echo '0||';
            die();        
        }
        //ajax part        
		$out = $this->getOutput();
        
		$out->addExtensionStyle( $wgUserProfileScripts . '/UserProfile.css' );
        $out->addExtensionStyle( $wgUserProfileScripts . '/jquery.Jcrop.min.css' );
        $out->addScriptFile( $wgUserProfileScripts . '/jquery.Jcrop.min.js' );
        $out->addScriptFile( $wgUserProfileScripts . '/userCoverPhotoCrop.js' );
        
		parent::execute( $params );

		if ( $this->mUploadSuccessful ) {
			// Cancel redirect
			$out->redirect( '' );

			$this->showSuccess( $this->mUpload->mExtension );
			// Run a hook on avatar change
			wfRunHooks( 'NewAvatarUploaded', array( $this->getUser() ) );
		}
	}

	/**
	 * Show some text and linkage on successful upload.
	 *
	 * @param $ext String: file extension (gif, jpg or png)
	 */
	private function showSuccess( $ext ) {
		global $wgDBname, $wgUploadPath, $wgUploadAvatarInRecentChanges,$wgUploadDirectory,$IP;

		$user = $this->getUser();
		$log = new LogPage( 'avatar' );
		if ( !$wgUploadAvatarInRecentChanges ) {
			$log->updateRecentChanges = false;
		}
		$log->addEntry(
			'avatar',
			$user->getUserPage(),
			wfMsgForContent( 'user-profile-picture-log-entry' )
		);

		$uid = $user->getId();

		$output = '<h1>' . wfMsg( 'uploadavatar' ) . '</h1>';
		$output .= UserProfile::getEditProfileNav( wfMsg( 'user-profile-section-picture' ) );
		$output .= '<div class="profile-info">';
		$output .= '<p class="profile-update-title"> Your Cover Photo </p>';		
		$output .= '<table cellspacing="0" cellpadding="0" style="margin-top:20px;">';
		$output .= '<tr>'. 
			'<td style="padding-bottom:20px;" colspan=2>
				<img src="' . $wgUploadPath . '/cover_photos/' . $wgDBname . '_cover_' . $uid . '.jpg' . '?ts=' . rand() . '" alt="" border="0" id="user_cover_photo" />
			</td>
		</tr>';
		$output .= '<tr>
			<td>
				<input type="button" onclick="javascript:history.go(-1)" class="site-button" value="' . wfMsg( 'user-profile-picture-uploaddifferent' ) . '" />
			</td>
		</tr>';
		$output .= '</table>';                
		$output .= '</div>';
  
                 
        if ( is_file( $wgUploadDirectory . '/temp/' . 'usr_tmp_cover_photos_' . $this->getUser()->getId() . '.jpg' ) ) {
            $temp_avatar_dir = $wgUploadPath.'/temp';  
            $tmp_usr_img_path = $temp_avatar_dir . '/' . 'usr_tmp_cover_photos_' . $this->getUser()->getId() . '.jpg';  
            $output .= '<div id="img_cover_target_container"><img src="' . $tmp_usr_img_path.'?ts=' . rand() . '" alt="" border="0" id="target" /></div>';
        
            $output .= '<form id="user_avatar_upload" method="post" enctype="multipart/form-data" action="">';
            $output .= '<input type="hidden" id="x1" name="x1" />';
            $output .= '<input type="hidden" id="y1" name="y1" />';
            $output .= '<input type="hidden" id="x2" name="x2" />';
            $output .= '<input type="hidden" id="y2" name="y2" />';
            $output .= '<input type="hidden" id="w" name="h" />';
            $output .= '<input type="hidden" id="h" name="h" />';            
            $output .= '<input type="button" id="user_avatar_sub" name="user_avatar_sub" value="submit" />';
            $output .= '</form>';
        }
        
		$this->getOutput()->addHTML( $output );
	}

	/**
	 * Displays the main upload form, optionally with a highlighted
	 * error message up at the top.
	 *
	 * @param $msg String: error message as HTML
	 * @param $sessionKey String: session key in case this is a stashed upload
	 * @param $hideIgnoreWarning Boolean: whether to hide "ignore warning" check box
	 * @return HTML output
	 */
	protected function getUploadForm( $message = '', $sessionKey = '', $hideIgnoreWarning = false ) {
		global $wgUseCopyrightUpload,$wgServer,$wgScriptPath;

		if ( $message != '' ) {
			$sub = wfMsg( 'uploaderror' );
			$this->getOutput()->addHTML( "<h2>{$sub}</h2>\n" .
				"<h4 class='error'>{$message}</h4>\n" );
		}

		$ulb = wfMsg( 'uploadbtn' );

		$source = null;

		if ( $wgUseCopyrightUpload ) {
			$source = "
				<td align='right' nowrap='nowrap'>" . wfMsg( 'filestatus' ) . ":</td>
				<td><input tabindex='3' type='text' name=\"wpUploadCopyStatus\" value=\"" .
				htmlspecialchars( $this->mUploadCopyStatus ) . "\" size='40' /></td>
				</tr><tr>
				<td align='right'>" . wfMsg( 'filesource' ) . ":</td>
				<td><input tabindex='4' type='text' name='wpUploadSource' value=\"" .
				htmlspecialchars( $this->mUploadSource ) . "\" style='width:100px' /></td>
				";
		}

		$output = '<h1>' . wfMsg( 'uploadavatar' ) . '</h1>';
		$output .= UserProfile::getEditProfileNav( wfMsg( 'user-profile-section-picture' ) );
		$output .= '<div class="profile-info">';
		$output .= '<table>
			<tr>
				<td colspan=2>
					<a href="'.SpecialPage::getTitleFor( 'UploadAvatar' )->getFullURL().'">Upload Display picture</a>
				</td>
			</tr>
		</table>';
		if ( $this->getCoverPhoto() != '' ) {
			$output .= '<table>
				<tr>
					<td>
						<p class="profile-update-title">' . wfMsg( 'user-profile-picture-currentimage' ) . '</p>
					</td>
				</tr>';
				$output .= '<tr>
					<td>' . $this->getCoverPhoto() . '</td>
				</tr>
			</table>';
		}

		$output .= '<form id="upload" method="post" enctype="multipart/form-data" action="">';
		// The following two lines are delicious copypasta from HTMLForm.php,
		// function getHiddenFields() and they are required; wpEditToken is, as
		// of MediaWiki 1.19, checked _unconditionally_ in
		// SpecialUpload::loadRequest() and having the hidden title doesn't
		// hurt either
		// @see https://bugzilla.wikimedia.org/show_bug.cgi?id=30953
		$output .= Html::hidden( 'wpEditToken', $this->getUser()->getEditToken(), array( 'id' => 'wpEditToken' ) ) . "\n";
		$output .= Html::hidden( 'title', $this->getTitle()->getPrefixedText() ) . "\n";                 
		$output .= '<table border="0">
				<tr>
					<td>
						<p class="profile-update-title">Choose Your Cover Photo </p>
						<p style="margin-bottom:10px;">' .
							wfMsg( 'user-profile-picture-picsize' ) .
						'</p>
						<input tabindex="1" type="file" name="wpUploadFile" id="wpUploadFile" size="36"/>
						</td>
				</tr>
				<tr>' . $source . '</tr>
				<tr>
					<td>
						<input tabindex="5" type="submit" name="wpUpload" class="site-button" value="' . $ulb . '" />
					</td>
				</tr>
			</table>
			</form>' . "\n";

		$output .= '</div>';

		return $output;
	}

	/**
	 * Gets an avatar image with the specified size
	 *
	 * @param $size String: size of the image ('s' for small, 'm' for medium,
	 * 'ml' for medium-large and 'l' for large)
	 * @return String: full img HTML tag
	 */
	function getCoverPhoto() {	   
		global $wgDBname, $wgUploadDirectory, $wgUploadPath;                
        $files = glob(
			$wgUploadDirectory . '/cover_photos/' . $wgDBname . '_cover_' . $this->getUser()->getID() . '*'
		);          
		if ( isset( $files[0] ) && $files[0] ) {		  
			return "<img src=\"{$wgUploadPath}/cover_photos/" .
				basename( $files[0] ) . '" alt="" border="0" />';
		}                            		
	}

}

class UploadCoverPhoto extends UploadFromFile {
	public $mExtension;

	/**
	 * Create the thumbnails and delete old files
	 */
	public function performUpload( $comment, $pageText, $watch, $user ) {
		global $wgUploadDirectory, $wgDBname, $wgMemc, $IP;

		$this->avatarUploadDirectory = $wgUploadDirectory . '/cover_photos';

		$imageInfo = getimagesize( $this->mTempPath );                
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
			case 6:
				$ext = 'bmp';
				break;                
			default:
                $first_var = array("$1","$2");
                $second_var  = array('these files','.gif, .jpg, .png, .bmp');                                
				return Status::newFatal( str_replace($first_var, $second_var, wfMsgForContent( 'filetype-banned-type' )) );
		}		
		
        gospellCommonFunctions::uploadUserAvatarToTemp($user->getId(), $allowed_file_size = '2097152', $tmp_img_for='cover');//user avatar move to temp folder

		$this->mExtension = $ext;
		return Status::newGood();
	}

	/**
	 * Don't verify the upload, since it all dangerous stuff is killed by
	 * making thumbnails
	 */
	public function verifyUpload() {
		return array( 'status' => self::OK );
	}

	/**
	 * Only needed for the redirect; needs fixage
	 */
	public function getTitle() {
		return Title::makeTitle( NS_FILE, 'Avatar.jpg' );
	}

	/**
	 * We don't overwrite stuff, so don't care
	 */
	public function checkWarnings() {
		return array();
	}
}
