<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample data for students
        Student::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'birthdate' => '2001-01-01',
        ]);

        Student::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'birthdate' => '2002-02-02',
        ]);

        Student::create([
            'name' => 'Philip Scott',
            'email' => 'philip@example.com',
            'birthdate' => '2003-03-03',
        ]);
    }
}
