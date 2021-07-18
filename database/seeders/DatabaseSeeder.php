<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Thiago',
            'email' => 'thiago@gmail.com',
            'password' => bcrypt(0),
            'is_admin' => true
        ]);

        $this->call(LessonSeeder::class);
    }
}
