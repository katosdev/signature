<?php

namespace katosdev\Signature\Api;

use Flarum\Api\Serializer\UserSerializer;
use Flarum\User\User;
use katosdev\Signature\Formatter\SignatureFormatter;

class AddUserAttributes
{
    protected $formatter;
    
    public function __construct(SignatureFormatter $formatter)
    {
        $this->formatter = $formatter;
    }
    
    public function __invoke(UserSerializer $serializer, User $user, array $attributes): array
    {
        $attributes['signature'] = $user->signature ? $this->formatter->unparse($user->signature) : null;
        if ($user->signature) {
            $attributes['signatureHtml'] = $this->formatter->render($user->signature);
        }
        $attributes['canEditSignature'] = $serializer->getActor()->can('editSignature', $user);

        return $attributes;
    }
}
