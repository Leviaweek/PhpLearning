<?php
return [
    'path' => __DIR__ . '/../data/database.sqlite',
    'pragmas' => [
        'journal_mode' => 'WAL',
        'foreign_keys' => true,
        'encoding' => 'UTF_8'
    ]
];