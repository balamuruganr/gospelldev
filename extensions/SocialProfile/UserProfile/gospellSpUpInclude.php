<?php

/**
 * @author 
 * @copyright 2013
 * include file for SpecialUploadAvatar.php
 */
 
$temp_avatar_dir = $wgUploadPath.'/temp';
$user = $this->getUser();    
if ( is_file( $wgUploadDirectory . '/temp/' . 'usr_tmp_avatar_' . $user->getId() . '.jpg' ) ) {
    $tmp_usr_img_path = $temp_avatar_dir . '/' . 'usr_tmp_avatar_' . $user->getId() . '.jpg';  
    $output .= '<div id="img_target_container"><img src="' . $tmp_usr_img_path.'?ts=' . rand() . '" alt="" border="0" id="target" /></div>';

    $output .= '<form id="user_avatar_upload" method="post" enctype="multipart/form-data" action="">';
    $output .= '<input type="hidden" id="uploadavatar_link" value='.$upload_avatar_link = SpecialPage::getTitleFor( 'UploadAvatar' )->getFullURL().' />';
    $output .= '<input type="hidden" id="x1" name="x1" />';
    $output .= '<input type="hidden" id="y1" name="y1" />';
    $output .= '<input type="hidden" id="x2" name="x2" />';
    $output .= '<input type="hidden" id="y2" name="y2" />';
    $output .= '<input type="button" id="user_avatar_sub" name="user_avatar_sub" value="submit" />';
    $output .= '</form>';
}


?>
