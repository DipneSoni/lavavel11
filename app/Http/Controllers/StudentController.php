<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    // List all students
    public function index()
    {
        return Student::select('id','name','email','birthdate')->orderBy('id','desc')->paginate(10);
    }

    // Show a specific student
    public function show($id)
    {
        return Student::findOrFail($id);
    }

    // Create a new student
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students',
            'birthdate' => 'required|date',
        ]);

        $student = Student::create($validatedData);
        return response()->json($student, 201);
    }

    // Update a student
    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:students,email,' . $student->id,
            'birthdate' => 'sometimes|required|date',
        ]);

        $student->update($validatedData);
        return response()->json($student, 200);
    }

    // Delete a student
    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();

        return response()->json(null, 204);
    }
}
