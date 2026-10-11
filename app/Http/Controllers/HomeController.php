<?php

namespace App\Http\Controllers;

use App\Models\Mobil;
use App\Models\Motor;
use Illuminate\Http\Request;
use App\Models\KebutuhanPosting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $q = Mobil::query();

        // FILTER
        if ($request->filled('kondisi')) {
            $q->where('kondisi_mobil', $request->kondisi);
        }

        if ($request->filled('merk')) {
            $q->where('merk_mobil', $request->merk);
        }

        if ($request->filled('model')) {
            $q->where('model_mobil', $request->model);
        }

        if ($request->filled('lokasi')) {
            $q->where('lokasi', $request->lokasi);
        }

        // harga_range: "150-200" (anggap harga_mobil disimpan dalam JUTA)
        if ($request->filled('harga_range')) {
            [$min, $max] = array_pad(explode('-', $request->harga_range, 2), 2, null);
            $min = is_numeric($min) ? (int) $min : null;
            $max = is_numeric($max) ? (int) $max : null;

            if ($min !== null) $q->where('harga_mobil', '>=', $min);
            if ($max !== null) $q->where('harga_mobil', '<=', $max);
        }

        // Listing (optional)
        $mobils = $q->orderByDesc('id')->paginate(12)->withQueryString();

        // Options dari DB
        $kondisis = Mobil::select('kondisi_mobil')
            ->whereNotNull('kondisi_mobil')
            ->distinct()
            ->orderBy('kondisi_mobil')
            ->pluck('kondisi_mobil');

        $merks = Mobil::select('merk_mobil')
            ->whereNotNull('merk_mobil')
            ->distinct()
            ->orderBy('merk_mobil')
            ->pluck('merk_mobil');

        $modelsQuery = Mobil::select('model_mobil')->whereNotNull('model_mobil');
        if ($request->filled('merk')) $modelsQuery->where('merk_mobil', $request->merk);
        $models = $modelsQuery->distinct()->orderBy('model_mobil')->pluck('model_mobil');

        $lokasis = Mobil::select('lokasi')
            ->whereNotNull('lokasi')
            ->distinct()
            ->orderBy('lokasi')
            ->pluck('lokasi');

        $rekomendasis = Mobil::inRandomOrder()->limit(3)->get();
        $populers = Mobil::orderByDesc('likes_mobil')->orderByDesc('id')->limit(3)->get();

        // ambil 4 mobil terbaru yang bisa dipasangkan per merk (2-2)
        $comparePairs = [];

        $brandsForCompare = Mobil::select('merk_mobil')
            ->whereNotNull('merk_mobil')
            ->distinct()
            ->inRandomOrder()
            ->limit(6)
            ->pluck('merk_mobil');

        foreach ($brandsForCompare as $brand) {
            $items = Mobil::where('merk_mobil', $brand)
                ->whereNotNull('model_mobil')
                ->orderByDesc('id')     // terbaru dulu
                ->limit(2)
                ->get();

            if ($items->count() === 2) {
                $comparePairs[] = $items;
            }

            if (count($comparePairs) >= 2) break; // butuh 2 pair saja
        }

        // --- Harga Filter Section (no refresh) ---
        $priceTabs = [
            '100'     => ['label' => '< 100 Juta',     'min' => 0,   'max' => 100],
            '100-150' => ['label' => '100 – 150 Juta', 'min' => 100, 'max' => 150],
            '200-250' => ['label' => '200 – 250 Juta', 'min' => 200, 'max' => 250],
            '300'     => ['label' => '> 300 Juta',     'min' => 300, 'max' => null],
        ];

        $activePrice = '300'; // default active di awal

        $priceSections = [];
        foreach ($priceTabs as $key => $tab) {
            $q = Mobil::query()->whereNotNull('harga_mobil')
                ->where('harga_mobil', '>=', $tab['min'] * 1000000);

            if (!is_null($tab['max'])) {
                $q->where('harga_mobil', '<=', $tab['max'] * 1000000);
            }

            $priceSections[$key] = $q->orderBy('harga_mobil')->limit(3)->get();
        }


        // --- Tahun Filter Section (no refresh) ---
        $yearTabs = [
            '3'  => ['label' => '3 tahun terakhir',  'years' => 3],
            '5'  => ['label' => '5 tahun terakhir',  'years' => 5],
            '10' => ['label' => '10 tahun terakhir', 'years' => 10],
        ];

        $activeYear = '3'; // default aktif
        $yearSections = [];

        $nowYear = (int) date('Y');

        // ✅ BUCKET non-overlap supaya tiap tab terlihat beda
        $ranges = [
            '3'  => [$nowYear - 2, $nowYear],           // 3 tahun terakhir
            '5'  => [$nowYear - 4, $nowYear - 3],       // 4-5 tahun terakhir
            '10' => [$nowYear - 9, $nowYear - 5],       // 6-10 tahun terakhir
        ];

        foreach ($yearTabs as $key => $tab) {
            [$minYear, $maxYear] = $ranges[$key];

            $yearSections[$key] = Mobil::query()
                ->whereNotNull('tahun')
                ->whereBetween('tahun', [$minYear, $maxYear])
                ->orderByDesc('tahun')   // terbaru dulu (di dalam range tab itu)
                ->orderByDesc('id')
                ->limit(3)
                ->get();
        }

        $requests = KebutuhanPosting::with('user')
            ->latest()
            ->take(12)
            ->get()
            ->map(function ($p) {
                $u = $p->user;

                $avatar = ($u && $u->foto_profile)
                    ? asset('storage/'.$u->foto_profile)
                    : 'https://ui-avatars.com/api/?name='.urlencode($p->nama).'&background=E2E8F0&color=0F172A';

                $tags = array_values(array_filter([
                    $p->tipe_bodi,
                    $p->merek,
                    $p->transmisi,
                ]));

                return [
                    'user_id' => $u?->id,
                    'name'   => $p->nama,
                    'city'   => $p->kota ?? '-',
                    'car'    => $p->judul,              // tetap sama dengan tampilan kamu
                    'price'  => $p->range_harga ?: '—',  // tetap sama
                    'tags'   => $tags,
                    'avatar' => $avatar,
                ];
            });

        $populerMotors = Motor::orderByDesc('likes_motor')
        ->latest()
        ->take(3)
        ->get();

        $likedMotorIds = [];
        if (Auth::check()) {
            $likedMotorIds = DB::table('motor_likes')
                ->where('user_id', Auth::id())
                ->pluck('motor_id')
                ->toArray();
        }

        $kondisisMotor = Motor::query()->select('kondisi_motor')->whereNotNull('kondisi_motor')->distinct()->orderBy('kondisi_motor')->pluck('kondisi_motor');
        $merksMotor    = Motor::query()->select('merk_motor')->whereNotNull('merk_motor')->distinct()->orderBy('merk_motor')->pluck('merk_motor');
        $modelsMotor   = Motor::query()->select('model_motor')->whereNotNull('model_motor')->distinct()->orderBy('model_motor')->pluck('model_motor');
        $lokasisMotor  = Motor::query()->select('lokasi_motor')->whereNotNull('lokasi_motor')->distinct()->orderBy('lokasi_motor')->pluck('lokasi_motor');

        
        return view('home', compact('mobils', 'kondisis', 'merks', 'models', 'lokasis', 'rekomendasis', 'populers', 'comparePairs','priceTabs','activePrice','priceSections',
                    'yearTabs', 'activeYear', 'yearSections','requests','populerMotors', 'likedMotorIds','kondisisMotor','merksMotor','modelsMotor','lokasisMotor'));
    }
}
