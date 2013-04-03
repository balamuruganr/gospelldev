<?php
/**
 * @author 
 * @copyright 2013
 * gospell include code here
 * 
 */

//user name suggest signup part (include/specials/SpecialUserlogin.php)
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
     if(isset($_REQUEST['checkuser'])) {
        $user_first_last_name = '';
        if(isset($_REQUEST['uname'])) {
            $user_name = substr($_REQUEST['uname'], 0, -4);
            $split_user_name = explode('|||',$user_name);
            $user_name = $split_user_name[0];
            $user_first_last_name = $split_user_name[1]; 
        }            
        if($user_name == '') {        
            echo '0||';
            die();
        }                        
        $dbw = wfGetDB( DB_SLAVE );
        $res = $dbw->select(
    		'user',
    		array( 'user_name' ),
    		array( 'user_name' => ucfirst( $user_name )),        
    		__METHOD__,
            array( 'LIMIT' => '1' )
    	);        
    	foreach ( $res as $row ) {
    	 if(isset($row->user_name)){    
    	   echo $user_first_last_name;
    	     if(!empty($user_first_last_name)) {
    	           //$suggest_user_name = $user_first_last_name.gospellCommonFunctions::generateRandomString();
    	         echo '1||'.$user_first_last_name.gospellCommonFunctions::generateRandomString();
             }else{
                //$suggest_user_name = $user_name.gospellCommonFunctions::generateRandomString();
                echo '1||'.$user_name.gospellCommonFunctions::generateRandomString();
             }
             //TODO:
            //check suggested user name already exists in db
            /* 
            $dbw = wfGetDB( DB_SLAVE );
            $res_suggest = $dbw->select(
                		'user',
                		array( 'user_name' ),
                		array( 'user_name' => ucfirst( $suggest_user_name )),                    
                		__METHOD__,
                        array( 'LIMIT' => '1' )
                	);  
            */                       
             die();
    	 }
    	}            
        echo '0||';
        die();                                    
    }
}


?>