<?php
/**
 * Html form for account creation.
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
 *
 * @file
 * @ingroup Templates
 */

/**
 * @defgroup Templates Templates
 */

if( !defined( 'MEDIAWIKI' ) ) die( -1 );

/**
 * @ingroup Templates
 */
class UsercreateTemplate extends QuickTemplate {
	function addInputItem( $name, $value, $type, $msg, $helptext = false ) {
		$this->data['extraInput'][] = array(
			'name' => $name,
			'value' => $value,
			'type' => $type,
			'msg' => $msg,
			'helptext' => $helptext,
		);
	}
	
	function execute() {
	   global $wgGospellSettingsProfileAboutMaxLenth;
		if( $this->data['message'] ) {
?>
	<div class="<?php $this->text('messagetype') ?>box">
		<?php if ( $this->data['messagetype'] == 'error' ) { ?>
			<strong><?php $this->msg( 'loginerror' )?></strong><br />
		<?php } ?>
		<?php $this->html('message') ?>
	</div>
	<div class="visualClear"></div>
<?php } ?>
<div id="signupstart"><?php $this->msgWiki( 'signupstart' ); ?></div>
<div id="userlogin">
<form name="userlogin2" id="userlogin2" method="post" action="<?php $this->text('action') ?>">
<input type='hidden' class='loginText' name="wpRealName" id="wpRealName" value="<?php $this->text('realname') ?>" size='20' />
	<h2><?php $this->msg('createaccount') ?></h2>
	<p id="userloginlink"><?php $this->html('link') ?></p>
	<?php $this->html('header'); /* pre-table point for form plugins... */ ?>
	<?php if( $this->haveData( 'languages' ) ) { ?><div id="languagelinks"><p><?php $this->html( 'languages' ); ?></p></div><?php } ?>
	<table>
		<tr>
			<td class="mw-label"><label for='wpFirstName2'><?php $this->msg('firstname') ?></label></td>
			<td class="mw-input">
				<?php
			echo Html::input( 'wpFirstName', $this->data['firstname'], 'text', array(
				'class' => 'required',
				'id' => 'wpFirstName2',
				'tabindex' => '1',
				'size' => '20',
				'required '  => 'required',
				'autofocus'
			) ); ?> 
            <span id="errfirstname2" class="error"></span>               
			</td>
		</tr>    
		<tr>
			<td class="mw-label"><label for='wpLastName2'><?php $this->msg('lastname') ?></label></td>
			<td class="mw-input">
				<?php
			echo Html::input( 'wpLastName', $this->data['lastname'], 'text', array(
				'class' => 'loginText required',
				'id' => 'wpLastName2',
				'tabindex' => '2',
				'size' => '20',
				'required '  => 'required'				
			) ); ?>  
            <span id="errlastname2" class="error"></span>                      			
            </td>
		</tr>
		<tr>
			<td class="mw-label"><label for='wpGender2'><?php echo wfMsg( 'user-profile-personal-gender' ); ?></label></td>
			<td class="mw-input">
            <select name="wpGender2" id="wpGender2" class="required" title="<?php echo wfMsg( 'user-profile-personal-gender' ); ?>" required="required" tabindex="3">
            <option value="">-Select Gender-</option>
            <?php
            $genders = explode( "\n*", wfMsgForContent( 'userprofile-gender-list' ) );
            array_shift( $genders );              
            foreach ( $genders as $gendr ) {
            	 echo  "<option value=\"{$gendr}\" >{$gendr}</option>";			 
            }        
            ?>
            </select>  
            <span id="errgender" class="error"></span>
            </td>
		</tr>
		<tr>
			<td class="mw-label"><label for='birthday'><?php echo wfMsg( 'user-profile-personal-birthday' ); ?></label></td>
			<td class="mw-input">
            <input type="text" class="long-birthday required" required="required" size="25" name="birthday" id="birthday" title="<?php echo wfMsg( 'user-profile-personal-birthday' ); ?>"  tabindex="4"/>
            <span id="errbirthday" class="error"></span>
            </td>            
		</tr>                  
                    
		<tr>
			<td class="mw-label"><label for='wpName2'><?php $this->msg('yourname') ?></label></td>
			<td class="mw-input">
				<?php
			echo Html::input( 'wpName', $this->data['name'], 'text', array(
				'class' => 'loginText required',
				'id' => 'wpName2',
				'tabindex' => '5',
				'size' => '20',
				'required '  => 'required'				
			) ); ?>            
            <div id="uNameExists" style="color: red;"></div>
			</td>
		</tr>
		<tr>
			<td class="mw-label"><label for='wpPassword2'><?php $this->msg('yourpassword') ?></label></td>
			<td class="mw-input">
<?php
			echo Html::input( 'wpPassword', null, 'password', array(
				'class' => 'loginPassword required',
				'id' => 'wpPassword2',
				'tabindex' => '6',
                'required '  => 'required',                
				'size' => '20'
			) + User::passwordChangeInputAttribs() ); ?>
            <span id="errpassword2" class="error"></span>
            <span id="pwd_strength"></span>            
			</td>
		</tr>
	<?php if( $this->data['usedomain'] ) {
		$doms = "";
		foreach( $this->data['domainnames'] as $dom ) {
			$doms .= "<option>" . htmlspecialchars( $dom ) . "</option>";
		}
	?>
		<tr>
			<td class="mw-label"><?php $this->msg( 'yourdomainname' ) ?></td>
			<td class="mw-input">
				<select name="wpDomain" value="<?php $this->text( 'domain' ) ?>"
					tabindex="5">
					<?php echo $doms ?>
				</select>
			</td>
		</tr>
	<?php } ?>
		<tr>
			<td class="mw-label"><label for='wpRetype'><?php $this->msg('yourpasswordagain') ?></label></td>
			<td class="mw-input">
				<?php
		echo Html::input( 'wpRetype', null, 'password', array(
			'class' => 'loginPassword required',
			'id' => 'wpRetype',
			'tabindex' => '7',
            'required '  => 'required',
			'size' => '20'
		) + User::passwordChangeInputAttribs() ); ?>
			<span id="errretype" class="error"></span>     
            </td>                   
		</tr>
		<tr>
			<?php if( $this->data['useemail'] ) { ?>
				<td class="mw-label"><label for='wpEmail'><?php $this->msg('youremail') ?></label></td>
				<td class="mw-input">
					<?php
		echo Html::input( 'wpEmail', $this->data['email'], 'email', array(
			'class' => 'loginText required',
			'id' => 'wpEmail',
			'tabindex' => '8',
			'size' => '20',
            'type' => 'email',
            'required ' => 'required',
            'placeholder ' => 'Email Address'
		) ); ?>					
					<div class="prefsectiontip">
						<?php /* // duplicated in Preferences.php profilePreferences()
							if( $this->data['emailrequired'] ) {
								$this->msgWiki('prefs-help-email-required');
							} else {
								$this->msgWiki('prefs-help-email');
							}
							if( $this->data['emailothers'] ) {
								$this->msgWiki('prefs-help-email-others');
							} */ ?>
					</div>	
                    <span id="erremail" class="error"></span>				
				</td>
			<?php } ?>
			<?php if( $this->data['usereason'] ) { ?>
				</tr>
				<tr>
					<td class="mw-label"><label for='wpReason'><?php $this->msg('createaccountreason') ?></label></td>
					<td class="mw-input">
						<input type='text' class='loginText' name="wpReason" id="wpReason"
							tabindex="7"
							value="<?php $this->text('reason') ?>" size='20' />
					</td>
			<?php } ?>
		</tr>
        <tr>
		<td class="mw-label"><label for='hometown_country'>Country</label></td>
		<td class="mw-input">
        <?php 
            $countries = explode( "\n*", wfMsgForContent( 'userprofile-country-list' ) );            
            array_shift( $countries );            
            echo "<select class='required' required='required' name=\"hometown_country\" id=\"hometown_country\" class=\"required\" title=\"" . wfMsg( 'user-profile-personal-location' ) . "-" . wfMsg( 'user-profile-personal-country' ) . "\"  tabindex='9'>
            <option value=\"\">-Select Country-</option>";
            foreach ( $countries as $country ) {
    			echo "<option value=\"{$country}\" >{$country}</option>";
		    }           
            echo '</select>';         
        ?>
        <span id="errhomecountry" class="error"></span>
        </td>
        </tr>        
		<tr>
			<td class="mw-label"><label for='aboutme'><?php echo wfMsg( 'user-profile-personal-aboutme' ); ?></label></td>
			<td class="mw-input">
                <textarea class="required" required="required" maxlength="<?php echo $wgGospellSettingsProfileAboutMaxLenth; ?>" name="aboutme" id="aboutme" tabindex="10"></textarea>
                <span id="erraboutme" class="error"></span>
            </td>            
		</tr>                  
        
		<?php if( $this->data['canremember'] ) { ?>
		<tr>
			<td></td>
			<td class="mw-input">
				<?php
				global $wgCookieExpiration;
				$expirationDays = ceil( $wgCookieExpiration / ( 3600 * 24 ) );
				echo Xml::checkLabel(
					wfMessage( 'remembermypassword' )->numParams( $expirationDays )->text(),
					'wpRemember',
					'wpRemember',
					$this->data['remember'],
					array( 'tabindex' => '8' )
				)
				?>
			</td>
		</tr>
<?php   }

		$tabIndex = 9;
		if ( isset( $this->data['extraInput'] ) && is_array( $this->data['extraInput'] ) ) {
			foreach ( $this->data['extraInput'] as $inputItem ) { ?>
		<tr>
			<?php 
				if ( !empty( $inputItem['msg'] ) && $inputItem['type'] != 'checkbox' ) {
					?><td class="mw-label"><label for="<?php 
					echo htmlspecialchars( $inputItem['name'] ); ?>"><?php
					$this->msgWiki( $inputItem['msg'] ) ?></label><?php
				} else {
					?><td><?php
				}
			?></td>
			<td class="mw-input">
				<input type="<?php echo htmlspecialchars( $inputItem['type'] ) ?>" name="<?php
				echo htmlspecialchars( $inputItem['name'] ); ?>"
					tabindex="<?php echo $tabIndex++; ?>"
					value="<?php 
				if ( $inputItem['type'] != 'checkbox' ) {
					echo htmlspecialchars( $inputItem['value'] );
				} else {
					echo '1';
				}					
					?>" id="<?php echo htmlspecialchars( $inputItem['name'] ); ?>"
					<?php 
				if ( $inputItem['type'] == 'checkbox' && !empty( $inputItem['value'] ) )
					echo 'checked="checked"'; 
					?> /> <?php 
					if ( $inputItem['type'] == 'checkbox' && !empty( $inputItem['msg'] ) ) {
						?>
				<label for="<?php echo htmlspecialchars( $inputItem['name'] ); ?>"><?php
					$this->msgHtml( $inputItem['msg'] ) ?></label><?php
					}
				if( $inputItem['helptext'] !== false ) {
				?>
				<div class="prefsectiontip">
					<?php $this->msgWiki( $inputItem['helptext'] ); ?>
				</div>
				<?php } ?>
			</td>
		</tr>
<?php				
				
			}
		}
?>
		<tr>
			<td></td>
			<td class="mw-submit">
				<input type='submit' name="wpCreateaccount" id="wpCreateaccount"
					tabindex="<?php echo $tabIndex++; ?>"
					value="<?php $this->msg('createaccount') ?>" />
				<?php if( $this->data['createemail'] ) { ?>
				<input type='submit' name="wpCreateaccountMail" id="wpCreateaccountMail"
					tabindex="<?php echo $tabIndex++; ?>"
					value="<?php $this->msg('createaccountmail') ?>" />
				<?php } ?>
			</td>
		</tr>
	</table>
<?php if( $this->haveData( 'uselang' ) ) { ?><input type="hidden" name="uselang" value="<?php $this->text( 'uselang' ); ?>" /><?php } ?>
<?php if( $this->haveData( 'token' ) ) { ?><input type="hidden" name="wpCreateaccountToken" value="<?php $this->text( 'token' ); ?>" /><?php } ?>
</form>
</div>
<div id="signupend"><?php $this->html( 'signupend' ); ?></div>
<?php

	}
}
