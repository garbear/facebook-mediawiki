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
 * Inspired by FacebookHooks.php, this class contains entry points for Timeline
 * events. Hooks are established in FacebookInit::init().
 */
class FacebookTimeline {
	/**
	 * Given a possible action defined in $wgFbOpenGraphRegisteredActions,
	 * this function translates the action into the proper Open Graph ID
	 * (which usually looks like NAMESPACE:action).
	 */
	private static function getAction($action) {
		global $wgFbOpenGraph, $wgFbNamespace, $wgFbOpenGraphRegisteredActions;
		if ( FacebookAPI::isNamespaceSetup() && !empty( $wgFbOpenGraph ) &&
				!empty( $wgFbOpenGraphRegisteredActions ) &&
				!empty( $wgFbOpenGraphRegisteredActions[$action] ) ) {
			return $wgFbNamespace . ':' . $wgFbOpenGraphRegisteredActions[$action];
		}
		return false;
	}
	
	/**
	 * AchievementsNotification hook.
	 */
	public static function AchievementsNotification($user, $badge) {
		global $facebook;
		if ( $badge->getTypeId() != BADGE_WELCOME && self::getAction( 'earn' ) ) {
			$fbUser = new FacebookUser();
			if ( $fbUser->getMWUser()->getId() == $user->getId() ) {
				// TODO: Need to implement FacebookOpenGraph::newObjectFromBadge()
				// This requires each badge to have a URL that Facebook can scrape for meta tags
				$object = false && FacebookOpenGraph::newObjectFromBadge( $badge );
				if ( $object ) {
					try {
						// Publish the action
						$facebook->api('/' . $fbUser->getId() . '/' . self::getAction('earn'), 'POST', array(
								$object->getType() => $object->getUrl(),
						));
					} catch ( FacebookApiException $e ) {
						// echo $e->getType() . ": " . $e->getMessage() . "<br/>\n";
					}
				}
			}
		}
		return true;
	}
	
	/**
	 * Add an action to the user's Timeline when they rate an article.
	 */
	public static function ArticleAfterVote($user_id, &$page, $vote) {
		global $facebook;
		if ( self::getAction( 'rate' ) ) {
			$fbUser = new FacebookUser();
			if ( $fbUser->getMWUser()->getId() == $user_id ) {
				$article = Article::newFromID( $page );
				if ( $article instanceof Article ) {
					$object = FacebookOpenGraph::newObjectFromTitle( $article->getTitle() );
					if ( $object ) {
						try {
							// Publish the action
							$facebook->api('/' . $fbUser->getId() . '/' . self::getAction('rate'), 'POST', array(
									$object->getType() => $object->getUrl(),
							));
						} catch ( FacebookApiException $e ) {
							// echo $e->getType() . ": " . $e->getMessage() . "<br/>\n";
						}
					}
				}
			}
		}
		return true;
	}
	
	/**
	 * Action for protecting an article. Protect actions don't expire, but if
	 * the user updates the protection status of a page, a new protect action
	 * will be pushed to their Timeline and the old protect actions for the
	 * page will be removed.
	 */
	public static function ArticleProtectComplete(&$article, &$user, $protect, $reason, $moveonly = false) {
		global $facebook;
		// $protect might be a boolean, or array( [edit] => *group* [move] => *group* )
		$unprotect = is_array($protect) && empty($protect['edit']) && empty($protect['edit']);
		if ( $protect && !$unprotect && self::getAction( 'protect' ) ) {
			$fbUser = new FacebookUser();
			if ( $fbUser->getMWUser()->getId() == $user->getId() ) {
				$object = FacebookOpenGraph::newObjectFromTitle( $article->getTitle() );
				if ( $object ) {
					try {
						// Expire old protect actions
						self::removeAction( 'protect', $object );
						// Publish the action
						$facebook->api('/' . $fbUser->getId() . '/' . self::getAction('protect'), 'POST', array(
								$object->getType() => $object->getUrl(),
						));
					} catch ( FacebookApiException $e ) {
						// echo $e->getType() . ": " . $e->getMessage() . "<br/>\n";
					}
				}
			}
		}
		return true;
	}
	
	/**
	 * Hook for various kinds of actions related to article edits. Some actions
	 * included here are specific to extensions in use by Wikia and are not
	 * supported, tested, or perhaps even fully implemented.
	 */
	public static function ArticleSaveComplete(&$article, &$user, $text, $summary, $minoredit,
			$watchthis, /*unused*/ $section, &$flags, $revision, &$status, $baseRevId, &$redirect = true) {
		global $facebook;
		
		if ( self::getAction( 'edit' ) || self::getAction( 'discuss' ) ) {
			$object = FacebookOpenGraph::newObjectFromTitle( $article->getTitle() );
			
			// Blog object
			if ( $object && $object->getType() == 'blog' ) {
				// Blog post
				if ( self::getAction( 'edit' ) && $article->getTitle()->getNamespace() == NS_BLOG_ARTICLE ) {
					// Only push if it's a newly created article
					if ( $flags & EDIT_NEW ) {
						$fbUser = new FacebookUser();
						if ( $fbUser->getMWUser()->getId() == $user->getId() ) {
							try {
								// Publish the action
								$facebook->api('/' . $fbUser->getId() . '/' . self::getAction('edit'), 'POST', array(
										$object->getType() => $object->getUrl(),
								));
							} catch ( FacebookApiException $e ) {
								// echo $e->getType() . ": " . $e->getMessage() . "<br/>\n";
							}
						}
					}
				// Blog comment
				} else if (self::getAction('discuss') && $article->getTitle()->getNamespace() == NS_BLOG_ARTICLE_TALK) {
					$fbUser = new FacebookUser();
					if ( $fbUser->getMWUser()->getId() == $user->getId() ) {
						try {
							// Publish the action
							$facebook->api('/' . $fbUser->getId() . '/' . self::getAction('discuss'), 'POST', array(
									$object->getType() => $object->getUrl(),
							));
						} catch ( FacebookApiException $e ) {
							// echo $e->getType() . ": " . $e->getMessage() . "<br/>\n";
						}
					}
				}
			}
			
			// Article object
			if ( $object && $object->getType() == 'article' ) {
				// Subject page
				global $wgContentNamespaces;
				if ( self::getAction('edit') && in_array( $article->getTitle()->getNamespace(), $wgContentNamespaces ) ) {
					// Make sure something has changed
					if ( !is_null( $revision ) ) {
						// Do not push reverts
						$baseRevision = Revision::newFromId( $baseRevId );
						if ( !$baseRevision || $revision->getTextId() != $baseRevision->getTextId() ) {
							// Check for minor edit action first
							if ( !defined('MIN_CHARS_TO_EDIT') ) {
								define('MIN_CHARS_TO_EDIT', 10);
							}
							if ( self::getAction('tweak') && ( $minoredit ||
									(strlen( $text ) - strlen( $article->getRawText() ) <= MIN_CHARS_TO_EDIT) ) ) {
								$fbUser = new FacebookUser();
								if ( $fbUser->getMWUser()->getId() == $user->getId() ) {
									try {
										// Publish the action
										$facebook->api('/' . $fbUser->getId() . '/' . self::getAction('tweak'), 'POST', array(
												$object->getType() => $object->getUrl(),
										));
									} catch ( FacebookApiException $e ) {
										// echo $e->getType() . ": " . $e->getMessage() . "<br/>\n";
									}
								}
							} else if ( self::getAction('edit') ) {
								$fbUser = new FacebookUser();
								if ( $fbUser->getMWUser()->getId() == $user->getId() ) {
									try {
										// Publish the action
										$facebook->api('/' . $fbUser->getId() . '/' . self::getAction('edit'), 'POST', array(
												$object->getType() => $object->getUrl(),
										));
									} catch ( FacebookApiException $e ) {
										// echo $e->getType() . ": " . $e->getMessage() . "<br/>\n";
									}
								}
							}
						}
					}
				}
				// Talk page
				else if ( self::getAction('discuss') && $article->getTitle()->isTalkPage() ) {
					$fbUser = new FacebookUser();
					if ( $fbUser->getMWUser()->getId() == $user->getId() ) {
						try {
							// Publish the action
							$facebook->api('/' . $fbUser->getId() . '/' . self::getAction('discuss'), 'POST', array(
									$object->getType() => $object->getUrl(),
							));
						} catch ( FacebookApiException $e ) {
							// echo $e->getType() . ": " . $e->getMessage() . "<br/>\n";
						}
					}
				}
			}
			
			// Media object
			if ( $object && $object->getType() == 'file' ) {
				// Called when a user edits the summary of an image
				// Currently, we don't do anything here
			}
		}
		return true;
	}
	
	/**
	 * Adds an action to the user's Timeline when they upload an image.
	 */
	public static function UploadComplete(&$image) {
		global $facebook;
		if ( self::getAction( 'upload' ) ) {
			// Because I don't know what version mLocalFile turned into getLocalFile()
			try {
				if ( $image->getLocalFile()->fileExists ) {
					$file = $image->getLocalFile();
				} else {
					$file = false;
				}
			} catch ( Exception $e ) {
				if ( $image->mLocalFile->fileExists ) {
					$file = $image->mLocalFile;
				} else {
					$file = false;
				}
			}
			
			if ( $file ) {
				$fbUser = new FacebookUser();
				global $wgUser;
				if ( $fbUser->getMWUser()->getId() == $wgUser->getId() ) {
					$object = FacebookOpenGraph::newObjectFromFile( $file );
					if ( $object ) {
						try {
							// Publish the action
							$facebook->api('/' . $fbUser->getId() . '/' . self::getAction('upload'), 'POST', array(
									$object->getType() => $object->getUrl(),
							));
						} catch ( FacebookApiException $e ) {
							// echo $e->getType() . ": " . $e->getMessage() . "<br/>\n";
						}
					}
				}
			}
		}
		return true;
	}
	
	/**
	 * Pushes an item to the Facebook user's Timeline when they add an article
	 * to their watchlist.
	 */
	public static function WatchArticleComplete(&$user, &$article) {
		global $facebook;
		if ( self::getAction( 'watch' ) ) {
			$fbUser = new FacebookUser();
			if ( $fbUser->getMWUser()->getId() == $user->getId() ) {
				$object = FacebookOpenGraph::newObjectFromTitle( $article->getTitle() );
				if ( $object ) {
					try {
						// Publish the action
						$facebook->api('/' . $fbUser->getId() . '/' . self::getAction('watch'), 'POST', array(
								$object->getType() => $object->getUrl(),
						));
					} catch ( FacebookApiException $e ) {
						// echo $e->getType() . ": " . $e->getMessage() . "<br/>\n";
					}
				}
			}
		}
		return true;
	}
	
	/**
	 * Remove the watch action from the user's Timeline when they unwatch an
	 * article.
	 */
	public static function UnwatchArticleComplete(&$user, &$article) {
		global $facebook;
		if ( self::getAction( 'watch' ) ) {
			$fbUser = new FacebookUser();
			if ( $fbUser->getMWUser()->getId() == $user->getId() ) {
				$object = FacebookOpenGraph::newObjectFromTitle( $article->getTitle() );
				try {
					self::removeAction( 'watch', $object );
				} catch ( FacebookApiException $e ) {
					// echo $e->getType() . ": " . $e->getMessage() . "<br/>\n";
				}
			}
		}
		return true;
	}
	
	/**
	 * Sometimes an action may expire (such as when the user unlikes an
	 * article). This function requests data from Facebook and removes all
	 * occurrences of the action-object connection.
	 * 
	 * The limit is currently set at 5000. If the limit is lowered to a more
	 * practical number, the actions will be obtained recursively.
	 * 
	 * Precondition: User is logged in to Facebook and MediaWiki, and the
	 * Facebook ID matches the one associated with the MediaWiki account.
	 * 
	 * @throws FacebookApiException
	 */
	private static function removeAction($action, $object, $limit = 5000, $offset = 0, $accumulatedActions = NULL) {
		global $facebook;
		$actions = $facebook->api( '/' . $facebook->getUser() . '/' . self::getAction( $action ) .
				"?offset=$offset&limit=$limit" );
		
		if ( isset( $actions['data'] ) ) {
			if ( $accumulatedActions ) {
				$accumulatedActions = array_merge( $accumulatedActions, $actions['data'] );
			} else {
				$accumulatedActions = $actions['data'];
			}
		}
		
		if ( isset( $actions['data'] ) && count ( $actions['data'] ) == $limit ) {
			self::removeAction($action, $object,  $limit, $offset + $limit, $accumulatedActions);
		} else if ( !empty( $accumulatedActions ) ) {
			// Base case: we got all the actions
			foreach ( $accumulatedActions as $action ) {
				if ( isset($action['data']) && isset($action['data'][$object->getType()]) ) {
					if ( $action['data'][$object->getType()]['url'] == $object->getUrl() ) {
						$facebook->api( '/' . $action['id'], 'DELETE' );
					}
				}
			}
		}
		
	}
}
