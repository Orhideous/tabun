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
 * Модуль для работы с комментариями
 *
 * @package modules.comment
 * @since 1.0
 */
class ModuleComment extends Module {
	/**
	 * Объект маппера
	 *
	 * @var ModuleComment_MapperComment
	 */
	protected $oMapper;
	/**
	 * Объект текущего пользователя
	 *
	 * @var ModuleUser_EntityUser|null
	 */
	protected $oUserCurrent=null;

	/**
	 * Инициализация
	 *
	 */
	public function Init() {
		$this->oMapper=Engine::GetMapper(__CLASS__);
		$this->oUserCurrent=$this->User_GetUserCurrent();
	}
	/**
	 * Получить коммент по айдишнику
	 *
	 * @param int $sId	ID комментария
	 * @return ModuleComment_EntityComment|null
	 */
	public function GetCommentById($sId) {
		if (!is_numeric($sId)) {
			return null;
		}
		$aComments=$this->GetCommentsAdditionalData($sId);
		if (isset($aComments[$sId])) {
			return $aComments[$sId];
		}
		return null;
	}
	/**
	 * Получает уникальный коммент, это помогает спастись от дублей комментов
	 *
	 * @param int $sTargetId	ID владельца комментария
	 * @param string $sTargetType	Тип владельца комментария
	 * @param int $sUserId	ID пользователя
	 * @param int $sCommentPid	ID родительского комментария
	 * @param string $sHash	Хеш строка текста комментария
	 * @return ModuleComment_EntityComment|null
	 */
	public function GetCommentUnique($sTargetId,$sTargetType,$sUserId,$sCommentPid,$sHash) {
		$sId=$this->oMapper->GetCommentUnique($sTargetId,$sTargetType,$sUserId,$sCommentPid,$sHash);
		return $this->GetCommentById($sId);
	}
	/**
	 * Получить все комменты
	 *
	 * @param string $sTargetType	Тип владельца комментария
	 * @param int $iPage	Номер страницы
	 * @param int $iPerPage	Количество элементов на страницу
	 * @param array $aExcludeTarget	Список ID владельцев, которые необходимо исключить из выдачи
	 * @param array $aExcludeParentTarget	Список ID родителей владельцев, которые необходимо исключить из выдачи, например, исключить комментарии топиков к определенным блогам(закрытым)
	 * @return array('collection'=>array,'count'=>int)
	 */
	public function GetCommentsAll($sTargetType,$iPage,$iPerPage,$aExcludeTarget=array(),$aExcludeParentTarget=array()) {
        $s=serialize($aExcludeTarget).serialize($aExcludeParentTarget);
        $sCacheKey = "comment_all_{$sTargetType}_{$iPage}_{$iPerPage}_{$s}";

        if (false === ($data = $this->Cache_Get($sCacheKey))) {
            $iCount = 0;
            $data = [
                'collection' => $this->oMapper->GetCommentsAll(
                    $sTargetType,
                    $iCount,
                    $iPage,
                    $iPerPage,
                    $aExcludeTarget,
                    $aExcludeParentTarget
                ),
                'count' => $iCount
            ];
			$this->Cache_Set(
				$data,
                $sCacheKey,
				[
					"comment_new_{$sTargetType}",
					"comment_update_status_{$sTargetType}"
				]
			);
		}
        $data['collection'] = $this->GetCommentsAdditionalData(
            $data['collection'],
            ['target', 'favourite', 'user' => []]
        );
        return $data;
	}
	/**
	 * Получает дополнительные данные(объекты) для комментов по их ID
	 *
	 * @param array $aCommentId	Список ID комментов
	 * @param array|null $aAllowData	Список типов дополнительных данных, которые нужно получить для комментариев
	 * @return array
	 */
	public function GetCommentsAdditionalData($aCommentId,$aAllowData=null) {
		if (is_null($aAllowData)) {
			$aAllowData=array('vote','target','favourite','user'=>array());
		}
		func_array_simpleflip($aAllowData);
		if (!is_array($aCommentId)) {
			$aCommentId=array($aCommentId);
		}
		/**
		 * Получаем комменты
		 */
		$aComments=$this->GetCommentsByArrayId($aCommentId);
		/**
		 * Формируем ID дополнительных данных, которые нужно получить
		 */
		$aUserId=array();
		$aTargetId=array('topic'=>array(),'talk'=>array());
		foreach ($aComments as $oComment) {
			if (isset($aAllowData['user'])) {
				$aUserId[]=$oComment->getUserId();
			}
			if (isset($aAllowData['target'])) {
				$aTargetId[$oComment->getTargetType()][]=$oComment->getTargetId();
			}
		}
		/**
		 * Получаем дополнительные данные
		 */
		$aUsers=isset($aAllowData['user']) && is_array($aAllowData['user']) ? $this->User_GetUsersAdditionalData($aUserId,$aAllowData['user']) : $this->User_GetUsersAdditionalData($aUserId);
		/**
		 * В зависимости от типа target_type достаем данные
		 */
		$aTargets=array();
        if (isset($aAllowData['target']) && is_array($aAllowData['target'])) {
            $aTargets['topic'] = $this->Topic_GetTopicsAdditionalData($aTargetId['topic'], $aAllowData['target']);
        } else {
            $aTargets['topic'] = $this->Topic_GetTopicsAdditionalData($aTargetId['topic']);
        }
        $aVote=array();
		if (isset($aAllowData['vote']) and $this->oUserCurrent) {
			$aVote=$this->Vote_GetVoteByArray($aCommentId,'comment',$this->oUserCurrent->getId());
		}
		if (isset($aAllowData['favourite']) and $this->oUserCurrent) {
			$aFavouriteComments=$this->Favourite_GetFavouritesByArray($aCommentId,'comment',$this->oUserCurrent->getId());
		}
		/**
		 * Добавляем данные к результату
		 */
		foreach ($aComments as $oComment) {
			if (isset($aUsers[$oComment->getUserId()])) {
				$oComment->setUser($aUsers[$oComment->getUserId()]);
			} else {
				$oComment->setUser(null); // или $oComment->setUser(new ModuleUser_EntityUser());
			}
			if (isset($aTargets[$oComment->getTargetType()][$oComment->getTargetId()])) {
				$oComment->setTarget($aTargets[$oComment->getTargetType()][$oComment->getTargetId()]);
			} else {
				$oComment->setTarget(null);
			}
			if (isset($aVote[$oComment->getId()])) {
				$oComment->setVote($aVote[$oComment->getId()]);
			} else {
				$oComment->setVote(null);
			}
			if (isset($aFavouriteComments[$oComment->getId()])) {
				$oComment->setIsFavourite(true);
			} else {
				$oComment->setIsFavourite(false);
			}
		}
		return $aComments;
	}
	/**
	 * Список комментов по ID
	 *
	 * @param array $aCommentId	Список ID комментариев
	 * @return array
	 */
	public function GetCommentsByArrayId($aCommentId) {
		if (!$aCommentId) {
			return array();
		}
		if (!is_array($aCommentId)) {
			$aCommentId=array($aCommentId);
		}
		$aCommentId=array_unique($aCommentId);
		$aComments=array();
		$aCommentIdNotNeedQuery=array();
		/**
		 * Делаем мульти-запрос к кешу
		 */
		$aCacheKeys=func_build_cache_keys($aCommentId,'comment_');
		if (false !== ($data = $this->Cache_Get($aCacheKeys))) {
			/**
			 * Проверяем что досталось из кеша
			 */
			foreach ($aCacheKeys as $sValue => $sKey ) {
				if (array_key_exists($sKey,$data)) {
					if ($data[$sKey]) {
						$aComments[$data[$sKey]->getId()]=$data[$sKey];
					} else {
						$aCommentIdNotNeedQuery[]=$sValue;
					}
				}
			}
		}
		/**
		 * Смотрим каких комментов не было в кеше и делаем запрос в БД
		 */
		$aCommentIdNeedQuery=array_diff($aCommentId,array_keys($aComments));
		$aCommentIdNeedQuery=array_diff($aCommentIdNeedQuery,$aCommentIdNotNeedQuery);
		$aCommentIdNeedStore=$aCommentIdNeedQuery;
		if ($data = $this->oMapper->GetCommentsByArrayId($aCommentIdNeedQuery)) {
			foreach ($data as $oComment) {
				/**
				 * Добавляем к результату и сохраняем в кеш
				 */
				$aComments[$oComment->getId()]=$oComment;
				$this->Cache_Set(
                    $oComment,
                    "comment_{$oComment->getId()}",
                    []
                );
				$aCommentIdNeedStore=array_diff($aCommentIdNeedStore,array($oComment->getId()));
			}
		}
		/**
		 * Сохраняем в кеш запросы не вернувшие результата
		 */
		foreach ($aCommentIdNeedStore as $sId) {
			$this->Cache_Set(
                null,
                "comment_{$sId}",
                []
            );
		}
		/**
		 * Сортируем результат согласно входящему массиву
		 */
		$aComments=func_array_sort_by_keys($aComments,$aCommentId);
		return $aComments;
	}
	/**
	 * Получить все комменты сгрупированные по типу(для вывода прямого эфира)
	 *
	 * @param string $sTargetType	Тип владельца комментария
	 * @param int $iLimit	Количество элементов
	 * @return array
	 */
	public function GetCommentsOnline($sTargetType,$iLimit) {
		/**
		 * Исключаем из выборки идентификаторы закрытых блогов (target_parent_id)
		 */
		$aCloseBlogs = ($this->oUserCurrent)
			? $this->Blog_GetInaccessibleBlogsByUser($this->oUserCurrent)
			: $this->Blog_GetInaccessibleBlogsByUser();

		$s=serialize($aCloseBlogs);
        $sCacheKey = "comment_online_{$sTargetType}_{$s}_{$iLimit}";

		if (false === ($data = $this->Cache_Get($sCacheKey))) {
			$data = $this->oMapper->GetCommentsOnline($sTargetType,$aCloseBlogs,$iLimit);
			$this->Cache_Set(
                $data,
                $sCacheKey,
                ["comment_online_update_{$sTargetType}"]
            );
		}
		$data=$this->GetCommentsAdditionalData($data);
		return $data;
	}
	/**
	 * Получить комменты по юзеру
	 *
	 * @param  int $sId	ID пользователя
	 * @param  string $sTargetType	Тип владельца комментария
	 * @param  int    $iPage	Номер страницы
	 * @param  int    $iPerPage	Количество элементов на страницу
	 * @return array
	 */
	public function GetCommentsByUserId($sId,$sTargetType,$iPage,$iPerPage) {
		/**
		 * Исключаем из выборки идентификаторы закрытых блогов
		 */
		$aCloseBlogs = ($this->oUserCurrent && $sId==$this->oUserCurrent->getId())
			? array()
			: $this->Blog_GetInaccessibleBlogsByUser();
		$s=serialize($aCloseBlogs);
        $sCacheKey = "comment_user_{$sId}_{$sTargetType}_{$iPage}_{$iPerPage}_{$s}";

		if (false === ($data = $this->Cache_Get($sCacheKey))) {
            $iCount = 0;
            $data = [
                'collection' => $this->oMapper->GetCommentsByUserId(
                    $sId,
                    $sTargetType,
                    $iCount,
                    $iPage,
                    $iPerPage,
                    [],
                    $aCloseBlogs
                ),
                'count' => $iCount
            ];
			$this->Cache_Set(
                $data,
                $sCacheKey,
                [
                    "comment_new_user_{$sId}_{$sTargetType}",
                    "comment_update_status_{$sTargetType}"
                ]
            );
		}
		$data['collection']=$this->GetCommentsAdditionalData($data['collection']);
		return $data;
	}
	/**
	 * Получает количество комментариев одного пользователя
	 *
	 * @param  id $sId ID пользователя
	 * @param  string $sTargetType	Тип владельца комментария
	 * @return int
	 */
	public function GetCountCommentsByUserId($sId,$sTargetType) {
		/**
		 * Исключаем из выборки идентификаторы закрытых блогов
		 */
		$aCloseBlogs = ($this->oUserCurrent && $sId==$this->oUserCurrent->getId())
			? array()
			: $this->Blog_GetInaccessibleBlogsByUser();
		$s=serialize($aCloseBlogs);

        $sCacheKey = "comment_count_user_{$sId}_{$sTargetType}_{$s}";
		if (false === ($data = $this->Cache_Get($sCacheKey))) {
			$data = $this->oMapper->GetCountCommentsByUserId($sId,$sTargetType,array(),$aCloseBlogs);
			$this->Cache_Set(
                $data,
                $sCacheKey,
                [
                    "comment_new_user_{$sId}_{$sTargetType}",
                    "comment_update_status_{$sTargetType}"
                ]
            );
		}
		return $data;
	}
	/**
	 * Получить комменты по рейтингу и дате
	 *
	 * @param  string $sDate	Дата за которую выводить рейтинг, т.к. кеширование происходит по дате, то дату лучше передавать с точностью до часа (минуты и секунды как 00:00)
	 * @param  string $sTargetType	Тип владельца комментария
	 * @param  int    $iLimit	Количество элементов
	 * @return array
	 */
	public function GetCommentsRatingByDate($sDate,$sTargetType,$iLimit=20) {
		/**
		 * Выбираем топики, комметарии к которым являются недоступными для пользователя
		 */
		$aCloseBlogs = ($this->oUserCurrent)
			? $this->Blog_GetInaccessibleBlogsByUser($this->oUserCurrent)
			: $this->Blog_GetInaccessibleBlogsByUser();
		$s=serialize($aCloseBlogs);
        $sCacheKey = "comment_rating_{$sDate}_{$sTargetType}_{$iLimit}_{$s}";
		/**
		 * Т.к. время передаётся с точностью 1 час то можно по нему замутить кеширование
		 */
		if (false === ($data = $this->Cache_Get($sCacheKey))) {
			$data = $this->oMapper->GetCommentsRatingByDate($sDate,$sTargetType,$iLimit,array(),$aCloseBlogs);
			$this->Cache_Set(
                $data, 
                $sCacheKey,
                [
					"comment_new_{$sTargetType}",
					"comment_update_status_{$sTargetType}",
					"comment_update_rating_{$sTargetType}"
				]
			);
		}
		$data=$this->GetCommentsAdditionalData($data);
		return $data;
	}
	/**
	 * Получить комменты по владельцу
	 *
	 * @param  int $sId	ID владельца коммента
	 * @param  string $sTargetType	Тип владельца комментария
	 * @return array('comments'=>array,'iMaxIdComment'=>int)
	 */
	public function GetCommentsByTargetId($sId,$sTargetType) {
        $sCacheKey = "comment_target_{$sId}_{$sTargetType}";

		if (false === ($aCommentsRec = $this->Cache_Get($sCacheKey))) {
			$aCommentsRow=$this->oMapper->GetCommentsByTargetId($sId,$sTargetType);
			if (count($aCommentsRow)) {
				$aCommentsRec=$this->BuildCommentsRecursive($aCommentsRow);
			}
            
			$this->Cache_Set(
                $aCommentsRec,
                $sCacheKey,
                ["comment_new_{$sTargetType}_{$sId}"]
            );
		}
		if (!isset($aCommentsRec['comments'])) {
			return array('comments'=>array(),'iMaxIdComment'=>0);
		}
		$aComments=$aCommentsRec;
		$aComments['comments']=$this->GetCommentsAdditionalData(array_keys($aCommentsRec['comments']));
		foreach ($aComments['comments'] as $oComment) {
			$oComment->setLevel($aCommentsRec['comments'][$oComment->getId()]);
		}
		return $aComments;

	}
	/**
	 * Возвращает количество дочерних комментариев у корневого коммента
	 *
	 * @param int $sId	ID владельца коммента
	 * @param string $sTargetType	Тип владельца комментария
	 * @return int
	 */
	public function GetCountCommentsRootByTargetId($sId,$sTargetType) {
		return $this->oMapper->GetCountCommentsRootByTargetId($sId,$sTargetType);
	}
	/**
	 * Добавляет коммент
	 *
	 * @param  ModuleComment_EntityComment $oComment	Объект комментария
	 * @param  ModuleComment_EntityComment $oCommentParent	Объект комментария-родителя
	 * @return bool|ModuleComment_EntityComment
	 */
	public function AddComment(ModuleComment_EntityComment $oComment, ModuleComment_EntityComment $oCommentParent=null) {
		$sId=$this->oMapper->AddComment($oComment);
		if ($sId) {
			if ($oComment->getTargetType()=='topic') {
				$this->Topic_increaseTopicCountComment($oComment->getTargetId());
			}
            if($oCommentParent !== null && !$oCommentParent->isFlagRaised(ModuleComment_EntityComment::FLAG_HAS_ANSWER)) {
                // Установить статус "есть ответ" комментария-родителя
                $oCommentParent->setFlag(ModuleComment_EntityComment::FLAG_HAS_ANSWER);
                $this->oMapper->UpdateCommentFlags($oCommentParent);
                $this->Cache_Clean(
                    Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG,
                    [
                        "comment_update",
                        "comment_update_{$oCommentParent->getTargetType()}_{$oCommentParent->getTargetId()}"
                    ]
                );
                $this->Cache_Delete("comment_{$oCommentParent->getId()}");
            }
			//чистим зависимые кеши
			$this->Cache_Clean(
                Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG,
                [
                    "comment_new_{$oComment->getTargetType()}",
                    "comment_new_user_{$oComment->getUserId()}_{$oComment->getTargetType()}",
                    "comment_new_{$oComment->getTargetType()}_{$oComment->getTargetId()}"
                ]
            );
			$oComment->setId($sId);
			return $oComment;
		}
		return false;
	}
	/**
	 * Обновляет коммент
	 *
	 * @param  ModuleComment_EntityComment $oComment	Объект комментария
	 * @return bool
	 */
	public function UpdateComment(ModuleComment_EntityComment $oComment) {
		if ($this->oMapper->UpdateComment($oComment)) {
			//чистим зависимые кеши
			$this->Cache_Clean(
				Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG,
				[
					"comment_update",
					"comment_update_{$oComment->getTargetType()}_{$oComment->getTargetId()}"
				]
			);
			$this->Cache_Delete("comment_{$oComment->getId()}");
			return true;
		}
		return false;
	}
	/**
	 * Обновляет рейтинг у коммента
	 *
	 * @param  ModuleComment_EntityComment $oComment	Объект комментария
	 * @return bool
	 */
	public function UpdateCommentRating(ModuleComment_EntityComment $oComment) {
		if ($this->oMapper->UpdateComment($oComment)) {
			//чистим зависимые кеши
			$this->Cache_Clean(
                Zend_Cache::CLEANING_MODE_MATCHING_TAG,
                [
                    "comment_update_rating_{$oComment->getTargetType()}"
                ]
            );
			$this->Cache_Delete("comment_{$oComment->getId()}");
			return true;
		}
		return false;
	}
	/**
	 * Обновляет статус у коммента - delete или publish
	 *
	 * @param  ModuleComment_EntityComment $oComment	Объект комментария
	 * @return bool
	 */
	public function UpdateCommentStatus(ModuleComment_EntityComment $oComment) {
		if ($this->oMapper->UpdateComment($oComment)) {
			/**
			 * Если комментарий удаляется, удаляем его из прямого эфира
			 */
			if($oComment->getDelete()) $this->DeleteCommentOnlineByArrayId($oComment->getId(),$oComment->getTargetType());
			/**
			 * Обновляем избранное
			 */
			$this->Favourite_SetFavouriteTargetPublish($oComment->getId(),'comment',!$oComment->getDelete());
			/**
			 * Чистим зависимые кеши
			 */
			$this->Cache_Clean(
                Zend_Cache::CLEANING_MODE_MATCHING_TAG,
                [
                    "comment_update_status_{$oComment->getTargetType()}"
                ]
            );
			$this->Cache_Delete("comment_{$oComment->getId()}");
			return true;
		}
		return false;
	}
	/**
	 * Устанавливает publish у коммента
	 *
	 * @param  int $sTargetId	ID владельца коммента
	 * @param  string $sTargetType	Тип владельца комментария
	 * @param  int    $iPublish	Статус отображать комментарии или нет
	 * @return bool
	 */
	public function SetCommentsPublish($sTargetId,$sTargetType,$iPublish) {
		if(!$aComments = $this->GetCommentsByTargetId($sTargetId,$sTargetType)) {
			return false;
		}
		if(!isset($aComments['comments']) or count($aComments)==0) {
			return;
		}

		$this->Cache_Clean(
            Zend_Cache::CLEANING_MODE_MATCHING_TAG,
            [
                "comment_update_status_{$sTargetType}"
            ]
        );
		/**
		 * Если статус публикации успешно изменен, то меняем статус в отметке "избранное".
		 * Если комментарии снимаются с публикации, удаляем их из прямого эфира.
		 */
		if($this->oMapper->SetCommentsPublish($sTargetId,$sTargetType,$iPublish)){
			$this->Favourite_SetFavouriteTargetPublish(array_keys($aComments['comments']),'comment',$iPublish);
			if($iPublish!=1) $this->DeleteCommentOnlineByTargetId($sTargetId,$sTargetType);
			return true;
		}
		return false;
	}
	/**
	 * Удаляет коммент из прямого эфира
	 *
	 * @param  int $sTargetId	ID владельца коммента
	 * @param  string $sTargetType	Тип владельца комментария
	 * @return bool
	 */
	public function DeleteCommentOnlineByTargetId($sTargetId,$sTargetType) {
		$this->Cache_Clean(
            Zend_Cache::CLEANING_MODE_MATCHING_TAG,
            [
                "comment_online_update_{$sTargetType}"
            ]
        );
		return $this->oMapper->DeleteCommentOnlineByTargetId($sTargetId,$sTargetType);
	}
	/**
	 * Добавляет новый коммент в прямой эфир
	 *
	 * @param ModuleComment_EntityCommentOnline $oCommentOnline	Объект онлайн комментария
	 * @return bool|int
	 */
	public function AddCommentOnline(ModuleComment_EntityCommentOnline $oCommentOnline) {
		$this->Cache_Clean(
            Zend_Cache::CLEANING_MODE_MATCHING_TAG,
            [
                "comment_online_update_{$oCommentOnline->getTargetType()}"
            ]
        );
		return $this->oMapper->AddCommentOnline($oCommentOnline);
	}
	/**
	 * Получить новые комменты для владельца
	 *
	 * @param int $sId	ID владельца коммента
	 * @param string $sTargetType	Тип владельца комментария
	 * @param int $sIdCommentLast ID последнего прочитанного комментария
	 * @return array('comments'=>array,'iMaxIdComment'=>int)
	 */
	public function GetCommentsNewByTargetId($sId,$sTargetType,$sIdCommentLast) {
        $sCacheKey = "comment_target_{$sId}_{$sTargetType}_{$sIdCommentLast}";

		if (false === ($aComments = $this->Cache_Get($sCacheKey))) {
			$aComments=$this->oMapper->GetCommentsNewByTargetId($sId,$sTargetType,$sIdCommentLast);
			$this->Cache_Set(
                $aComments,
                $sCacheKey,
                ["comment_new_{$sTargetType}_{$sId}"]
            );
		}
		if (count($aComments)==0) {
			return array('comments'=>array(),'iMaxIdComment'=>0);
		}

		$iMaxIdComment=max($aComments);
		$aCmts=$this->GetCommentsAdditionalData($aComments);
		$oViewerLocal=$this->Viewer_GetLocalViewer();
		$oViewerLocal->Assign('oUserCurrent',$this->User_GetUserCurrent());
		$oViewerLocal->Assign('bOneComment',true);
		$oViewerLocal->Assign('sAuthorNotice', $aLang.topic_author);
		if($sTargetType=='topic') {
			$oViewerLocal->Assign('bAddCommentPermission',$this->Topic_GetTopicById($sId)->getIsAllowAddComment());
		} else {
			$oViewerLocal->Assign('bNoCommentFavourites',true);
		}
		$aCmt=array();
		foreach ($aCmts as $oComment) {
			$oViewerLocal->Assign('oComment',$oComment);
			$sText=$oViewerLocal->Fetch($this->GetTemplateCommentByTarget($sId,$sTargetType));
			$aCmt[]=array(
				'html' => $sText,
				'obj'  => $oComment,
			);
		}
		return array('comments'=>$aCmt,'iMaxIdComment'=>$iMaxIdComment);
	}
	/**
	 * Возвращает шаблон комментария для рендеринга
	 * Плагин может переопределить данный метод и вернуть свой шаблон в зависимости от типа
	 *
	 * @param int $iTargetId	ID объекта комментирования
	 * @param string $sTargetType	Типа объекта комментирования
	 * @return string
	 */
	public function GetTemplateCommentByTarget($iTargetId,$sTargetType) {
		return "comment.tpl";
	}
	/**
	 * Строит дерево комментариев
	 *
	 * @param array $aComments	Список комментариев
	 * @param bool $bBegin	Флаг начала построения дерева, для инициализации параметров внутри метода
	 * @return array('comments'=>array,'iMaxIdComment'=>int)
	 */
	protected function BuildCommentsRecursive($aComments,$bBegin=true) {
		static $aResultCommnets;
		static $iLevel;
		static $iMaxIdComment;
		if ($bBegin) {
			$aResultCommnets=array();
			$iLevel=0;
			$iMaxIdComment=0;
		}
		foreach ($aComments as $aComment) {
			$aTemp=$aComment;
			if ($aComment['comment_id']>$iMaxIdComment) {
				$iMaxIdComment=$aComment['comment_id'];
			}
			$aTemp['level']=$iLevel;
			unset($aTemp['childNodes']);
			$aResultCommnets[$aTemp['comment_id']]=$aTemp['level'];
			if (isset($aComment['childNodes']) and count($aComment['childNodes'])>0) {
				$iLevel++;
				$this->BuildCommentsRecursive($aComment['childNodes'],false);
			}
		}
		$iLevel--;
		return array('comments'=>$aResultCommnets,'iMaxIdComment'=>$iMaxIdComment);
	}
	/**
	 * Получает привязку комментария к ибранному(добавлен ли коммент в избранное у юзера)
	 *
	 * @param  int $sCommentId	ID комментария
	 * @param  int $sUserId	ID пользователя
	 * @return ModuleFavourite_EntityFavourite|null
	 */
	public function GetFavouriteComment($sCommentId,$sUserId) {
		return $this->Favourite_GetFavourite($sCommentId,'comment',$sUserId);
	}
	/**
	 * Получить список избранного по списку айдишников
	 *
	 * @param array $aCommentId	Список ID комментов
	 * @param int $sUserId	ID пользователя
	 * @return array
	 */
	public function GetFavouriteCommentsByArray($aCommentId,$sUserId) {
		return $this->Favourite_GetFavouritesByArray($aCommentId,'comment',$sUserId);
	}
	/**
	 * Получает список комментариев из избранного пользователя
	 *
	 * @param  int $sUserId	ID пользователя
	 * @param  int    $iCurrPage	Номер страницы
	 * @param  int    $iPerPage	Количество элементов на страницу
	 * @return array
	 */
	public function GetCommentsFavouriteByUserId($sUserId,$iCurrPage,$iPerPage) {
		$aCloseTopics = array();
		/**
		 * Получаем список идентификаторов избранных комментов
		 */
		$data = ($this->oUserCurrent && $sUserId==$this->oUserCurrent->getId())
			? $this->Favourite_GetFavouritesByUserId($sUserId,'comment',$iCurrPage,$iPerPage,$aCloseTopics)
			: $this->Favourite_GetFavouriteOpenCommentsByUserId($sUserId,$iCurrPage,$iPerPage);
		/**
		 * Получаем комменты по переданому массиву айдишников
		 */
		$data['collection']=$this->GetCommentsAdditionalData($data['collection']);
		return $data;
	}
	/**
	 * Возвращает число комментариев в избранном
	 *
	 * @param  int $sUserId	ID пользователя
	 * @return int
	 */
	public function GetCountCommentsFavouriteByUserId($sUserId) {
		return ($this->oUserCurrent && $sUserId==$this->oUserCurrent->getId())
			? $this->Favourite_GetCountFavouritesByUserId($sUserId,'comment')
			: $this->Favourite_GetCountFavouriteOpenCommentsByUserId($sUserId);
	}
	/**
	 * Добавляет комментарий в избранное
	 *
	 * @param  ModuleFavourite_EntityFavourite $oFavourite	Объект избранного
	 * @return bool|ModuleFavourite_EntityFavourite
	 */
	public function AddFavouriteComment(ModuleFavourite_EntityFavourite $oFavourite) {
		if( ($oFavourite->getTargetType()=='comment')
			&& ($oComment=$this->Comment_GetCommentById($oFavourite->getTargetId()))
			&& in_array($oComment->getTargetType(),Config::get('module.comment.favourite_target_allow'))) {
			return $this->Favourite_AddFavourite($oFavourite);
		}
		return false;
	}
	/**
	 * Удаляет комментарий из избранного
	 *
	 * @param  ModuleFavourite_EntityFavourite $oFavourite	Объект избранного
	 * @return bool
	 */
	public function DeleteFavouriteComment(ModuleFavourite_EntityFavourite $oFavourite) {
		if( ($oFavourite->getTargetType()=='comment')
			&& ($oComment=$this->Comment_GetCommentById($oFavourite->getTargetId()))
			&& in_array($oComment->getTargetType(),Config::get('module.comment.favourite_target_allow'))) {
			return $this->Favourite_DeleteFavourite($oFavourite);
		}
		return false;
	}
	/**
	 * Удаляет комментарии из избранного по списку
	 *
	 * @param  array $aCommentId	Список ID комментариев
	 * @return bool
	 */
	public function DeleteFavouriteCommentsByArrayId($aCommentId) {
		return $this->Favourite_DeleteFavouriteByTargetId($aCommentId, 'comment');
	}
	/**
	 * Удаляет комментарии из базы данных
	 *
	 * @param   array|int $aTargetId	Список ID владельцев
	 * @param   string $sTargetType	Тип владельцев
	 * @return  bool
	 */
	public function DeleteCommentByTargetId($aTargetId,$sTargetType) {
		if(!is_array($aTargetId)) $aTargetId = array($aTargetId);
		/**
		 * Получаем список идентификаторов удаляемых комментариев
		 */
		$aCommentsId = array();
		foreach ($aTargetId as $sTargetId) {
			$aComments=$this->GetCommentsByTargetId($sTargetId,$sTargetType);
			$aCommentsId = array_merge($aCommentsId, array_keys($aComments['comments']));
            /**
             * Чистим зависимые кеши
             */
            $this->Cache_Clean(
                Zend_Cache::CLEANING_MODE_MATCHING_TAG,
                [
                    "comment_target_{$sTargetId}_{$sTargetType}"
                ]
            );
		}
		/**
		 * Если ни одного комментария не найдено, выходим
		 */
		if(!count($aCommentsId)) return true;
        /**
         * Удаляем кеш для каждого комментария
         */
        foreach($aCommentsId as $iCommentId) $this->Cache_Delete("comment_{$iCommentId}");

        if($this->oMapper->DeleteCommentByTargetId($aTargetId,$sTargetType)){
			/**
			 * Удаляем комментарии из избранного
			 */
			$this->DeleteFavouriteCommentsByArrayId($aCommentsId);
			/**
			 * Удаляем комментарии к топику из прямого эфира
			 */
			$this->DeleteCommentOnlineByArrayId($aCommentsId,$sTargetType);
			/**
			 * Удаляем голосование за комментарии
			 */
			$this->Vote_DeleteVoteByTarget($aCommentsId,'comment');
			return true;
		}
		return false;
	}
	/**
	 * Удаляет коммент из прямого эфира по массиву переданных идентификаторов
	 *
	 * @param  array|int $aCommentId
	 * @param  string      $sTargetType	Тип владельцев
	 * @return bool
	 */
	public function DeleteCommentOnlineByArrayId($aCommentId,$sTargetType) {
		if(!is_array($aCommentId)) $aCommentId = array($aCommentId);
		/**
		 * Чистим кеш
		 */
		$this->Cache_Clean(
            Zend_Cache::CLEANING_MODE_MATCHING_TAG,
            [
                "comment_online_update_{$sTargetType}"
            ]
        );
		return $this->oMapper->DeleteCommentOnlineByArrayId($aCommentId,$sTargetType);
	}
	/**
	 * Меняем target parent по массиву идентификаторов
	 *
	 * @param  int $sParentId	Новый ID родителя владельца
	 * @param  string $sTargetType	Тип владельца
	 * @param  array|int $aTargetId	Список ID владельцев
	 * @return bool
	 */
	public function UpdateTargetParentByTargetId($sParentId, $sTargetType, $aTargetId) {
		if(!is_array($aTargetId)) $aTargetId = array($aTargetId);
		// чистим зависимые кеши
		$this->Cache_Clean(
            Zend_Cache::CLEANING_MODE_MATCHING_TAG,
            [
                "comment_new_{$sTargetType}"
            ]
        );

		return $this->oMapper->UpdateTargetParentByTargetId($sParentId, $sTargetType, $aTargetId);
	}
	/**
	 * Меняем target parent по массиву идентификаторов в таблице комментариев online
	 *
	 * @param  int $sParentId	Новый ID родителя владельца
	 * @param  string $sTargetType	Тип владельца
	 * @param  array|int $aTargetId	Список ID владельцев
	 * @return bool
	 */
	public function UpdateTargetParentByTargetIdOnline($sParentId, $sTargetType, $aTargetId) {
		if(!is_array($aTargetId)) $aTargetId = array($aTargetId);
		// чистим зависимые кеши
		$this->Cache_Clean(
            Zend_Cache::CLEANING_MODE_MATCHING_TAG,
            [
                "comment_online_update_{$sTargetType}"
            ]
        );

		return $this->oMapper->UpdateTargetParentByTargetIdOnline($sParentId, $sTargetType, $aTargetId);
	}
	/**
	 * Меняет target parent на новый
	 *
	 * @param int $sParentId	Прежний ID родителя владельца
	 * @param string $sTargetType	Тип владельца
	 * @param int $sParentIdNew	Новый ID родителя владельца
	 * @return bool
	 */
	public function MoveTargetParent($sParentId, $sTargetType, $sParentIdNew) {
		$this->Cache_Clean(
            Zend_Cache::CLEANING_MODE_MATCHING_TAG,
            [
				"comment_new_{$sTargetType}"
			]
		);
		return $this->oMapper->MoveTargetParent($sParentId, $sTargetType, $sParentIdNew);
	}
	/**
	 * Меняет target parent на новый в прямом эфире
	 *
	 * @param int $sParentId	Прежний ID родителя владельца
	 * @param string $sTargetType	Тип владельца
	 * @param int $sParentIdNew	Новый ID родителя владельца
	 * @return bool
	 */
	public function MoveTargetParentOnline($sParentId, $sTargetType, $sParentIdNew) {
		$this->Cache_Clean(
            Zend_Cache::CLEANING_MODE_MATCHING_TAG,
            [
                "comment_online_update_{$sTargetType}"
            ]
        );
		return $this->oMapper->MoveTargetParentOnline($sParentId, $sTargetType, $sParentIdNew);
	}
	/**
	 * Пересчитывает счетчик избранных комментариев
	 *
	 * @return bool
	 */
	public function RecalculateFavourite() {
		return $this->oMapper->RecalculateFavourite();
	}
	/**
	 * Получает список комментариев по фильтру
	 *
	 * @param array $aFilter	Фильтр выборки
	 * @param array $aOrder		Сортировка
	 * @param int $iCurrPage	Номер текущей страницы
	 * @param int $iPerPage		Количество элементов на одну страницу
	 * @param array $aAllowData		Список типов данных, которые нужно подтянуть к списку комментов
	 * @return array
	 */
	public function GetCommentsByFilter($aFilter,$aOrder,$iCurrPage,$iPerPage,$aAllowData=null) {
		if (is_null($aAllowData)) {
			$aAllowData=array('target','user'=>array());
		}
		$aCollection=$this->oMapper->GetCommentsByFilter($aFilter,$aOrder,$iCount,$iCurrPage,$iPerPage);
		return array('collection'=>$this->GetCommentsAdditionalData($aCollection,$aAllowData),'count'=>$iCount);
	}
	/**
	 * Алиас для корректной работы ORM
	 *
	 * @param array $aCommentId	Список ID комментариев
	 * @return array
	 */
	public function GetCommentItemsByArrayId($aCommentId) {
		return $this->GetCommentsByArrayId($aCommentId);
	}
	/**
	 * Обновляет флаги коммента
	 *
	 * @param  ModuleComment_EntityComment $oComment	Объект комментария
	 * @return bool
	 */
	public function UpdateCommentFlags(ModuleComment_EntityComment $oComment) {
		if ($this->oMapper->UpdateCommentFlags($oComment)) {
			//чистим зависимые кеши
			$this->Cache_Clean(
				Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG,
				[
					"comment_update",
					"comment_update_{$oComment->getTargetType()}_{$oComment->getTargetId()}"
				]
			);
			$this->Cache_Delete("comment_{$oComment->getId()}");
			return true;
		}
		return false;
	}
	/**
	 * Добавляет элемент истории изменений коммента
	 *
	 * @param  ModuleComment_EntityCommentHistoryItem $oHistoryItem
	 * @return int
	 */
	public function AddCommentHistoryItem(ModuleComment_EntityCommentHistoryItem $oHistoryItem) {
		return $this->oMapper->AddCommentHistoryItem($oHistoryItem);
	}
	/**
	 * Пост-обработка комментария
	 *
	 * @param  ModuleComment_EntityComment $oComment
	 */
	public function PostProcessComment($oComment) {
		if (
			   ($oComment->getTargetType() == 'topic')
			&& ($oTopic = $this->Topic_GetTopicById($oComment->getTargetId()))
			&& ($oBlog = $oTopic->getBlog())
		)
		{
			// Автоматически создаём BlogUser
			if (($oBlog->getType() == 'open') && !$this->Blog_GetBlogUserByBlogIdAndUserId($oBlog->getId(),$oComment->getUserId())) {
				$oBlogUserNew=Engine::GetEntity('Blog_BlogUser');
				$oBlogUserNew->setBlogId($oBlog->getId());
				$oBlogUserNew->setUserId($oComment->getUserId());
				$oBlogUserNew->setUserRole(ModuleBlog::BLOG_USER_ROLE_USER);
				$oBlogUserNew->setDeleted(true); // Если поставить здесь false, то юзеры будут автоматически подписываться на блог при добавлении комментариев. Причём, если юзер отпишется, то в следующий раз он не будет автоматически снова подписан.
				$this->Blog_AddRelationBlogUser($oBlogUserNew);
			}
		}
	}
}
