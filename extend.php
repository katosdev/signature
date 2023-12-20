<?php

namespace katosdev\Signature;

use Flarum\Extend;
use Flarum\User\User;
use Flarum\Api\Serializer\UserSerializer;
use Flarum\User\Event\Saving as UserSaving;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__ . '/js/dist/forum.js')
        ->css(__DIR__ . '/less/forum.less')
        ->route('/u:username/signature', 'user.signature'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__ . '/js/dist/admin.js'),

    new Extend\Locales(__DIR__ . '/locale'),

    (new Extend\ApiSerializer(UserSerializer::class))
        ->attributes(Api\AddUserAttributes::class),

    (new Extend\Event())
        ->listen(UserSaving::class, Listener\SaveSignatureToDatabase::class),

    (new Extend\Settings())
        ->default('signature.maximum_char_limit', 500)
        ->default('signature.maximum_image_count', 2)
        ->serializeToForum('allowInlineEditing', 'signature.allow_inline_editing', 'boolval', false),

    (new Extend\Model(User::class))
        ->cast('signature', 'string'),

    (new Extend\Policy())
        ->modelPolicy(User::class, Access\UserPolicy::class),

    (new Extend\ServiceProvider())
        ->register(Provider\SignatureFormatterProvider::class),
];
