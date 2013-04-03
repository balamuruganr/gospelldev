<?php

/**
 * @author 
 * @copyright 2013
 * include file for SpecialUploadAvatar.php
 */

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    $x1 = $_REQUEST['x1'];
    $y1 = $_REQUEST['y1'];
    $user_id = $this->getUser()->getId();
    $l_path = $wgUploadDirectory.'/avatars/'.$wgDBname.'_'.$user_id.'_l'.'.jpg';
    $lm_path = $wgUploadDirectory.'/avatars/'.$wgDBname.'_'.$user_id.'_ml'.'.jpg';
    $m_path = $wgUploadDirectory.'/avatars/'.$wgDBname.'_'.$user_id.'_m'.'.jpg';
    $s_path = $wgUploadDirectory.'/avatars/'.$wgDBname.'_'.$user_id.'_s'.'.jpg';
    
    if(gospellCommonFunctions::cropUserAvatar($user_id,160,160,$x1,$y1,160,160,$l_path)) {
        gospellCommonFunctions::cropUserAvatar($user_id,160,160,$x1,$y1,50,50,$lm_path);
        gospellCommonFunctions::cropUserAvatar($user_id,160,160,$x1,$y1,30,30,$m_path);
        gospellCommonFunctions::cropUserAvatar($user_id,160,160,$x1,$y1,16,16,$s_path);                                            
        @unlink($wgUploadDirectory . '/temp/usr_tmp_avatar_'.$user_id.'.jpg'); //delete the main temporary uploaded file
        echo '1||'.$wgUploadPath.'/avatars/'.$wgDBname.'_'.$user_id.'_';
        die();
    }
    echo '0||';
    die();        
}

if( $_REQUEST['gospell_include_var'] == 'specialuploadavatar_showsuccess') {
    $temp_avatar_dir = $wgUploadPath.'/temp';
    $user = $this->getUser();    
    if ( is_file( $wgUploadDirectory . '/temp/' . 'usr_tmp_avatar_' . $user->getId() . '.jpg' ) ) {
        $tmp_usr_img_path = $temp_avatar_dir . '/' . 'usr_tmp_avatar_' . $user->getId() . '.jpg';  
        $output .= '<div id="img_target_container"><img src="' . $tmp_usr_img_path.'?ts=' . rand() . '" alt="" border="0" id="target" /></div>';
    
        $output .= '<form id="user_avatar_upload" method="post" enctype="multipart/form-data" action="">';
        $output .= '<input type="hidden" id="x1" name="x1" />';
        $output .= '<input type="hidden" id="y1" name="y1" />';
        $output .= '<input type="hidden" id="x2" name="x2" />';
        $output .= '<input type="hidden" id="y2" name="y2" />';
        $output .= '<input type="button" id="user_avatar_sub" name="user_avatar_sub" value="submit" />';
        $output .= '</form>';
    }
}


?>
