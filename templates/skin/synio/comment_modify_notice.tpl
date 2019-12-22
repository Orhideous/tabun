{assign var="flags" value=$oComment->getFlags()}
{assign var="isModified" value=($flags & ModuleComment_EntityComment::FLAG_MODIFIED) !== 0}
{assign var="isHardModified" value=($flags & ModuleComment_EntityComment::FLAG_HARD_MODIFIED) !== 0}
{assign var="isLocked" value=($flags & ModuleComment_EntityComment::FLAG_LOCK_MODIFY) !== 0}
{strip}
{if $isModified or $isLocked}
	{assign var="title" value=""}
	{assign var="desc" value=""}
	{if $isModified}
		{assign var="title" value=$aLang.commentEditNotice_capitalChanged}
		{assign var="editorId" value=$oComment->getLastModifyUserId()}
		{assign var="isSelfModified" value=$editorId == $oComment->getUserId()}
		{if $isHardModified or not $isSelfModified}
			{assign var="title" value="$title `$aLang.commentEditNotice_byAdmin`"}
		{else}
			{assign var="title" value="$title `$aLang.commentEditNotice_byAuthor`"}
		{/if}
		{if $isHardModified}
			{assign var="title" value="$title `$aLang.commentEditNotice_afterAnswer`"}
		{/if}
		{if $isSelfModified}
			{assign var="desc" value=$LS->Lang_Get('commentEditNotice_descChanged_byAuthor', ['date' => {date_format date=$oComment->getLastModifyDate() format="j F Y, H:i:s"}])}
		{else}
			{assign var="editor" value=$LS->User_GetUserById($editorId)}
			{if $editor != null}{assign var="editorName" value=$editor->getLogin()}{/if}
			{assign var="desc" value="$desc`$LS->Lang_Get('commentEditNotice_descChanged_byAdminFull', ['user' => $editorName,'date' => {date_format date=$oComment->getLastModifyDate() format="j F Y, H:i:s"}])`"}
		{/if}
		{assign var="lastModifyId" value=$oComment->getLastModifyId()}
	{else}
		{assign var="lastModifyId" value="0"}
	{/if}
	{if $isLocked}
		{if $isModified}
			{assign var="title" value="$title `$aLang.commentEditNotice_withLocked`"}
			{assign var="desc" value="$desc\n"}
		{else}
			{assign var="title" value=$aLang.commentEditNotice_capitalLocked}
		{/if}
		{assign var="lockerId" value=$oComment->getLockModifyUserId()}
		{assign var="locker" value=$LS->User_GetUserById($lockerId)}
		{if $locker != null}{assign var="lockerName" value=$locker->getLogin()}{/if}
		{assign var="desc" value="$desc`$LS->Lang_Get('commentEditNotice_descLocked_byAdminFull', ['user' => $lockerName,'date' => {date_format date=$oComment->getLockModifyDate() format="j F Y, H:i:s"}])`"}
	{/if}
	{assign var="desc" value="$title\n$desc"}
	<span class="modify-notice" data-locked="{if $isLocked}1{else}0{/if}" data-last-modify-id="{$lastModifyId}" title="{$desc|escape:"html"}">
	{if $isModified}<i class="vicon vicon-edit"></i>{/if}
	{if $isHardModified or not $isSelfModified}<i class="vicon vicon-admin"></i>{/if}
	{if $isHardModified}<i class="vicon vicon-answers"></i>{/if}
	{if $isLocked}<i class="vicon vicon-lock"></i>{/if}
	</span>
{/if}
{/strip}
