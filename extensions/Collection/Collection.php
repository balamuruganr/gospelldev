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

# Not a valid entry point, skip unless MEDIAWIKI is defined
if ( !defined( 'MEDIAWIKI' ) ) {
	echo <<<EOT
To install the Collection extension, put the following line in LocalSettings.php:
require_once( "\$IP/extensions/Collection/Collection.php" );
EOT;
	exit( 1 );
}

$dir = __DIR__ . '/';

# Extension version. If you update it, please also update 'requiredVersion'
# in js/collection.js
$wgCollectionVersion = "1.6.1";

# ==============================================================================

# Configuration:

/** Bump the version number every time you change any of the JavaScript files */
$wgCollectionStyleVersion = 9;

/** URL of mw-serve render server */
$wgCollectionMWServeURL = 'http://tools.pediapress.com/mw-serve/';

/** Login credentials to this MediaWiki as 'USERNAME:PASSWORD' string */
$wgCollectionMWServeCredentials = null;

/** PEM-encoded SSL certificate for the mw-serve render server to pass to CURL */
$wgCollectionMWServeCert = null;

/** if not null, treat this string as hierarchy delimiter in page titles,
 * i.e. support subpages */
$wgCollectionHierarchyDelimiter = null;

/** Array of namespaces that can be added to a collection */
$wgCollectionArticleNamespaces = array(
	NS_MAIN,
	NS_TALK,
	NS_USER,
	NS_USER_TALK,
	NS_PROJECT,
	NS_PROJECT_TALK,
	NS_MEDIAWIKI,
	NS_MEDIAWIKI_TALK,
	100,
	101,
	102,
	103,
	104,
	105,
	106,
	107,
	108,
	109,
	110,
	111,
);

/** Namespace for "community books" */
$wgCommunityCollectionNamespace = NS_PROJECT;

/** Maximum no. of articles in a book */
$wgCollectionMaxArticles = 500;

/** Name of license */
$wgCollectionLicenseName = null;

/** HTTP(s) URL pointing to license in wikitext format: */
$wgCollectionLicenseURL = null;

/** List of available download formats,
		as mapping of mwlib writer to format name */
$wgCollectionFormats = array(
	'rl' => 'PDF',
	#'zeno' => 'ZENO',
	#'okawix_zeno' => 'Okawix (ZENO + search engine)',
);

/** For formats which rendering depends on an external server
*/
$wgCollectionFormatToServeURL = array(
	'zeno' => 'http://www.okawix.com/collections/render.php',
	'okawix_zeno' => 'http://www.okawix.com/collections/render.php',
);

$wgCollectionContentTypeToFilename = array(
	'application/pdf' => 'collection.pdf',
	'application/vnd.oasis.opendocument.text' => 'collection.odt',
);

$wgCollectionPortletFormats = array( 'rl' );

$wgCollectionPortletForLoggedInUsersOnly = false;

$wgCollectionMaxSuggestions = 10;

$wgCollectionSuggestCheapWeightThreshhold = 50;

$wgCollectionSuggestThreshhold = 100;

$wgCollectionPODPartners = array(
	'pediapress' => array(
		'name' => 'PediaPress',
		'url' => 'http://pediapress.com/',
		'posturl' => 'http://pediapress.com/api/collections/',
		'infopagetitle' => 'coll-order_info_article',
	),
);
# ==============================================================================

$wgExtensionCredits['specialpage'][] = array(
	'path' => __FILE__,
	'name' => 'Collection',
	'version' => $wgCollectionVersion,
	'author' => array( 'PediaPress GmbH', 'Siebrand Mazeland', 'Marcin Cieślak'),
	'url' => 'https://www.mediawiki.org/wiki/Extension:Collection',
	'descriptionmsg' => 'coll-desc',
);

# register Special:Book:
$wgAutoloadClasses['SpecialCollection'] = $dir . 'Collection.body.php';
$wgAutoloadClasses['CollectionSession'] = $dir . 'Collection.session.php';
$wgAutoloadClasses['CollectionHooks'] = $dir . 'Collection.hooks.php';
$wgAutoloadClasses['CollectionSuggest'] = $dir . 'Collection.suggest.php';
$wgAutoloadClasses['CollectionPageTemplate'] = $dir . 'Collection.templates.php';
$wgAutoloadClasses['CollectionListTemplate'] = $dir . 'Collection.templates.php';
$wgAutoloadClasses['CollectionLoadOverwriteTemplate'] = $dir . 'Collection.templates.php';
$wgAutoloadClasses['CollectionSaveOverwriteTemplate'] = $dir . 'Collection.templates.php';
$wgAutoloadClasses['CollectionRenderingTemplate'] = $dir . 'Collection.templates.php';
$wgAutoloadClasses['CollectionFinishedTemplate'] = $dir . 'Collection.templates.php';
$wgAutoloadClasses['CollectionSuggestTemplate'] = $dir . 'Collection.templates.php';
$wgExtensionMessagesFiles['CollectionCore'] = $dir . 'CollectionCore.i18n.php'; // Only contains essential messages outside the special page
$wgExtensionMessagesFiles['Collection'] = $dir . 'Collection.i18n.php'; // Contains all messages used on special page
$wgExtensionMessagesFiles['CollectionAlias'] = $dir . 'Collection.alias.php';
$wgSpecialPages['Book'] = 'SpecialCollection';
$wgSpecialPageGroups['Book'] = 'pagetools';

$wgHooks['SkinTemplateBuildNavUrlsNav_urlsAfterPermalink'][] = 'CollectionHooks::buildNavUrls';
$wgHooks['SkinBuildSidebar'][] = 'CollectionHooks::buildSidebar';
$wgHooks['SiteNoticeAfter'][] = 'CollectionHooks::siteNoticeAfter';
$wgHooks['OutputPageCheckLastModified'][] = 'CollectionHooks::checkLastModified';
$wgHooks['ResourceLoaderGetConfigVars'][] = 'CollectionHooks::resourceLoaderGetConfigVars';

$wgAvailableRights[] = 'collectionsaveasuserpage';
$wgAvailableRights[] = 'collectionsaveascommunitypage';

$collResourceTemplate = array(
	'localBasePath' => "$dir/js",
	'remoteExtPath' => 'Collection/js'
);

$wgResourceModules += array(
	'ext.collection.jquery.jstorage' => $collResourceTemplate + array(
		'scripts' => 'jstorage.js',
		'dependencies' => 'jquery.json'
	),
	'ext.collection.suggest' => $collResourceTemplate + array(
		'scripts' => 'suggest.js',
		'dependencies' => 'ext.collection.bookcreator'
	),
	'ext.collection' => $collResourceTemplate + array(
		'scripts' => 'collection.js',
		'dependencies' => array( 'ext.collection.bookcreator', 'jquery.ui.sortable' ),
	),
	'ext.collection.bookcreator' => $collResourceTemplate + array(
		'scripts' => 'bookcreator.js',
		'styles' => 'bookcreator.css',
		'dependencies' => 'ext.collection.jquery.jstorage'
	),
	'ext.collection.checkLoadFromLocalStorage' => $collResourceTemplate + array(
		'scripts' => 'check_load_from_localstorage.js',
		'styles' => 'bookcreator.css',
		'dependencies' => 'ext.collection.jquery.jstorage'
	)
);

# register global Ajax functions:

function wfAjaxGetCollection() {
	if ( isset( $_SESSION['wsCollection'] ) ) {
		$collection = $_SESSION['wsCollection'];
	} else {
		$collection = array();
	}
	$r = new AjaxResponse( FormatJson::encode( array( 'collection' => $collection ) ) );
	$r->setContentType( 'application/json' );
	return $r;
}

$wgAjaxExportList[] = 'wfAjaxGetCollection';

function wfAjaxPostCollection( $collection = '', $redirect = '' ) {
	$json = new Services_JSON( SERVICES_JSON_LOOSE_TYPE );
	if ( session_id() == '' ) {
		wfSetupSession();
	}
	$collection = $json->decode( $collection );
	$collection['enabled'] = true;
	$_SESSION['wsCollection'] = $collection;
	$r = new AjaxResponse();
	if ( $redirect ) {
		$title = Title::newFromText( $redirect );
		$redirecturl = wfExpandUrl( $title->getFullURL(), PROTO_CURRENT );
		$r->setResponseCode( 302 );
		header( 'Location: ' . $redirecturl );
	} else {
		$title = SpecialPage::getTitleFor( 'Book' );
		$redirecturl = wfExpandUrl( $title->getFullURL(), PROTO_CURRENT );
		$r->setContentType( 'application/json' );
		$r->addText( $json->encode( array( 'redirect_url' => $redirecturl ) ) );
	}
	return $r;
}

$wgAjaxExportList[] = 'wfAjaxPostCollection';

function wfAjaxGetMWServeStatus( $collection_id = '', $writer = 'rl' ) {
	$result = SpecialCollection::mwServeCommand( 'render_status', array(
		'collection_id' => $collection_id,
		'writer' => $writer
	) );
	if ( isset( $result['status']['progress'] ) ) {
		$result['status']['progress'] = number_format( $result['status']['progress'], 2, '.', '' );
	}
	$r = new AjaxResponse( FormatJson::encode( $result ) );
	$r->setContentType( 'application/json' );
	return $r;
}

$wgAjaxExportList[] = 'wfAjaxGetMWServeStatus';

function wfAjaxCollectionAddArticle( $namespace = 0, $title = '', $oldid = '', $book_id = 0 ) {
	SpecialCollection::addArticleFromName( $namespace, $title, $oldid, $book_id );
	return wfAjaxCollectionGetItemList();
}

$wgAjaxExportList[] = 'wfAjaxCollectionAddArticle';

function wfAjaxCollectionRemoveArticle( $namespace = 0, $title = '', $oldid = '', $book_id = 0 ) {
	SpecialCollection::removeArticleFromName( $namespace, $title, $oldid, $book_id );
	return wfAjaxCollectionGetItemList();
}

$wgAjaxExportList[] = 'wfAjaxCollectionRemoveArticle';

function wfAjaxCollectionAddCategory( $title = '' ) {
	SpecialCollection::addCategoryFromName( $title );
	return wfAjaxCollectionGetItemList();
}

$wgAjaxExportList[] = 'wfAjaxCollectionAddCategory';

function wfAjaxCollectionGetBookCreatorBoxContent( $ajaxHint = '', $oldid = null, $book_id = 0, $pageName = null ) {
	if ( !is_null( $oldid ) ) {
		$oldid = intval( $oldid );
	}
    
    if( !is_int( $book_id ) ) {
       $book_id = intval( $book_id ); 
    }

	$title = null;
	if ( !is_null( $pageName ) ) {
		$title = Title::newFromText( $pageName );
	}
	if ( is_null( $title ) ) {
		$title = Title::newMainPage();
	}

	$html = CollectionHooks::getBookCreatorBoxContent( $title, $ajaxHint, $oldid, $book_id );

	$result = array();
	$result['html'] = $html;
	$r = new AjaxResponse( FormatJson::encode( $result ) );
	$r->setContentType( 'application/json' );
	return $r;
}

$wgAjaxExportList[] = 'wfAjaxCollectionGetBookCreatorBoxContent';

function wfAjaxCollectionGetItemList() {
	$collection = $_SESSION['wsCollection'];

	$template = new CollectionListTemplate();
	$template->set( 'collection', $collection );
	$template->set( 'is_ajax', true );
	ob_start();
	$template->execute();
	$html = ob_get_contents();
	ob_end_clean();

	$result = array();
	$result['html'] = $html;
	$result['collection'] = $collection;
	$r = new AjaxResponse( FormatJson::encode( $result ) );
	$r->setContentType( 'application/json' );
	return $r;
}

$wgAjaxExportList[] = 'wfAjaxCollectionGetItemList';

function wfAjaxCollectionRemoveItem( $index ) {
	SpecialCollection::removeItem( (int)$index );
	return wfAjaxCollectionGetItemList();
}

$wgAjaxExportList[] = 'wfAjaxCollectionRemoveItem';

function wfAjaxCollectionAddChapter( $name ) {
	SpecialCollection::addChapter( $name );
	return wfAjaxCollectionGetItemList();
}

$wgAjaxExportList[] = 'wfAjaxCollectionAddChapter';

function wfAjaxCollectionRenameChapter( $index, $name ) {
	SpecialCollection::renameChapter( (int)$index, $name );
	return wfAjaxCollectionGetItemList();
}

$wgAjaxExportList[] = 'wfAjaxCollectionRenameChapter';

function wfAjaxCollectionSetTitles( $title, $subtitle ) {
	SpecialCollection::setTitles( $title, $subtitle );
	return wfAjaxCollectionGetItemList();
}

$wgAjaxExportList[] = 'wfAjaxCollectionSetTitles';

function wfAjaxCollectionSetSorting( $items_string ) {
	$parsed = array();
	parse_str( $items_string, $parsed );
	$items = array();
	foreach ( $parsed['item'] as $s ) {
		if ( is_numeric( $s ) ) {
			$items[] = intval( $s );
		}
	}
	SpecialCollection::setSorting( $items );
	return wfAjaxCollectionGetItemList();
}

$wgAjaxExportList[] = 'wfAjaxCollectionSetSorting';

function wfAjaxCollectionClear() {
	CollectionSession::clearCollection();
	CollectionSuggest::clear();
	return wfAjaxCollectionGetItemList();
}

$wgAjaxExportList[] = 'wfAjaxCollectionClear';

function wfAjaxCollectionGetPopupData( $title ) {
	global $wgExtensionAssetsPath;

	$result = array();
	$imagePath = "$wgExtensionAssetsPath/Collection/images";
	$t = Title::newFromText( $title );
	if ( $t && $t->isRedirect() ) {
		$wikiPage = WikiPage::factory( $t );
		$t = $wikiPage->followRedirect();
		if ( $t instanceof Title ) {
			$title = $t->getPrefixedText();
		}
	}
	if ( CollectionSession::findArticle( $title, 0, 0 ) == - 1 ) {
		$result['action'] = 'add';
		$result['text'] = wfMessage( 'coll-add_linked_article' )->text();
		$result['img'] = "$imagePath/silk-add.png";
	} else {
		$result['action'] = 'remove';
		$result['text'] = wfMessage( 'coll-remove_linked_article' )->text();
		$result['img'] = "$imagePath/silk-remove.png";
	}
	$result['title'] = $title;
	$r = new AjaxResponse( FormatJson::encode( $result ) );
	$r->setContentType( 'application/json' );

	return $r;
}

$wgAjaxExportList[] = 'wfAjaxCollectionGetPopupData';

/**
 * Backend of several following SAJAX function handlers...
 * @param String $action provided by the specific handlers internally
 * @param String $article title passed in from client
 * @return AjaxResponse with JSON-encoded array including HTML fragment.
 */
function wfCollectionSuggestAction( $action, $article ) {
	$result = CollectionSuggest::refresh( $action, $article );
	$undoLink = Xml::element( 'a',
		array(
			'href' => SkinTemplate::makeSpecialUrl(
				'Book',
				array( 'bookcmd' => 'suggest', 'undo' => $action, 'arttitle' => $article )
			),
			'onclick' => "collectionSuggestCall('UndoArticle'," .
				Xml::encodeJsVar( array( $action, $article ) ) . "); return false;",
			'title' => wfMessage( 'coll-suggest_undo_tooltip' )->text(),
		),
		wfMessage( 'coll-suggest_undo' )->text()
	);
	// Message keys used: coll-suggest_article_ban, coll-suggest_article_add, coll-suggest_article_remove
	$result['last_action'] = wfMessage( "coll-suggest_article_$action", $article )
		->rawParams( $undoLink )->parse();
	$result['collection'] = CollectionSession::getCollection();
	$r = new AjaxResponse( FormatJson::encode( $result ) );
	$r->setContentType( 'application/json' );
	return $r;
}

function wfAjaxCollectionSuggestBanArticle( $article ) {
	return wfCollectionSuggestAction( 'ban', $article );
}

$wgAjaxExportList[] = 'wfAjaxCollectionSuggestBanArticle';

function wfAjaxCollectionSuggestAddArticle( $article ) {
	return wfCollectionSuggestAction( 'add', $article );
}

$wgAjaxExportList[] = 'wfAjaxCollectionSuggestAddArticle';

function wfAjaxCollectionSuggestRemoveArticle( $article ) {
	return wfCollectionSuggestAction( 'remove', $article );
}

$wgAjaxExportList[] = 'wfAjaxCollectionSuggestRemoveArticle';

function wfAjaxCollectionSuggestUndoArticle( $lastAction, $article ) {
	$json = new Services_JSON();
	$result = CollectionSuggest::undo( $lastAction, $article );
	$r = new AjaxResponse( $json->encode( $result ) );
	$r->setContentType( 'application/json' );
	return $r;
}

$wgAjaxExportList[] = 'wfAjaxCollectionSuggestUndoArticle';

//////////////////////////////////////////////////////////////////
function wfAjaxCollectionGetRenderBookCreatorBox( $ajaxHint = '', $oldid = null, $book_id = 0, $pageName = null ) {
	if ( !is_null( $oldid ) ) {
		$oldid = intval( $oldid );
	}
    
    if( !is_int( $book_id ) ) {
       $book_id = intval( $book_id ); 
    }

	$title = null;
	if ( !is_null( $pageName ) ) {
		$title = Title::newFromText( $pageName );
	}
	if ( is_null( $title ) ) {
		$title = Title::newMainPage();
	}
            
    $_SESSION['wsCollection']['book_id'] = $book_id;
    $user_id = gospellCommonFunctions::userIdFromBookId( $book_id );
    $user_name = gospellCommonFunctions::userNameFromBookId( $book_id ); 
    $_SESSION['wsCollection']['user_id'] = $user_id;
    $_SESSION['wsCollection']['user_name'] = $user_name;
      
	$html = CollectionHooks::renderBookCreatorBox( $title, $mode = '', $book_id );
    
	$result = array();
	$result['html'] = $html;
	$r = new AjaxResponse( FormatJson::encode( $result ) );
	$r->setContentType( 'application/json' );
	return $r;
}

$wgAjaxExportList[] = 'wfAjaxCollectionGetRenderBookCreatorBox';

function wfAjaxSetDefaultBookSettings() {
	global $wgUser;
       $user_having_books = "";
        
       if(isset($_SESSION['wsCollection']['book_id']) && $_SESSION['wsCollection']['user_id'] && $_SESSION['wsCollection']['user_name']){ 
            $book_id = $_SESSION['wsCollection']['book_id'];
        } else { 
            if( $wgUser->getName() ){
              $user_having_books = gospellCommonFunctions::get_user_current_book( $wgUser->getID(), $wgUser->getName() );  
            }
            
            $book_id = (is_object($user_having_books))? $user_having_books->book_id : '0';
        }
        
    $result = array();
	$result['html'] = $book_id;
	$r = new AjaxResponse( FormatJson::encode( $result ) );
	$r->setContentType( 'application/json' );
	return $r;
}

$wgAjaxExportList[] = 'wfAjaxSetDefaultBookSettings';

function wfAjaxCollectionRemoveBook( $namespace = 0, $title = '', $oldid = '', $book_id = 0 ) {
	global $wgUser;
    //wfAjaxCollectionGetItemList();
    $user_id = gospellCommonFunctions::userIdFromBookId( $book_id );
    $user_name = gospellCommonFunctions::userNameFromBookId( $book_id );
    
    $is_book_removed = gospellCommonFunctions::remove_book( $book_id, $user_name );
    
    if($is_book_removed){
     $is_book_items_removed = gospellCommonFunctions::remove_book_allitems( $book_id, $user_name );  
    }
    
    if( $is_book_removed ){
      $user_having_books = gospellCommonFunctions::get_user_current_book( $user_id, $user_name );          
    }
    
    if(is_object($user_having_books)){
      $book_items = array();
            
        if(isset($user_having_books->book_id)){
            $new_book_id = $user_having_books->book_id;                          
         } else {
           $new_book_id = 0; 
         }
        
        $book_items = gospellCommonFunctions::get_book_items( $new_book_id, $user_name );   
         
         $_SESSION['wsCollection'] = array(
                        'book_id' => $user_having_books->book_id,
                        'book_name' => $user_having_books->book_name,
                        'user_id' => $user_having_books->user_id,
                        'user_name' => $user_having_books->user_name,
                        'is_anonymous_user' =>$user_having_books->is_anonym_user,
            			'enabled' => true,
            			'title' => $user_having_books->title,
            			'subtitle' => $user_having_books->subtitle,
                        'items' => $book_items,
                        'book_type' => $user_having_books->book_type,
                        'timestamp' => $user_having_books->unix_book_time
            		  ); 
    } else {
        
        $_SESSION['wsCollection'] = array(
                        'book_id' => '',
                        'book_name' => '', 
                        'user_id' => '',
                        'user_name' => '',
                        'is_anonymous_user' =>'',
            			'enabled' => false,
            			'title' => '',
            			'subtitle' => '',
            			'items' => array(),
                        'book_type' => '',
                        'timestamp' => wfTimestampNow()
            		  );
    }
    
    return wfAjaxCollectionGetItemList();                   
    /*$result = array();
	$result['html'] = "Madyvanan";
	$r = new AjaxResponse( FormatJson::encode( $result ) );
	$r->setContentType( 'application/json' );
	return $r;*/
}

$wgAjaxExportList[] = 'wfAjaxCollectionRemoveBook';
