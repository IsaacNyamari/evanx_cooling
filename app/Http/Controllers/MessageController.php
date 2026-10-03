<?php

namespace App\Http\Controllers;

use App\Models\ContactMessages;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        $messages = ContactMessages::orderBy('created_at', 'desc')->paginate(20);

        return view('layouts.admin.messages.index', compact('messages'));
    }

    public function destroy(string $id)
    {
        $message = ContactMessages::findOrFail($id);
        $message->delete();

        return redirect()->back()->with('success', 'Message deleted successfully');
    }

    public function markAsRead(Request $request)
    {
        $message = ContactMessages::findOrFail($request->id);
        $message->is_read = true;
        $message->save();

        return response()->json(['success' => true]);
    }
}
