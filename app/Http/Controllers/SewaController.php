<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MobilSewa;
use App\Models\MotorSewa;

class SewaController extends Controller
{
    public function index(Request $request)
    {
        $mobils = MobilSewa::orderByDesc('id')->take(6)->get();
        $motors = MotorSewa::orderByDesc('id')->take(6)->get();
        // dropdown options untuk search bar (ambil dari mobil_sewa)
        $lokasis = MobilSewa::select('lokasi')->whereNotNull('lokasi')->distinct()->orderBy('lokasi')->pluck('lokasi');
        $transmisis = MobilSewa::select('transmisi_mobil')->whereNotNull('transmisi_mobil')->distinct()->orderBy('transmisi_mobil')->pluck('transmisi_mobil');
        $jenisMobils = MobilSewa::select('jenis_mobil')->whereNotNull('jenis_mobil')->distinct()->orderBy('jenis_mobil')->pluck('jenis_mobil');

        // Deteksi: kalau ada input filter dari search bar / sidebar, tampilkan LIST page
        $hasFilter = $request->filled('lokasi')
            || $request->filled('tanggal')
            || $request->filled('transmisi')
            || $request->filled('jenis_mobil')
            || $request->filled('harga_min')
            || $request->filled('harga_max')
            || $request->filled('transmisi_opt')
            || $request->filled('kursi')
            || $request->filled('bahan_bakar');

        if ($hasFilter) {
            $q = MobilSewa::query();

            // filter search bar
            if ($request->filled('lokasi')) {
                $q->where('lokasi', $request->lokasi);
            }
            if ($request->filled('transmisi')) {
                $q->where('transmisi_mobil', $request->transmisi);
            }
            if ($request->filled('jenis_mobil')) {
                $q->where('jenis_mobil', $request->jenis_mobil);
            }

            // filter sidebar (opsional)
            if ($request->filled('harga_min')) {
                $q->where('harga_mobil', '>=', (int) str_replace(['.', ','], '', $request->harga_min));
            }
            if ($request->filled('harga_max')) {
                $q->where('harga_mobil', '<=', (int) str_replace(['.', ','], '', $request->harga_max));
            }

            // transmisi checkbox (transmisi_opt[]=Manual&transmisi_opt[]=Automatic)
            if (is_array($request->transmisi_opt) && count($request->transmisi_opt)) {
                $q->whereIn('transmisi_mobil', $request->transmisi_opt);
            }

            // kursi checkbox (kursi[]=4&kursi[]=6)
            if (is_array($request->kursi) && count($request->kursi)) {
                $q->whereIn('tempat_duduk_mobil', array_map('intval', $request->kursi));
            }

            // bahan bakar checkbox (bahan_bakar[]=Bensin)
            if (is_array($request->bahan_bakar) && count($request->bahan_bakar)) {
                $q->whereIn('jenis_bahan_bakar_mobil', $request->bahan_bakar);
            }

            $mobilsewa = $q->latest()->paginate(4)->withQueryString();

            return view('pages.sewa-list', compact('mobilsewa', 'lokasis', 'transmisis', 'jenisMobils'));
        }

        // kalau tidak ada filter → tetap tampilkan page sewa “landing” kamu
        // (pakai view sewa yang sudah kamu buat)
        return view('pages.sewa', compact('lokasis','mobils','motors', 'transmisis', 'jenisMobils'));
    }
    
    public function showMobil(Request $request, MobilSewa $mobilSewa)
    {
        // seller/customer service (relasi user)
        $seller = $mobilSewa->user ?? null;

        // rekomendasi (ambil random / latest selain yang sedang dibuka)
        $rekomendasi = MobilSewa::where('id', '!=', $mobilSewa->id)
            ->latest()
            ->take(3)
            ->get();

        return view('pages.sewa-show-mobil', [
            'mobil' => $mobilSewa,
            'seller' => $seller,
            'rekomendasi' => $rekomendasi,
        ]);
    }

    public function showMotor(Request $request, MotorSewa $motorSewa)
    {
        $seller = $motorSewa->user ?? null;

        $rekomendasi = MotorSewa::where('id', '!=', $motorSewa->id)
            ->latest()
            ->take(3)
            ->get();

        return view('pages.sewa-show-motor', [
            'motor' => $motorSewa,
            'seller' => $seller,
            'rekomendasi' => $rekomendasi,
        ]);
    }
}
