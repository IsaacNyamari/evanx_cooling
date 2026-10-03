<?php

namespace App\Http\Controllers;

use App\Mail\ContactPageForm;
use App\Models\ContactMessages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactPageController extends Controller
{
    public function send(Request $request)
    {
        $data = $request->validate([
            'name' => ['string', 'required'],
            'email' => ['email', 'required'],
            'subject' => ['string', 'required'],
            'phone' => ['string', 'required'],
            'message' => ['string', 'required'],
        ]);

        try {
            Mail::to('jablessions76@gmail.com')->send(new ContactPageForm($data));
            ContactMessages::create($data);
            return back()->with('success', 'message sent succcessfully!');
            } catch (\Throwable $e) {
            return back()->with('error', 'message not sent succcessfully!');
        }
    }
}
