<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Rules\ReCaptcha;
use Validator;

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

        if ($validator->fails())
            return response()->json(['errors' => $validator->errors()->all()], 400);

        $this->sendMail($req->email, $req->message);

        $this->saveMessage($req->email, $req->message);

        return response()->json(['status' => 'ok'], 200);
    }

    private function sendMail($from, $message)
    {
        Mail::raw($message, function($msg) use ($from) {
            $msg->to(env('EMAIL'))
                    ->from($from)
                    ->subject($from.' send you a message!');
        });
    }

    private function saveMessage($from, $message)
    {
        DB::insert('INSERT INTO `messages` (`from`, `message`, `created`) VALUES (?, ?, ?)', [$from, $message, date('Y-m-d H:i:s')]);
    }
}
