<?php

/**
 * Collection Extension for MediaWiki
 *
 * Copyright (C) PediaPress GmbH
 *
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
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 */

class CollectionSession {

	/**
	 * @return bool
	 */
	static function hasSession() {
		if ( !session_id() ) {
			return false;
		}
        return isset( $_SESSION['wsCollection'] );
	}

	static function startSession() {
		if ( session_id() == '' ) {
			wfSetupSession();
		}
		self::clearCollection();
	}

	static function touchSession() {
		$collection = $_SESSION['wsCollection'];
		$collection['timestamp'] = wfTimestampNow();
		$_SESSION['wsCollection'] = $collection;        
	}

	static function clearCollection() {	
	   global $wgUser, $wgOut, $wgTitle;
       $coll =  array();
       if( $wgUser->getName() && $wgUser->getID() ){
          $coll['user_id'] = $wgUser->getID(); 
          $coll['user_name'] = $wgUser->getName();
          $coll['is_anonymous_user'] = 0;
       }
		$_SESSION['wsCollection'] = array(
            'book_id' => '',
            'user_id' => '',
            'user_name' => '',
            'is_anonymous_user' =>'',
			'enabled' => true,
			'title' => '',
			'subtitle' => '',
			'items' => array(),
            'book_type' => ''
		);
		CollectionSuggest::clear();
		self::touchSession();
	}

	static function enable( $book_type = '', $book_name = '' ) {
	   global $wgUser, $wgOut, $wgTitle;
       
		if ( !self::hasSession() ) { 
			self::startSession();
		} else { 
			$_SESSION['wsCollection']['enabled'] = true;
			self::touchSession();
		}
        $coll = array();
        if( $wgUser->getName() && $wgUser->getID() ){
          $coll['user_id'] = $wgUser->getID(); 
          $coll['user_name'] = $wgUser->getName();
          $coll['is_anonymous_user'] = 0;    
        } else {
           $coll['user_id'] = 0; 
           $coll['user_name'] = $wgUser->getName();
           $coll['is_anonymous_user'] = 1; 
        }
           $coll['enabled'] = $_SESSION['wsCollection']['enabled']; 
           $coll['title'] = $_SESSION['wsCollection']['title'];
           $coll['subtitle'] = $_SESSION['wsCollection']['subtitle'];
           $coll['timestamp'] = date("Y-m-d H:i:s",strtotime($_SESSION['wsCollection']['timestamp']));
           $coll['book_type'] = $book_type;
           $coll['book_name'] = $book_name;
        
        $user_book = gospellCommonFunctions::get_user_current_book($wgUser->getID(), $wgUser->getName());
            
        if($wgUser->getID()){
            gospellCommonFunctions::send_user_book($coll);
        } else {
            
            if(!$user_book){
               gospellCommonFunctions::send_user_book($coll); 
            }           
        }
        
        $user_book_after_send = gospellCommonFunctions::get_user_current_book($wgUser->getID(), $wgUser->getName());
        
        if($user_book_after_send){
           $coll['book_id'] = $user_book_after_send->book_id; 
        }     
         
                   
        $_SESSION['wsCollection'] = $coll;  
        
        $wsBookCollection = array();
        $wsBookCollection = $_SESSION['wsCollection'];
     return $wsBookCollection;       
	}

	static function disable() {
	    global $wgUser, $wgOut, $wgTitle;
        
		if ( !self::hasSession() ) {
			return;
		}
        if( !$wgUser->getID() ){
    		self::clearCollection();
    		$_SESSION['wsCollection']['enabled'] = false;
    		self::touchSession();
         }   
        
        $wsBookCollection = array();
        $wsBookCollection = $_SESSION['wsCollection'];
     return $wsBookCollection;
	}

	/**
	 * @return bool
	 */
	static function isEnabled() {
		return ( self::hasSession() && $_SESSION['wsCollection']['enabled'] );
	}

	/**
	 * @return bool
	 */
	static function hasItems() {
		return self::hasSession() && isset( $_SESSION['wsCollection']['items'] );
	}

	/**
	 * @return int
	 */
	static function countArticles() {
		if ( !self::hasItems() ) {
			return 0;
		}
		$count = 0;
		foreach ( $_SESSION['wsCollection']['items'] as $item ) {
			if ( $item['type'] == 'article' ) {
				$count++;
			}
		}
		return $count;
	}

	/**
	 * @param $title
	 * @param $oldid int
	 * @return int
	 */
	static function findArticle( $title, $oldid = 0, $book_id = '' ) {
	   global $wgUser;
       
		if ( !self::hasItems() ) {
			return - 1;
		}
        //////////////////////
        if($book_id === 0){
           $_SESSION['wsCollection']['items'] = gospellCommonFunctions::get_book_items( $book_id, $wgUser->getName() );   
        }else if($book_id !== '' || $book_id !== 0 ){
          $_SESSION['wsCollection']['items'] = gospellCommonFunctions::get_book_items( $book_id, $wgUser->getName() );  
        }          
        /////////////////////////
        if(is_array($_SESSION['wsCollection']['items'])){
             foreach ( $_SESSION['wsCollection']['items'] as $index => $item ) {
    			if ( $item['type'] == 'article' && $item['title'] == $title && $item['book_id'] == $book_id ) {
    				if ( $oldid ) {
    					if ( $item['revision'] == strval( $oldid ) ) {
    						return $index;
    					}
    				} else {
    					if ( $item['revision'] == $item['latest'] && $item['book_id'] == $book_id ) {
    						return $index;
    					}
    				}
    			}
    		}   
        }
		        
		return - 1;
	}

	/**
	 * @return bool
	 */
	static function purge( $user_name = '', $book_id = '' ) {
	    
		if ( !self::hasSession() ) {
			return false;
		}        
		$coll = $_SESSION['wsCollection'];
		$newitems = array();
		if ( isset( $coll['items'] ) ) {
			$batch = new LinkBatch;
			$lc = LinkCache::singleton();
			foreach ( $coll['items'] as $item ) {
				if ( $item['type'] == 'article' ) {
					$t = Title::newFromText( $item['title'] );
					$batch->addObj( $t );
				}
			}
			$batch->execute();
			foreach ( $coll['items'] as $item ) {
				if ( $item['type'] == 'article' ) {
					$t = Title::newFromText( $item['title'] );
					if ( $t && !$lc->isBadLink( $t->getPrefixedDbKey() ) ) {
						$newitems[] = $item;
					}
				} else {
					$newitems[] = $item;
				}
			}
		}
		$coll['items'] = $newitems;
        
		$_SESSION['wsCollection'] = $coll;
		return true;
	}

	/**
	 * @return array
	 */
	static function getCollection() {
	    //$user_id = User::idFromName( $user_name );        
		return self::purge() ? $_SESSION['wsCollection'] : array();
	}

	/**
	 * @param $collection
	 */
	static function setCollection( $collection ) {
		$_SESSION['wsCollection'] = $collection;
		self::touchSession();
	}
}
