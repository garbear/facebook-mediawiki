--
-- SQL schema for FBConnect extension
--

CREATE TABLE `/*$wgDBprefix*/fbconnect_event_show` (
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) DEFAULT NULL,
  `post_time` int(11) DEFAULT NULL,
  `event_type` enum('OnAddBlogPost','OnAddImage','OnAddVideo','OnArticleComment','OnBlogComment','OnLargeEdit','OnRateArticle','OnWatchArticle','OnAchBadge') DEFAULT NULL
) ENGINE=InnoDB /*$wgDBTableOptions*/;
