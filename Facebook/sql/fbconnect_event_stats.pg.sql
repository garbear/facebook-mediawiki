--
-- Schema for the FBConnect extension (Postgres version)
--

CREATE TABLE fbconnect_event_stats (
  user_id int(10) unsigned NOT NULL,
  city_id int(10) unsigned NOT NULL,
  event_type int(5) unsigned NOT NULL,
  ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  name ENUM('OnAddBlogPost', 'OnAddImage', 'OnAddVideo','OnArticleComment','OnBlogComment','OnLargeEdit','OnRateArticle','OnWatchArticle')
);
