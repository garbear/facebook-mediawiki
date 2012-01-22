<?php
/*
 * Copyright © 2012 Garrett Brown <http://www.mediawiki.org/wiki/User:Gbruin>
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
 * with this program. If not, see <http://www.gnu.org/licenses/>.
 */


/*
 * Not a valid entry point, skip unless MEDIAWIKI is defined.
 */
if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}


/**
 * Class FacebookTimeline
 */
class FacebookTimeline {
	/**
	 * Install the hooks for Timeline events.
	 */
	public static function init() {
		$hooks = FacebookInit::enumMethods( 'FacebookTimelineHooks' );
		foreach( $hooks as $hookName ) {
			$wgHooks[$hookName][] = "FacebookTimelineHooks::$hookName";
		}
	}
}


/**
 * Inspired by FacebookHooks.php, this class contains entry points for Timeline
 * events.
 */
class FacebookTimelineHooks {
	/**
	 * AchievementsNotification hook.
	 * @author Garrett Brown
	 * @author Sean Colombo
	 */
	public static function AchievementsNotification($user, $badg) {
		global $wgSitename;
		
		if ( $badg->getTypeId() != BADGE_WELCOME ) {
			$name = $badg->getName();
			$img  = $badg->getPictureUrl();
			$desc = $badg->getPersonalGivenFor();
			$title = Title::makeTitle( NS_USER, $user->getName() );
			$params = array(
				'$ACHIE_NAME'  => $name,
				'$ARTICLE_URL' => $title->getFullUrl("ref=fbfeed&fbtype=achbadge"),
				'$WIKINAME'    => $wgSitename,
				'$EVENTIMG'    => $img,
				'$DESC'        => $desc
			);
			FacebookPushEvent::pushEvent('facebook-msg-OnAchBadge', $params, 'FBPush_OnAchBadge');
		}
		
		return true;
	}
	
	/**
	 * Adds an event to the Facebook user's Timeline when they rate an article.
	 * @author Garrett Brown
	 * @author Sean Colombo
	 */
	public static function ArticleAfterVote($user_id, &$page, $vote) {
		global $wgSitename;
		$article = Article::newFromID( $page );
		$params = array(
			'$ARTICLENAME' => $article->getTitle()->getText(),
			'$WIKINAME'    => $wgSitename,
			'$ARTICLE_URL' => $article->getTitle()->getFullURL('ref=fbfeed&fbtype=ratearticle'),
			'$RATING'      => $vote,
			'$EVENTIMG'    => 'rating.png',
			'$TEXT'        => FacebookPushEvent::shortenText(FacebookPushEvent::parseArticle($article)),
			'$ARTICLE_OBJ' => $article,
		);
		FacebookPushEvent::pushEvent('facebook-msg-OnRateArticle', $params, 'FBPush_OnRateArticle' );
		return true;
	}
	
	/**
	 * Adds an event to the Facebook user's Timeline when they add a video to the site.
	 * @author Garrett Brown
	 * @author Sean Colombo
	 */
	public static function ArticleSave(&$article, &$user, &$text, &$summary, $minor,
			$watchthis, /*unused*/ $section, &$flags, &$status) {
		global $wgContentNamespaces, $wgSitename;
		
		if ( in_array( $article->getTitle()->getNamespace(), $wgContentNamespaces ) ) {
			$matches = array();
			$expr = "/\[\[\s*(video):.*?\]\]/i";
			$wNewCount = preg_match_all( $expr, $newText, $matches );
			$wOldCount = preg_match_all( $expr,  $article->getRawText(), $matches );
			$countDiff = $wNewCount - $wOldCount;
			if ($countDiff > 0) {
				$params = array(
					'$ARTICLENAME' => $article->getTitle()->getText(),
					'$WIKINAME'    => $wgSitename,
					'$ARTICLE_URL' => $article->getTitle()->getFullURL("ref=fbfeed&fbtype=addvideo"),
					'$EVENTIMG'    => 'video.png'
				);
				FacebookPushEvent::pushEvent('facebook-msg-OnAddVideo', $params, 'FBPush_OnAddVideo');
			}
		}
		
		return true;
	}
	
	/**
	 * ArticleSaveComplete hook.
	 * @author Garrett Brown
	 * @author Sean Colombo
	 */
	public static function ArticleSaveComplete(&$article, &$user, $text, $summary, $minoredit,
			$watchthis, /*unused*/ $section, &$flags, $revision, &$status, $baseRevId, &$redirect) {
		global $wgSitename;
		
		// Blog post
		if ( strlen( $article->getId() ) ) {
			// Only push if it's a newly created article
			if ( $flags & EDIT_NEW ) {
				if (defined('NS_BLOG_ARTICLE') && $article->getTitle()->getNamespace() == NS_BLOG_ARTICLE) {
					$params = array(
						'$WIKINAME'      => $wgSitename,
						'$BLOG_POST_URL' => $article->getTitle()->getFullURL('ref=fbfeed&fbtype=blogpost'),
						'$BLOG_PAGENAME' => $article->getTitle()->getText(),
						'$ARTICLE_URL'   => $article->getTitle()->getFullURL('ref=fbfeed&fbtype=blogpost'), // Internal use
						'$EVENTIMG'      => 'blogpost.png',
						'$TEXT'          => FacebookPushEvent::shortenText(FacebookPushEvent::parseArticle($article))
					);
					FacebookPushEvent::pushEvent('facebook-msg-OnAddBlogPost', $params, 'FBPush_OnAddBlogPost');
				}
			}
		}
		
		// Article comment
		global $wgEnableArticleCommentsExt;
		if ( !empty( $wgEnableArticleCommentsExt ) ) {
			if ( $article->getTitle()->getNamespace() == NS_TALK && ($flags & EDIT_NEW) ) {
				$title = explode('/', $article->getTitle()->getText());
				$title = $title[0];
				$title = Title::newFromText($title);
				$id = $article->getId();
				$params = array(
					'$ARTICLENAME' => $title->getText(),
					'$WIKINAME'    => $wgSitename,
					'$ARTICLE_URL' => $title->getFullURL('ref=fbfeed&fbtype=articlecomment'),
					'$EVENTIMG'    => 'comment.png',
					'$TEXT'        => FacebookPushEvent::shortenText(FacebookPushEvent::parseArticle($article))
				);
				FacebookPushEvent::pushEvent('facebook-msg-OnArticleComment', $params, 'FBPush_OnArticleComment');
			}
		}
		
		// Blog comment
		if( defined('NS_BLOG_ARTICLE_TALK') && $article->getTitle()->getNamespace() == NS_BLOG_ARTICLE_TALK ) {
			$title_explode = explode('/', $article->getTitle()->getText());
			$title = Title::newFromText($title_explode[0] . '/' . $title_explode[1], NS_BLOG_ARTICLE);
			$id = $article->getId();
			$params = array(
				'$WIKINAME'      => $wgSitename,
				'$BLOG_POST_URL' => $title->getFullURL(),
				'$BLOG_PAGENAME' =>	$title_explode[0] . '/' . $title_explode[1],
				'$ARTICLE_URL'   => $title->getFullURL('ref=fbfeed&fbtype=blogcomment'), // Internal use
				'$EVENTIMG'      => 'blogpost.png',
				'$TEXT'          => FacebookPushEvent::shortenText(FacebookPushEvent::parseArticle($article))
			);
			FacebookPushEvent::pushEvent('facebook-msg-OnBlogComment', $params, 'FBPush_OnBlogComment');
		}
		
		// Large edit
		global $wgContentNamespaces;
		// Make sure something has changed
		if ( !is_null( $revision ) ) {
			if ( in_array( $article->getTitle()->getNamespace(), $wgContentNamespaces ) ) {
				// Do not push reverts
				$baseRevision = Revision::newFromId( $baseRevId );
				if ( !$baseRevision || $revision->getTextId() != $baseRevision->getTextId() ) {
					$diff = 0;
					$wNewCount = strlen( $text );
					$wOldCount = strlen( $article->getRawText() );
					$countDiff = $wNewCount - $wOldCount;
					if ( $countDiff > FacebookPushEvent::$MIN_CHARS_TO_PUSH ) {
						$params = array(
							'$ARTICLENAME' => $article->getTitle()->getText(),
							'$WIKINAME'    => $wgSitename,
							'$ARTICLE_URL' => $article->getTitle()->getFullURL('ref=fbfeed&fbtype=largeedit'),
							'$EVENTIMG'    => 'edits.png',
							'$SUMMART'     => $summary,
							'$TEXT'        => FacebookPushEvent::shortenText(FacebookPushEvent::parseArticle($article, $text))
						);
						FacebookPushEvent::pushEvent('facebook-msg-OnLargeEdit', $params, 'FBPush_OnLargeEdit');
					}
				}
			}
		}
		
		// Add image
		if ( $article->getTitle()->getNamespace() == NS_FILE ) {
			$img = wfFindFile( $article->getTitle()->getText() );
			if ( !empty( $img ) && $img->media_type == 'BITMAP' ) {
				self::uploadNews($img, $img->title->getText(), $img->title->getFullUrl('?ref=fbfeed&fbtype=addimage'));
			}
		}
		
		return true;
	}
	
	/**
	 * Pushes an item to Facebook News Feed when the user adds an Image to the site.
	 * @author Garrett Brown
	 * @author Sean Colombo
	 */
	public static function UploadComplete(&$image) {
		global $wgServer, $wgSitename;
		$localFile = $image->getLocalFile();
		if ( $localFile->mLocalFile->media_type == 'BITMAP' ) {
			self::uploadNews($localFile, $localFile->getTitle(), $localFile->getTitle()->getFullUrl('?ref=fbfeed&fbtype=addimage'));
		}
		return true;
	}
	
	/**
	 * Called from ArticleSaveComplete() and UploadComplete().
	 * @author Garrett Brown
	 * @author Sean Colombo
	 */
	private static function uploadNews($image, $name, $url) {
		global $wgSitename;
		
		$is = new imageServing( array(), 90 );
		$thumb_url = $is->getThumbnails( array( $image ) );
		$thumb_url = array_pop( $thumb_url );
		$thumb_url = $thumb_url['url'];
		$params = array(
			'$IMGNAME'     => $name,
			'$ARTICLE_URL' => $url, // Internal use
			'$WIKINAME'    => $wgSitename,
			'$IMG_URL'     => $url,
			'$EVENTIMG'    => $thumb_url,
		);
		FacebookPushEvent::pushEvent('facebook-msg-OnAddImage', $params, 'FBPush_OnAddImage');
		
		return true;
	}
	
	/**
	 * Pushes an item to the Facebook user's Timeline when they add an article
	 * to their watchlist.
	 * @author Garrett Brown
	 * @author Sean Colombo
	 */
	public static function WatchArticleComplete(&$user, &$article) {
		global $wgSitename, $wgRequest;
		
		if ( $wgRequest->getVal( 'action', '' ) != 'submit' ) {
			if ( $article->getTitle()->getFirstRevision() ) {
				$params = array(
					'$ARTICLENAME' => $article->getTitle()->getText(),
					'$WIKINAME'    => $wgSitename,
					'$ARTICLE_URL' => $article->getTitle()->getFullURL('ref=fbfeed&fbtype=watcharticle'),
					'$EVENTIMG'    => 'following.png',
					'$TEXT'        => FacebookPushEvent::shortenText(FacebookPushEvent::parseArticle($article))
				);
				FacebookPushEvent::pushEvent('facebook-msg-OnWatchArticle', $params, 'FBPush_OnWatchArticle');
			}
		}
		
		return true;
	}
}
