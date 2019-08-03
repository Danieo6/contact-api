<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Mail;
use App\Rules\ReCaptcha;

class ContactController extends Controller
{
    public function processMessage(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'name' => 'required|string',
            'email' => 'required|email',
            'message' => 'required|string',
            'recaptcha' => ['required', new ReCaptcha]
        ]);

        if($validator->fails())
        {
            return response()->json(['errors' => $validator->errors()->all()], 400);
        }

        $from = $req->email;

        Mail::raw($req->message, function($message) use ($from) {
            $message->to(env('EMAIL'))
                    ->from($from)
                    ->subject($from.' send you a message!');
        });

        return response()->json(['status' => 'ok'], 200);
    }
}
