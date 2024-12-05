<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Employee;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    public function index()
    {
        $employees = Employee::all()->toArray();

        return response()->json($employees);
    }

    public function store(Request $request)
    {
        $employee = null;
        try {
            DB::beginTransaction();

            // Create new employee
            $employee = Employee::create($request->all());

            DB::commit();

            return response()->json($employee);

        } catch (Exception $exception) {
            DB::rollBack();

            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }
}
