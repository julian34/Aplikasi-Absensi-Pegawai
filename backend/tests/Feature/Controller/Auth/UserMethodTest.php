<?php

namespace Tests\Feature\Controller\Auth;

use Tests\Feature\Controller\ControllerTestCase;

class UserMethodTest extends ControllerTestCase
{
    public function test_user_mengembalikan_data_user_yang_sedang_login(): void
    {
        [$user, $pegawai] = $this->actingAsPegawai();

        $response = $this->getJson('/api/user');

        $response->assertOk()
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('user.pegawai.id', $pegawai->id);
    }

    public function test_user_ditolak_jika_belum_login(): void
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
    }
}