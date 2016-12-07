{include file='header.tpl' menu='blog'}

{include file='topic.tpl'}
{include 
	file='comment_tree.tpl' 	
	iTargetId=$oTopic->getId()
	iAuthorId=$oTopic->getUserId()
	sAuthorNotice=$aLang.topic_author
	sTargetType='topic'
	iCountComment=$oTopic->getCountComment()
	sDateReadLast=$oTopic->getDateRead()
	bAllowNewComment=$oTopic->getForbidComment()
	bAddCommentPermission=$oTopic->getIsAllowAddComment()
	bReadCommentPermission=$oTopic->getIsAllowReadComments()
	sNoticeNotAllow=$aLang.topic_comment_notallow
	sNoticeNoPermission=$aLang.topic_comment_no_permission
	sNoticeNoReadPermission=$aLang.topic_comment_no_permission_read
	sNoticeCommentAdd=$aLang.topic_comment_add
	bAllowSubscribe=true
	bNoCommentFavourites=false
	oSubscribeComment=$oTopic->getSubscribeNewComment()
}


{include file='footer.tpl' scripts=["comments"]}
