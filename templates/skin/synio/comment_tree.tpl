{add_block group='toolbar' name='toolbar_comment.tpl'
	iTargetId=$iTargetId
	sTargetType=$sTargetType
	iMaxIdComment=$iMaxIdComment
}

{hook run='comment_tree_begin' iTargetId=$iTargetId sTargetType=$sTargetType}

{if $bReadCommentPermission}

	<div class="comments" id="comments">
		<header class="comments-header">
			<h3><span id="count-comments">{$iCountComment}</span> <span id="name-count-comments">{t plural="comments" count=$iCountComment}comment{/t}</span></h3>
		
			{if $bAllowSubscribe and $oUserCurrent}
				<div class="subscribe">
					<input {if $oSubscribeComment and $oSubscribeComment->getStatus()}checked="checked"{/if} type="checkbox" id="comment_subscribe" class="input-checkbox" onchange="ls.subscribe.toggle('{$sTargetType}_new_comment','{$iTargetId}','',this.checked);">
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
	{elseif !$oUserCurrent}
		<div class="comments-not-allowed">{$aLang.comment_unregistered}</div>
	{elseif !$bAddCommentPermission}
		<div class="comments-not-allowed">{$sNoticeNoPermission}</div>
	{else}
		{include file='editor.tpl' sImgToLoad='form_comment_text'}

		<h4 class="reply-header" id="comment_id_0">
			<a href="#" class="link-dotted" onclick="ls.comments.toggleCommentForm(0); return false;">{$sNoticeCommentAdd}</a>
		</h4>
	
		<div id="reply" class="reply h-hidden">
			<form method="post" id="form_comment" onsubmit="return false;" enctype="multipart/form-data">
				{hook run='form_add_comment_begin'}
			
				<textarea name="comment_text" id="form_comment_text" class="markitup-editor input-width-full"></textarea>
			
				{hook run='form_add_comment_end'}
			
				<button type="submit"  name="submit_comment" 
						id="comment-button-submit" 
						onclick="ls.comments.add('form_comment',{$iTargetId},'{$sTargetType}'); return false;" 
						class="button button-primary">{$aLang.comment_add}</button>
				<button type="button" onclick="ls.comments.preview();" class="button">{$aLang.comment_preview}</button>
			
				<input type="hidden" name="reply" value="0" id="form_comment_reply" />
				<input type="hidden" name="cmt_target_id" value="{$iTargetId}" />
			</form>
		</div>
	{/if}	
{else}
	<div class="comments-not-allowed">{$sNoticeNoReadPermission}</div>
{/if}

