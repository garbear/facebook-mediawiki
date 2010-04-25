<?php
/**
 * @author Sean Colombo
 *
 * This class is an extendable superclass for events to push to a facebook news-feed.
 *
 * To create a push event, override this class, then add it to config.php in the way
 * defined in config.sample.php.
 */

$wgExtensionFunctions[] = 'FBConnectPushEvent::initExtension';

class FBConnectPushEvent {
	protected $isAllowedUserPreferenceName = ''; // implementing classes MUST override this with their own value.

	/**
	 * Accessor for the user preference to which (if set to 1) allows this type of event
	 * to be used.
	 */
	public function getUserPreferenceName(){
		return $this->isAllowedUserPreferenceName;
	}
	
	/**
	 * Initialize the extension itself.  This includes creating the user-preferences for
	 * the push events.
	 */
	static public function initExtension(){
		wfProfileIn(__METHOD__);

		// TODO: This initialization should only be run if the user is fb-connected.  Otherwise, the same Connect form as Special:Connect should be shown.
		// TODO: This initialization should only be run if the user is fb-connected.  Otherwise, the same Connect form as Special:Connect should be shown.

		
		$PREFERENCES_TAB_NAME = "fbconnect-prefstext"; // this must correspond to the name of the message for the text on the tab itself.

		// Adds the user-preferences (making use of the "PreferencesExtension" extension).
		/*
		$checkBoxName = "fbtest";
		wfAddPreferences(array(
			array(
				"name" => $checkBoxName,
				"section" => $PREFERENCES_TAB_NAME,
				"type" => PREF_TOGGLE_T,
				//"size" => "", // Not relevant to this type.
				//"html" => "",
				//"min" => "",
				//"max" => "",
				//"validate" => "",
				//"save" => "",
				//"load" => "",
				"default" => "",
			)
		));
		*/
		
		
		
		
		
		wfProfileOut(__METHOD__);
	}

	/**
	 * This static function is called by the FBConnect extension if push events are enabled.  It checks
	 * to make sure that the configured push-events are valid and then gives them each a chance to initialize.
	 */
	static public function initAll(){
		global $fbPushEventClasses;
		if(!empty($fbPushEventClasses)){
			// Fail fast (and hard) if a push event was coded incorrectly.
			foreach($fbPushEventClasses as $pushEventClassName){
				$pushObj = new $pushEventClassName;
				$className = get_class();
				$prefName = $pushObj->getUserPreferenceName();
				if(empty($prefName)){
					$dirName = dir( __FILE__ );
					$msg = "FATAL ERROR: The push event class <strong>\"$pushEventClassName\"</strong> does not return a valid user preference name! ";
					$msg.= " It was probably written incorrectly.  Either fix the class or remove it from being used in <strong>$dirName/config.php</strong>";
					die($msg);
				} else if(!is_subclass_of($className)){
					$msg = "FATAL ERROR: The push event class <strong>\"$pushEventClassName\"</strong> is not a subclass of <strong>$className</strong>! ";
					$msg.= " It was probably written incorrectly.  Either fix the class or remove it from being used in <strong>$dirName/config.php</strong>";
					die($msg);
				}
				
				// The push event is valid, let it initialize itself if needed.
				$pushObj->init();
			}
		}
	}

	/**
	 * Overridable function to do any initialization needed by the push event.
	 *
	 * This is only called if this particular push-event is enabled in config.php
	 * and the getUserPreferenceName() call checks out (the result must be non-empty).
	 */
	public function init(){}


} // end FBConnectPushEvent class
