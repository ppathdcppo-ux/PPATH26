<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ActivityController;
use Illuminate\Support\Facades\Artisan;
Route::get('/', function () {
    return redirect('/login');
});
/*
|--------------------------------------------------------------------------
| LOGIN PAGE
|--------------------------------------------------------------------------
*/




Route::post('/activities', [ActivityController::class, 'store'])
    ->middleware('admin.auth');
Route::get('/login', function () {
    return view('auth');
})->name('login');

/*
|--------------------------------------------------------------------------
| AUTHENTICATION (LOGIN / SIGNUP)
|--------------------------------------------------------------------------
*/
Route::post('/auth', [AuthController::class, 'submit'])
    ->name('auth.submit');

/*
|--------------------------------------------------------------------------
| DASHBOARD (SECURED ROUTE)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {

    $attendees = DB::table('attendees')
        ->orderBy('name', 'asc')
        ->get();

    $activities = DB::table('activities')
        ->orderBy('id', 'desc')
        ->get();

    return view('dashboard', compact('attendees', 'activities'));

})->middleware('admin.auth');
Route::get('/activities/delete/{id}', function ($id) {

    // Delete all attendees belonging to this activity
    DB::table('attendees')
        ->where('activity_id', $id)
        ->delete();

    // Delete the activity
    DB::table('activities')
        ->where('id', $id)
        ->delete();

    return redirect('/dashboard');

})->middleware('admin.auth');

/*
|--------------------------------------------------------------------------
| SUPER ADMIN: ADMIN APPROVAL PAGE (STEP 4F)
|--------------------------------------------------------------------------
*/
Route::get('/admin-requests', function () {

    $pendingAdmins = DB::table('admin')
        ->where('status', 'pending')
        ->get();

    return view('admin_requests', compact('pendingAdmins'));

})->middleware(['admin.auth', 'super.admin']);

/*
|--------------------------------------------------------------------------
| LOGOUT (SECURE SESSION DESTROY)
|--------------------------------------------------------------------------
*/
Route::get('/logout', function (Request $request) {

    // 🔴 GET USER ID BEFORE DESTROYING SESSION
    $id = session('userId');

    // 🟢 REMOVE ONLINE STATUS
    \Illuminate\Support\Facades\Cache::forget('admin-online-' . $id);

    // 🔴 CLEAR SESSION
    $request->session()->flush();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');

})->name('logout');

/*Route::get('/superadmin-dashboard', function () {

    if (!session()->has('userId') || session('role') !== 'super_admin') {
        return redirect('/login');
    }

    return view('superadmin_dashboard');

})->middleware(['web', \App\Http\Middleware\UpdateLastSeen::class]);

*/
Route::get('/superadmin-dashboard', function () {

    if (!session()->has('userId') || session('role') !== 'super_admin') {
        return redirect('/login');
    }

    // pending admins
    $pendingAdmins = DB::table('admin')
        ->where('status', 'pending')
        ->get();

    // approved admins
    $approvedAdmins = DB::table('admin')
        ->where('status', 'active')
        ->get();

    return view('superadmin_dashboard', compact('pendingAdmins', 'approvedAdmins'));

})->middleware('admin.auth');
Route::get('/admin-approve/{id}', function ($id) {

    DB::table('admin')
        ->where('id', $id)
        ->update(['status' => 'active']);

    return redirect()->back()->with('success', 'Admin approved.');

})->middleware('admin.auth');


Route::get('/admin-reject/{id}', function ($id) {

    DB::table('admin')
        ->where('id', $id)
        ->delete();

    return redirect()->back()->with('error', 'Admin rejected and deleted.');

})->middleware('admin.auth');
Route::get('/make-superadmin/{id}', function ($id) {

    DB::table('admin')
        ->where('id', $id)
        ->update(['role' => 'super_admin']);

    return redirect()->back()->with('success', 'User promoted to Super Admin');

})->middleware('admin.auth');


Route::get('/delete-admin/{id}', function ($id) {

    DB::table('admin')
        ->where('id', $id)
        ->delete();

    return redirect()->back();

})->middleware(['admin.auth', 'super.admin']);
Route::post('/admin/reset-password/{id}', [AuthController::class, 'resetPassword']);

Route::post('/update-admin', [AuthController::class, 'updateAdmin'])
    ->middleware(['admin.auth', 'super.admin']);


Route::get('/scanner', function () {

    if (!session('scanner_authorized')) {

        return redirect('/scanner-pin');

    }

    $activities = DB::table('activities')
        ->latest()
        ->get();

    return view('scanner', compact('activities'));

});

Route::get('/scanner-pin', function () {

    return view('scanner_pin');

});

Route::post('/scanner-pin', function (Request $request) {

    if ($request->pin === env('SCANNER_PIN')) {

        session([
            'scanner_authorized' => true
        ]);

        return redirect('/scanner');

    }

    return back()->with('error','Incorrect Scanner PIN.');

});

Route::post('/scanner/save', function (Request $request) {

    if (!session('scanner_authorized')) {

        return response()->json([
            'success' => false,
            'message' => 'Unauthorized.'
        ], 403);

    }

    try {

        // Check if this attendee is already scanned for this activity
        $alreadyScanned = DB::table('attendees')
            ->where('activity_id', $request->activity_id)
            ->where('name', $request->name)
            ->exists();

        if ($alreadyScanned) {

            return response()->json([
                'success' => false,
                'message' => 'This attendee has already been scanned.'
            ]);

        }

        DB::table('attendees')->insert([

            'name' => $request->name,
            'gender' => $request->gender,
            'age' => 0,
            'category' => 'N/A',
            'activity_id' => $request->activity_id

        ]);

        return response()->json([
            'success' => true
        ]);

    } catch (\Exception $e) {

        return response()->json([

            'success' => false,
            'message' => $e->getMessage()

        ]);

    }

});

Route::get('/scanner/logout', function () {

    session()->forget('scanner_authorized');

    return redirect('/scanner-pin');

});
Route::get('/attendance/live/{activity}', function ($activity) {

    $attendees = DB::table('attendees')
        ->where('activity_id', $activity)
        ->get();

   return response()->json([
    'attendees' => $attendees,
    'total' => $attendees->count(),
    'male' => $attendees->where('gender', 'MALE')->count(),
    'female' => $attendees->where('gender', 'FEMALE')->count()
]);

});
use App\Http\Controllers\MobileUploadController;


Route::get('/mobile-uploads',
[MobileUploadController::class,'index'])
->middleware('admin.auth');


Route::get('/mobile-uploads/delete/{id}', 
[MobileUploadController::class,'delete'])
->middleware('admin.auth');
