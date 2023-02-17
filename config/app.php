<?php

return [
    'salt' => env('LARAVELHASHID_SALT'),

    'min_hash_length' => env('LARAVELHASHID_MIN_HASH_LENGTH', 16),

    'alphabet' => env('LARAVELHASHID_ALPHABET', 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'),
];