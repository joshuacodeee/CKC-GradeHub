<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $courses = ['BS Elementary Education','BS Secondary Education','BS Business Administration','BS Accountancy','BS Information Technology','BS Social Work','BS Hospitality Management','BS Nursing','BS Criminology'];
        $yearLevels = ['1st Year', '2nd Year', '3rd Year', '4th Year'];
        $roles = ['admin', 'teacher', 'student'];
        $role = fake()->randomElement($roles);

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'student_id' => $role === 'student' ? Str::upper(fake()->unique()->bothify('2026-####')) : null,
            'course' => $role === 'student' ? fake()->randomElement($courses) : null,
            'year_level' => $role === 'student' ? fake()->randomElement($yearLevels) : null,
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => $role,
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user is an admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
            'student_id' => null,
            'course' => null,
            'year_level' => null,
        ]);
    }

    /**
     * Indicate that the user is a teacher.
     */
    public function teacher(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'teacher',
            'student_id' => null,
            'course' => null,
            'year_level' => null,
        ]);
    }

    /**
     * Indicate that the user is a student.
     */
    public function student(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'student',
        ]);
    }
}
