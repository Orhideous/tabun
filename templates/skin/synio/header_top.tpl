<div id="c-header">

    <ul class="main-menu">
        <li id="logolink"><a href="/">Да, это — Табун!</a></li>

        <li><a href="//everypony.ru">{$aLang.menu_main}</a></li>
        <li><a href="//forum.everypony.ru">{$aLang.menu_forum}</a></li>
        <li><a href="//ponyfiction.org">{$aLang.menu_stories}</a></li>
        <li><a href="//radio.everypony.ru">{$aLang.menu_radio}</a></li>
        <li><a href="//ru.mlp.wikia.com">{$aLang.menu_wiki}</a></li>
        <li><a href="//blog.mc4ep.ru">{$aLang.menu_minecraft}</a></li>
    </ul>
    <ul>
        <li><a class="rss" href="/rss/" title="RSS поток Табуна"></a></li>
        <li><a class="twitter" href="https://twitter.com/everypony_ru" title="Твиттер Everypony.ru"></a></li>
    </ul>
</div>
<header id="header" role="banner">
    {hook run='header_banner_begin'}

    <ul class="nav nav-main" id="nav-main">
        {if $oUserCurrent}
            <li>
                <a href="{router page='topic'}add/" class="button-write js-write-window-show"
                   id="modal_write_show">{$aLang.block_create}</a>
            </li>
        {/if}
        <li {if $sMenuHeadItemSelect=='blog'}class="active"{/if}>
            <a href="{cfg name='path.root.web'}">{$aLang.topic_title}</a>
        </li>
        <li {if $sMenuHeadItemSelect=='blogs'}class="active"{/if}>
            <a href="{router page='blogs'}">{$aLang.blogs}</a>
        </li>
        <li {if $sMenuHeadItemSelect=='people'}class="active"{/if}>
            <a href="{router page='people'}">{$aLang.people}</a>
        </li>
        <li {if $sMenuHeadItemSelect=='stream'}class="active"{/if}>
            <a href="{router page='stream'}">{$aLang.stream_menu}</a>
        </li>

        {hook run='main_menu_item'}
    </ul>

    {hook run='main_menu'}

    {hook run='userbar_nav'}

    {if $oUserCurrent}
        <div class="dropdown-user" id="dropdown-user">
            <a href="{$oUserCurrent->getUserWebPath()}"><img src="{$oUserCurrent->getProfileAvatarPath(100)}"
                                                              class="avatar"/></a>
            <a href="{$oUserCurrent->getUserWebPath()}" class="username">{$oUserCurrent->getLogin()}</a>

            <ul class="dropdown-user-menu" id="dropdown-user-menu">
                {hook run='userbar_item_first'}
                <li class="item-messages">
                    <a href="{router page='talk'}" id="new_messages">
                        {$aLang.user_privat_messages}
                    </a>
                    {if $iUserCurrentCountTalkNew}<a href="{router page='talk'}" class="new-messages">
                        +{$iUserCurrentCountTalkNew}</a>{/if}
                </li>
                <li class="item-favourite"><a
                            href="{$oUserCurrent->getUserWebPath()}favourites/topics/">{$aLang.user_menu_profile_favourites}</a>
                </li>
                <li class="item-settings"><a href="{router page='settings'}profile/">{$aLang.user_settings}</a></li>
                {hook run='userbar_item_last'}
                <li class="item-signout"><a
                            href="{router page='login'}exit/?security_ls_key={$LIVESTREET_SECURITY_KEY}">{$aLang.exit}</a>
                </li>
            </ul>
            <ul class="dropdown-user-menu">
                <li class="item-stat">
                    <span class="strength" title="Силушка"><i
                                class="icon-synio-star-green"></i> {$oUserCurrent->getSkill()}</span>
                </li>
                <li>
                    <span class="rating {if $oUserCurrent->getRating() < 0}negative{/if}" title="Кармочка"><i
                                class="icon-synio-rating"></i> {$oUserCurrent->getRating()}</span>
                    {hook run='userbar_stat_item'}
                </li>
            </ul>
        </div>
    {else}
        <ul class="auth">
            {hook run='userbar_item'}
            <li><a href="{router page='registration'}"
                   class="js-registration-form-show">{$aLang.registration_submit}</a></li>
            <li><a href="{router page='login'}" class="js-login-form-show sign-in">{$aLang.user_login_submit}</a></li>
        </ul>
    {/if}

    {hook run='header_banner_end'}
    <nav id="nav">
        {if isset($menu)}
            {if in_array($menu,$aMenuContainers)}{$aMenuFetch.$menu}{else}{include file="menu.$menu.tpl"}{/if}
        {/if}
    </nav>
</header>
