<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin
        $admin = User::create([
            'name'              => 'Chef Admin',
            'email'             => 'admin@mealit.com',
            'email_verified_at' => now(),
            'password'          => Hash::make('Admin@1234'),
            'role'              => 'admin',
            'bio'               => 'Lead Developer and Platform Moderator of MealIt AI.',
            'is_active'         => true,
        ]);

        UserPreference::create([
            'user_id' => $admin->id,
            'preferred_cuisines' => ['Pakistani', 'Italian'],
            'dietary_flags' => ['halal'],
            'spice_level' => 'spicy',
            'skill_level' => 'professional',
        ]);

        // Regular User
        $user = User::create([
            'name'              => 'Hamza Fitness',
            'email'             => 'user@mealit.com',
            'email_verified_at' => now(),
            'password'          => Hash::make('User@1234'),
            'role'              => 'user',
            'bio'               => 'Fitness enthusiast from Karachi, looking to optimize protein intake with delicious home-cooked meals.',
            'is_active'         => true,
        ]);

        UserPreference::create([
            'user_id' => $user->id,
            'preferred_cuisines' => ['Pakistani', 'Turkish'],
            'dietary_flags' => ['halal'],
            'spice_level' => 'spicy',
            'skill_level' => 'medium',
            'calorie_target' => 2500,
            'health_goal' => 'muscle_gain',
            'height' => 180,
            'weight' => 78,
            'age' => 24,
            'activity_level' => 'Active',
        ]);
    }
}
