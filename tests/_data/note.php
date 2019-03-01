<?php

return [
    [
        'user_id'      => 1,
        'title'        => 'First note title',
        'text'         => 'First note text',
        'created_at'   => time(),
        'published_at' => time(),
    ],
    [
        'user_id'      => 1,
        'title'        => 'Second note title',
        'text'         => 'Second note text',
        'created_at'   => time(),
        'published_at' => time(),
        'deleted_at'   => time()
    ],
    [
        'user_id'      => 1,
        'title'        => 'Third note title',
        'text'         => 'Third note text',
        'created_at'   => time(),
        'published_at' => strtotime('+2 day'),
    ],
    [
        'user_id'      => 1,
        'title'        => 'Fourth note title',
        'text'         => 'Fourth note text',
        'created_at'   => time(),
        'published_at' => strtotime('-2 day'),
    ],
    [
        'user_id'      => 1,
        'title'        => 'Fourth note title',
        'text'         => 'Fourth note text',
        'created_at'   => strtotime('-1 day'),
        'published_at' => time(),
    ],
    [
        'user_id'      => 2,
        'title'        => 'First note title',
        'text'         => 'First note text',
        'created_at'   => time(),
        'published_at' => time(),
    ],
    [
        'user_id'      => 2,
        'title'        => 'Second note title',
        'text'         => 'Second note text',
        'created_at'   => time(),
        'published_at' => time(),
    ],
    [
        'user_id'      => 2,
        'title'        => 'Third note title',
        'text'         => 'Third note text',
        'created_at'   => time(),
        'published_at' => strtotime('+2 day'),
    ],
    [
        'user_id'      => 2,
        'title'        => 'Fourth note title',
        'text'         => 'Fourth note text',
        'created_at'   => time(),
        'published_at' => strtotime('-2 day'),
    ],
];