<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Responsibility;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class ResponsibilityController extends Controller
{
    public function create(Request $request, $id, $departmentId)
    {
        $resp = Responsibility::create(
            [
                'employee_id' => $id,
                'department_id' => $departmentId,
            ]
        );

        $user = User::where('employee_id', '=', $id)->firstOrFail();

        if (! $user->hasRole('manager')) {
            $user->role = 'manager';
            $user->update();
        }

        return redirect()->route('departments.edit', $departmentId);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request)
    {
        $id = $request->id;
        $department_id = $request->department_id;
        try {
            $responsibility = Responsibility::where('employee_id', '=', $id)->get()->first();
        } catch (Exception $ex) {
            dd($ex);
        }

        $countResponsibilities = Responsibility::where('employee_id', '=', $id)->count();

        // sollte vor dem Löschen 1 sein
        if ($countResponsibilities === 1) {
            $responsibility->employee->user->role = 'employee';
        }

        $responsibility->delete();

        return redirect()->route('departments.edit', $department_id);
    }
}
