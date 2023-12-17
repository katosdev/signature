<?php

namespace katosdev\Signature\Tests\integration\api;

use Flarum\Testing\integration\RetrievesAuthorizedUsers;
use Flarum\Testing\integration\TestCase;
use Flarum\User\User;

class EditSignatureTest extends TestCase
{
    use RetrievesAuthorizedUsers;
    
    public function setUp(): void
    {
        parent::setUp();

        $this->extension('katosdev-signature');

        $this->prepareDatabase([
            'users' => [
                $this->normalUser(),
                ['id' => 3, 'username' => 'normal2', 'email' => 'normal2@machine.local', 'is_email_confirmed' => true, 'signature' => 'too-obscure'],
                ['id' => 4, 'username' => 'moderator', 'email' => 'moderator@machine.local', 'signature' => 'too-obscure2'],
                ['id' => 5, 'username' => 'normal3', 'email' => 'normal3@machine.local', 'is_email_confirmed' => true, 'signature' => 'too-obscure3'],
            ],
            'group_permission' => [
                ['permission' => 'user.allowSignature', 'group_id' => 5],
                ['permission' => 'user.allowSignature', 'group_id' => 4],
                ['permission' => 'user.editSignature', 'group_id' => 4],
            ],
            'groups' => [
                ['id' => 5, 'name_singular' => 'TestSig', 'name_plural' => 'TestSigs', 'color' => '#FF0000', 'icon' => 'fas fa-user'],
            ],
            'group_user' => [
                ['user_id' => 5, 'group_id' => 5],
            ],
        ]);
    }

    /**
     * @test
     */
    public function user_can_edit_own_signature_when_allowed_to_have_one()
    {
        $response = $this->send(
            $this->request('PATCH', '/api/users/5',
                [
                    'authenticatedAs' => 5,
                    'json' => [
                        'data' => [
                            'attributes' => [
                                'signature' => 'This is my new signature',
                            ],
                        ],
                    ],
                ]
            )
        );

        $this->assertEquals(200, $response->getStatusCode(), 'User cannot edit own signature');

        $json = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals('This is my new signature', $json['data']['attributes']['signature']);

        $user = User::find(5);

        $this->assertEquals('<t>This is my new signature</t>', $user->signature);
    }
}
