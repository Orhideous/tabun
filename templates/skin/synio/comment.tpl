{assign var="oUser" value=$oComment->getUser()}
{assign var="oVote" value=$oComment->getVote()}
{assign var="editAccessMask" value=$oComment->getEditAccessMask($oUserCurrent)}

<section data-id="{$oComment->getId()}" id="comment_id_{$oComment->getId()}" class="comment {if $oComment->isBad()}comment-bad{/if} {if $oComment->getDelete()}comment-deleted{elseif $oUserCurrent and $oComment->getUserId() == $oUserCurrent->getId()} comment-self{elseif $sDateReadLast <= $oComment->getDate()} comment-new{/if}">
    {if $oComment->getRating() < $oConfig->GetValue('module.user.bad_rating')}
        <div id="comment_content_id_{$oComment->getId()}" class="comment-content">
            <div class="text"><em>{$aLang.comment_was_hidden}</em></div>
        </div>
    {elseif !$oComment->getDelete() or $bOneComment or ($oUserCurrent and $oComment->testAllowDelete($oUserCurrent))}
        <a name="comment{$oComment->getId()}"></a>
        <div data-id="{$oComment->getId()}" class="folding"></div>
        <div id="comment_content_id_{$oComment->getId()}" class="comment-content">
            <div class="text current">{$oComment->getText()}</div>
        </div>
        <ul class="comment-info">
            <li class="comment-author {if $iAuthorId == $oUser->getId()}comment-topic-author{/if}"
                title="{if $iAuthorId == $oUser->getId() and $sAuthorNotice}{$sAuthorNotice}{/if}">
                <a href="{$oUser->getUserWebPath()}">
                    <img src="{$oUser->getProfileAvatarPath(24)}"  class="comment-avatar"/>
                </a>
                <a href="{$oUser->getUserWebPath()}">{$oUser->getLogin()}</a>
            </li>
            <li class="comment-date">
                <time datetime="{date_format date=$oComment->getDate() format='c'}"
                      title="{date_format date=$oComment->getDate() hours_back="12" minutes_back="60" now="60" day="day H:i" format="j F Y, H:i"}">
                    {date_format date=$oComment->getDate() format="j F Y, H:i"}
                </time>
            </li>
            {if $oComment->getTargetType() != 'talk'}
                <li id="vote_area_comment_{$oComment->getId()}"
                    class="vote
                    {if $oComment->getRating() > 0}
                        vote-count-positive
                        {elseif $oComment->getRating() < 0}
                        vote-count-negative
                    {/if}
                    {if (strtotime($oComment->getDate()) < $smarty.now - $oConfig->GetValue('acl.vote.comment.limit_time') && !$oVote) || ($oUserCurrent && $oUserCurrent->getId() == $oUser->getId())}vote-expired{/if}
                    {if $oVote}
                        voted
                            {if $oVote->getDirection() > 0}
                                voted-up
                            {else}
                                voted-down
                            {/if}
                     {/if}
                 ">
                    <div class="vote-up" onclick="return ls.vote.vote({$oComment->getId()},this,1,'comment');"></div>
                    <span class="vote-count" id="vote_total_comment_{$oComment->getId()}">{if $oComment->getRating() > 0}+{/if}{$oComment->getRating()}</span>
                    <div class="vote-down" onclick="return ls.vote.vote({$oComment->getId()},this,-1,'comment');"></div>
                </li>
            {/if}
            {if $oUserCurrent and !$bNoCommentFavourites}
                <li class="comment-favourite">
                    <div onclick="return ls.favourite.toggle({$oComment->getId()},this,'comment');"
                         class="favourite {if $oComment->getIsFavourite()}active{/if}">
                        {if $oComment->getIsFavourite()}
                            {t}favourite_in{/t}
                        {else}
                            {t}favourite_add{/t}
                        {/if}
                    </div>
                    <span class="favourite-count" id="fav_count_comment_{$oComment->getId()}" {if $oComment->getCountFavourite() == 0}hidden{/if}>{if $oComment->getCountFavourite() > 0}{$oComment->getCountFavourite()}{/if}</span>
                </li>
            {/if}
            <li class="comment-link">
                <a href="#comment{$oComment->getId()}" title="{$aLang.comment_url_notice}">
                    <i class="icon-synio-link"></i>
                </a>
            </li>
            {if $oComment->getPid()}
                <li class="goto goto-comment-parent">
                    <a href="{router page='comments'}{$oComment->getPid()}"
                        onclick="ls.comments.goToParentComment({$oComment->getId()},{$oComment->getPid()}); return false;"
                        title="{$aLang.comment_goto_parent}">↑</a>
                </li>
            {/if}
            <li class="goto goto-comment-child">
                <a href="#" title="{$aLang.comment_goto_child}">↓</a>
            </li>
            {if $oUserCurrent}
                {if !$oComment->getDelete() and !$bAllowNewComment and $bAddCommentPermission}
                    <li>
                        <a href="#" onclick="ls.comments.toggleCommentForm({$oComment->getId()}); return false;" class="reply-link link-dotted">{$aLang.comment_answer}</a>
                    </li>
                {/if}

                {if !$oComment->getDelete() and $oComment->testAllowDelete($oUserCurrent)}
                    <li>
                        <a href="#" class="comment-delete link-dotted" onclick="ls.comments.toggle(this,{$oComment->getId()}); return false;">{$aLang.comment_delete}</a>
                    </li>
                {/if}

                {if $oComment->getDelete() and $oComment->testAllowDelete($oUserCurrent)}
                    <li>
                        <a href="#" class="comment-repair link-dotted" onclick="ls.comments.toggle(this,{$oComment->getId()}); return false;">{$aLang.comment_repair}</a>
                    </li>
                {/if}

                {if $oComment->testAllowEdit($editAccessMask)}
                    <li class="comment-edit-bw"><a href="#" class="link-dotted" onclick="return ls.comments.toggleEditForm({$oComment->getId()}, true, {if $oComment->testAllowLock($editAccessMask)}true{else}false{/if});">{$aLang.comment_edit}</a></li>
                    <li class="comment-save-edit-bw"><a href="#" class="link-dotted" onclick="return ls.comments.saveEdit({$oComment->getId()});">{$aLang.comment_save_edit}</a></li>
                    <li class="comment-preview-edit-bw"><a href="#" class="link-dotted" onclick="return ls.comments.previewEdit({$oComment->getId()});">{$aLang.comment_preview_edit}</a></li>
                    <li class="comment-cancel-edit-bw"><a href="#" class="link-dotted" onclick="return ls.comments.toggleEditForm({$oComment->getId()}, false);">{$aLang.comment_cancel_edit}</a></li>
                {/if}

                {hook run='comment_action' comment=$oComment}
            {/if}
            {include file='comment_modify_notice.tpl'}
        </ul>
    {else}
        <div id="comment_content_id_{$oComment->getId()}" class="comment-content">
            <div class="text"><em>{$aLang.comment_was_delete}</em></div>
        </div>
    {/if}
</section>
