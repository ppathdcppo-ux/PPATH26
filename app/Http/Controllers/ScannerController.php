
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ScannerController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->token || $request->token !== session('scanner_token')) {
            abort(403, 'Unauthorized scanner access');
        }

        return view('scanner');
    }
}
