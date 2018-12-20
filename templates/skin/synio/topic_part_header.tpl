{assign var="oBlog" value=$oTopic->getBlog()}
{assign var="oUser" value=$oTopic->getUser()}
{assign var="oVote" value=$oTopic->getVote()}
{assign var="oTopicId" value=$oTopic->getId()}
{assign var="oTopicRating" value=$oTopic->getRating()}

{if $oVote || ($oUserCurrent && $oTopic->getUserId() == $oUserCurrent->getId()) || strtotime($oTopic->getDateAdd()) < $smarty.now-$oConfig->GetValue('acl.vote.topic.limit_time')}
	{assign var="bVoteInfoShow" value=true}
{/if}

<article class="topic topic-type-{$oTopic->getType()} js-topic">
    <header class="topic-header">
        {strip}
            <h1 class="topic-title word-wrap">
                {if $bTopicList}
                    <a href="{$oTopic->getUrl()}">{$oTopic->getTitle()|escape:'html'}</a>
                {else}
                    {$oTopic->getTitle()|escape:'html'}
                {/if}

                {if $oTopic->getPublish() == 0}
                    <i class="icon-synio-topic-draft" title="{$aLang.topic_unpublish}"></i>
                {/if}
            </h1>
		{/strip}
		<div class="topic-info">
			<div class="topic-info-vote">
				<div id="vote_area_topic_{$oTopicId}" class="vote-topic
					{if $bVoteInfoShow}
						{if $oTopic->getCountVote() > 0}
							{if $oTopicRating > 0}
								vote-count-positive
							{elseif $oTopicRating < 0}
								vote-count-negative
							{else}
								vote-count-mixed
							{/if}
						{else}
							vote-count-zero
						{/if}
					{/if}
					{if $oUserCurrent}
						{if $oVote}
							voted
							{if $oVote->getDirection() > 0}
								voted-up
							{elseif $oVote->getDirection() < 0}
								voted-down
							{else}
								voted-zero
							{/if}
						{else}
							{if $oTopic->getUserId()!=$oUserCurrent->getId()}
								not-voted
								{if strtotime($oTopic->getDateAdd()) > $smarty.now-$oConfig->GetValue('acl.vote.topic.limit_time')} vote-enabled{/if}
							{/if}
						{/if}
					{/if}
					{if $bVoteInfoEnabledForTopics} vote-info-enabled{/if}
					">

					{assign var="iTopicCountVote" value=$oTopic->getCountVote()}
					<div class="vote-item vote-up" data-direction="1" data-target_id="{$oTopicId}" data-target_type="topic"></div>
					<span id="vote_total_topic_{$oTopicId}" class="vote-item vote-count" title="{$aLang.topic_vote_count}: {$iTopicCountVote}" data-count="{$iTopicCountVote}" data-direction="0" data-target_id="{$oTopicId}" data-target_type="topic">
							{if $bVoteInfoShow}
								{if $oTopicRating > 0}+{/if}{$oTopicRating}
							{else}
								?
							{/if}
					</span>
					<div class="vote-item vote-down" data-direction="-1" data-target_id="{$oTopicId}" data-target_type="topic"></div>
				</div>
			</div>

			<a href="{$oUser->getUserWebPath()}"><img src="{$oUser->getProfileAvatarPath(48)}"  class="avatar" /></a>
			<a rel="author" href="{$oUser->getUserWebPath()}" data-user_id="{$oUser->getId()}">{$oUser->getLogin()}</a> в блоге
			<a href="{$oBlog->getUrlFull()}" class="topic-blog{if $oBlog->getType()=='close'} private-blog{/if}">{$oBlog->getTitle()|escape:'html'}</a>

		{if $oTopic->getIsAllowAction()}
			<span class="topic-actions">
				{if $oTopic->getIsAllowEdit()}
					<span class="edit"><i class="icon-synio-actions-edit"></i><a href="{$oTopic->getUrlEdit()}" title="{$aLang.topic_edit}" class="actions-edit">{$aLang.topic_edit}</a></span>
				{/if}

				{if $oTopic->getIsAllowDelete()}
					<span class="delete"><i class="icon-synio-actions-delete"></i><a href="{router page='topic'}delete/{$oTopicId}/?security_ls_key={$LIVESTREET_SECURITY_KEY}" title="{$aLang.topic_delete}" onclick="return confirm('{$aLang.topic_delete_confirm}');" class="actions-delete">{$aLang.topic_delete}</a></span>
				{/if}
			</span>
		{/if}
		</div>
	</header>
