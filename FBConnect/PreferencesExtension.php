<?php
 
/**
 * See: http://www.mediawiki.org/wiki/Extension:PreferencesExtension
 *
 * This allows other extensions to add their own preferences to the default preferences display
 *
 * Author: Austin Che <http://openwetware.org/wiki/User:Austin_J._Che>
 */
 
$wgExtensionCredits['specialpage'][] = array(
    'name' => 'PreferencesExtension',
    'version' => '2006/11/16',
    'author' => 'Austin Che',
    'url' => 'http://openwetware.org/wiki/User:Austin_J._Che/Extensions/PreferencesExtension',
    'description' => 'Enables extending user preferences',
);
$wgHooks['SpecialPage_initList'][] = 'wfOverridePreferences';
 
// constants for pref types
define('PREF_USER_T', 1);
define('PREF_TOGGLE_T', 2);
define('PREF_TEXT_T', 3);
define('PREF_PASSWORD_T', 4);
define('PREF_INT_T', 5);
 
// each element of the following should be an array that can have keys:
// name, section, type, size, validate, load, save, html, min, max, default
if (!isset($wgExtensionPreferences))
     $wgExtensionPreferences = array();
 
/**
 * Adds an array of prefs to be displayed in the user preferences
 *
 * @param array $prefs
 */
function wfAddPreferences($prefs)
{
    global $wgExtensionPreferences;
 
    foreach ($prefs as $pref)
    {
        $wgExtensionPreferences[] = $pref;
    }
}
 
function wfOverridePreferences(&$list)
{
    // we 'override' the default preferences special page with our own
    $list["Preferences"] = array("SpecialPage", "Preferences", "", true, "wfSpecialPreferencesExtension");
    return true;
}
 
function wfSpecialPreferencesExtension()
{
	// Should be able to auto-load this.
	//global $IP;
    //require_once($IP.'SpecialPreferences.php');
 
    // override the default preferences form
    class SpecialPreferencesExtension extends PreferencesForm
    {
        // unlike parent, we don't load in posted form values in constructor
        // until savePreferences when we need it
        // we also don't need resetPrefs, instead loading the newest values when displaying the form
        // finally parent's execute function doesn't need overriding
        // this leaves only two functions to override
        // one for displaying the form and one for saving the values
 
        function savePreferences() 
        {    
            // handle extension prefs first
            global $wgUser, $wgRequest;
            global $wgExtensionPreferences;
 
            foreach ($wgExtensionPreferences as $p)
            {
                $name = isset($p['name']) ? $p['name'] : "";
                if (! $name)
                    continue;
 
                $value = $wgRequest->getVal($name);
                $type = isset($p['type']) ? $p['type'] : PREF_USER_T;
                switch ($type)
                {
                    case PREF_TOGGLE_T:
                        if (isset($p['save']))
                            $p['save']($name, $value);
                        else
                            $wgUser->setOption($name, $wgRequest->getCheck("wpOp{$name}"));
                        break;
 
                    case PREF_INT_T:
                        $min = isset($p['min']) ? $p['min'] : 0;
                        $max = isset($p['max']) ? $p['max'] : 0x7fffffff;
                        if (isset($p['save']))
                            $p['save']($name, $value);
                        else
                            $wgUser->setOption($name, $this->validateIntOrNull($value, $min, $max));
                        break;
 
                    case PREF_PASSWORD_T:
                    case PREF_TEXT_T:
                    case PREF_USER_T:
                    default:
                        if (isset($p['validate']))
                            $value = $p['validate']($value);
                        if (isset($p['save']))
                            $p['save']($name, $value);
                        else
                            $wgUser->setOption($name, $value);
                        break;
                }
            }
 
            // call parent's function which saves the normal prefs and writes to the db
            parent::savePreferences();
        }
 
        function mainPrefsForm( $status , $message = '' )
        {
            global $wgOut, $wgRequest, $wgUser;
            global $wgExtensionPreferences;
 
            // first get original form, then hack into it new options
            parent::mainPrefsForm($status, $message);
            $html = $wgOut->getHTML();
            $wgOut->clearHTML();
 
            $sections = array();
            foreach ($wgExtensionPreferences as $p)
            {
                if (! isset($p['section']) || ! $p['section'])
                    continue;
                 $section = $p['section'];
 
                $name = isset($p['name']) ? $p['name'] : "";
                $value = "";
                if ($name)
                {
                    if (isset($p['load']))
                        $value = $p['load']($name);
                    else
                        $value = $wgUser->getOption($name);
                }
                if ($value === '' && isset($p['default']))
                    $value = $p['default'];
 
                $sectext = htmlspecialchars(wfMsg($section));
                $regex = "/(<fieldset>\s*<legend>\s*" . preg_quote($sectext) . 
                    "\s*<\/legend>.*?)(<\/fieldset>)/s";
 
                // check if $section exists in prefs yet
                // cache the existence of sections
                if (!isset($sections[$section]))
                {
                    $sections[$section] = true;
 
                    if (! preg_match($regex, $html, $m))
                    {
                        // doesn't exist so add an empty section to end
                        $addhtml = "<fieldset><legend>$sectext</legend></fieldset>";
                        $html = preg_replace("/(<div id='prefsubmit'.*)/s", "$addhtml $1", $html);
                    }
 
                }
 
                $type = isset($p['type']) ? $p['type'] : PREF_USER_T;
                switch ($type)
                {
                    case PREF_TOGGLE_T:
                        $addhtml = $this->getToggle($name);
                        break;
 
                    case PREF_INT_T:
                    case PREF_TEXT_T:
                    case PREF_PASSWORD_T:
                        $size = isset($p['size']) && $p['size'] ? "size=\"{$p['size']}\"" : "";
                        $caption = isset($p['caption']) && $p['caption'] ? $p['caption'] : wfMsg($name);
                        if ($type == PREF_PASSWORD_T)
                            $type = "password";
                        else
                            $type = "text";
                        $addhtml = "<table>" . 
                            $this->addRow("<label for=\"{$name}\">$caption</label>",
                                          "<input type=\"$type\" name=\"{$name}\" value=\"{$value}\" $size />") . "</table>" ;
                        break;
 
                    case PREF_USER_T:
                    default:
                        $addhtml = preg_replace("/@VALUE@/", $value, isset($p['html']) ? $p['html'] : "");
                        break;
                }
 
                // the section exists
                $html = preg_replace($regex, "$1 $addhtml $2", $html);
            }
 
            $wgOut->addHTML($html);
 
            // debugging
            //$wgOut->addHTML($wgUser->encodeOptions());
        }
    }
 
    global $wgRequest;
    $prefs = new SpecialPreferencesExtension($wgRequest);
    $prefs->execute();
}
