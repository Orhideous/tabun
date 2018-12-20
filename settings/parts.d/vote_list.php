<?php

return [
    'comment' => [
        'enable' => true,
        'expose_from_date' => 1545339600,   // strtotime('2018-12-21 +03:00')
        'user_required_level' => 2,
        'user_required_rating' => +20.0,
        'superuser_required_level' => 128,
        'superuser_required_rating' => +20.0,
        'date_sort_mode' => SORT_ASC,
        'enable_from_list' => true,
    ],
    'topic' => [
        'enable' => true,
        'expose_from_date' => 1545339600,   // strtotime('2018-12-21 +03:00')
        'user_required_level' => 2,
        'user_required_rating' => +20.0,
        'superuser_required_level' => 128,
        'superuser_required_rating' => +20.0,
        'date_sort_mode' => SORT_ASC,
        'enable_from_list' => true,
    ],
    'blog' => [
        'enable' => true,
        'expose_from_date' => 1545339600,   // strtotime('2018-12-21 +03:00')
        'user_required_level' => 2,
        'user_required_rating' => +20.0,
        'superuser_required_level' => 128,
        'superuser_required_rating' => +20.0,
        'date_sort_mode' => SORT_DESC,
    ],
    'user' => [
        'enable' => true,
        'expose_from_date' => 1545339600,   // strtotime('2018-12-21 +03:00')
        'user_required_level' => 2,
        'user_required_rating' => +20.0,
        'superuser_required_level' => 128,
        'superuser_required_rating' => +20.0,
        'date_sort_mode' => SORT_DESC,
    ],
];
// enable                      — Статус функции
// expose_from_date            — Дата, начиная с которой голоса открываются для user_required_level, unix timestamp
// user_required_level         — Требуемый уровень пользователя для просмотра открытых голосов, а также просто запроса списка
// superuser_required_level    — Требуемый уровень для просмотра скрытых голосов
// date_sort_mode              — Режим сортировки голосов по дате, ASC/DESC
// Уровни пользователей:
//     1   - Любой посетитель, имеющий доступ к объекту на чтение
//     2   - Любой авторизованный пользователь, имеющий доступ к объекту на чтение
//     128 - Администратор сайта
// user_required_rating        — Требуемый рейтинг* пользователя для просмотра открытых голосов, а также просто запроса списка
// superuser_required_rating   — Требуемый рейтинг* пользователя для просмотра скрытых голосов
// * Для всех неавторизованных посетителей рейтинг считается равным 0.0.
// См. также ModuleACL::VoteListCheckAccess
