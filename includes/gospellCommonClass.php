<?php
/**
 * @author 
 * @copyright 2013
 * gospell common functions
 * 
 */

class gospellCommonFunctions {
     /*
     * Create random text generation 	 	 
     * @param $length int
     */        
    public static function generateRandomString($length = 5) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzgospell';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
    
    public static function get_virtual_img($mime,$temp_file){
    	$vir_img_arr = array();
    	switch($mime) {
    		case 'image/jpg':
    			$ext = '.jpg';
    			// create a new image from file
    			$vir_img = @imagecreatefromjpeg($temp_file);
    			break;
    			
    		case 'image/jpeg':
    			$ext = '.jpeg';
    			// create a new image from file
    			$vir_img = @imagecreatefromjpeg($temp_file);
    			break;
    			
    		case 'image/png':
    			$ext = '.png';
    			// create a new image from file
    			$vir_img = @imagecreatefrompng($temp_file);
    			break;
    			
    		case 'image/gif':
    			$ext = '.gif';
    			// create a new image from file
    			$vir_img = @imagecreatefromgif($temp_file);
    			break;
    		default:
    			@unlink($temp_file);
    			return;
    	}
     $vir_img_arr['ext'] = $ext;
     $vir_img_arr['img'] = $vir_img;
     return $vir_img_arr;
    }

    public static function cropUserAvatar($user_id,$w,$h,$x1,$y1,$desired_width=160,$desired_height=160,$result) { 
        global $wgUploadDirectory;
            
        $upload_dir = $wgUploadDirectory . '/temp';                    
        $temp_file = $upload_dir. '/usr_tmp_avatar_'. $user_id. '.jpg';                        
        //Again check if the file was uploaded properly without any error
        if (file_exists($temp_file) && filesize($temp_file) > 0) {                               
            $file_size_arr = getimagesize($temp_file); // get the image detail
            if (!$file_size_arr) {
                @unlink($temp_file); //if file size array not exits then delete it
                return;
            }
			$mime = $file_size_arr['mime'];
            $virtual_img_arr = gospellCommonFunctions::get_virtual_img($mime,$temp_file);
			$virtual_img = $virtual_img_arr['img'];
			$virtual_img_ext = $virtual_img_arr['ext'];
            // create a new true color image
            $true_color_img = @imagecreatetruecolor( $desired_width, $desired_height );
            // copy and resize part of an image with resampling
            imagecopyresampled($true_color_img, $virtual_img, 0, 0, (int)$x1, (int)$y1, $desired_width, $desired_height, (int)$w, (int)$h);                
            // upload resultant file to the folder
            if(imagejpeg($true_color_img, $result, 100)) {                    
                return true;                    
            }
            return false;
        }
        
    }        

    public static function uploadUserAvatarToTemp($user_id) {
        global $wgUploadDirectory;
        
        $upload_dir = $wgUploadDirectory . '/temp';
        if ($_FILES) {                                
            $file = $_FILES['wpUploadFile'];
            //20971520 -. 20 mb in bytes;        
            if (! $file['error'] && $file['size'] < 20971520) {                                               
                if (is_uploaded_file($file['tmp_name'])) {                        
                    $temp_file = $upload_dir. '/usr_tmp_avatar_'. $user_id. '.jpg';
                    if(move_uploaded_file($file['tmp_name'], $temp_file)){
                        return true;
                    }else {
                        return false;
                    }  
                }
            }
            else {
                echo 'image size exceeds maximum allowable size.Please upload reduce its length';
            }
        }        
    }


}

?>