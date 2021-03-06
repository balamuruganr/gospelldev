<?php
# This file was automatically generated by the MediaWiki 1.20.3
# installer. If you make manual changes, please keep track in case you
# need to recreate them later.
#
# See includes/DefaultSettings.php for all configurable settings
# and their default values, but don't forget to make changes in _this_
# file, not there.
#
# Further documentation for configuration settings may be found at:
# http://www.mediawiki.org/wiki/Manual:Configuration_settings

# Protect against web entry
if ( !defined( 'MEDIAWIKI' ) ) {
	exit;
}

## Uncomment this to disable output compression
# $wgDisableOutputCompression = true;

$wgSitename      = "gospelldev";
$wgMetaNamespace = "Gospelldev";

## The URL base path to the directory containing the wiki;
## defaults for all runtime URL paths are based off of this.
## For more information on customizing the URLs
## (like /w/index.php/Page_title to /wiki/Page_title) please see:
## http://www.mediawiki.org/wiki/Manual:Short_URL
$wgScriptPath       = "/gospelldev";
$wgScriptExtension  = ".php";

## The protocol and server name to use in fully-qualified URLs
$wgServer           = "http://localhost";

## The relative URL path to the skins directory
$wgStylePath        = "$wgScriptPath/skins";

## The relative URL path to the logo.  Make sure you change this from the default,
## or else you'll overwrite your logo when you upgrade!
$wgLogo             = "$wgStylePath/common/images/wiki.png";

## UPO means: this is also a user preference option

$wgEnableEmail      = true;
$wgEnableUserEmail  = true; # UPO

$wgEmergencyContact = "apache@localhost";
$wgPasswordSender   = "apache@localhost";

$wgEnotifUserTalk      = true; # UPO
$wgEnotifWatchlist     = true; # UPO
$wgEmailAuthentication = true;

## Database settings
$wgDBtype           = "mysql";
$wgDBserver         = "localhost";
$wgDBname           = "gospelldev";
$wgDBuser           = "root";
$wgDBpassword       = "";

# MySQL specific settings
$wgDBprefix         = "";

# MySQL table options to use during installation or update
$wgDBTableOptions   = "ENGINE=InnoDB, DEFAULT CHARSET=binary";

# Experimental charset support for MySQL 5.0.
$wgDBmysql5 = false;

## Shared memory settings
$wgMainCacheType    = CACHE_NONE;
$wgMemCachedServers = array();

## To enable image uploads, make sure the 'images' directory
## is writable, then set this to true:
$wgEnableUploads  = true;
#$wgUseImageMagick = true;
#$wgImageMagickConvertCommand = "/usr/bin/convert";

# InstantCommons allows wiki to use images from http://commons.wikimedia.org
$wgUseInstantCommons  = false;

## If you use ImageMagick (or any other shell command) on a
## Linux server, this will need to be set to the name of an
## available UTF-8 locale
$wgShellLocale = "en_US.utf8";

## If you want to use image uploads under safe mode,
## create the directories images/archive, images/thumb and
## images/temp, and make them all writable. Then uncomment
## this, if it's not already uncommented:
$wgHashedUploadDirectory = false;

## Set $wgCacheDirectory to a writable directory on the web server
## to make your wiki go slightly faster. The directory should not
## be publically accessible from the web.
#$wgCacheDirectory = "$IP/cache";

# Site language code, should be one of the list in ./languages/Names.php
$wgLanguageCode = "en";

$wgSecretKey = "28b44131c6f8927905d92ee364393ec8539399c5fbf03d5b4fba525ba18c40d5";

# Site upgrade key. Must be set to a string (default provided) to turn on the
# web installer while LocalSettings.php is in place
$wgUpgradeKey = "8df979074eba3b1c";

## Default skin: you can change the default skin. Use the internal symbolic
## names, ie 'standard', 'nostalgia', 'cologneblue', 'monobook', 'vector':
$wgDefaultSkin = "vector";

## For attaching licensing metadata to pages, and displaying an
## appropriate copyright notice / icon. GNU Free Documentation
## License and Creative Commons licenses are supported so far.
$wgRightsPage = ""; # Set to the title of a wiki page that describes your license/copyright
$wgRightsUrl  = "";
$wgRightsText = "";
$wgRightsIcon = "";

# Path to the GNU diff3 utility. Used for conflict resolution.
$wgDiff3 = "";

# Query string length limit for ResourceLoader. You should only set this if
# your web server has a query string length limit (then set it to that limit),
# or if you have suhosin.get.max_value_length set in php.ini (then set it to
# that value)
$wgResourceLoaderMaxQueryLength = -1;

//=============================Edit and Read Permissions ===================================//
                       //////////// Updated By Mathivanan ///////////////////
//Remove edit and read access from anonymous users
$wgGroupPermissions['*']['edit'] = false;
$wgGroupPermissions['*']['read'] = true;
//add/Remove edit permissions from registered users
$wgGroupPermissions['user']['edit'] = true;
// Grant edit access to sysops
$wgGroupPermissions['sysop']['edit'] = true;

$wgGospellSettingsProfileAboutMaxLenth = 512;
$wgGospellSettingsUserProfileAgeLimit  = 13;
$wgGospellSettingsUserBordMessageFileSize = 10485760; //10485760 = 10MB & 2097152 = 2 mb in bytes;
$wgGospellSettingsUserBordMessageTextLenth = 600;

$wgLegacyJavaScript = true;
$wgFileExtensions = array('png','gif','jpg','jpeg','doc','xls','mpp','mp4','mp3','pdf','ppt','tiff','bmp','docx', 'xlsx', 'pptx','ps','odt','ods','odp','odg');

require_once("$IP/extensions/Collection/Collection.php");
$wgCollectionPODPartners = array(
    'pediapress' => array(
        'name' => 'PediaPress',
        'url' => 'http://pediapress.com/',
        'posturl' => 'http://pediapress.com/api/collections/',
        'infopagetitle' => 'coll-order_info_article',
    ),
);


require_once("$IP/extensions/MediawikiPlayer/MediawikiPlayer.php");
MediawikiPlayer::useAddOn('PlayerPointer');  
$wgMWPlayerDefaultSettings = array(
              'width' => '400',
              'height' => '300',
              'allowfullscreen' => 'true',
              'backcolor' => 'eeeeee',
              );
$wgMWPlayerDir =  "$IP/extensions/MediawikiPlayer";   
$wgMWPlayerUseSWFObject = true;

require_once('extensions/FlashMP3/flashmp3.php');
//require_once('extensions/MP3/mp3.php');
/*
* 
*
* Note: This (require_once('extensions/ShareThis.php');) works by hooking into 'MonoBookTemplateToolboxEnd'. 
  If you're using a skin which doesn't call this hook, then the above will not work.
*/
//require_once('extensions/ShareThis/ShareThis.php');
//$wgShowShareThisSidebar = true;

//$wgCollectionPODPartners = false;
//$wgGroupPermissions['user']['collectionsaveascommunitypage'] = true;
//$wgGroupPermissions['user']['collectionsaveasuserpage']      = true;

                     //////////// Updated By Mathivanan /////////////////// 
//==========================================================================================//

# Enabled Extensions. Most extensions are enabled by including the base extension file here
# but check specific extension documentation for more details
# The following extensions were automatically enabled:
require_once( "$IP/extensions/WikiEditor/WikiEditor.php" );


# End of automatically generated settings.
# Add more configuration options below.

require_once("$IP/extensions/SocialProfile/SocialProfile.php");
$wgUserProfileDisplay['friends']    = true;
$wgUserProfileDisplay['foes']       = false;
$wgUserBoard                        = true;
$wgUserProfileDisplay['board']      = true;
$wgUserProfileDisplay['stats']      = true;

require_once("$IP/extensions/Facebook/Facebook.php");
//hide ip address , because facebook config showing
$wgShowIPinHeader = false; 

$wgUploadDirectory = "{$IP}/images";

//Disambiguation Custom Namespace
define("NS_DISAMBIGUATION", 500);
define("NS_DISAMBIGUATION_TALK", 501);
$wgExtraNamespaces[NS_DISAMBIGUATION]               = "Disambiguation";
$wgExtraNamespaces[NS_DISAMBIGUATION_TALK]          = "Disambiguation_talk";

//Namespace edit protection from user , and sysop user can edit 
$wgGroupPermissions['sysop']['edittemplate']        = true;
$wgGroupPermissions['sysop']['edithelp']            = true;

$wgNamespaceProtection[ NS_TEMPLATE ]               = array( 'edittemplate' );
$wgNamespaceProtection[ NS_HELP ]                   = array( 'edithelp' );

