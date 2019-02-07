<?php

return [
    '/' => '/site/index',
    '/login' => '/site/login',
    '/logout' => '/site/logout',
    '<module>' => '<module>/default/index',
    '<module>/<action>' => '<module>/default/<action>',
    '<module>/<action>/<id:\d+>' => '<module>/default/<action>',
    '<module>/<controller>/<action>' => '<module>/<controller>/<action>',
    '<module>/<controller>/<action>/<id:[a-z0-9-]+>' => '<module>/<controller>/<action>',
];
