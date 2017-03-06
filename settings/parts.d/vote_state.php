<?php

return [
    'comment' => [
        // точка завершения "старого" периода, unix timestamp
        'oe_end' => 0 /*test*/+2000000000,
        // кому разрешено видеть юзеров оценок за "старый" период
        'oe_enable_level' => 6 /*test*/-6,
        // кому разрешено видеть юзеров оценок за "новый" период
        'ne_enable_level' => 6,
        //	0 — никто
        //	1 — администраторы сайта
        //	3 — администраторы сайта, администраторы блогов
        //	4 — администраторы сайта, администраторы блогов, модераторы блогов
        //	5 — администраторы сайта, администраторы блогов, модераторы блогов, автор объекта (если он может видеть объект)
        //	6 — все пользователи, которые могут видеть объект
        //	7 — все пользователи
        //	8 — все
        // см. также ModuleACL::CheckSimpleAccessLevel
    ]
];