<?php

namespace katosdev\Signature\Access;

use Flarum\User\Access\AbstractPolicy;
use Flarum\User\User;

class UserPolicy extends AbstractPolicy
{
    public function editSignature(User $actor, User $user)
    {
        if ($user->isAdmin() && !$actor->isAdmin()) {
            return $this->deny();
        }
        
        if (($actor->id === $user->id && $user->hasPermission('haveSignature')) || $actor->hasPermission('moderateSignature')) {
            return $this->allow();
        }
    }
}
