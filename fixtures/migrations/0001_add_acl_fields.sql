ALTER TABLE `ls_blog_user`
  ADD COLUMN `user_blog_permissions`    SMALLINT UNSIGNED  NULL  DEFAULT NULL,
  ADD COLUMN `user_topic_permissions`   SMALLINT UNSIGNED  NULL  DEFAULT NULL,
  ADD COLUMN `user_comment_permissions` SMALLINT UNSIGNED  NULL  DEFAULT NULL,
  ADD COLUMN `user_vote_permissions`    SMALLINT UNSIGNED  NULL  DEFAULT NULL;
