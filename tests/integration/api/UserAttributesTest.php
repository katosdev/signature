<?php

namespace katosdev\Signature\Tests\integration\api;

use Flarum\Testing\integration\RetrievesAuthorizedUsers;
use Flarum\Testing\integration\TestCase;

class UserAttributesTest extends TestCase
{
    use RetrievesAuthorizedUsers;
    
    public function setUp(): void
    {
        parent::setUp();

        $this->extension('katosdev-signature');

        $this->prepareDatabase([
            'users' => [
                $this->normalUser(),
                ['id' => 3, 'username' => 'moderator', 'email' => 'moderator@machine.local', 'is_email_confirmed' => true]
            ],
            'group_user' => [
                ['user_id' => 3, 'group_id' => 4]
            ],
            'group_permission' => [
                ['group_id' => 4, 'permission' => 'user.editSignature']
            ],
        ]);
    }

    /**
     * @test
     */
    public function it_includes_signature_in_user_attributes()
    {
        $response = $this->send(
            $this->request('GET', '/api/users/1', ['authenticatedAs' => 2])
        );

        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode($response->getBody(), true);

        $this->assertNull($json['data']['attributes']['signature']);
    }

    /**
     * @test
     */
    public function normal_user_has_permission_to_edit_own_signature()
    {
        $response = $this->send(
            $this->request('GET', '/api/users/2', ['authenticatedAs' => 2])
        );

        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode($response->getBody(), true);

        $this->assertTrue($json['data']['attributes']['canEditSignature']);
    }

    /**
     * @test
     */
    public function normal_user_does_not_have_permission_to_edit_other_users_signature()
    {
        $response = $this->send(
            $this->request('GET', '/api/users/1', ['authenticatedAs' => 2])
        );

        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode($response->getBody(), true);

        $this->assertFalse($json['data']['attributes']['canEditSignature']);
    }

    /**
     * @test
     */
    public function user_with_permission_can_edit_others_signature()
    {
        $response = $this->send(
            $this->request('GET', '/api/users/2', ['authenticatedAs' => 3])
        );

        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode($response->getBody(), true);

        $this->assertTrue($json['data']['attributes']['canEditSignature']);
    }
}
