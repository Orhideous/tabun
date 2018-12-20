<div class="comments comment-list">
{foreach from=$aComments item=oComment}
    {assign var="oCommentAuthor" value=$oComment->getUser()}
    {assign var="oCommentAuthorLogin" value=$oCommentAuthor->getLogin()}
    {assign var="oCommentId" value=$oComment->getId()}
    {assign var="oCommentDate" value=$oComment->getDate()}
    {assign var="oTopic" value=$oComment->getTarget()}
    {if $oTopic}
        {assign var="oBlog" value=$oTopic->getBlog()}
    {/if}
    {if $oUserCurrent}
        {assign var="oCommentRating" value=$oComment->getRating()}
        {assign var="oCommentVoteCount" value=$oComment->getCountVote()}
    {/if}

    <section class="comment">
        <div class="comment-content">
            <div class="text">{$oComment->getText()}</div>
        </div>
        <div class="comment-info{if $bVoteInfoEnabledForComments} vote-info-enabled{/if}" data-id="{$oCommentId}">
            <a href="/profile/{$oCommentAuthorLogin}">
                <img src="{$oCommentAuthor->getProfileAvatarPath(24)}"  class="comment-avatar"/>
            </a>
            <a class="comment-author {if $iAuthorId == $oCommentAuthor->getId()}comment-topic-author{/if}" {if sAuthorNotice} title="{$sAuthorNotice}"{/if} href="/profile/{$oCommentAuthorLogin}">{$oCommentAuthorLogin}</a>
            <time class="comment-date" datetime="{date_format date=$oCommentDate format='c'}"
                      title="{date_format date=$oCommentDate hours_back="12" minutes_back="60" now="60" day="day H:i" format="j F Y, H:i"}">
                {date_format date=$oCommentDate format="j F Y, H:i"}
            </time>
            <a class="comment-link icon-synio-link" href="#comment{$oCommentId}" title="{$aLang.comment_url_notice}"></a>
            {if $oComment->getPid()}
                <a href="{router page='comments'}{$oComment->getPid()}" class="goto goto-comment-parent" title="{$aLang.comment_goto_parent}">↑</a>
            {/if}
            {if $oUserCurrent}
                {if !$bNoCommentFavourites}
                <div class="comment-favourite">
                    {if $oComment->getCountFavourite() > 0}
                        <div class="favourite link-dotted{if $oComment->getIsFavourite()} active{/if}" data-target_id="{$oCommentId}" data-target_type="comment">
                            {if $oComment->getIsFavourite()}
                                {$aLang.comment_favourite_add_already}
                            {else}
                                {$aLang.comment_favourite_add}
                            {/if}
                        </div>
                        <span class="favourite-count" id="fav_count_comment_{$oCommentId}">{$oComment->getCountFavourite()}</span>
                    {else}
                        <div class="favourite link-dotted" data-target_id="{$oCommentId}" data-target_type="comment">{$aLang.comment_favourite_add}</div>
                        <span class="favourite-count" id="fav_count_comment_{$oCommentId}"></span>
                    {/if}
                </div>
                <div id="vote_area_comment_{$oCommentId}" class="vote comment-vote
                    {if $oCommentVoteCount > 0}
                        {if $oCommentRating > 0} vote-count-positive
                        {elseif $oCommentRating < 0} vote-count-negative
                        {else} vote-count-mixed
                        {/if}
                    {/if}
                    ">
                    <span class="vote-count" id="vote_total_comment_{$oCommentId}" data-target_id="{$oCommentId}" data-target_type="comment" data-count="{$oCommentVoteCount}">{$oCommentRating}</span>
                </div>
                {/if}
                {include file='comment_modify_notice.tpl' bGetShort=true}
            {/if}
            <div>
                {if $oBlog}
                    <a href="{$oBlog->getUrlFull()}" class="blog-name">{$oBlog->getTitle()|escape:'html'}</a>
                    &rarr;
                {/if}
                <a href="{$oTopic->getUrl()}" class="comment-path-topic">{$oTopic->getTitle()|escape:'html'}</a>
                <a href="{$oTopic->getUrl()}#comments"  class="comment-path-comments">{$oTopic->getCountComment()}</a>
            </div>
        </div>
    </section>
{/foreach}
</div>

{include file='paging.tpl' aPaging=$aPaging}
