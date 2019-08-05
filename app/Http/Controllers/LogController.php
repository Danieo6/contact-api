<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Validator;

class LogController extends Controller
{
    public function getLog(Request $req)
    {
        if ($this->verifyAuth(['user' => $req->user, 'password' => $req->password]))
        {
            $logs = DB::select('SELECT `id`, `from`, `message`, `created` FROM `messages`');

            return response()->json(['log' => $logs], 200);
        }
        else
        {
            return response()->json(['error' => 'User not authorized'], 401);
        }
    }

    private function verifyAuth($credentials)
    {
        $validator = Validator::make($credentials, [
            'user' => 'required|alpha',
            'password' => 'required|string'
        ]);

        if ($validator->fails())
            return false;

        if($credentials['user'] !== env('LOG_ACCESS_USER'))
            return false;

        if (Hash::check($credentials['password'], env('LOG_ACCESS_PASSWORD')))
            return true;
        else
            return false;
    }
}