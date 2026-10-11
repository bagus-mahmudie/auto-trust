<?php

namespace App\Http\Controllers;

use App\Models\KebutuhanPosting;
use Illuminate\Http\Request;

class KebutuhanPostingPublicController extends Controller
{
    public function index(Request $request)
    {
        $postings = KebutuhanPosting::with('user')
            ->latest()
            ->paginate(12)      // 12 item per halaman (grid 4 kolom desktop)
            ->withQueryString();

        return view('butuh_mobil.index', compact('postings'));
    }
}
