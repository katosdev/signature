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
                ['id' => 3, 'username' => 'normal2', 'email' => 'normal2@machine.local', 'is_email_confirmed' => true],
                ['id' => 4, 'username' => 'moderator', 'email' => 'moderator@machine.local', 'is_email_confirmed' => true],
                ['id' => 5, 'username' => 'normal3', 'email' => 'normal3@machine.local', 'is_email_confirmed' => true],
            ],
            'groups' => [
                ['id' => 5, 'name_singular' => 'TestSig', 'name_plural' => 'TestSigs', 'color' => '#FF0000', 'icon' => 'fas fa-user'],
            ],
            'group_permission' => [
                ['permission' => 'haveSignature', 'group_id' => 5],
                ['permission' => 'haveSignature', 'group_id' => 4],
                ['permission' => 'moderateSignature', 'group_id' => 4],
            ],
            'group_user' => [
                ['user_id' => 5, 'group_id' => 5],
                ['user_id' => 4, 'group_id' => 4],
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

        $user = User::find(2);

        $this->assertNull($user->signature);
    }

    /**
     * @test
     */
    public function user_can_create_signature_with_permission()
    {
        $response = $this->send(
            $this->request('PATCH', '/api/users/5',
                [
                    'authenticatedAs' => 5,
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
        $this->assertEquals('This is my signature', $json['data']['attributes']['signatureHtml']);

        $user = User::find(5);

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
                    'authenticatedAs' => 5,
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
    public function user_with_permission_can_create_signature_for_other_user_who_can_have_signature()
    {
        $response = $this->send(
            $this->request('PATCH', '/api/users/5',
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

        $this->assertArrayHasKey('data', $json, 'Expecting a data key to be returned');
        $this->assertArrayHasKey('signature', $json['data']['attributes'], 'Expecting a signature to be returned');
        $this->assertEquals('This is my signature', $json['data']['attributes']['signature']);
        $this->assertEquals('This is my signature', $json['data']['attributes']['signatureHtml']);

        $user = User::find(5);

        $this->assertEquals('<t>This is my signature</t>', $user->signature);
    }

    /**
     * @test
     */
    public function user_with_permission_cannot_create_signature_for_other_user_who_cannot_have_signature()
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
}
