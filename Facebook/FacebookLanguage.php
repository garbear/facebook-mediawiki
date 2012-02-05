<?php
/**
 * @author Sean Colombo
 *
 * This file helps with languages and internationalization (i18n) for dealing with facebook.
 * Since MediaWiki has a custom list of languages that differs from the Facebook languages, this class
 * will help with the conversion.
 *
 * No automated conversions are attempted because that could result in much stranger results than just defaulting to english.  For instance,
 * if we just searched for a facebook locale which started with the MediaWiki language code, we could attempt to deliver en_PI (Pirate English)
 * and there would be false-negatives in the other direction also (such as chr -> ck_US being missed).
 *
 * Relevant documentation:
 *	- Tutorial in making the Facebook popups from Facebook be internationalized: http://developers.facebook.com/blog/post/264
 *	- XML of Facebook's languages: http://www.facebook.com/translations/FacebookLocales.xml
 *	- Overview of where Facebook's lang-codes come from: http://wiki.developers.facebook.com/index.php/Facebook_Locales
 *	- MediaWiki i18n: http://www.mediawiki.org/wiki/Internationalization
 *	- List which has MediaWiki fallback languages all on the same page: http://www.mediawiki.org/wiki/Localization_statistics (will be helpful in building the mapping).
 *	- Comments in /languages/Names.php in MediaWiki has comments next to each mapping which should help. It is approximately RFC 3066
 */

class FacebookLanguage{

	// All of the Facebook Locales according to http://www.facebook.com/translations/FacebookLocales.xml as of 20100622
	private static $allFbLocales = array(
		'af_ZA', 'ar_AR', 'ay_BO', 'az_AZ', 'be_BY', 'bg_BG', 'bn_IN', 'bs_BA', 'ca_ES', 'ck_US', 'cs_CZ', 'cy_GB', 'da_DK', 'de_DE', 'el_GR',
		'en_GB', 'en_PI', 'en_UD', 'en_US', 'eo_EO', 'es_CL', 'es_CO', 'es_ES', 'es_LA', 'es_MX', 'es_VE', 'et_EE', 'eu_ES', 'fa_IR', 'fb_FI',
		'fb_LT', 'fi_FI', 'fo_FO', 'fr_CA', 'fr_FR', 'ga_IE', 'gl_ES', 'gn_PY', 'gu_IN', 'he_IL', 'hi_IN', 'hr_HR', 'hu_HU', 'hy_AM', 'id_ID',
		'is_IS', 'it_IT', 'ja_JP', 'jv_ID', 'ka_GE', 'kk_KZ', 'km_KH', 'kn_IN', 'ko_KR', 'ku_TR', 'la_VA', 'li_NL', 'lt_LT', 'lv_LV', 'mg_MG',
		'mk_MK', 'ml_IN', 'mn_MN', 'mr_IN', 'ms_MY', 'mt_MT', 'nb_NO', 'ne_NP', 'nl_BE', 'nl_NL', 'nn_NO', 'pa_IN', 'pl_PL', 'ps_AF', 'pt_BR',
		'pt_PT', 'qu_PE', 'rm_CH', 'ro_RO', 'ru_RU', 'sa_IN', 'se_NO', 'sk_SK', 'sl_SI', 'so_SO', 'sq_AL', 'sr_RS', 'sv_SE', 'sw_KE', 'sy_SY',
		'ta_IN', 'te_IN', 'tg_TJ', 'th_TH', 'tl_PH', 'tl_ST', 'tr_TR', 'tt_RU', 'uk_UA', 'ur_PK', 'uz_UZ', 'vi_VN', 'xh_ZA', 'yi_DE', 'zh_CN',
		'zh_HK', 'zh_TW', 'zu_ZA'
	);
	
	private static $messageKey = 'facebook-mediawiki-lang-to-fb-locale';

	/**
	 * Given a MediaWiki language code, gets a corresponding Facebook locale.
	 */
	public static function getFbLocaleForLangCode($mediaWikiLangCode) {
		$locale = 'en_US'; // default facebook locale to use
		
		if ( empty( $mediaWikiLangCode ) )
			return $locale;
		
		wfProfileIn(__METHOD__);
				
		// See if the mapping is in memcache already.  If not, figure out the mapping from the mediawiki message.
		global $wgMemc;
		$memkey = wfMemcKey( 'FacebookLanguage', self::$messageKey);
		$langMapping = $wgMemc->get($memkey);
		if(!$langMapping){
			$langMapping = array();
			//wfLoadExtensionMessages('FacebookLanguage'); // Deprecated since 1.16
			$rawMappingText = wfMsg( self::$messageKey );

			// Split the message by line.
			$lines = explode("\n", $rawMappingText);
			foreach($lines as $line){
				// Remove comments
				$index = strpos($line, "#");
				if($index !== false){
					$line = substr($line, 0, $index); // keep only the text before the comment
				}
			
				// Split the line into two pieces (if present) for the mapping.
				$tokens = explode(',', $line, 2);
				if(count($tokens) == 2){
					// Trim off whitespace
					$mwLang = trim($tokens[0]);
					$fbLocale = trim($tokens[1]);

					if(($mwLang != "") && ($fbLocale != "")){
						// Verify that this is a valid fb locale before storing (otherwise a typo in the message could break Facebook javascript by including an invalid fbScript URL).
						if(self::isValidFacebookLocale($fbLocale)){
							$langMapping[$mwLang] = $fbLocale;
						} else {
							error_log("Facebook: WARNING: Facebook Locale was found in the wiki-message but does not appear to be a Facebook Locale that we know about: \"$fbLocale\".\n");
							error_log("Facebook: Skipping locale for now.  If you want this locale to be permitted, please add it to FacebookLanguage::\$allFbLocales.\n");
						}
					}
				}
			}

			$wgMemc->set($memkey, $langMapping, 60 * 60 * 3); // cache for a while since this is fairly expensive to compute & shouldn't change often
		}

		// Use the array to find if there is a mapping from mediaWikiLangCode to a Facebook locale.
		if(isset($langMapping[$mediaWikiLangCode])){
			$locale = $langMapping[$mediaWikiLangCode];
		}

		wfProfileOut(__METHOD__);
		return $locale;
	} // end getFbLocaleForLangCode()
	
	/**
	 * Returns true if the value provided is one of the Facebook Locales that was supported according to
	 * http://www.facebook.com/translations/FacebookLocales.xml
	 * at the last time that this code was updated.  This is a manual process, so if FacebookLocales.xml changes,
	 * we will most likely be out of sync until someone brings this to our attention).
	 */
	public static function isValidFacebookLocale($locale){
		return in_array($locale, self::$allFbLocales);
	}
	
	/**
	 * This function can be run as a unit-test to test the coverage of the mapping (to make sure that there is at least a row in
	 * the MediaWiki message to potentially map to a Facebook locale.
	 */
	public static function testCoverage(){
		global $wgLanguageNames;
		$passed = true;
		
		// Split the message by line.
		$langMapping = array();
		//wfLoadExtensionMessages('FacebookLanguage'); // Deprecated since 1.16
		$rawMappingText = wfMsg( self::$messageKey );
		$lines = explode("\n", $rawMappingText);
		foreach($lines as $line){
			// Remove comments
			$index = strpos($line, "#");
			if($index !== false){
				$line = substr($line, 0, $index); // keep only the text before the comment
			}
		
			// Split the line into two pieces (if present) for the mapping.
			$tokens = explode(',', $line, 2);
			if(count($tokens) == 2){
				// Trim off whitespace
				$mwLang = trim($tokens[0]);
				$fbLocale = trim($tokens[1]);

				// NOTE: THIS DIFFERS FROM NORMAL LOADING BECAUSE WE WANT EVEN THE MAPPINGS WITH NO DESTINATION.
				if($mwLang != ""){
					// Verify that this is a valid fb locale before storing (otherwise a typo in the message could break Facebook javascript by including an invalid fbScript URL).
					$langMapping[$mwLang] = $fbLocale;
					if(($fbLocale != "") && (!self::isValidFacebookLocale($fbLocale))){
						error_log("Facebook: WARNING: Facebook Locale was found in the wiki-message but does not appear to be a Facebook Locale that we know about: \"$fbLocale\".\n");
						error_log("Facebook: Skipping locale for now.  If you want this locale to be permitted, please add it to FacebookLanguage::\$allFbLocales.\n");
					}
				}
			}
		}

		// Look through each of the MediaWiki languages.
		foreach(array_keys($wgLanguageNames) as $lang){
			if( !isset($langMapping[$lang]) ){
				$passed = false;
				error_log("Facebook: MediaWiki language \"$lang\" does not have a row for mapping it to a Facebook Locale. Add it to the MediaWiki message!\n");
			}
		}

		return $passed;
	} // end testCoverage()

}
