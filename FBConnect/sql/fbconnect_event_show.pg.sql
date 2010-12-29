--
-- Schema for the FBConnect extension (Postgres version)
--

CREATE TABLE fbconnect_event_show (
  ts timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  user_id int(11) DEFAULT NULL,
  post_time int(11) DEFAULT NULL,
  event_type enum('OnAddBlogPost','OnAddImage','OnAddVideo','OnArticleComment','OnBlogComment','OnLargeEdit','OnRateArticle','OnWatchArticle','OnAchBadge') DEFAULT NULL
);
