<?php

return [

    'guard' => 'web',

    'token_expiration' => env('PASSPORT_TOKEN_EXPIRATION', 60),

    'refresh_token_expiration' => env('PASSPORT_REFRESH_TOKEN_EXPIRATION', 43200),

    'personal_access_token_expiration' => env('PASSPORT_PERSONAL_ACCESS_TOKEN_EXPIRATION', 360),

    'client_uuids' => true,

    'key_path' => env('PASSPORT_KEY_PATH', storage_path()),

];
