<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    // List all students
    public function index(Request $request)
    {
        $studSQL = Student::select('id', 'name', 'email', 'birthdate');

        if ($request->has('search')) {
            $search = $request->search;
            $wildcardSearch = '%' . $search . '%';
            // Initialize flag to check if it's a valid date
            $isDate = false;
            $mysqlDate = "";
            try {
                // Attempt to parse the search term as a full date
                $parsedDate = Carbon::parse($search);
                $mysqlDate = $parsedDate->format('Y-m-d');
                $isDate = true;
            } catch (\Exception $e) {
                // If parsing fails, treat it as partial input (not a full date)
                $isDate = false;
            }
            // Build the query with a flexible search
            $studSQL->where(function ($query) use ($wildcardSearch, $mysqlDate, $isDate, $search) {
                // Search in name and email with wildcards
                $query->where('name', 'like', $wildcardSearch)
                    ->orWhere('email', 'like', $wildcardSearch);
                // If it's a valid full date, search by exact date in birthdate
                if ($isDate) {
                    $query->orWhere('birthdate', $mysqlDate);
                } else {
                    // If not a valid date, perform a partial search on the birthdate (e.g. '%23%')
                    $query->orWhere('birthdate', 'like', '%' . $search . '%');
                }
            });
        }

        // if ($request->has('search')) {
        //     $search = '%' . $request->search . '%'; // Add % for wildcard search
        //     $studSQL->where(function ($query) use ($search) {
        //         $query->where('name', 'like', $search)
        //             ->orWhere('email', 'like', $search)
        //             ->orWhere('birthdate', 'like', $search);
        //     });
        // }
        if ($request->has('sortKey') && $request->has('sortOrder')) {
            $studSQL->orderBy($request->sortKey, $request->sortOrder);
        } else {
            $studSQL->latest();
        }
        return $studSQL->paginate(10);
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
