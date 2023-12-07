<?php

namespace katosdev\Signature\Event;

use Flarum\User\User;

abstract class AbstractSignatureEvent
{
    /**
     * @var User
     */
    public $user;

    /**
     * @var User|null
     */
    public $actor;

    public function __construct(User $user, User $actor = null)
    {
        $this->user = $user;
        $this->actor = $actor;
    }
}
