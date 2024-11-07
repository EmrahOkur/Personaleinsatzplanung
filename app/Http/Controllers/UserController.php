<?php

declare(strict_types=1);

namespace App\Http\Controllers;

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
    public function index()
    {
        $users = User::paginate(20);

        return view('users.index', compact('users'));
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

        $users = User::where('vorname', 'LIKE', "%{$term}%")
            ->orWhere('name', 'LIKE', "%{$term}%")
            ->paginate(20);

        return response()->json([
            'users' => $users->items(),
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
        $user = User::select(['id', 'vorname', 'name', 'email'])
            ->findOrFail($id);

        return view('users.edit', compact('user'));
    }

    public function createEmployeeCreds(Request $request)
    {
        User::create($request->except(['_token']));

        return response()->json();
    }

    public function updateEmployeeCreds(Request $request, $id)
    {
        // User::update($request->except(['_token']));

        return response()->json();
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
