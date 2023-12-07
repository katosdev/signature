<?php

namespace katosdev\Signature\Listener;

use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\Event\Saving;
use Flarum\User\User;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use katosdev\Signature\Event\SignatureSaved;
use katosdev\Signature\Event\SignatureSaving;
use katosdev\Signature\Formatter\SignatureFormatter;
use katosdev\Signature\Validator\SignatureValidator;

class SaveSignatureToDatabase
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * @var Dispatcher
     */
    protected $events;

    /**
     * @var SignatureValidator
     */
    protected $validator;

    /**
     * @var SignatureFormatter
     */
    protected $formatter;
    
    public function __construct(SettingsRepositoryInterface $settings, Dispatcher $events, SignatureValidator $validator, SignatureFormatter $formatter)
    {
        $this->settings = $settings;
        $this->events = $events;
        $this->validator = $validator;
        $this->formatter = $formatter;
    }
    
    public function handle(Saving $event)
    {
        $attributes = Arr::get($event->data, 'attributes', []);

        if (Arr::exists($attributes, 'signature')) {
            $user = $event->user;
            $actor = $event->actor;

            $actor->assertCan('editSignature', $user);

            $this->validator->assertValid(Arr::only($attributes, 'signature'));

            $signature = Str::of(Arr::get($attributes, 'signature'))->trim();

            if ($signature->isEmpty()) {
                $signature = null;
            }

            $user->signature = $signature ? $this->formatter->parse($signature) : null;

            if ($user->isDirty('signature')) {
                $this->events->dispatch(
                    new SignatureSaving($user, $actor->id === $user->id ? null : $actor)
                );

                $user->afterSave(function (User $user) use ($actor) {
                    $user->raise(new SignatureSaved($user, $actor->id === $user->id ? null : $actor));
                });
            }
        }
    }
}
