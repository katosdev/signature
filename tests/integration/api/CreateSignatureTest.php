<?php

namespace katosdev\Signature\Tests\integration\api;

use Flarum\Testing\integration\RetrievesAuthorizedUsers;
use Flarum\Testing\integration\TestCase;
use Flarum\User\User;

class CreateSignatureTest extends TestCase
{
    use RetrievesAuthorizedUsers;
    
    public function setUp(): void
    {
        parent::setUp();

        $this->extension('katosdev-signature');

        $this->prepareDatabase([
            'users' => [
                $this->normalUser(),
                ['id' => 2, 'username' => 'normal2', 'email' => 'normal2@machine.local', 'is_email_confirmed' => true],
                ['id' => 3, 'username' => 'moderator', 'email' => 'moderator@machine.local'],
                ['id' => 4, 'username' => 'normal3', 'email' => 'normal3@machine.local', 'is_email_confirmed' => true],
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
                ['user_id' => 4, 'group_id' => 5],
            ],
        ]);
    }

    /**
     * @test
     */
    public function user_cannot_create_signature_without_permission()
    {
        $response = $this->send(
            $this->request('PATCH', '/api/users/2',
                [
                    'authenticatedAs' => 2,
                    'json' => [
                        'data' => [
                            'attributes' => [
                                'signature' => 'This is my signature',
                            ],
                        ],
                    ],
                ]
            )
        );

        $this->assertEquals(403, $response->getStatusCode(), 'User without permission can create signature');

        $json = json_decode($response->getBody()->getContents(), true);

        $this->assertNull($json['data']['attributes']['signature']);

        $user = User::find(2);

        $this->assertNull($user->signature);
    }

    /**
     * @test
     */
    public function user_can_create_signature_with_permission()
    {
        $response = $this->send(
            $this->request('PATCH', '/api/users/4',
                [
                    'authenticatedAs' => 4,
                    'json' => [
                        'data' => [
                            'attributes' => [
                                'signature' => 'This is my signature',
                            ],
                        ],
                    ],
                ]
            )
        );

        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode($response->getBody()->getContents(), true);

        $this->assertArrayHasKey('signature', $json['data']['attributes'], 'Creating a signature failed');
        $this->assertEquals('This is my signature', $json['data']['attributes']['signature']);

        $user = User::find(4);

        $this->assertEquals('<t>This is my signature</t>', $user->signature);
    }

    /**
     * @test
     */
    public function user_cannot_create_signature_for_other_user()
    {
        $response = $this->send(
            $this->request('PATCH', '/api/users/2',
                [
                    'authenticatedAs' => 4,
                    'json' => [
                        'data' => [
                            'attributes' => [
                                'signature' => 'This is my signature',
                            ],
                        ],
                    ],
                ]
            )
        );

        $this->assertEquals(403, $response->getStatusCode(), 'Expecting a permission denied 403');

        $user = User::find(2);

        $this->assertNull($user->signature);
    }

    /**
     * @test
     */
    public function user_with_permission_can_create_signature_for_other_user()
    {
        $response = $this->send(
            $this->request('PATCH', '/api/users/2',
                [
                    'authenticatedAs' => 3,
                    'json' => [
                        'data' => [
                            'attributes' => [
                                'signature' => 'This is my signature',
                            ],
                        ],
                    ],
                ]
            )
        );

        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode($response->getBody()->getContents(), true);

        $this->assertArrayHasKey('signature', $json['data']['attributes'], 'Expecting a signature to be returned');
        $this->assertEquals('This is my signature', $json['data']['attributes']['signature']);

        $user = User::find(2);

        $this->assertEquals('This is my signature', $user->signature);
    }
}