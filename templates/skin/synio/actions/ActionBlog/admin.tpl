{include file='header.tpl'}
{include file='menu.blog_edit.tpl'}



{if $aBlogUsers}
	<form method="post" enctype="multipart/form-data" class="mb-20">
		<input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
		{if $oBlogEdit->getType() == "open"}
		<div class="blog-message blog-message-notice">
			<strong>{$aLang.blog_admin_attention}</strong>
			{$aLang.blog_admin_open_blog_permissions_notice}
			<ul>
				<li>{$aLang.blog_admin_users_perm_topics_read}</li>
				<li>{$aLang.blog_admin_users_perm_comments_read}</li>
				<li>{$aLang.blog_admin_users_perm_votes_create}</li>
			</ul>
		</div>
		{/if}
		<table class="table table-users">
			<thead>
				<tr>
					<th class="cell-name">{$aLang.blog_admin_users}</th>
					<th class="ta-c">{$aLang.blog_admin_users_perm_blog}</th>
					<th class="ta-c">{$aLang.blog_admin_users_perm_topics}</th>
					<th class="ta-c">{$aLang.blog_admin_users_perm_comments}</th>
					<th class="ta-c">{$aLang.blog_admin_users_perm_votes}</th>
				</tr>
			</thead>
			
			<tbody>
				{foreach from=$aBlogUsers item=oBlogUser}
					{assign var="oUser" value=$oBlogUser->getUser()}
					{assign var="oUserBlogPermissions"    value=$oBlogUser->getBlogPermissions()}
					{assign var="oUserTopicPermissions"   value=$oBlogUser->getTopicPermissions()}
					{assign var="oUserCommentPermissions" value=$oBlogUser->getCommentPermissions()}
					{assign var="oUserVotePermissions"    value=$oBlogUser->getVotePermissions()}
					
					<tr>
						<td class="cell-name">
							<a href="{$oUser->getUserWebPath()}"><img src="{$oUser->getProfileAvatarPath(24)}" class="avatar" /></a>
							<a href="{$oUser->getUserWebPath()}">{$oUser->getLogin()}</a>
						</td>
						<td class="ta-c">
							<label title="{$aLang.blog_admin_users_perm_blog_update}">{$aLang.blog_admin_users_perm_label_update} <input type="checkbox" name="user_perm[{$oUser->getId()}][blog_update]" {if $oUserBlogPermissions->check(Permissions::UPDATE)}checked{/if} /></label>
						</td>
						<td class="ta-c">
							<label title="{$aLang.blog_admin_users_perm_topics_create}">{$aLang.blog_admin_users_perm_label_create} <input type="checkbox" name="user_perm[{$oUser->getId()}][topics_create]" {if $oUserTopicPermissions->check(Permissions::CREATE)}checked{/if} /></label>
							<label title="{$aLang.blog_admin_users_perm_topics_read}"  >{$aLang.blog_admin_users_perm_label_read}   <input type="checkbox" name="user_perm[{$oUser->getId()}][topics_read]"   {if $oUserTopicPermissions->check(Permissions::READ  )}checked{/if} /></label>
							<label title="{$aLang.blog_admin_users_perm_topics_update}">{$aLang.blog_admin_users_perm_label_update} <input type="checkbox" name="user_perm[{$oUser->getId()}][topics_update]" {if $oUserTopicPermissions->check(Permissions::UPDATE)}checked{/if} /></label>
							<label title="{$aLang.blog_admin_users_perm_topics_delete}">{$aLang.blog_admin_users_perm_label_delete} <input type="checkbox" name="user_perm[{$oUser->getId()}][topics_delete]" {if $oUserTopicPermissions->check(Permissions::DELETE)}checked{/if} /></label>
						</td>
						<td class="ta-c">
							<label title="{$aLang.blog_admin_users_perm_comments_create}">{$aLang.blog_admin_users_perm_label_create} <input type="checkbox" name="user_perm[{$oUser->getId()}][comments_create]" {if $oUserCommentPermissions->check(Permissions::CREATE)}checked{/if} /></label>
							<label title="{$aLang.blog_admin_users_perm_comments_read}"  >{$aLang.blog_admin_users_perm_label_read}   <input type="checkbox" name="user_perm[{$oUser->getId()}][comments_read]"   {if $oUserCommentPermissions->check(Permissions::READ  )}checked{/if} /></label>
							<label title="{$aLang.blog_admin_users_perm_comments_update}">{$aLang.blog_admin_users_perm_label_update} <input type="checkbox" name="user_perm[{$oUser->getId()}][comments_update]" {if $oUserCommentPermissions->check(Permissions::UPDATE)}checked{/if} /></label>
							<label title="{$aLang.blog_admin_users_perm_comments_delete}">{$aLang.blog_admin_users_perm_label_delete} <input type="checkbox" name="user_perm[{$oUser->getId()}][comments_delete]" {if $oUserCommentPermissions->check(Permissions::DELETE)}checked{/if} /></label>
						</td>
						<td class="ta-c">
							<label title="{$aLang.blog_admin_users_perm_votes_create}">{$aLang.blog_admin_users_perm_label_create} <input type="checkbox" name="user_perm[{$oUser->getId()}][votes_create]" {if $oUserVotePermissions->check(Permissions::CREATE)}checked{/if} /></label>
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>

		<button type="submit"  name="submit_blog_admin" class="button button-primary">{$aLang.blog_admin_users_submit}</button>
	</form>

	{include file='paging.tpl' aPaging=$aPaging}
{else}
	{$aLang.blog_admin_users_empty}
{/if}



{include file='footer.tpl'}
