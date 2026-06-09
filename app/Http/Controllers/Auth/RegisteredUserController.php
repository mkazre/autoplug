<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Fleet;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'account_type' => ['required', 'in:car_owner,garage_owner,fleet'],
            'phone' => ['nullable', 'string', 'max:30'],
            'garage_name' => ['nullable', 'required_if:account_type,garage_owner', 'string', 'max:255'],
            'company_name' => ['nullable', 'required_if:account_type,fleet', 'string', 'max:255'],
        ]);

        $role = $data['account_type'] === 'garage_owner' ? 'garage_owner' : 'car_owner';

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $role,
            'phone' => $data['phone'] ?? null,
        ]);

        $user->assignRole($role);

        if ($data['account_type'] === 'garage_owner') {
            $user->garage()->create([
                'name' => $data['garage_name'],
                'status' => 'pending',
            ]);
        }

        if ($data['account_type'] === 'fleet') {
            $fleet = Fleet::create(['owner_id' => $user->id, 'name' => $data['company_name']]);
            $user->update(['fleet_id' => $fleet->id]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
