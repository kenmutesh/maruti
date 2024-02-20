<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\AprotecUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;

class AprotecUserFeaturesTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;

    protected $globalAprotecUser;

    public function test_can_create_an_aprotec_user(){
        $aprotecUser = AprotecUser::factory()->create();
        $this->assertTrue($aprotecUser->save(), "Created an aprotec user succesfully");
    }

    public function test_can_set_valid_aprotec_user_reset_token(){
        $aprotecUser = AprotecUser::factory()->validpassworedreset()->create();
        $this->assertTrue($aprotecUser->save(), "A user with a valid reset was created");
        // TODO:: Access reset page with reset token and validate its a 200 or 403 otherwise
    }

    public function test_cannot_save_duplicate_username(){
        $userOneDetail = array(
            'email' => 'testone@test.com',
            'username' => 'tester',
            'password' => Hash::make('12345678')
        );
        $userTwoDetail = array(
            'email' => 'testtwo@test.com',
            'username' => 'tester',
            'password' => Hash::make('12345678')
        );
        AprotecUser::create($userOneDetail);
        try {
            AprotecUser::create($userTwoDetail);
            $this->fail("Application can create an aprotec user with duplicate usernames");
        } catch (\Throwable $th) {
            $this->assertTrue(true, "You cannot create aprotec user with duplicate username");
        }
    }

    public function test_cannot_save_duplicate_email(){
        $userOneDetail = array(
            'email' => 'test@test.com',
            'username' => 'tester',
            'password' => Hash::make('12345678')
        );
        $userTwoDetail = array(
            'email' => 'test@test.com',
            'username' => 'tester',
            'password' => Hash::make('12345678')
        );
        AprotecUser::create($userOneDetail);
        try {
            AprotecUser::create($userTwoDetail);
            $this->fail("Application can create an aprotec user with duplicate email");
        } catch (\Throwable $th) {
            $this->assertTrue(true, "You cannot create aprotec user with duplicate email");
        }
    }

    
}
