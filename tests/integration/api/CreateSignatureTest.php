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

        $this->prepareDatabase([
            'users' => [
                $this->normalUser(),
                ['id' => 2, 'username' => 'normal2', 'email' => 'normal2@machine.local', 'is_email_confirmed' => true],
                ['id' => 3, 'username' => 'moderator', 'email' => 'moderator@machine.local']
            ],
            'group_permission' => [
                ['permission' => 'user.allowSignature', 'group_id' => 4],
                ['permission' => 'user.editSignature', 'group_id' => 4],
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

        $this->assertEquals(403, $response->getStatusCode());

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
            $this->request('PATCH', '/api/users/3',
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

        $this->assertEquals('This is my signature', $json['data']['attributes']['signature']);

        $user = User::find(2);

        $this->assertEquals('This is my signature', $user->signature);
    }

    /**
     * @test
     */
    public function user_cannot_create_signature_for_other_user()
    {
        $response = $this->send(
            $this->request('PATCH', '/api/users/3',
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

        $this->assertEquals(403, $response->getStatusCode());

        $json = json_decode($response->getBody()->getContents(), true);

        $this->assertNull($json['data']['attributes']['signature']);

        $user = User::find(3);

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

        $this->assertEquals('This is my signature', $json['data']['attributes']['signature']);

        $user = User::find(2);

        $this->assertEquals('This is my signature', $user->signature);
    }
}
