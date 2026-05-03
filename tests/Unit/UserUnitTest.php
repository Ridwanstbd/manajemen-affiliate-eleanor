<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\User;

class UserUnitTest extends TestCase
{
    /**
     * Uji pengecekan role menggunakan string tunggal.
     */
    public function test_user_has_role_with_string()
    {
        $user = new User();
        $user->role = 'ADMINISTRATOR';

        $this->assertTrue($user->hasRole('ADMINISTRATOR'));
        $this->assertFalse($user->hasRole('AFFILIATOR'));
    }

    /**
     * Uji pengecekan role menggunakan array.
     */
    public function test_user_has_role_with_array()
    {
        $user = new User();
        $user->role = 'AFFILIATOR';

        $this->assertTrue($user->hasRole(['ADMINISTRATOR', 'AFFILIATOR']));
        
        $this->assertFalse($user->hasRole(['GUEST', 'MANAGER']));
    }
}