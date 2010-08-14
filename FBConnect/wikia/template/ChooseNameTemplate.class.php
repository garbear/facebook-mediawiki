<?php

class ChooseNameTemplate extends QuickTemplate {

	function addInputItem( $name, $value, $type, $msg ) {
		$this->data['extraInput'][] = array(
			'name' => $name,
			'value' => $value,
			'type' => $type,
			'msg' => $msg,
		);
	}

	function execute(){
		global $wgOut, $wgStylePath, $wgStyleVersion, $wgBlankImgUrl;

		$wgOut->addScript('<link rel="stylesheet" type="text/css" href="'. $wgStylePath . '/wikia/common/NewUserRegister.css?' . $wgStyleVersion . "\" />\n");

		if (!array_key_exists('message', $this->data)) {
			$this->data['message'] = "";
		}
		if (!array_key_exists('ajax', $this->data)) {
			$this->data['ajax'] = "";
		}
		if( $this->data['message'] && !$this->data['ajax'] ) {
?>
	<div class="<?php $this->text('messagetype') ?>box">
		<?php if ( $this->data['messagetype'] == 'error' ) { ?>
			<h2><?php $this->msg('loginerror') ?></h2>
		<?php } ?>
		<?php $this->html('message') ?>
	</div>
	<div class="visualClear"></div>
<?php	} ?>
<div id="userloginErrorBox">
	<table>
	<tr>
		<td style="vertical-align:top;">
			<span style="position: relative; top: -1px;"><img alt="status" src="<?php print $wgBlankImgUrl; ?>"/></span>
		</td>
	<td>
		<div id="userloginInnerErrorBox"></div>
	</td>
	</table>
</div>
<table id="userloginSpecial" style='margin-top:0px;cell-spacing:0px' width="100%">
<tr>
<td width="55%" style="border:none; vertical-align: top;">
<div id="userRegisterAjax">
<form id="userajaxregisterform" method="post" action="<?php $this->text('actioncreate') ?>" onsubmit="return UserRegistration.checkForm()">
	<input type='hidden' name='wpNameChoice' value='manual' />
<?php		if( $this->data['message'] && $this->data['ajax'] ) { ?>
	<div class="<?php $this->text('messagetype') ?>box" style="margin:0px">
		<?php if ( $this->data['messagetype'] == 'error' ) { ?>
			<h2><?php $this->msg('loginerror') ?>:</h2>
		<?php } ?>
		<?php $this->html('message') ?>
	</div>
	<div class="visualClear"></div>
<?php } ?>
	<?php $this->html('header'); /* pre-table point for form plugins... */ ?>
	<?php /* LoginLanguageSelector used to be here, moved downward and modified as part of rt#16889 */ ?>
	<table class="wpAjaxRegisterTable" >
		<colgroup>
			<col width="350" />
			<col width="330" />
		</colgroup>
		<tr class="wpAjaxLoginPreLine">
			<td class="wpAjaxLoginInput" id="wpNameTD">
				<label for='wpName2'><?php $this->msg('yourname') ?></label><span>&nbsp;<img alt="status" src="<?php print $wgBlankImgUrl; ?>"/></span>
				<input type='text'  name="wpName2" id="wpName2"	value="<?php $this->text('name') ?>" size='20' />
			</td>
			<td class="mw-input" rowspan="2" style='vertical-align:top;'>
				<div id="msgToExistingUsers" style="width:240px;">
					<?php
					// The login button should open the ajax login dialog and select the login-and-connect form.
					$jsHref = "openLoginAndConnect();return false;";
					print wfMsg('fbconnect-msg-for-existing-users', $jsHref);
					?>
				</div>
			</td>
		</tr>
		<tr class="wpAjaxLoginPreLine" >
			<td class="wpAjaxLoginInput" id="wpEmailTD">
				<?php if( $this->data['useemail'] ) { ?>
					<label for='wpEmail'><?php $this->msg('signup-mail') ?></label><a style='float:left' id="wpEmailInfo" href="#"><?php $this->msg( 'signup-moreinfo' ) ?></a><span>&nbsp;<img alt="status" src="<?php print $wgBlankImgUrl; ?>"/></span>
					<input type='text'  name="wpEmail" id="wpEmail" value="<?php $this->text('email') ?>" size='20' />
				<?php } ?>
			</td>
		</tr>

		<tr class="wpAjaxLoginLine">
			<td class="wpAjaxLoginInput" colspan="2">
<?php
	global $wgLanguageCode;

	$aLanguages = wfGetFixedLanguageNames();

	// If we have a language setting from facebook, just hide that in the form, otherwise show
	// the normal dropdown.
	$allLanguageCodes = array_keys($aLanguages);

	// We get a language code from facebook, so we have to see if it is one we can use.
	$uselang = (isset($this->data['uselang'])?$this->data['uselang']:"");
	if($uselang && (in_array($uselang, $allLanguageCodes))){
		print "<input type='hidden' name='uselang' id='uselang' value='$uselang'/>\n";	
	} else {
		// If we didn't get an acceptable language from facebook, display the form.
		?><label for='uselang'><?php $this->msg('yourlanguage') ?></label>
		<select style="height:22px;" name="uselang" id="uselang"><?php
		$isSelected = false;

		$aTopLanguages = explode(',', wfMsg('wikia-language-top-list'));
		asort( $aLanguages );
			if (!empty($aTopLanguages) && is_array($aTopLanguages)) :
	?>
									<optgroup label="<?= wfMsg('wikia-language-top', count($aTopLanguages)) ?>">
	<?php foreach ($aTopLanguages as $sLang) :
					$selected = '';
					if ( !$isSelected && ( $wgLanguageCode == $sLang ) ) :
							$isSelected = true;
							$selected = ' selected="selected"';
					endif;
	?>
									<option value="<?=$sLang?>" <?=$selected?>><?=$aLanguages[$sLang]?></option>
	<?php endforeach ?>
									</optgroup>
	<?php endif ?>
									<optgroup label="<?= wfMsg('wikia-language-all') ?>">
	<?php if (!empty($aLanguages) && is_array($aLanguages)) : ?>
	<?php
			foreach ($aLanguages as $sLang => $sLangName) :
					if ( empty($isSelected) && ( $wgLanguageCode == $sLang ) ) :
							$isSelected = true;
							$selected = ' selected="selected"';
					endif;
	?>
									<option value="<?=$sLang?>" <?=$selected?>><?=$sLangName?></option>
	<?php endforeach ?>
									</optgroup>
	<?php endif ?>
									</select>
<?php
	}
?>
			</td>
		</tr>
		<tr class="wpAjaxLoginLine" >
	<?php
		$tabIndex = 8;
		if ( isset( $this->data['extraInput'] ) && is_array( $this->data['extraInput'] ) ) {
			foreach ( $this->data['extraInput'] as $inputItem ) { ?>
		<tr>
			<td class="mw-input" >
			<?php 
				if ( !empty( $inputItem['msg'] ) && $inputItem['type'] != 'checkbox' ) {
					?><label for="<?php 
					echo htmlspecialchars( $inputItem['name'] ); ?>"><?php
					$this->msgWiki( $inputItem['msg'] ) ?></label><?php } ?>
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
				?>
			</td>
			<td class="mw-input">
				<div id="termsOfUse" style="width:240px;">
					<?php $this->msgWiki('prefs-help-terms'); ?>
				</div>
			</td>
			<?php
				// The checkboxes for which fields to auto-update on every future facebook connection for this user.
				print $this->html('updateOptions');
			?>
		</tr>
<?php				
				
			}
		}

		// Allow initial setting of Facebook Push Event preferences if those are enabled.
		global $fbEnablePushToFacebook;
		if(!empty($fbEnablePushToFacebook)){
			print "<tr id='fbConnectPushEventBar' class='wpAjaxLoginLine' style=''>\n<td colspan='2'>\n";
			print wfMsg( 'fbconnect-prefsheader' );
			print "<em>\n";
			print "<br/>\n";
			print wfMsg( 'fbconnect-prefs-can-be-updated', wfMsg('fbconnect-prefstext'));
			print "</em></td></tr>";

			print "<tr id='fbConnectPushEventToggles' class='wpAjaxLoginPreLine' style='display:none;width:100%'><td colspan='2'>\n";
			$FIRST_TIME = true; // this is the first time we're using the form, so default all to checked rather than looking up the user-option.
			print FBConnectPushEvent::createPreferencesToggles($FIRST_TIME);
			print "</td></tr>\n";
		}
?>
	</table>

	<input type="submit" value="Register" style="position: absolute; left: -10000px; width: 0" />
<?php if( @$this->haveData( 'uselang' ) ) { ?><input type="hidden" name="uselang" value="<?php $this->text( 'uselang' ); ?>" /><?php } ?>

	<input type="hidden" id="wpCreateaccountXSteer" name="wpCreateaccount" value="true" >

</div>
</td>
</tr>
</table>
<div id="signupWhyProvide"></div>
<div id="signupend" style="clear: both;height: 12px;"><?php $this->msgWiki( 'signupend' ); ?></div>

<div class="modalToolbar neutral">
	<input type="submit" id="wpCreateaccountXSteer" name="wpCreateaccountMail" onclick="return UserRegistration.submitForm_fb();" value="<?php print wfMsg("createaccount") ?>" />	
	<!-- <input type="button" id="wpCreateaccountClose" class="secondary" onclick="AjaxLogin.close(); return false;" value="<?php print wfMsg("Cancel") ?>" /> -->
</div>
</form>


<script type='text/javascript'>
	$(document).ready(function(){
		$.getScript(window.wgScript + '?action=ajax&rs=getRegisterJS&uselang=' + window.wgUserLanguage + '&cb=' + wgMWrevId + '-' + wgStyleVersion, function(){
			//override submitForm
			if (typeof UserRegistration != 'undefined')
			{
				UserRegistration.submitForm_fb = function() {
					if( typeof UserRegistration.submitForm.statusAjax == 'undefined' ) { // java script static var
						UserRegistration.submitForm.statusAjax = false;
					}

					if(UserRegistration.submitForm.statusAjax)
					{
						return false;
					}
					UserRegistration.submitForm.statusAjax = true;
					if (UserRegistration.checkForm()) {
						$("#userloginErrorBox").hide();
						return true;
						<?php
						// TODO: This may be a useful reference when we convert this page to be an ajax form instead of just a special page.
						/*
						$.ajax({
							   type: "POST",
							   dataType: "json",
							   url: window.wgScriptPath  + "/index.php",
							   data: $("#userajaxregisterform").serialize() + "&action=ajax&rs=createUserLogin",
							   beforeSend: function(){
									$("#userRegisterAjax").find("input,select").attr("disabled",true);
							   },
							   success: function(msg){
									$("#userRegisterAjax").find("input,select").removeAttr("disabled");
									$("#wpCaptchaWord").val("");
									// post data to normal form if age < 13
									if (msg.type === "redirectQuery") {
										WET.byStr(UserRegistration.WET_str + '/createaccount/failure');
										$('#userajaxregisterform').submit();
										return ;
									}

									if( msg.status == "OK" ) {
										$('#AjaxLoginBoxWrapper').closeModal();
										WET.byStr(UserRegistration.WET_str + '/createaccount/success');
										AjaxLogin.doSuccess();
										return ;
									}

									WET.byStr(UserRegistration.WET_str + '/createaccount/failure');
									$('#userloginInnerErrorBox').empty().append(msg.msg);
									$("#userloginErrorBox").show();
									$(".captcha img").attr("src",msg.captchaUrl);
									$("#wpCaptchaId").val(msg.captcha);
									UserRegistration.submitForm.statusAjax = false;

							   }
						});
						*/
						?>
					} else {
						$("#userloginErrorBox").show();
						WET.byStr(UserRegistration.WET_str + '/createaccount/failure');
						UserRegistration.submitForm.statusAjax = false;
					}
				}
				
				UserRegistration.checkUsername(); // since we'll auto-fill it, show the user that it's already okay
			}
		});
		
		// Control show/hide of push-event preferences.
		$('#fbConnectPushEventBar_show').click(function(){
			$('#fbConnectPushEventBar_show').hide();
			$('#fbConnectPushEventToggles').show();
			$('#fbConnectPushEventBar_hide').show();
			return false;
		});
		$('#fbConnectPushEventBar_hide').click(function(){
			$('#fbConnectPushEventBar_hide').hide();
			$('#fbConnectPushEventToggles').hide();
			$('#fbConnectPushEventBar_show').show();
			return false;
		});
	});
</script>
<?php

	} // end execute()
} // end ChooseNameTemplate

