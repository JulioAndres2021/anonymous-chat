<?php

return [

    'retention' => [

        'trash_days' => env(
            'ANONYMOUS_TRASH_DAYS',
            30
        ),

        'backup_days' => env(
            'ANONYMOUS_BACKUP_DAYS',
            7
        ),

    ],

];