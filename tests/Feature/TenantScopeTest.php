<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\WithFaker;

test('a model has a tenant id on the migration', function () {
    $now = now();
    dd($now->year . '_' . $now->format('m') . '_' . $now->format('d') . '_' . $now->format('h') . $now->format('i') . $now->format('s') . '_create_tests_table.php');
    $this->artisan('make:model Test -m');
    $filename = $now->year . '_' . $now->format('m') . '_' . $now->format('d') . '_' . $now->format('h') . $now->format('i') . $now->format('s') . '_create_tests_table.php';
    $this->assertTrue(File::exists(database_path('migrations/'.$filename))); // migrations are named differently
    $this->assertStringContainsString('$table->unsignedBigInteger(\'tenant_id\')->index();', File::get(database_path('migrations/'.$filename)));
    //clean up
    File::delete(database_path('migrations/'.$filename));
    File::delete(app_path('Models/Test.php'));
});

test('a user can see only see tenants in the same tenant', function () {
    $tenant1 = \App\Models\Tenant::factory()->create();
    $tenant2 = \App\Models\Tenant::factory()->create();

    $user1 = \App\Models\User::factory()->create([
        'tenant_id' => $tenant1,
    ]);

    \App\Models\User::factory()->count(9)->create([
        'tenant_id' => $tenant1,
    ]);

    \App\Models\User::factory()->count(10)->create([
        'tenant_id' => $tenant2,
    ]);

    // total users
    $this->assertEquals(20, User::count());

    // tenant users
    auth()->login($user1);
    $this->assertEquals(10, User::count());
});

test('a user can only create a user in his tenant even if other tenant_id is submitted', function () {
    $tenant1 = \App\Models\Tenant::factory()->create();
    $tenant2 = \App\Models\Tenant::factory()->create();

    $user1 = \App\Models\User::factory()->create([
        'tenant_id' => $tenant1,
    ]);

    auth()->login($user1);

    $createdUser = User::factory()->make();
    $createdUser->tenant_id = $tenant2->id;
    $createdUser->save();

    $this->assertTrue($createdUser->tenant_id == $user1->tenant_id);
});
