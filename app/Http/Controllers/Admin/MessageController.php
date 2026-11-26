<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;

class MessageController extends Controller
{
    public function index()
    {
        return view('admin.messages.index', [
            'messages' => ContactMessage::latest()->latest('id')->paginate(15),
        ]);
    }

    public function show(ContactMessage $message)
    {
        if (! $message->is_read) {
            $message->forceFill(['is_read' => true])->save();
        }

        return view('admin.messages.show', compact('message'));
    }

    public function toggleRead(ContactMessage $message)
    {
        $message->forceFill(['is_read' => ! $message->is_read])->save();

        return back();
    }

    public function destroy(ContactMessage $message)
    {
        $message->delete();

        return redirect()->route('admin.messages.index')->with('success', t('a_deleted'));
    }
}
