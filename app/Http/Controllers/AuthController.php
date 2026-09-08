<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function submit(Request $request)
    {
        $mode = $request->auth_mode;

        /*
        |--------------------------------------------------------------------------
        | SIGN UP (NEW ADMIN = PENDING BY DEFAULT)
        |--------------------------------------------------------------------------
        */
        if ($mode === 'signup') {

            $request->validate([
                'firstname' => 'required|max:50',
                'lastname'  => 'required|max:50',
                'email'     => 'required|email|max:50',
                'password'  => 'required|min:8'
            ]);

            $key = Str::lower($request->email).'|'.$request->ip();

            // Rate limit signup
            if (RateLimiter::tooManyAttempts($key, 5)) {
                return back()->with('error', 'Too many attempts. Try again later.');
            }

            RateLimiter::hit($key, 60);

            // check existing email
            $exists = DB::table('admin')
                ->where('email', $request->email)
                ->first();

            if ($exists) {
                return back()->with('error', 'Email already registered');
            }

            // create admin (PENDING)
            DB::table('admin')->insert([
                'firstname' => $request->firstname,
                'lastname'  => $request->lastname,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'status'    => 'pending'
            ]);

            return redirect('/login')
                ->with('success', 'Account created. Waiting for admin approval.');
        }

        /*
        |--------------------------------------------------------------------------
        | LOGIN (ONLY ACTIVE ADMINS CAN LOGIN)
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:8'
        ]);

        $key = Str::lower($request->email).'|'.$request->ip();

        // Rate limit login
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->with('error', 'Too many login attempts. Try again later.');
        }

        $user = DB::table('admin')
            ->where('email', $request->email)
            ->first();

        // invalid credentials
        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($key, 60);
            return back()->with('error', 'Wrong email or password');
            
        }

        // block unapproved accounts
        if ($user->status !== 'active') {
            return back()->with('error', 'Your account is pending approval.');
        }

        // success login
        RateLimiter::clear($key);
if ($user->status !== 'active') {
    return back()->with('error', 'Account is not active or has been rejected.');
}
$request->session()->regenerate();

session([
    'userId'   => $user->id,
    'userName' => $user->firstname,
    'role'     => $user->role
]);

// 🔥 ROLE-BASED REDIRECT
if ($user->role === 'super_admin') {
    return redirect('/superadmin-dashboard');
}

return redirect('/dashboard');
    }
    public function resetPassword(Request $request, $id)
{
    $request->validate([
        'password' => 'required|min:8'
    ]);

    DB::table('admin')
        ->where('id', $id)
        ->update([
            'password' => Hash::make($request->password)
        ]);

    return back()->with('success', 'Password updated successfully.');
}
    public function updateAdmin(Request $request)
{
    $request->validate([
        'firstname' => 'required|max:50',
        'lastname'  => 'required|max:50',
        'email'     => 'required|email|max:50',
        'role'      => 'required|in:admin,super_admin',
        'password'  => 'nullable|min:8'
    ]);

    // Check if the email is already used by another admin
    $existing = DB::table('admin')
        ->where('email', $request->email)
        ->where('id', '!=', $request->id)
        ->first();

    if ($existing) {
        return back()->with('error', 'Email is already in use by another admin.');
    }

    $data = [
        'firstname' => $request->firstname,
        'lastname'  => $request->lastname,
        'email'     => $request->email,
        'role'      => $request->role,
    ];

    // Update password only if a new one was entered
    if (!empty($request->password)) {
        $data['password'] = Hash::make($request->password);
    }

    DB::table('admin')
        ->where('id', $request->id)
        ->update($data);

    return back()->with('success', 'User updated successfully.');
}
}
