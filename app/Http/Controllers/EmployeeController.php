<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {   
        
        if(Employee::doesntExist()){
            return response()->json([
                'error' => 'No data found'
            ],404);
        }
        
        $employees = Employee::all();
        
        return response()->json([
                'success' => 'true',
                'employees' => $employees
            ],200);
    }
}
