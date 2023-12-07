<?php

use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        $schema->getConnection()->table('users')
            ->where('signature', '')
            ->update(['signature' => null]);
    },
    'down' => function (Builder $schema) {
        $schema->getConnection()->table('users')
            ->whereNull('signature')
            ->update(['signature' => '']);
    }
];
