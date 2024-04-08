<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pageTitle = 'Employee List';

        $employees = DB::table('employees')
            ->select([
                'employees.*',
                DB::raw('employees.id AS employee_id'),
                'positions.name AS position_name',
            ])
            ->leftJoin('positions', function ($join) {
                $join->on('employees.position_id', '=', 'positions.id');
            })
            ->get();

        // RAW SQL QUERY
        // $employees = DB::select('
        //     select *, employees.id as employee_id, positions.name as position_name
        //     from employees
        //     left join positions on employees.position_id = positions.id
        // ');

        return view('employee.index', [
            'pageTitle' => $pageTitle,
            'employees' => $employees
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Create Employee';
        // RAW SQL Query
        $positions = DB::table('positions')->get();

        return view('employee.create', compact('pageTitle', 'positions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $messages = [
            'required' => ':Attribute harus diisi.',
            'email' => 'Isi :attribute dengan format yang benar',
            'numeric' => 'Isi :attribute dengan angka'
        ];
    
        $validator = Validator::make($request->all(), [
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|email',
            'age' => 'required|numeric',
            'position' => 'required', // Add validation for position
        ], $messages);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        DB::table('employees')->insert([
            'firstname' => $request->firstName,
            'lastname' => $request->lastName,
            'email' => $request->email,
            'age' => $request->age,
            'position_id' => $request->position,
        ]);
    
        return redirect()->route('employees.index');
    }
    
    public function update(Request $request, string $id)
    {
        $messages = [
            'required' => ':Attribute harus diisi.',
            'email' => 'Isi :attribute dengan format yang benar',
            'numeric' => 'Isi :attribute dengan angka'
        ];

        $validator = Validator::make($request->all(), [
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|email',
            'age' => 'required|numeric',
            'position' => 'required', // Add validation for position
        ], $messages);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        DB::table('employees')->where('id', $id)->update([
            'firstname' => $request->firstName,
            'lastname' => $request->lastName,
            'email' => $request->email,
            'age' => $request->age,
            'position_id' => $request->position,
        ]);
    
        return redirect()->route('employees.index');
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // QUERY BUILDER
        DB::table('employees')
            ->where('id', $id)
            ->delete();

        return redirect()->route('employees.index');
    }

    public function edit(string $id)
{
    $pageTitle = 'Edit Employee';

    $employee = DB::table('employees')
        ->select([
            'employees.*',
            DB::raw('employees.id AS employee_id'),
            'positions.name AS position_name',
        ])
        ->leftJoin('positions', function ($join) use ($id) {
            $join->on('employees.position_id', '=', 'positions.id');
        })
        ->where('employees.id', '=', $id)
        ->first();

    $positions = DB::table('positions')->get();

    return view('employee.edit', compact('pageTitle', 'employee', 'positions'));
}
public function show(string $id)
    {
        $pageTitle = 'Employee Detail';

        // RAW SQL QUERY

        $employee = DB::table('employees')
            ->select([
                'employees.*',
                DB::raw('employees.id AS employee_id'),
                'positions.name AS position_name',
            ])
            ->leftJoin('positions', function ($join) use ($id) {
                $join->on('employees.position_id', '=', 'positions.id');
            })
            ->where('employees.id', '=', $id)
            ->first();

        // $employee = collect(DB::select('
        //     select *, employees.id as employee_id, positions.name as position_name
        //     from employees
        //     left join positions on employees.position_id = positions.id
        //     where employees.id = ?
        // ', [$id]))->first();

        return view('employee.show', compact('pageTitle', 'employee'));
    }

}

