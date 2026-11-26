<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $target = route('home').'#contact';

        // Honeypot: bots fill the hidden "website" field. Pretend success, store nothing.
        if ($request->filled('website')) {
            return redirect()->to($target)->with('contact_success', true);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc', 'max:150'],
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
        ]);

        if ($validator->fails()) {
            return redirect()->to($target)->withErrors($validator, 'contact')->withInput();
        }

        ContactMessage::create($validator->validated() + ['ip_address' => $request->ip()]);
        Activity::log('New contact message from '.$request->input('name'));

        return redirect()->to($target)->with('contact_success', true);
    }
}
