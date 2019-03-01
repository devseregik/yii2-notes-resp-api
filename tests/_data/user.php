<?php

return [
    [
        'id'                   => 1,
        'username'             => 'user_1',
        'email'                => 'user_1@user.com',
        'password_hash'        => \Yii::$app->getSecurity()->generatePasswordHash('password_1'),
        'access_token'         => \Yii::$app->security->generateRandomString(),
        'created_at'           => time(),
        'updated_at'           => time(),
    ],
    [
        'id'                   => 2,
        'username'             => 'user_2',
        'email'                => 'user_2@user.com',
        'access_token'         => \Yii::$app->security->generateRandomString(),
        'password_hash'        => \Yii::$app->getSecurity()->generatePasswordHash('password_2'),
        'created_at'           => time(),
        'updated_at'           => time(),
    ],
];