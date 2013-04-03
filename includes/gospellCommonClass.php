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
    		case 'image/x-ms-bmp':
    			$ext = '.bmp';
    			// create a new image from file
    			$vir_img = self::ImageCreateFromBMP($temp_file);
    			break;                
    		default:
    			@unlink($temp_file);
    			return;
    	}
     $vir_img_arr['ext'] = $ext;
     $vir_img_arr['img'] = $vir_img;
     return $vir_img_arr;
    }

    public static function cropUserAvatar($user_id,$w,$h,$x1,$y1,$desired_width=160,$desired_height=160,$result,$image_for = 'avatar') { 
        global $wgUploadDirectory;
                    
        $upload_dir = $wgUploadDirectory . '/temp';    
        if($image_for == 'avatar') {                
            $temp_file = $upload_dir. '/usr_tmp_avatar_'. $user_id. '.jpg';
        }
        if($image_for == 'cover') {                
            $temp_file = $upload_dir. '/usr_tmp_cover_photos_'. $user_id. '.jpg';
        }           
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

    public static function uploadUserAvatarToTemp($user_id, $allowed_file_size = '2097152', $tmp_img_for = 'avatar') {
        global $wgUploadDirectory;
        
        $upload_dir = $wgUploadDirectory . '/temp';
        if ($_FILES) {                                
            $file = $_FILES['wpUploadFile'];            
            if (! $file['error'] && $file['size'] < $allowed_file_size) {//2097152 -. 2 mb in bytes;        
            
                $file_size_arr = getimagesize($file['tmp_name']); // get the image detail
                if (!$file_size_arr) {                
                    return false;
                }
    			$mime = $file_size_arr['mime'];
                $virtual_img_arr = self::get_virtual_img($mime,$file['tmp_name']);
                if (is_uploaded_file($file['tmp_name'])) {                                        
                    if($tmp_img_for == 'avatar'){
                        $temp_file = $upload_dir. '/usr_tmp_avatar_'. $user_id. '.jpg';
                    }
                    if($tmp_img_for == 'cover'){
                        $temp_file = $upload_dir. '/usr_tmp_cover_photos_'. $user_id. '.jpg';
                    }                    
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

    public static function ImageCreateFromBMP($filename)
    {
     //Ouverture du fichier en mode binaire
       if (! $f1 = fopen($filename,"rb")) return FALSE;
    
     //1 : Chargement des ent?tes FICHIER
       $FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1,14));
       if ($FILE['file_type'] != 19778) return FALSE;
    
     //2 : Chargement des ent?tes BMP
       $BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.
                     '/Vcompression/Vsize_bitmap/Vhoriz_resolution'.
                     '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1,40));
       $BMP['colors'] = pow(2,$BMP['bits_per_pixel']);
       if ($BMP['size_bitmap'] == 0) $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
       $BMP['bytes_per_pixel'] = $BMP['bits_per_pixel']/8;
       $BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
       $BMP['decal'] = ($BMP['width']*$BMP['bytes_per_pixel']/4);
       $BMP['decal'] -= floor($BMP['width']*$BMP['bytes_per_pixel']/4);
       $BMP['decal'] = 4-(4*$BMP['decal']);
       if ($BMP['decal'] == 4) $BMP['decal'] = 0;
    
     //3 : Chargement des couleurs de la palette
       $PALETTE = array();
       if ($BMP['colors'] < 16777216)
       {
        $PALETTE = unpack('V'.$BMP['colors'], fread($f1,$BMP['colors']*4));
       }
    
     //4 : Cr?ation de l'image
       $IMG = fread($f1,$BMP['size_bitmap']);
       $VIDE = chr(0);
    
       $res = imagecreatetruecolor($BMP['width'],$BMP['height']);
       $P = 0;
       $Y = $BMP['height']-1;
       while ($Y >= 0)
       {
        $X=0;
        while ($X < $BMP['width'])
        {
         if ($BMP['bits_per_pixel'] == 24)
            $COLOR = unpack("V",substr($IMG,$P,3).$VIDE);
         elseif ($BMP['bits_per_pixel'] == 16)
         { 
            $COLOR = unpack("n",substr($IMG,$P,2));
            $COLOR[1] = $PALETTE[$COLOR[1]+1];
         }
         elseif ($BMP['bits_per_pixel'] == 8)
         { 
            $COLOR = unpack("n",$VIDE.substr($IMG,$P,1));
            $COLOR[1] = $PALETTE[$COLOR[1]+1];
         }
         elseif ($BMP['bits_per_pixel'] == 4)
         {
            $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
            if (($P*2)%2 == 0) $COLOR[1] = ($COLOR[1] >> 4) ; else $COLOR[1] = ($COLOR[1] & 0x0F);
            $COLOR[1] = $PALETTE[$COLOR[1]+1];
         }
         elseif ($BMP['bits_per_pixel'] == 1)
         {
            $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
            if     (($P*8)%8 == 0) $COLOR[1] =  $COLOR[1]        >>7;
            elseif (($P*8)%8 == 1) $COLOR[1] = ($COLOR[1] & 0x40)>>6;
            elseif (($P*8)%8 == 2) $COLOR[1] = ($COLOR[1] & 0x20)>>5;
            elseif (($P*8)%8 == 3) $COLOR[1] = ($COLOR[1] & 0x10)>>4;
            elseif (($P*8)%8 == 4) $COLOR[1] = ($COLOR[1] & 0x8)>>3;
            elseif (($P*8)%8 == 5) $COLOR[1] = ($COLOR[1] & 0x4)>>2;
            elseif (($P*8)%8 == 6) $COLOR[1] = ($COLOR[1] & 0x2)>>1;
            elseif (($P*8)%8 == 7) $COLOR[1] = ($COLOR[1] & 0x1);
            $COLOR[1] = $PALETTE[$COLOR[1]+1];
         }
         else
            return FALSE;
         imagesetpixel($res,$X,$Y,$COLOR[1]);
         $X++;
         $P += $BMP['bytes_per_pixel'];
        }
        $Y--;
        $P+=$BMP['decal'];
       }
    
     //Fermeture du fichier
       fclose($f1);
    
     return $res;
    }

}

?>