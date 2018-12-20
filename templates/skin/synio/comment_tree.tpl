{add_block group='toolbar' name='toolbar_comment.tpl'
	iTargetId=$iTargetId
	sTargetType=$sTargetType
	iMaxIdComment=$iMaxIdComment
}
{if $oUserCurrent and $sTargetType == 'topic' and $oBlog}
	{assign var="bAllowUserToEditBlogComments" value=$LS->ACL_IsAllowEditComments($oBlog, $oUserCurrent)}
{else}
	{assign var="bAllowUserToEditBlogComments" value=false}
{/if}
{hook run='comment_tree_begin' iTargetId=$iTargetId sTargetType=$sTargetType}

<div class="comments{if !$bAllowNewComment} comments-allowed{/if}{if $oUserCurrent and $oUserCurrent->isAdministrator()} is-admin{/if}{if $bAllowUserToEditBlogComments} is-moder{/if}{if $bVoteInfoEnabledForComments} vote-info-enabled{/if}" id="comments">
	<header class="comments-header">
		<h3><span id="count-comments">{$iCountComment}</span> <span id="name-count-comments">{t plural="comments" count=$iCountComment}comment{/t}</span></h3>
		
		{if $bAllowSubscribe and $oUserCurrent}
			<div class="subscribe">
				<input {if $oSubscribeComment and $oSubscribeComment->getStatus()}checked="checked"{/if} data-target_type="{$sTargetType}" data-target_id="{$iTargetId}" type="checkbox" id="comment_subscribe" class="input-checkbox">
				<label for="comment_subscribe">{$aLang.comment_subscribe}</label>
			</div>
		{/if}
	
		<a name="comments"></a>
	</header>

	{assign var="nesting" value="-1"}
	{foreach from=$aComments item=oComment name=rublist}
		{assign var="cmtlevel" value=$oComment->getLevel()}

		{if $nesting < $cmtlevel}
		{elseif $nesting > $cmtlevel}
			{section name=closelist1  loop=$nesting-$cmtlevel+1}</div>{/section}
		{elseif not $smarty.foreach.rublist.first}
			</div>
		{/if}

		<div class="comment-wrapper comment-level-{$cmtlevel}" id="comment_wrapper_id_{$oComment->getId()}">

		{include file='comment.tpl'}
		{assign var="nesting" value=$cmtlevel}
		{if $smarty.foreach.rublist.last}
			{section name=closelist2 loop=$nesting+1}</div>{/section}
		{/if}
	{/foreach}
</div>

{hook run='comment_tree_end' iTargetId=$iTargetId sTargetType=$sTargetType}

{if $bAllowNewComment}
	<div class="comments-not-allowed">{$sNoticeNotAllow}</div>
{else}
	{if $oUserCurrent}

		{include file='editor.tpl' sImgToLoad='form_comment_text'}
	
		<h4 class="reply-header" id="comment_id_0">
			<a class="link-dotted">{$sNoticeCommentAdd}</a>
		</h4>
		
		<div id="reply" class="reply h-hidden">
			<form method="post" id="form_comment" onsubmit="return false;" enctype="multipart/form-data">
				{hook run='form_add_comment_begin'}

				<textarea name="comment_text" id="form_comment_text" class="markitup-editor input-width-full"></textarea>

				{hook run='form_add_comment_end'}
				
				<button type="submit" name="submit_comment" id="comment-button-submit" data-target_type="{$sTargetType}" data-target_id="{$iTargetId}" class="button button-primary">{$aLang.comment_add}</button>
				<button type="button" id="comment-button-preview" class="button">{$aLang.comment_preview}</button>
				<input type="hidden" name="reply" value="0" id="form_comment_reply" />
				<input type="hidden" name="cmt_target_id" value="{$iTargetId}" />
			</form>
		</div>
	{else}
		<div class="comments-not-allowed">{$aLang.comment_unregistered}</div>
	{/if}
{/if}
<div data-parent_id="" data-quote="" id="quote" style="display: none;"><i>&nbsp;</i>цитировать<b>&nbsp;</b></div>
<div id="hidden-message" class="h-hidden">Скрыто <b></b> <span></span> <a>Показать</a></div>
