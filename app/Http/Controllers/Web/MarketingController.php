<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class MarketingController extends Controller
{
    public function home()
    {
        return view('marketing.home');
    }

    public function features()
    {
        return view('marketing.features');
    }

    public function solutions()
    {
        return view('marketing.solutions');
    }

    public function howItWorks()
    {
        return view('marketing.how-it-works');
    }

    public function pricing()
    {
        return view('marketing.pricing');
    }

    public function about()
    {
        return view('marketing.about');
    }

    public function faq()
    {
        return view('marketing.faq');
    }

    public function privacy()
    {
        return view('marketing.privacy');
    }

    public function terms()
    {
        return view('marketing.terms');
    }

    public function contact()
    {
        return view('marketing.contact');
    }

    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        ContactMessage::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'] ?? null,
            'message' => $validated['message'],
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('marketing.contact')->with('success', 'Your message has been sent. We will get back to you soon.');
    }
}
