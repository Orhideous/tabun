{assign var="oUserProfileId" value=$oUserProfile->getId()}
<div class="profile">
	{hook run='profile_top_begin' oUserProfile=$oUserProfile}
	
	<div class="vote-profile">
		<div id="vote_area_user_{$oUserProfileId}" class="vote-topic
																	{if $oUserProfile->getRating() > 0}
																		vote-count-positive
																	{elseif $oUserProfile->getRating() < 0}
																		vote-count-negative
																	{elseif $oUserProfile->getRating() == 0}
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
																	
																	{if $oUserCurrent and $oUserCurrent->getId()==$oUserProfile->getId() and !$oVote and $LS->ACL_CanVoteUser($oUserCurrent,$oUserProfile) and $LS->ACL_CanVoteUser($oUserCurrent, $oUserProfile)} vote-enabled{/if}
																	{if $bVoteInfoEnabledForProfile} vote-info-enabled{/if}
																	">
			{assign var="iUserProfileCountVote" value=$oUserProfile->getCountVote()}
			<div class="vote-item vote-up" data-direction="1" data-target_id="{$oUserProfileId}" data-target_type="user"></div>
			<span id="vote_total_user_{$oUserProfileId}" class="vote-count" data-count="{$iUserProfileCountVote}" data-target_id="{$oUserProfileId}" data-target_type="user">{if $oUserProfile->getRating() > 0}+{/if}{$oUserProfile->getRating()}</span>
			<div class="vote-item vote-down" data-direction="-1" data-target_id="{$oUserProfileId}" data-target_type="user"></div>
		</div>
		<div class="vote-label">{$aLang.user_vote_count}: {$iUserProfileCountVote}</div>
	</div>
	
	<div class="strength">
		<div class="count" id="user_skill_{$oUserProfileId}">{$oUserProfile->getSkill()}</div>
		<div class="vote-label">{$aLang.user_skill}</div>
	</div>

	{if $oUserCurrent && $oUserCurrent->getId()!=$oUserProfileId}
		<a href="{router page='talk'}add/?talk_users={$oUserProfile->getLogin()}"><button type="submit"  class="button button-action button-action-send-message" title="{$aLang.user_write_prvmsg}"><i class="icon-synio-send-message"></i></button></a>
	{/if}

	<h2 class="page-header user-login word-wrap {if !$oUserProfile->getProfileName()}no-user-name{/if}" itemprop="nickname">{$oUserProfile->getLogin()}</h2>
	
	{if $oUserProfile->getProfileName()}
		<p class="user-name" itemprop="name" data-user_id="{$oUserProfile->getId()}">{$oUserProfile->getProfileName()|escape:'html'}</p>
	{/if}
	
	{hook run='profile_top_end' oUserProfile=$oUserProfile}
</div>
