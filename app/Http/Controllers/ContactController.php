<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function store(Request $request): \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:11|min:11',  // Added min:11 for exact 11 digits
            'message' => 'required|string|min:10|max:2000',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }

            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $contactMessage = ContactMessage::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'message' => $request->message,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Log contact message submission
        \Log::info('Contact message received', [
            'message_id' => $contactMessage->id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'ip_address' => $request->ip(),
            'received_at' => now()->toDateTimeString(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you for your message! We will get back to you within 24 hours.'
            ]);
        }

        return redirect()->back()->with('success', 'Thank you for your message! We will get back to you within 24 hours.');
    }
}
