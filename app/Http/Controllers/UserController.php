<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CreateEmployeeCredsRequest;
use App\Http\Requests\UpdateEmployeeCredsRequest;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the employees.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = User::query()
            ->leftJoin('employees', 'users.employee_id', '=', 'employees.id')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->select('users.*', 'departments.name as department_name');

        // Handle role filtering
        if ($request->has('role') && $request->role !== 'all') {
            $query->where('users.role', $request->role);
        }

        // Handle department filtering
        if ($request->has('department') && $request->department !== 'all') {
            $query->where('departments.id', $request->department);
        }

        $users = $query->paginate(20);

        // Get departments for the dropdown
        $departments = Department::select('id', 'name')->orderBy('name')->get();

        return view('users.index', compact('users', 'departments'));
    }

    public function new(): View
    {
        return view('users.new');
    }

    public function create(Request $request)
    {
        User::insert($request->except(['_token']));

        return redirect()
            ->route('users');
    }

    public function search(Request $request)
    {
        $term = $request->input('term');
        $role = $request->input('role', 'all');
        $department = $request->input('department', 'all');

        $query = User::query()
            ->leftJoin('employees', 'users.employee_id', '=', 'employees.id')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->select('users.*', 'departments.name as department_name');

        // Apply search term
        if (! empty($term)) {
            $query->where(function ($q) use ($term) {
                $q->where('users.vorname', 'LIKE', "%{$term}%")
                    ->orWhere('users.name', 'LIKE', "%{$term}%");
            });
        }

        // Apply role filter
        if ($role !== 'all') {
            $query->where('users.role', $role);
        }

        // Apply department filter
        if ($department !== 'all') {
            $query->where('departments.id', $department);
        }

        $users = $query->paginate(20);

        $transformedUsers = collect($users->items())->map(function ($user) {
            return [
                'id' => $user->id,
                'vorname' => $user->vorname,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->getRole(),
                'department' => $user->department_name ?? '-',
            ];
        })->all();

        return response()->json([
            'users' => $transformedUsers,
            'pagination' => [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
            ],
            'links' => (string) $users->links(),
        ]);
    }

    public function edit(Request $request, $id)
    {
        $user = User::select(['id', 'vorname', 'name', 'email', 'employee_id', 'role'])
            ->with('employee')
            ->findOrFail($id);

        return view('users.edit', compact('user'));
    }

    public function createEmployeeCreds(CreateEmployeeCredsRequest $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();

            $user = User::create([
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
                'employee_id' => $validated['employee_id'],
                'role' => 'employee',
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Zugangsdaten wurden erfolgreich erstellt',
                'user' => [
                    'employee_id' => $user->id,
                    'email' => $user->email,
                ],
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            dd($e->getMessage());

            return response()->json([
                'message' => 'Fehler beim Erstellen der Zugangsdaten' . $e->getMessage(),
            ], 500);
        }
    }

    public function updateEmployeeCreds(UpdateEmployeeCredsRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // User direkt über ID finden
            $user = User::findOrFail($id);

            $validated = $request->validated();

            $updateData = [
                'email' => $validated['email'],
            ];

            // Passwort nur aktualisieren wenn angegeben
            if (! empty($validated['password'])) {
                $updateData['password'] = bcrypt($validated['password']);
            }

            $user->update($updateData);

            DB::commit();

            return response()->json([
                'message' => 'Zugangsdaten wurden erfolgreich aktualisiert',
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                ],
            ]);

        } catch (ModelNotFoundException $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Benutzer wurde nicht gefunden',
            ], 404);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Fehler beim Aktualisieren der Zugangsdaten',
            ], 500);
        }
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $user->update($request->all());

        return redirect()
            ->route('users');
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Get validated data
            $validatedData = $request->validated();

            // Create new employee
            $user = User::create($validatedData);

            DB::commit();

            return redirect()
                ->route('Request', $user)
                ->with('success', 'Mitarbeiter wurde erfolgreich angelegt.');

        } catch (Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Fehler beim Anlegen des Mitarbeiters. Bitte versuchen Sie es erneut.');
        }
    }
}
