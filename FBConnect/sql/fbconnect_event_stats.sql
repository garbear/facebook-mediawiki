--
-- SQL schema for FBConnect extension
--

CREATE TABLE /*$wgDBprefix*/fbconnect_event_stats (
  user_id int(10) unsigned NOT NULL,
  city_id int(10) unsigned NOT NULL,
  `status` int(10) unsigned NOT NULL,
  ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  event_type ENUM('OnAddBlogPost', 'OnAddImage', 'OnAddVideo','OnArticleComment','OnBlogComment','OnLargeEdit','OnRateArticle','OnWatchArticle')
) /*$wgDBTableOptions*/;
