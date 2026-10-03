<?php

namespace App\Http\Controllers;

class ContactMessagesController extends Controller
{
    public function index()
    {
        return view('layouts.admin.messages.index');
    }
}
