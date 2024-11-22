<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Responsibility;
use Illuminate\Http\Request;

class ResponsibilityController extends Controller
{
    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request, $id, $departmentId)
    {
        $id = $request->id;
        $responsibility = Responsibility::findOrFail($id);
        $responsibility->delete();

        return redirect()->route('departments.edit', $departmentId);
    }
}
