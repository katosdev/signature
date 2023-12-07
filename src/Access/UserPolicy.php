<?php

namespace katosdev\Signature\Access;

use Flarum\User\Access\AbstractPolicy;
use Flarum\User\User;

class UserPolicy extends AbstractPolicy
{
    public function editSignature(User $actor, User $user)
    {
        if ($actor->id === $user->id || $actor->can('user.editSignature')) {
            return $this->allow();
        }
    }
}
