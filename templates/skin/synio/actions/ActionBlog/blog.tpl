{include file='header.tpl'}
{assign var="oUserOwner" value=$oBlog->getOwner()}
{assign var="oVote" value=$oBlog->getVote()}
{assign var="oBlogId" value=$oBlog->getId()}
{assign var="oBlogRating" value=$oBlog->getRating()}

{if $oUserCurrent and $oUserCurrent->isAdministrator()}
	<div id="blog_delete_form" class="modal">
		<header class="modal-header">
			<h3>{$aLang.blog_admin_delete_title}</h3>
			<a href="#" class="close jqmClose"></a>
		</header>

		<form action="{router page='blog'}delete/{$oBlogId}/" method="POST" class="modal-content">
			<p><label for="topic_move_to">{$aLang.blog_admin_delete_move}:</label>
			<select name="topic_move_to" id="topic_move_to" class="input-width-full">
				<option value="-1">{$aLang.blog_delete_clear}</option>
				{if $aBlogs}
					<optgroup label="{$aLang.blogs}">
						{foreach from=$aBlogs item=oBlogDelete}
							<option value="{$oBlogDelete->getId()}">{$oBlogDelete->getTitle()|escape:'html'}</option>
						{/foreach}
					</optgroup>
				{/if}
			</select></p>
			
			<input type="hidden" value="{$LIVESTREET_SECURITY_KEY}" name="security_ls_key" />
			<button type="submit"  class="button button-primary">{$aLang.blog_delete}</button>
		</form>
	</div>
{/if}

<div class="blog-top">
	<h2 class="page-header">{$oBlog->getTitle()|escape:'html'} {if $oBlog->getType()=='close'} <i title="{$aLang.blog_closed}" class="icon-synio-topic-private"></i>{/if}</h2>

	<div id="vote_area_blog_{$oBlogId}" class="vote-topic 
															{if $oBlogRating > 0}
																vote-count-positive
															{elseif $oBlogRating < 0}
																vote-count-negative
															{elseif $oBlogRating == 0}
																vote-count-zero
															{/if}
															{if $oVote} 
																voted 
																{if $oVote->getDirection() > 0}
																	voted-up
																{elseif $oVote->getDirection() < 0}
																	voted-down
																{/if}
															{else}
																not-voted
															{/if}
															{if $oUserCurrent and !$oVote and $oBlog->getOwnerId()!=$oUserCurrent->getId() and $LS->ACL_CanVoteBlog($oUserCurrent, $oBlog)} vote-enabled{/if}
															{if $bVoteInfoEnabledForBlog} vote-info-enabled{/if}
															">
		{assign var="iBlogCountVote" value=$oBlog->getCountVote()}
		<div class="vote-item vote-up" data-direction="1" data-target_id="{$oBlogId}" data-target_type="blog"></div>
		<span class="vote-count" title="{$aLang.blog_vote_count}: {$iBlogCountVote}" data-count="{$iBlogCountVote}" id="vote_total_blog_{$oBlogId}" data-target_id="{$oBlogId}" data-target_type="blog">{if $oBlogRating > 0}+{/if}{$oBlogRating}</span>
		<div class="vote-item vote-down" data-direction="-1" data-target_id="{$oBlogId}" data-target_type="blog"></div>
	</div>
</div>

<div class="blog-mini" id="blog-mini">
		{if $oUserCurrent and $oUserCurrent->getId()!=$oBlog->getOwnerId()}
			<button type="submit"  class="button button-small" id="button-blog-join-first-{$oBlogId}" data-button-additional="button-blog-join-second-{$oBlogId}" data-only-text="1" onclick="ls.blog.toggleJoin(this, {$oBlogId}); return false;">{if $oBlog->getUserIsJoin()}{$aLang.blog_leave}{else}{$aLang.blog_join}{/if}</button>
		{/if}
	<span id="blog_user_count_{$oBlogId}">{$iCountBlogUsers}</span> {$iCountBlogUsers|declension:$aLang.reader_declension:'russian'},
	{$oBlog->getCountTopic()} {$oBlog->getCountTopic()|declension:$aLang.topic_declension:'russian'}
	<div class="h-float-right" id="blog-mini-header">
		<a href="#" class="link-dotted" onclick="ls.blog.toggleInfo(); return false;">{$aLang.blog_expand_info}</a>
		<a href="{router page='rss'}blog/{$oBlog->getUrl()}/">RSS</a>
	</div>
	{if $oUserCurrent}
		<div class="user-role">
			{if $oUserCurrent->getId()==$oBlog->getOwnerId()}
				<div class="system-message-notice">Вы создатель этого блога</div>
			{else}
				{assign var="oBlogUser" value=$LS->Blog_GetBlogUserByBlogIdAndUserId($oBlogId,$oUserCurrent->getId())}
				{if $oBlogUser}
					{if $oBlogUser->getIsAdministrator()}
						<div class="system-message-notice">Вы администрируете этот блог</div>
					{elseif $oBlogUser->getIsModerator()}
						<div class="system-message-notice">Вы модерируете этот блог</div>
					{elseif $oBlogUser->getUserRole()==$BLOG_USER_ROLE_USER}
						<div class="system-message-notice">Вы состоите в этом блоге</div>
					{elseif $oBlogUser->getUserRole()==$BLOG_USER_ROLE_INVITE}
						<div class="system-message-notice">Вы приглашены в этот блог</div>
					{elseif $oBlogUser->getUserRole()==$BLOG_USER_ROLE_REJECT}
						<div class="system-message-error">Вы отказались от приглашения в этот блог</div>
					{elseif $oBlogUser->getUserRole()==$BLOG_USER_ROLE_BAN}
						<div class="system-message-error">Вы забанены в этом блоге</div>
					{elseif $oBlogUser->getUserRole()==$BLOG_USER_ROLE_GUEST}
						<div class="system-message-error">Вы не состоите в этом блоге</div>
					{/if}
				{/if}
			{/if}
		</div>
	{/if}
</div>

<div class="blog" id="blog" style="display: none">
	<div class="blog-inner">
		<header class="blog-header">
			<img src="{$oBlog->getAvatarPath(48)}"  class="avatar" />
			<span class="close" onclick="ls.blog.toggleInfo(); return false;"><a href="#" class="link-dotted">{$aLang.blog_fold_info}</a><i class="icon-synio-close"></i></span>
		</header>

		<div class="blog-content text">
			<div class="blog-description">{$oBlog->getDescription()}</div>
		
			<ul class="blog-info">
				<li><span>{$aLang.infobox_blog_create}</span> <strong>{date_format date=$oBlog->getDateAdd() format="j F Y"}</strong></li>
				<li><span>{$aLang.infobox_blog_topics}</span> <strong>{$oBlog->getCountTopic()}</strong></li>
				<li><span><a href="{$oBlog->getUrlFull()}users/">{$aLang.infobox_blog_users}</a></span> <strong>{$iCountBlogUsers}</strong></li>
				<li class="rating"><span>{$aLang.infobox_blog_rating}</span> <strong>{$oBlogRating}</strong></li>
			</ul>

			{hook run='blog_info_begin' oBlog=$oBlog}
			<strong>{$aLang.blog_user_administrators} ({$iCountBlogAdministrators})</strong><br />
			<span class="user-avatar">
				<a href="{$oUserOwner->getUserWebPath()}"><img src="{$oUserOwner->getProfileAvatarPath(24)}"  /></a>
				<a href="{$oUserOwner->getUserWebPath()}">{$oUserOwner->getLogin()}</a>
			</span>
			{if $aBlogAdministrators}			
				{foreach from=$aBlogAdministrators item=oBlogUser}
					{assign var="oUser" value=$oBlogUser->getUser()}  
					<span class="user-avatar">
						<a href="{$oUser->getUserWebPath()}"><img src="{$oUser->getProfileAvatarPath(24)}"  /></a>
						<a href="{$oUser->getUserWebPath()}">{$oUser->getLogin()}</a>
					</span>
				{/foreach}	
			{/if}<br /><br />		

			<strong>{$aLang.blog_user_moderators} ({$iCountBlogModerators})</strong><br />
			{if $aBlogModerators}						
				{foreach from=$aBlogModerators item=oBlogUser}  
					{assign var="oUser" value=$oBlogUser->getUser()}							
					<span class="user-avatar">
						<a href="{$oUser->getUserWebPath()}"><img src="{$oUser->getProfileAvatarPath(24)}"  /></a>
						<a href="{$oUser->getUserWebPath()}">{$oUser->getLogin()}</a>
					</span>
				{/foreach}							
			{else}
				<span class="notice-empty">{$aLang.blog_user_moderators_empty}</span>
			{/if}
			{hook run='blog_info_end' oBlog=$oBlog}

			{if $oUserCurrent and ($oUserCurrent->getId()==$oBlog->getOwnerId() or $oUserCurrent->isAdministrator() or $oBlog->getUserIsAdministrator() )}
				<br /><br />
				<ul class="actions">
					<li>
						<a href="{router page='blog'}edit/{$oBlogId}/" title="{$aLang.blog_edit}" class="edit">{$aLang.blog_edit}</a></li>
						{if $oUserCurrent->isAdministrator()}
							<li><a href="#" title="{$aLang.blog_delete}" id="blog_delete_show" class="delete">{$aLang.blog_delete}</a>
						{else}
							<a href="{router page='blog'}delete/{$oBlogId}/?security_ls_key={$LIVESTREET_SECURITY_KEY}" title="{$aLang.blog_delete}" onclick="return confirm('{$aLang.blog_admin_delete_confirm}');" >{$aLang.blog_delete}</a>
						{/if}
					</li>
				</ul>
			{/if}
		</div>
	</div>

	<footer class="blog-footer" id="blog-footer">
		{if $oUserCurrent and $oUserCurrent->getId()!=$oBlog->getOwnerId()}
			<button type="submit"  class="button button-small" id="button-blog-join-second-{$oBlogId}" data-button-additional="button-blog-join-first-{$oBlogId}" data-only-text="1" onclick="ls.blog.toggleJoin(this, {$oBlogId}); return false;">{if $oBlog->getUserIsJoin()}{$aLang.blog_leave}{else}{$aLang.blog_join}{/if}</button>
		{/if}
		<a href="{router page='rss'}blog/{$oBlog->getUrl()}/" class="rss">RSS</a>
		
		<div class="admin">
			{$aLang.blogs_owner} —
			<a href="{$oUserOwner->getUserWebPath()}"><img src="{$oUserOwner->getProfileAvatarPath(24)}"  class="avatar" /></a>
			<a href="{$oUserOwner->getUserWebPath()}">{$oUserOwner->getLogin()}</a>
		</div>
	</footer>
</div>

{hook run='blog_info' oBlog=$oBlog}

<div class="nav-menu-wrapper">
	<ul class="nav nav-pills">
		<li {if $sMenuSubItemSelect=='good'}class="active"{/if}><a href="{$sMenuSubBlogUrl}">{$aLang.blog_menu_collective_good}</a></li>
		<li {if $sMenuSubItemSelect=='new'}class="active"{/if}><a href="{$sMenuSubBlogUrl}newall/">{$aLang.blog_menu_collective_new}</a>{if $iCountTopicsBlogNew>0} <a href="{$sMenuSubBlogUrl}new/" class="new">+{$iCountTopicsBlogNew}</a>{/if}</li>
		<li {if $sMenuSubItemSelect=='discussed'}class="active"{/if}><a href="{$sMenuSubBlogUrl}discussed/">{$aLang.blog_menu_collective_discussed}</a></li>
		<li {if $sMenuSubItemSelect=='top'}class="active"{/if}><a href="{$sMenuSubBlogUrl}top/">{$aLang.blog_menu_collective_top}</a></li>
		{hook run='menu_blog_blog_item'}
	</ul>

	{if isset($sPeriodSelectCurrent)}
		<ul class="nav nav-pills">
			<li {if $sPeriodSelectCurrent=='1'}class="active"{/if}><a href="{$sPeriodSelectRoot}?period=1">{$aLang.blog_menu_top_period_24h}</a></li>
			<li {if $sPeriodSelectCurrent=='7'}class="active"{/if}><a href="{$sPeriodSelectRoot}?period=7">{$aLang.blog_menu_top_period_7d}</a></li>
			<li {if $sPeriodSelectCurrent=='30'}class="active"{/if}><a href="{$sPeriodSelectRoot}?period=30">{$aLang.blog_menu_top_period_30d}</a></li>
			<li {if $sPeriodSelectCurrent=='all'}class="active"{/if}><a href="{$sPeriodSelectRoot}?period=all">{$aLang.blog_menu_top_period_all}</a></li>
		</ul>
	{/if}
</div>

{if $bCloseBlog}
	{$aLang.blog_close_show}
{else}
	{include file='topic_list.tpl'}
{/if}

{include file='footer.tpl'}
