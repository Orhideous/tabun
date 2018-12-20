<?php
/*-------------------------------------------------------
*
*   LiveStreet Engine Social Networking
*   Copyright © 2008 Mzhelskiy Maxim
*
*--------------------------------------------------------
*
*   Official site: www.livestreet.ru
*   Contact e-mail: rus.engine@gmail.com
*
*   GNU General Public License, version 2:
*   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*
---------------------------------------------------------
*/

/**
 * ACL(Access Control List)
 * Модуль для разруливания ограничений по карме/рейтингу юзера
 *
 * @package modules.acl
 * @since 1.0
 */
class ModuleACL extends Module
{
    /**
     * Коды ответов на запрос о возможности
     * пользователя голосовать за блог
     */
    const CAN_VOTE_BLOG_FALSE = 0;
    const CAN_VOTE_BLOG_TRUE = 1;
    const CAN_VOTE_BLOG_ERROR_CLOSE = 2;
    /**
     * Коды механизма удаления блога
     */
    const CAN_DELETE_BLOG_EMPTY_ONLY  = 1;
    const CAN_DELETE_BLOG_WITH_TOPICS = 2;

    /**
     * Инициализация модуля
     *
     */
    public function Init()
    {
    }
    /**
     * Проверяет может ли пользователь создавать блоги
     *
     * @param ModuleUser_EntityUser $oUser	Пользователь
     * @return bool
     */
    public function CanCreateBlog(ModuleUser_EntityUser $oUser)
    {
        if ($oUser->getRating()>=Config::Get('acl.create.blog.rating')) {
            return true;
        }
        return false;
    }
    /**
     * Проверяет может ли пользователь создавать топики в определенном блоге
     *
     * @param ModuleUser_EntityUser $oUser	Пользователь
     * @param ModuleBlog_EntityBlog $oBlog	Блог
     * @return bool
     */
    public function CanAddTopic(ModuleUser_EntityUser $oUser, ModuleBlog_EntityBlog $oBlog)
    {
        /**
         * Если юзер является создателем блога то разрешаем ему постить
         */
        if ($oUser->getId()==$oBlog->getOwnerId()) {
            return true;
        }
        /**
         * Если рейтинг юзера больше либо равен порогу постинга в блоге то разрешаем постинг
         */
        if ($oUser->getRating()>=$oBlog->getLimitRatingTopic()) {
            return true;
        }
        return false;
    }
    /**
     * Проверяет может ли пользователь создавать комментарии
     *
     * @param  ModuleUser_EntityUser $oUser	Пользователь
     * @return bool
     */
    public function CanPostComment(ModuleUser_EntityUser $oUser)
    {
        if ($oUser->getRating()>=Config::Get('acl.create.comment.rating')) {
            return true;
        }
        return false;
    }
    /**
     * Проверяет может ли пользователь создавать комментарии по времени(например ограничение максимум 1 коммент в 5 минут)
     *
     * @param ModuleUser_EntityUser $oUser	Пользователь
     * @return bool
     */
    public function CanPostCommentTime(ModuleUser_EntityUser $oUser)
    {
        if (Config::Get('acl.create.comment.limit_time')>0 and $oUser->getDateCommentLast()) {
            $sDateCommentLast=strtotime($oUser->getDateCommentLast());
            if ($oUser->getRating()<Config::Get('acl.create.comment.limit_time_rating') and ((time()-$sDateCommentLast)<Config::Get('acl.create.comment.limit_time'))) {
                return false;
            }
        }
        return true;
    }
    /**
     * Проверяет может ли пользователь создавать топик по времени
     *
     * @param  ModuleUser_EntityUser $oUser	Пользователь
     * @return bool
     */
    public function CanPostTopicTime(ModuleUser_EntityUser $oUser)
    {
        // Для администраторов ограничение по времени не действует
        if ($oUser->isAdministrator()
            or Config::Get('acl.create.topic.limit_time')==0
            or $oUser->getRating()>=Config::Get('acl.create.topic.limit_time_rating')) {
            return true;
        }

        /**
         * Проверяем, если топик опубликованный меньше чем acl.create.topic.limit_time секунд назад
         */
        $aTopics=$this->Topic_GetLastTopicsByUserId($oUser->getId(), Config::Get('acl.create.topic.limit_time'));
        if (isset($aTopics['count']) and $aTopics['count']>0) {
            return false;
        }
        return true;
    }
    /**
     * Проверяет может ли пользователь отправить инбокс по времени
     *
     * @param  ModuleUser_EntityUser $oUser	Пользователь
     * @return bool
     */
    public function CanSendTalkTime(ModuleUser_EntityUser $oUser)
    {
        // Для администраторов ограничение по времени не действует
        if ($oUser->isAdministrator()
            or Config::Get('acl.create.talk.limit_time')==0
            or $oUser->getRating()>=Config::Get('acl.create.talk.limit_time_rating')) {
            return true;
        }

        /**
         * Проверяем, если топик опубликованный меньше чем acl.create.topic.limit_time секунд назад
         */
        $aTalks=$this->Talk_GetLastTalksByUserId($oUser->getId(), Config::Get('acl.create.talk.limit_time'));
        if (isset($aTalks['count']) and $aTalks['count']>0) {
            return false;
        }
        return true;
    }
    /**
     * Проверяет может ли пользователь создавать комментарии к инбоксу по времени
     *
     * @param  ModuleUser_EntityUser $oUser	Пользователь
     * @return bool
     */
    public function CanPostTalkCommentTime(ModuleUser_EntityUser $oUser)
    {
        /**
         * Для администраторов ограничение по времени не действует
         */
        if ($oUser->isAdministrator()
            or Config::Get('acl.create.talk_comment.limit_time')==0
            or $oUser->getRating()>=Config::Get('acl.create.talk_comment.limit_time_rating')) {
            return true;
        }
        /**
         * Проверяем, если топик опубликованный меньше чем acl.create.topic.limit_time секунд назад
         */
        $aTalkComments=$this->Comment_GetCommentsByUserId($oUser->getId(), 'talk', 1, 1);
        /**
         * Если комментариев не было
         */
        if (!is_array($aTalkComments) or $aTalkComments['count']==0) {
            return true;
        }
        /**
         * Достаем последний комментарий
         */
        $oComment = array_shift($aTalkComments['collection']);
        $sDate = strtotime($oComment->getDate());

        if ($sDate and ((time()-$sDate)<Config::Get('acl.create.talk_comment.limit_time'))) {
            return false;
        }
        return true;
    }
    /**
     * Проверяет может ли пользователь голосовать за конкретный комментарий
     *
     * @param ModuleUser_EntityUser $oUser	Пользователь
     * @param ModuleComment_EntityComment $oComment	Комментарий
     * @return bool
     */
    public function CanVoteComment(ModuleUser_EntityUser $oUser, ModuleComment_EntityComment $oComment)
    {
        if ($oUser->getRating()>=Config::Get('acl.vote.comment.rating')) {
            return true;
        }
        return false;
    }
    /**
     * Проверяет может ли пользователь голосовать за конкретный блог
     *
     * @param ModuleUser_EntityUser $oUser	Пользователь
     * @param ModuleBlog_EntityBlog $oBlog	Блог
     * @return bool
     */
    public function CanVoteBlog(ModuleUser_EntityUser $oUser, ModuleBlog_EntityBlog $oBlog)
    {
        /**
         * Если блог закрытый, проверяем является ли пользователь его читателем
         */
        if ($oBlog->getType()=='close') {
            $oBlogUser = $this->Blog_GetBlogUserByBlogIdAndUserId($oBlog->getId(), $oUser->getId());
            if (!$oBlogUser || $oBlogUser->getUserRole()<ModuleBlog::BLOG_USER_ROLE_GUEST) {
                return self::CAN_VOTE_BLOG_ERROR_CLOSE;
            }
        }
        if ($oUser->getRating()>=Config::Get('acl.vote.blog.rating')) {
            return self::CAN_VOTE_BLOG_TRUE;
        }
        return self::CAN_VOTE_BLOG_FALSE;
    }
    /**
     * Проверяет может ли пользователь голосовать за конкретный топик
     *
     * @param ModuleUser_EntityUser $oUser	Пользователь
     * @param ModuleTopic_EntityTopic $oTopic	Топик
     * @return bool
     */
    public function CanVoteTopic(ModuleUser_EntityUser $oUser, ModuleTopic_EntityTopic $oTopic)
    {
        if ($oUser->getRating()>=Config::Get('acl.vote.topic.rating')) {
            return true;
        }
        return false;
    }
    /**
     * Проверяет может ли пользователь голосовать за конкретного пользователя
     *
     * @param ModuleUser_EntityUser $oUser	Пользователь
     * @param ModuleUser_EntityUser $oUserTarget	Пользователь за которого голосуем
     * @return bool
     */
    public function CanVoteUser(ModuleUser_EntityUser $oUser, ModuleUser_EntityUser $oUserTarget)
    {
        if ($oUser->getRating()>=Config::Get('acl.vote.user.rating')) {
            return true;
        }
        return false;
    }
    /**
     * Проверяет можно ли юзеру слать инвайты
     *
     * @param ModuleUser_EntityUser $oUser	Пользователь
     * @return bool
     */
    public function CanSendInvite(ModuleUser_EntityUser $oUser)
    {
        if ($this->User_GetCountInviteAvailable($oUser)==0) {
            return false;
        }
        return true;
    }
    /**
     * Проверяет можно или нет юзеру постить в данный блог
     *
     * @param ModuleBlog_EntityBlog $oBlog	Блог
     * @param ModuleUser_EntityUser $oUser	Пользователь
     */
    public function IsAllowBlog($oBlog, $oUser)
    {
        if (!$oUser) {
            return false;
        }
        if ($oUser->isAdministrator()) {
            return true;
        }
        if ($oUser->getRating()<=Config::Get('acl.create.topic.limit_rating')) {
            return false;
        }
        if ($oBlog->getOwnerId()==$oUser->getId()) {
            return true;
        }
        if ($oBlogUser=$this->Blog_GetBlogUserByBlogIdAndUserId($oBlog->getId(), $oUser->getId())) {
            if ($this->ACL_CanAddTopic($oUser, $oBlog) or $oBlogUser->getIsAdministrator() or $oBlogUser->getIsModerator()) {
                return true;
            }
        }
        return false;
    }
    /**
     * Проверяет можно или нет пользователю редактировать данный топик
     *
     * @param  ModuleTopic_EntityTopic $oTopic	Топик
     * @param  ModuleUser_EntityUser $oUser	Пользователь
     * @return bool
     */
    public function IsAllowEditTopic($oTopic, $oUser)
    {
        if (!$oUser) {
            return false;
        }
        /**
         * Разрешаем если это админ сайта или автор топика
         */
        if ($oTopic->getUserId()==$oUser->getId() or $oUser->isAdministrator()) {
            return true;
        }
        /**
         * Если автор(смотритель) блога
         */
        if ($oTopic->getBlog()->getOwnerId()==$oUser->getId()) {
            return true;
        }
        /**
         * Если модер или админ блога
         */
        if ($this->User_GetUserCurrent() and $this->User_GetUserCurrent()->getId()==$oUser->getId()) {
            /**
             * Для авторизованного пользователя данный код будет работать быстрее
             */
            if ($oTopic->getBlog()->getUserIsAdministrator() or $oTopic->getBlog()->getUserIsModerator()) {
                return true;
            }
        } else {
            $oBlogUser=$this->Blog_GetBlogUserByBlogIdAndUserId($oTopic->getBlogId(), $oUser->getId());
            if ($oBlogUser and ($oBlogUser->getIsModerator() or $oBlogUser->getIsAdministrator())) {
                return true;
            }
        }

        return false;
    }
    /**
     * Проверяет можно или нет пользователю удалять данный топик
     *
     * @param ModuleTopic_EntityTopic $oTopic	Топик
     * @param ModuleUser_EntityUser $oUser	Пользователь
     * @return bool
     */
    public function IsAllowDeleteTopic($oTopic, $oUser)
    {
        if (!$oUser) {
            return false;
        }
        /**
         * Разрешаем если это админ сайта или автор топика
         */
        if ($oTopic->getUserId()==$oUser->getId() or $oUser->isAdministrator()) {
            return true;
        }
        /**
         * Если автор(смотритель) блога
         */
        if ($oTopic->getBlog()->getOwnerId()==$oUser->getId()) {
            return true;
        }
        /**
         * Если модер или админ блога
         */
        if ($this->User_GetUserCurrent() and $this->User_GetUserCurrent()->getId()==$oUser->getId()) {
            /**
             * Для авторизованного пользователя данный код будет работать быстрее
             */
            if ($oTopic->getBlog()->getUserIsAdministrator() or $oTopic->getBlog()->getUserIsModerator()) {
                return true;
            }
        } else {
            $oBlogUser=$this->Blog_GetBlogUserByBlogIdAndUserId($oTopic->getBlogId(), $oUser->getId());
            if ($oBlogUser and ($oBlogUser->getIsModerator() or $oBlogUser->getIsAdministrator())) {
                return true;
            }
        }
        return false;
    }
    /**
     * Проверяет можно или нет пользователю удалять данный блог
     *
     * @param ModuleBlog_EntityBlog $oBlog	Блог
     * @param ModuleUser_EntityUser $oUser	Пользователь
     * @return bool
     */
    public function IsAllowDeleteBlog($oBlog, $oUser)
    {
        if (!$oUser) {
            return false;
        }
        /**
         * Разрешаем если это админ сайта или автор блога
         */
        if ($oUser->isAdministrator()) {
            return self::CAN_DELETE_BLOG_WITH_TOPICS;
        }
        /**
         * Разрешаем удалять администраторам блога и автору, но только пустой
         */
        if ($oBlog->getOwnerId()==$oUser->getId()) {
            return self::CAN_DELETE_BLOG_EMPTY_ONLY;
        }

        $oBlogUser=$this->Blog_GetBlogUserByBlogIdAndUserId($oBlog->getId(), $oUser->getId());
        if ($oBlogUser and $oBlogUser->getIsAdministrator()) {
            return self::CAN_DELETE_BLOG_EMPTY_ONLY;
        }
        return false;
    }
    /**
     * Проверяет может ли пользователь удалить комментарий
     *
     * @param  ModuleUser_EntityUser $oUser	Пользователь
     * @return bool
     */
    public function CanDeleteComment($oUser)
    {
        if (!$oUser || !$oUser->isAdministrator()) {
            return false;
        }
        return true;
    }
    /**
     * Проверяет может ли пользователь публиковать на главной
     *
     * @param  ModuleUser_EntityUser $oUser	Пользователь
     * @return bool
     */
    public function IsAllowPublishIndex(ModuleUser_EntityUser $oUser)
    {
        if ($oUser->isAdministrator()) {
            return true;
        }
        return false;
    }
    /**
     * Проверяет можно или нет пользователю редактировать данный блог
     *
     * @param  ModuleBlog_EntityBlog $oBlog	Блог
     * @param  ModuleUser_EntityUser $oUser	Пользователь
     * @return bool
     */
    public function IsAllowEditBlog($oBlog, $oUser)
    {
        if (!$oUser) {
            return false;
        }
        if ($oUser->isAdministrator()) {
            return true;
        }
        /**
         * Разрешаем если это создатель блога
         */
        if ($oBlog->getOwnerId() == $oUser->getId()) {
            return true;
        }
        /**
         * Явлется ли авторизованный пользователь администратором блога
         */
        $oBlogUser = $this->Blog_GetBlogUserByBlogIdAndUserId($oBlog->getId(), $oUser->getId());

        if ($oBlogUser && $oBlogUser->getIsAdministrator()) {
            return true;
        }
        return false;
    }
    /**
     * Проверяет можно или нет пользователю управлять пользователями блога
     *
     * @param  ModuleBlog_EntityBlog $oBlog	Блог
     * @param  ModuleUser_EntityUser $oUser	Пользователь
     * @return bool
     */
    public function IsAllowAdminBlog($oBlog, $oUser)
    {
        if (!$oUser) {
            return false;
        }
        if ($oUser->isAdministrator()) {
            return true;
        }
        /**
         * Разрешаем если это создатель блога
         */
        if ($oBlog->getOwnerId() == $oUser->getId()) {
            return true;
        }
        /**
         * Явлется ли авторизованный пользователь администратором блога
         */
        $oBlogUser = $this->Blog_GetBlogUserByBlogIdAndUserId($oBlog->getId(), $oUser->getId());
        if ($oBlogUser && $oBlogUser->getIsAdministrator()) {
            return true;
        }
        return false;
    }

    // флаги разрешения редактирования
    const EDIT_ALLOW_FIX = 1;
    const EDIT_ALLOW_LOCK = 2;
    // причина разрешения редактирования
    const EDIT_ALLOWED_AS_AUTHOR = 0x4;
    const EDIT_ALLOWED_AS_BLOG_ADMIN = 0x8;
    const EDIT_ALLOWED_AS_ADMIN = 0xC;
    const EDIT_ALLOW_REASON_OFFSET = 2;
    const EDIT_ALLOW_REASON_MASK = 3;
    // флаги ошибок
    const EDIT_DENIED_DUE_OWNERSHIP = 0x10;
    const EDIT_DENIED_DUE_ANSWER = 0x20;
    const EDIT_DENIED_DUE_OUTDATE = 0x40;
    const EDIT_DENIED_DUE_LOCK = 0x80;
    const EDIT_DENY_REASON_OFFSET = 4;
    const EDIT_DENY_REASON_MASK = 0xF;

    public function IsAllowEditComments($oBlog, $oUser)
    {
        return $this::IsAllowAdminComments($oBlog, $oUser);
    }
    /**
     * Проверяет, может ли пользователь администрировать комментарии как администратор блога
     *
     * @param  ModuleBlog_EntityBlog $oBlog Блог
     * @param  ModuleUser_EntityUser $oUser Пользователь
     * @return bool
     */
    public function IsAllowAdminComments($oBlog, $oUser)
    {
        if (!$oUser) {
            return false;
        }
        if ($oUser->isAdministrator()) {
            return true;
        }
        if (!in_array($oBlog->getType(), ['open', 'close'])) {
            return false;
        }
        /**
         * Разрешаем если это создатель блога
         */
        if ($oBlog->getOwnerId() == $oUser->getId()) {
            return true;
        }
        /**
         * Явлется ли авторизованный пользователь администратором блога
         */
        $oBlogUser = $this->Blog_GetBlogUserByBlogIdAndUserId($oBlog->getId(), $oUser->getId());
        if ($oBlogUser && $oBlogUser->getIsAdministrator()) {
            return true;
        }
        return false;
    }

    /**
     * Проверяет, может ли пользователь изменить комментарий и заблокировать его дальнейшее изменение
     *
     * @param  ModuleComment_EntityComment $oComment Комментарий
     * @param  ModuleUser_EntityUser $oUser Пользователь
     * @param  bool|null $bAllowUserToEditBlogComments
     * @return int
     */
    public function GetCommentEditAllowMask($oComment, $oUser, $bAllowUserToEditBlogComments=null)
    {
        if (!$oUser) {
            return 0;
        }

        $userIsNotAuthor = $oComment->getUserId() != $oUser->getId();

        if ($oUser->isAdministrator()) {
            return $this::GetAdminCommentEditAllowMask($userIsNotAuthor);
        }

        /*
         * TODO: target=classes.Target
         * Если кроме Topic и Talk появится что-то ещё — переделать блоки,
         * использующие $targetIsTopic и $oComment->getTarget()
         */
        $targetIsTopic = $oComment->getTargetType() === 'topic';
        if ($targetIsTopic) {
            $editExpiredLimit = Config::Get('acl.edit.comment.limit_time');
        } else {
            $editExpiredLimit = Config::Get('acl.edit.talk_comment.limit_time');
        }

        if ($targetIsTopic) {
            if ($bAllowUserToEditBlogComments === null) {
                $bAllowUserToEditBlogComments = $this::IsAllowEditComments($oComment->getTarget()->getBlog(), $oUser);
            }
            if ($bAllowUserToEditBlogComments) {
                return $this::GetAdminCommentEditAllowMask($userIsNotAuthor);
            }
            return 0;
        } else {
            $bEditCondition = (
                $userIsNotAuthor ||
                $oComment->isFlagRaised(ModuleComment_EntityComment::FLAG_HAS_ANSWER) ||
                $oComment->isFlagRaised(ModuleComment_EntityComment::FLAG_LOCK_MODIFY) ||
                strtotime($oComment->getDate()) <= time() - $editExpiredLimit
            );
            return $bEditCondition ? 0 : $this::EDIT_ALLOW_FIX;
        }
    }

    public function GetCommentEditAccessInfo($oComment, $oUser)
    {
        if (!$oUser) {
            return 0;
        }

        $userIsNotAuthor = $oComment->getUserId() != $oUser->getId();

        /*
         * TODO: target=classes.Target
         * Если кроме Topic и Talk появится что-то ещё — переделать блоки,
         * использующие $targetIsTopic и $oComment->getTarget()
         */
        $targetIsTopic = $oComment->getTargetType() === 'topic';
        if ($targetIsTopic) {
            $editExpiredLimit = Config::Get('acl.edit.comment.limit_time');
        } else {
            $editExpiredLimit = Config::Get('acl.edit.talk_comment.limit_time');
        }

        $deny_flags = 0;

        if ($userIsNotAuthor) {
            $deny_flags |= $this::EDIT_DENIED_DUE_OWNERSHIP;
        }
        if ($oComment->isFlagRaised(ModuleComment_EntityComment::FLAG_HAS_ANSWER)) {
            $deny_flags |= $this::EDIT_DENIED_DUE_ANSWER;
        }
        if ($oComment->isFlagRaised(ModuleComment_EntityComment::FLAG_LOCK_MODIFY)) {
            $deny_flags |= $this::EDIT_DENIED_DUE_LOCK;
        }
        if (strtotime($oComment->getDate()) <= time() - $editExpiredLimit) {
            $deny_flags |= $this::EDIT_DENIED_DUE_OUTDATE;
        }
        if ($deny_flags === 0) {
            return $this::EDIT_ALLOW_FIX | $this::EDIT_ALLOWED_AS_AUTHOR;
        } else {
            // TODO: Implement more precise ACL here
            if ($oUser->isAdministrator()) {
                return $this::GetAdminCommentEditAllowMask($userIsNotAuthor) | $this::EDIT_ALLOWED_AS_ADMIN;
            } elseif ($targetIsTopic && $this::IsAllowEditComments($oComment->getTarget()->getBlog(), $oUser)) {
                return $this::GetAdminCommentEditAllowMask($userIsNotAuthor) | $this::EDIT_ALLOWED_AS_BLOG_ADMIN;
            } else {
                return $deny_flags;
            }
        }
    }

    public function GetAdminCommentEditAllowMask($userIsNotAuthor)
    {
        return $this::EDIT_ALLOW_FIX | ($userIsNotAuthor && Config::Get('acl.edit.comment.enable_lock') ? $this::EDIT_ALLOW_LOCK : 0);
    }

    // oTarget — Объект, к которому нужно проверить доступ при единичном запросе, ИЛИ true, если известно, что доступ к объекту уже есть
    public function VoteListCheckAccess($oUser, $oTarget, $sTargetType, $bRequireSuperuser=false)
    {
        $bEnable = Config::Get('vote_list.'.$sTargetType.'.enable');
        if (!$bEnable) {
            // Deny:
            return false;
        }
        
        if ($bRequireSuperuser) {
            $iUserRequiredLevel = Config::Get('vote_list.'.$sTargetType.'.superuser_required_level');
            $fUserRequiredRating = Config::Get('vote_list.'.$sTargetType.'.superuser_required_rating');
        } else {
            $iUserRequiredLevel = Config::Get('vote_list.'.$sTargetType.'.user_required_level');
            $fUserRequiredRating = Config::Get('vote_list.'.$sTargetType.'.user_required_rating');
        }
        
        if ($oUser) {
            $fUserRating = $oUser->getRating();
        } else {
            $fUserRating = 0.0;
        }
        
        if ($fUserRating < $fUserRequiredRating) {
            // Deny:
            return false;
        }
        
        switch ($iUserRequiredLevel) {
            case 128:
                // Allow|Deny:
                return $oUser && $oUser->isAdministrator();
            case 2:
                if (!$oUser) {
                    // Deny:
                    return false;
                }
                // fall-through
                // Если пользователь авторизован, то дальше проверяем остальные права на доступ, но проверяя на предмет авторизации в нужных местах
            case 1:
                if ($oUser && $oUser->isAdministrator()) {
                    // Allow:
                    return true;
                }
                
                if ($oTarget === true) {
                    // Allow:
                    return true;
                }
                
                switch ($sTargetType) {
                    case 'comment':
                        if ($oTarget->getTargetType() === 'topic') {
                            $oTopic = $oTarget->getTarget();
                        } elseif ($oTarget->getTargetType() === 'talk') {
                            // На данный момент невозможно даже оценивать комменты в ЛС
                            // Deny:
                            return false;
                        }
                        break;
                    case 'topic':
                        $oTopic = $oTarget;
                        break;
                    case 'blog':
                        $oBlog = $oTarget;
                        break;
                    case 'user':
                        // Allow:
                        return true;
                }
                
                if (isset($oTopic) || isset($oBlog)) {
                    if (!isset($oBlog) && $oTopic) {
                        $oBlog = $oTopic->getBlog();
                    }
                    if ($oUser && $oBlog->getOwnerId() == $oUser->getId()) {
                        // Allow:
                        return true;
                    }
                    $bAllowBansInOpenBlogs = true;
                    if (in_array($oBlog->getType(), ['open', 'personal'])) {
                        if ($bAllowBansInOpenBlogs) {
                            if ($oUser) {
                                $oBlogUser = $this->Blog_GetBlogUserByBlogIdAndUserId($oBlog->getId(), $oUser->getId());
                                if ($oBlogUser && $oBlogUser->getUserRole() == ModuleBlog::BLOG_USER_ROLE_BAN) {
                                    // Deny:
                                    return false;
                                } else {
                                    // Allow:
                                    return true;
                                }
                            } else {
								// Allow:
								return true;
							}
                        } else {
                            // Allow:
                            return true;
                        }
                    } else {
                        if ($oUser) {
                            $oBlogUser = $this->Blog_GetBlogUserByBlogIdAndUserId($oBlog->getId(), $oUser->getId());
                            if ($oBlogUser && $oBlogUser->getUserRole() != ModuleBlog::BLOG_USER_ROLE_BAN) {
                                // Allow:
                                return true;
                            }
                        }
                    }
                }
                break;
        }
        // Deny:
        return false;
    }
}
