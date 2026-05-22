<?php

namespace Tests\Feature\Controller\Auth;

use Tests\Feature\Controller\ControllerTestCase;

class LogoutMethodTest extends ControllerTestCase
{
    public function test_logout_berhasil_menghapus_token(): void
    {
        [$user] = $this->makeUserWithPegawai();

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/logout');

        $response->assertOk();

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'test-token',
        ]);
    }

    public function test_logout_ditolak_jika_belum_login(): void
    {
        $response = $this->postJson('/api/logout');

        $response->assertStatus(401);
    }
}