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
 * Настройки HTML вида
 */
$config['view']['skin'] = 'synio';
$config['view']['name'] = 'Табун - место, где пасутся брони';
$config['view']['description'] = 'Блогосфера фандома My Little Pony: Friendship is Magic';
$config['view']['keywords'] = 'пони, брони, my little pony, friendship is magic';
$config['view']['noindex'] = false;             // "прятать" или нет ссылки от поисковиков, оборачивая их в тег <noindex> и добавляя rel="nofollow"
$config['view']['img_resize_width'] = 570;      // до какого размера в пикселях ужимать картинку по щирине при загрузки её в топики и комменты
$config['view']['img_max_width'] = 10000;       // максимальная ширина загружаемых изображений в пикселях
$config['view']['img_max_height'] = 10000;      // максимальная высота загружаемых изображений в пикселях

/**
 * Настройки СЕО для вывода топиков
 */
$config['seo']['description_words_count'] = 20;               // количество слов из топика для вывода в метатег description

/**
 * Настройка основных блоков
 */
$config['block']['stream']['row'] = 20;                     // сколько записей выводить в блоке "Прямой эфир"
$config['block']['blogs']['row'] = 20;                      // сколько записей выводить в блоке "Блоги"
$config['block']['tags']['tags_count'] = 20;                // сколько тегов выводить в блоке "теги"
$config['block']['tags']['personal_tags_count'] = 20;       // сколько тегов пользователя выводить в блоке "теги"

/**
 * Настройка пагинации
 */
$config['pagination']['pages']['count'] = 4;                // количество ссылок на другие страницы в пагинации

/**
 * Настройка путей
 */
$config['path']['root']['web'] = 'https://' . $_SERVER['HTTP_HOST'];         // полный WEB адрес сайта
$config['path']['root']['server'] = dirname(dirname(__FILE__));             // полный путь до сайта в файловой системе
$config['path']['root']['engine'] = '___path.root.server___/engine';        // полный путь до сайта в файловой системе;
$config['path']['root']['engine_lib'] = '___path.root.web___/engine/lib';   // полный путь до сайта в файловой системе

$config['path']['static']['url'] = '/static';                               // url для локальных файлов TODO: удалить этот fallback
$config['path']['uploads']['url'] = '/storage';                             // url для отдачи загруженых файлов
$config['path']['uploads']['storage'] = '___path.root.server___/storage';   // путь для хранения загруженых файлов
$config['path']['offset_request_url'] = 0;                                  // иногда помогает если сервер использует внутренние реврайты
/**
 * Настройки шаблонизатора Smarty
 */
$config['path']['smarty']['template'] = '___path.root.server___/templates/skin/___view.skin___';
$config['path']['smarty']['compiled'] = '/var/smarty/compiled';
$config['path']['smarty']['cache'] = '/var/smarty/cache';
$config['path']['smarty']['plug'] = '___path.root.engine___/modules/viewer/plugs';
$config['smarty']['compile_check'] = true; // Проверять или нет файлы шаблона на изменения перед компиляцией, false может значительно увеличить быстродействие, но потребует ручного удаления кеша при изменения шаблона
/**
 * Настройки куков
 */
$config['sys']['cookie']['host'] = null;                    // хост для установки куков
$config['sys']['cookie']['path'] = '/';                     // путь для установки куков
$config['sys']['cookie']['time'] = 60 * 60 * 24 * 7;        // время жизни куки когда пользователь остается залогиненым на сайте
/**
 * Настройки сессий
 */
$config['sys']['session']['name'] = 'TABUNSESSIONID';           // название сессии
$config['sys']['session']['timeout'] = '___sys.cookie.time___'; // Тайм-аут сессии в секундах (тот же, что и в cookies)
$config['sys']['session']['host'] = '___sys.cookie.host___';    // хост сессии в куках
$config['sys']['session']['path'] = '___sys.cookie.path___';    // путь сессии в куках
/**
 * Настройки почтовых уведомлений
 */
$config['sys']['mail']['from_email'] = 'noreply@example.com';    // Мыло с которого отправляются все уведомления
$config['sys']['mail']['from_name'] = 'PonyMail';              // Имя с которого отправляются все уведомления
$config['sys']['mail']['include_comment'] = true;                   // Включает в уведомление о новых комментах текст коммента
$config['sys']['mail']['include_talk'] = true;                      // Включает в уведомление о новых личных сообщениях текст сообщения

/**
 * Настройки кеширования
 */
$config['sys']['cache']['use'] = true;              // использовать кеширование или нет
$config['sys']['cache']['servers'] = [
    [
        'host' => '127.0.0.1',
        'port' => 6379,
        'dbindex' => 11,
    ],
];
$config['sys']['cache']['lifetime'] = 3600;
$config['sys']['cache']['automatic_serialization'] = true;
$config['sys']['cache']['dir'] = '/tmp/';           // каталог для файлового кеша, также используется для временных картинок. По умолчанию подставляем каталог для хранения сессий
$config['sys']['cache']['prefix'] = 'tabun_cache';  // префикс кеширования, чтоб можно было на одной машине держать несколько сайтов с общим кешевым хранилищем

/**
 * Настройки логирования
 */
$config['sys']['logs']['dir'] = '/log';                     // папка с логами приложения
$config['sys']['logs']['file'] = 'log.log';                 // файл общего лога
$config['sys']['logs']['sql_query'] = false;                // логировать или нет SQL запросы
$config['sys']['logs']['sql_error'] = true;                 // логировать или нет ошибки SQl
$config['sys']['logs']['sql_error_file'] = 'sql_error.log'; // файл лога ошибок SQL
/**
 * Общие настройки
 */
$config['general']['close'] = false;                                    // использовать закрытый режим работы сайта
$config['general']['rss_editor_mail'] = '___sys.mail.from_email___';    // мыло редактора РСС
$config['general']['reg']['invite'] = false;                            // использовать режим регистрации по приглашению
$config['general']['reg']['activation'] = true;                         // использовать активацию при регистрации
$config['general']['prosody']['key'] = 'secretkey';			// ключ для доступа с сервера prosody
/**
 * Языковые настройки
 */
$config['lang']['current'] = 'russian';                                 // текущий язык текстовок
$config['lang']['default'] = 'russian';                                 // язык, который будет использовать на сайте по умолчанию
$config['lang']['path'] = '___path.root.server___/templates/language';  // полный путь до языковых файлов
$config['lang']['load_to_js'] = array();                                // Массив текстовок, которые необходимо прогружать на страницу в виде JS хеша, позволяет использовать текстовки внутри js
/**
 * Настройки ACL(Access Control List — список контроля доступа)
 */
$config['acl']['create']['blog']['rating'] = 1000;                      // порог рейтинга при котором юзер может создать коллективный блог
$config['acl']['create']['comment']['rating'] = -10;                    // порог рейтинга при котором юзер может добавлять комментарии
$config['acl']['create']['comment']['limit_time'] = 60 * 10;            // время в секундах между постингом комментариев, если 0 то ограничение по времени не будет работать
$config['acl']['create']['comment']['limit_time_rating'] = -1;          // рейтинг, выше которого перестаёт действовать ограничение по времени на постинг комментов. Не имеет смысла при $config['acl']['create']['comment']['limit_time']=0
$config['acl']['create']['topic']['limit_time'] = 60 * 60 * 1;          // время в секундах между созданием записей, если 0 то ограничение по времени не будет работать
$config['acl']['create']['topic']['limit_time_rating'] = 100;           // рейтинг, выше которого перестаёт действовать ограничение по времени на создание записей
$config['acl']['create']['topic']['limit_rating'] = -100;               // порог рейтинга при котором юзер может создавать топики (учитываются любые блоги, включая персональные), как дополнительная защита от спама/троллинга
$config['acl']['create']['talk']['limit_time'] = 60;                    // время в секундах между отправкой инбоксов, если 0 то ограничение по времени не будет работать
$config['acl']['create']['talk']['limit_time_rating'] = 10;             // рейтинг, выше которого перестаёт действовать ограничение по времени на отправку инбоксов
$config['acl']['create']['talk_comment']['limit_time'] = 10;            // время в секундах между отправкой инбоксов, если 0 то ограничение по времени не будет работать
$config['acl']['create']['talk_comment']['limit_time_rating'] = 10;     // рейтинг, выше которого перестаёт действовать ограничение по времени на отправку инбоксов
$config['acl']['vote']['comment']['rating'] = -1;                       // порог рейтинга при котором юзер может голосовать за комментарии
$config['acl']['vote']['blog']['rating'] = -5;                          // порог рейтинга при котором юзер может голосовать за блог
$config['acl']['vote']['topic']['rating'] = -5;                         // порог рейтинга при котором юзер может голосовать за топик
$config['acl']['vote']['user']['rating'] = -10;                         // порог рейтинга при котором юзер может голосовать за пользователя
$config['acl']['vote']['topic']['limit_time'] = 60 * 60 * 24 * 21;      // ограничение времени голосования за топик
$config['acl']['vote']['comment']['limit_time'] = 60 * 60 * 24 * 7;     // ограничение времени голосования за комментарий
$config['acl']['edit']['comment']['limit_time'] = 60 * 5;               // ограничение времени редактирования комментария в блоге
$config['acl']['edit']['talk_comment']['limit_time'] = 60 * 60 * 1;     // ограничение времени редактирования комментария в ЛС
$config['acl']['edit']['comment']['enable_lock'] = false;               // разрешить блокировку редактирования комментария в блоге
/**
 * Настройки модулей
 */
// Модуль Blog
$config['module']['blog']['per_page'] = 20;             // Число блогов на страницу
$config['module']['blog']['users_per_page'] = 50;       // Число пользователей блога на страницу
$config['module']['blog']['personal_good'] = -1;        // Рейтинг топика в персональном блоге ниже которого он считается плохим
$config['module']['blog']['collective_good'] = -1;      // рейтинг топика в коллективных блогах ниже которого он считается плохим
$config['module']['blog']['index_good'] = 120;          // Рейтинг топика выше которого(включительно) он попадает на главную
$config['module']['blog']['encrypt'] = '';              // Ключ XXTEA шифрования идентификаторов в ссылках приглашения в блоги
$config['module']['blog']['avatar_size'] = [48, 24, 0]; // Список размеров аватаров у блога. 0 - исходный размер
$config['module']['blog']['index_comment_good'] = -10;  // Рейтинг топика выше которого(включительно) комментарии из него попадают в "Прямой эфир"
$config['module']['blog']['index_display_good'] = -30;  // Рейтинг топика НИЖЕ которого(включительно) он попадает в спецблог
$config['module']['blog']['semi_closed_id'] = [];       // Список полузакрытых блогов
$config['module']['blog']['selective_filter'] = [];     // Поблоговое выставление фильтра рейтинга

// Модуль Topic
$config['module']['topic']['new_time'] = 60 * 60 * 24 * 1;  // Время в секундах в течении которого топик считается новым
$config['module']['topic']['per_page'] = 10;                // Число топиков на одну страницу
$config['module']['topic']['max_length'] = 64000;           // Максимальное количество символов
$config['module']['topic']['question_max_length'] = 6400;   // Максимальное количество символов в одном топике-опросе
$config['module']['topic']['allow_empty_tags'] = false;     // Разрешать или нет не заполнять теги
// Модуль User
$config['module']['user']['per_page'] = 50;                             // Число юзеров на страницу на странице статистики и в профиле пользователя
$config['module']['user']['friend_on_profile'] = 50;                    // Ограничение на вывод числа друзей пользователя на странице его профиля
$config['module']['user']['friend_notice']['delete'] = false;           // Отправить talk-сообщение в случае удаления пользователя из друзей
$config['module']['user']['friend_notice']['accept'] = true;            // Отправить talk-сообщение в случае одобрения заявки на добавление в друзья
$config['module']['user']['friend_notice']['reject'] = false;           // Отправить talk-сообщение в случае отклонения заявки на добавление в друзья
$config['module']['user']['avatar_size'] = array(100, 64, 48, 24, 0);   // Список размеров аватаров у пользователя. 0 - исходный размер
$config['module']['user']['login']['min_size'] = 3;                     // Минимальное количество символов в логине
$config['module']['user']['login']['max_size'] = 30;                    // Максимальное количество символов в логине
$config['module']['user']['login']['charset'] = '0-9a-z_\-';            // Допустимые в имени пользователя символы
$config['module']['user']['time_active'] = 60 * 60 * 24 * 30 * 3;       // Число секунд с момента последнего посещения пользователем сайта, в течение которых он считается активным
$config['module']['user']['usernote_text_max'] = 64000;                 // Максимальный размер заметки о пользователе
$config['module']['user']['usernote_per_page'] = 100;                   // Число заметок на одну страницу
$config['module']['user']['userfield_max_identical'] = 3;               // Максимальное число контактов одного типа
$config['module']['user']['profile_photo_width'] = 250;                 // ширина квадрата фотографии в профиле, px
$config['module']['user']['name_max'] = 30;                             // максимальная длинна имени в профиле пользователя
$config['module']['user']['captcha_use_registration'] = true;           // проверять поле капчи при регистрации пользователя
$config['module']['user']['bad_rating'] = -10;                          // Предел деактивации пользователя

// Модуль Comment
$config['module']['comment']['per_page'] = 50;                              // Число комментариев на одну страницу(это касается только полного списка комментариев прямого эфира)
$config['module']['comment']['bad'] = -5;                                   // Рейтинг комментария, начиная с которого он будет скрыт
$config['module']['comment']['max_tree'] = 40;                              // Максимальная вложенность комментов при отображении
$config['module']['comment']['favourite_target_allow'] = array('topic');    // Список типов комментов, которые разрешено добавлять в избранное
$config['module']['comment']['comment_max_length'] = 64000;                 // максимальная длинна комментария
// Модуль Talk
$config['module']['talk']['per_page'] = 100;            // Число приватных сообщений на одну страницу
$config['module']['talk']['encrypt'] = '';              // Ключ XXTEA шифрования идентификаторов в ссылках
$config['module']['talk']['max_users'] = 100;           // Максимальное число адресатов в одном личном сообщении
// Модуль Lang
$config['module']['lang']['delete_undefined'] = true;   // Если установлена true, то модуль будет автоматически удалять из языковых конструкций переменные вида %%var%%, по которым не была произведена замена
// Модуль Notify
$config['module']['notify']['delayed'] = false;         // Указывает на необходимость использовать режим отложенной рассылки сообщений на email
$config['module']['notify']['insert_single'] = true;    // Если опция установлена в true, систему будет собирать записи заданий удаленной публикации, для вставки их в базу единым INSERT
$config['module']['notify']['per_process'] = 1000;      // Количество отложенных заданий, обрабатываемых одним крон-процессом
// Модуль Image
$config['module']['image']['allowed_mime'] = [
    "image/bmp" => "bmp",
    "image/x-bmp" => "bmp",
    "image/x-ms-bmp" => "bmp",
    "image/gif" => "gif",
    "image/png" => "png",
    "image/jpeg" => "jpg",
    "image/pjpeg" => "jpg",
    "image/svg+xml" => "svg",
    "image/tiff" => "tiff",
    "image/x-tiff" => "tiff",
];
$config['module']['image']['max_x'] = 10000;
$config['module']['image']['max_y'] = 20000;
$config['module']['image']['max_size'] = 30000; // максимальный размер картинки в kB для загрузки по URL

// Модуль Security
$config['module']['security']['hash'] = "";             // "примесь" к строке, хешируемой в качестве security-кода

// Модуль Stream
$config['module']['userfeed']['count_default'] = 20;    // Число топиков в ленте по умолчанию
$config['module']['stream']['count_default'] = 20;      // Число топиков в ленте по умолчанию
$config['module']['stream']['disable_vote_events'] = false;

// Модуль Search
$config['module']['search']['index'] = "tabun"; // Имя индекса в Elasticsearch
$config['module']['search']['topic_key'] = "topic";
$config['module']['search']['comment_key'] = "comment";
$config['module']['search']['per_page'] = 30;
// Какие модули должны быть загружены на старте
$config['module']['autoLoad'] = ['Hook', 'Cache', 'Security', 'Session', 'Lang', 'Message', 'User'];
/**
 * Настройка базы данных
 */
$config['db']['params']['host'] = 'localhost';
$config['db']['params']['port'] = '3306';
$config['db']['params']['user'] = '';
$config['db']['params']['pass'] = '';
$config['db']['params']['type'] = 'mysql';
$config['db']['params']['dbname'] = '';
/**
 * Настройка таблиц базы данных
 */
$config['db']['table']['prefix'] = 'ls_';

$config['db']['table']['user'] = '___db.table.prefix___user';
$config['db']['table']['blog'] = '___db.table.prefix___blog';
$config['db']['table']['topic'] = '___db.table.prefix___topic';
$config['db']['table']['topic_tag'] = '___db.table.prefix___topic_tag';
$config['db']['table']['comment'] = '___db.table.prefix___comment';
$config['db']['table']['vote'] = '___db.table.prefix___vote';
$config['db']['table']['topic_read'] = '___db.table.prefix___topic_read';
$config['db']['table']['blog_user'] = '___db.table.prefix___blog_user';
$config['db']['table']['favourite'] = '___db.table.prefix___favourite';
$config['db']['table']['favourite_tag'] = '___db.table.prefix___favourite_tag';
$config['db']['table']['talk'] = '___db.table.prefix___talk';
$config['db']['table']['talk_user'] = '___db.table.prefix___talk_user';
$config['db']['table']['talk_blacklist'] = '___db.table.prefix___talk_blacklist';
$config['db']['table']['friend'] = '___db.table.prefix___friend';
$config['db']['table']['topic_content'] = '___db.table.prefix___topic_content';
$config['db']['table']['topic_question_vote'] = '___db.table.prefix___topic_question_vote';
$config['db']['table']['user_administrator'] = '___db.table.prefix___user_administrator';
$config['db']['table']['comment_online'] = '___db.table.prefix___comment_online';
$config['db']['table']['comment_change_history'] = '___db.table.prefix___comment_change_history';
$config['db']['table']['invite'] = '___db.table.prefix___invite';
$config['db']['table']['page'] = '___db.table.prefix___page';
$config['db']['table']['city'] = '___db.table.prefix___city';
$config['db']['table']['city_user'] = '___db.table.prefix___city_user';
$config['db']['table']['country'] = '___db.table.prefix___country';
$config['db']['table']['country_user'] = '___db.table.prefix___country_user';
$config['db']['table']['reminder'] = '___db.table.prefix___reminder';
$config['db']['table']['session'] = '___db.table.prefix___session';
$config['db']['table']['notify_task'] = '___db.table.prefix___notify_task';
$config['db']['table']['userfeed_subscribe'] = '___db.table.prefix___userfeed_subscribe';
$config['db']['table']['stream_subscribe'] = '___db.table.prefix___stream_subscribe';
$config['db']['table']['stream_event'] = '___db.table.prefix___stream_event';
$config['db']['table']['stream_user_type'] = '___db.table.prefix___stream_user_type';
$config['db']['table']['user_field'] = '___db.table.prefix___user_field';
$config['db']['table']['user_field_value'] = '___db.table.prefix___user_field_value';
$config['db']['table']['subscribe'] = '___db.table.prefix___subscribe';
$config['db']['table']['user_note'] = '___db.table.prefix___user_note';
$config['db']['table']['geo_country'] = '___db.table.prefix___geo_country';
$config['db']['table']['geo_region'] = '___db.table.prefix___geo_region';
$config['db']['table']['geo_city'] = '___db.table.prefix___geo_city';
$config['db']['table']['geo_target'] = '___db.table.prefix___geo_target';
$config['db']['table']['user_changemail'] = '___db.table.prefix___user_changemail';

$config['db']['tables']['engine'] = 'InnoDB';
/**
 * Настройка memcache
 */
$config['memcache']['servers'][0]['host'] = 'localhost';
$config['memcache']['servers'][0]['port'] = '11211';
$config['memcache']['servers'][0]['persistent'] = true;
$config['memcache']['compression'] = true;
/**
 * Настройки роутинга
 */
$config['router']['rewrite'] = array();
// Правила реврайта для REQUEST_URI
$config['router']['uri'] = array(
    // короткий вызов топиков из личных блогов
    '~^(\d+)\.html~i' => "blog/\\1.html",
);
// Распределение action
$config['router']['page']['error'] = 'ActionError';
$config['router']['page']['registration'] = 'ActionRegistration';
$config['router']['page']['profile'] = 'ActionProfile';
$config['router']['page']['my'] = 'ActionMy';
$config['router']['page']['blog'] = 'ActionBlog';
$config['router']['page']['personal_blog'] = 'ActionPersonalBlog';
$config['router']['page']['index'] = 'ActionIndex';
$config['router']['page']['topic'] = 'ActionTopic';
$config['router']['page']['login'] = 'ActionLogin';
$config['router']['page']['people'] = 'ActionPeople';
$config['router']['page']['settings'] = 'ActionSettings';
$config['router']['page']['tag'] = 'ActionTag';
$config['router']['page']['talk'] = 'ActionTalk';
$config['router']['page']['comments'] = 'ActionComments';
$config['router']['page']['rss'] = 'ActionRss';
$config['router']['page']['question'] = 'ActionQuestion';
$config['router']['page']['blogs'] = 'ActionBlogs';
$config['router']['page']['search'] = 'ActionSearch';
$config['router']['page']['admin'] = 'ActionAdmin';
$config['router']['page']['ajax'] = 'ActionAjax';
$config['router']['page']['feed'] = 'ActionUserfeed';
$config['router']['page']['stream'] = 'ActionStream';
$config['router']['page']['subscribe'] = 'ActionSubscribe';
$config['router']['page']['page'] = 'ActionPage';
// Глобальные настройки роутинга
$config['router']['config']['action_default'] = 'index';
$config['router']['config']['action_not_found'] = 'error';

/**
 * Настройки вывода блоков
 */
$config['block']['rule_index_blog'] = [
    'action' => ['index', 'blog' => ['{topics}', '{topic}', '{blog}']],
    'blocks' => [
        'right' => ['sidetop', 'search', 'stream', 'donate', 'herdmind', 'blogs'],
    ]
];
$config['block']['rule_topic_type'] = [
    'action' => [
        'question' => ['add', 'edit'],
        'topic' => ['add', 'edit'],
    ],
    'blocks' => ['right' => ['blogInfo']],
];
$config['block']['rule_people'] = [
    'action' => ['people'],
    'blocks' => ['right' => ['actions/ActionPeople/sidebar.tpl']],
];
$config['block']['rule_personal_blog'] = [
    'action' => ['personal_blog'],
    'blocks' => ['right' => ['sidetop', 'search', 'stream', 'donate', 'herdmind']],
];
$config['block']['rule_profile'] = [
    'action' => ['profile', 'talk', 'settings'],
    'blocks' => ['right' => ['actions/ActionProfile/sidebar.tpl']],
];
$config['block']['rule_tag'] = [
    'action' => ['tag'],
    'blocks' => ['right' => ['sidetop', 'search', 'stream', 'donate', 'herdmind']],
];
$config['block']['rule_blogs'] = [
    'action' => ['blogs'],
    'blocks' => ['right' => ['sidetop', 'search', 'stream', 'donate', 'herdmind']],
];
$config['block']['userfeedBlogs'] = [
    'action' => ['feed'],
    'blocks' => ['right' => ['userfeedBlogs']]
];
$config['block']['userfeedUsers'] = [
    'action' => ['feed'],
    'blocks' => ['right' => ['userfeedUsers']]
];
$config['block']['rule_blog_info'] = [
    'action' => ['blog' => ['{topic}']],
    'blocks' => ['right' => ['blog']],
];
$config['block']['rule_search'] = [
    'action' => ['search'],
    'blocks' => ['right' => ['sidetop', 'search', 'donate', 'herdmind']],
];
/**
 * Установка локали
 */
$config['locale']['path'] = 'locale';                // файлы l10n
$config['locale']['lang'] = 'ru_RU';
$config['locale']['timezone'] = 'Europe/Moscow';

/**
 * Настройки типографа текста Jevix
 */
$config['jevix'] = require(dirname(__FILE__) . '/jevix.php');

/**
 * Настройки системы флагов
 */
$config['flags'] = [];

/**
 * Настройки Celery
 */
$config['sys']['celery']['host'] = 'localhost';
$config['sys']['celery']['login'] = '';
$config['sys']['celery']['password'] = '';
$config['sys']['celery']['db'] = 5;
$config['sys']['celery']['exchange'] = 'celery';
$config['sys']['celery']['binding'] = 'celery';
$config['sys']['celery']['port'] = 6379;
$config['sys']['celery']['backend'] = 'redis';

/**
 * Настройки Elasticsearch
 */
$config['sys']['elastic']['hosts'] = [
    "0.0.0.0"
];

/**
 * Разное
 */
$config['misc']['ga'] = '';
$config['misc']['ver']['front'] = file_get_contents(dirname(__FILE__) . "/frontend.version");
$config['misc']['ver']['code'] = file_get_contents(dirname(__FILE__) . "/backend.version");
$config['misc']['debug'] = false;

// Отключение подсчёта числа страниц для первых страниц ленты комментариев для их ускорения.
// Так как это по определению костыль, перед включением убедитесь, что есть более
// 20000 комментариев в открытых блогах во избежание глюков
$config['misc']['simplify_comments_pagination'] = false;

/**
 * Рекапча
 */
$config['module']['user']['captcha_use_registration'] = false;                   // Disable default captcha
$config['module']['user']['recaptcha_use_registration'] = true;                  // Enable Google Recaptcha
$config['recaptcha']['key'] = '6LftnB8TAAAAAIt6Fh42c7OusIOctL9uFIjpm-TD';        // Google Recaptcha Key
$config['recaptcha']['secret'] = '6LftnB8TAAAAANPR7AVuLydz16EmiHY_PmwsFuup';     // Google Recaptcha Secret
$config['recaptcha']['url'] = "https://www.google.com/recaptcha/api/siteverify"; // Google Recaptcha Url

/**
 * Статические страницы
 */
$config['page']['show_block_structure'] = false;

return $config;
