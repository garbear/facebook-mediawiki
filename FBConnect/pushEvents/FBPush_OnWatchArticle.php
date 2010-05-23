<?php
/**
 * @author Sean Colombo
 *
 * Pushes an item to Facebook News Feed when the user adds an article to their watchlist.
 */

global $wgExtensionMessagesFiles;
$pushDir = dirname(__FILE__) . '/';
$wgExtensionMessagesFiles['FBPush_OnWatchArticle'] = $pushDir . "FBPush_OnWatchArticle.i18n.php";

class FBPush_OnWatchArticle extends FBConnectPushEvent {
	protected $isAllowedUserPreferenceName = 'fbconnect-push-allow-OnWatchArticle'; // must correspond to an i18n message that is 'tog-[the value of the string on this line]'.
	
	public function init(){
		global $wgHooks;
		wfProfileIn(__METHOD__);

		$wgHooks['ArticleSaveComplete'][] = 'FBPush_OnWatchArticle::articleCountPagesAddedInLastHour';
		wfLoadExtensionMessages('FBPush_OnWatchArticle');
		
		wfProfileOut(__METHOD__);
	}
	
	
	public static function articleCountPagesAddedInLastHour(&$article, &$user, $text, $summary,$flag, $fake1, $fake2, &$flags, $revision, &$status, $baseRevId){
		global $wgContentNamespaces;
		wfProfileIn(__METHOD__);
		if( in_array($article->getTitle()->getNamespace(), $wgContentNamespaces) ) {
			self::pushEvent($article->getTitle()->getText(), $article->getTitle()->getFullURL(), "Read more");
		}
		wfProfileOut(__METHOD__);
		return true;
	}
}
