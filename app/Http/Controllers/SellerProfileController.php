<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Mobil;
use App\Models\Motor;
use App\Models\KebutuhanPosting;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class SellerProfileController extends Controller
{
    public function me(Request $request)
    {
        return $this->render($request, $request->user());
    }

    public function show(Request $request, User $user)
    {
        return $this->render($request, $user);
    }

    private function render(Request $request, User $seller)
    {
        $tab = $request->get('tab', 'iklan');

        // =========================
        // FILTERS (sudah dari kamu)
        // =========================
        $q = trim((string) $request->get('q', ''));
        $min = $request->get('min_price');
        $max = $request->get('max_price');
        $transmissions = (array) $request->input('transmission', []);
        $seats = (array) $request->input('seats', []);
        $fuels = (array) $request->input('fuel', []);

        // =========================
        // PAGINATION CONTROL (biar gak get() semua)
        // =========================
        $perPage = 9;
        $page = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();

        // ambil secukupnya saja untuk halaman sekarang + buffer
        $limit = ($page * $perPage) + 20;

        // =========================
        // MOBIL QUERY (dengan eager load jika perlu)
        // =========================
        $carsQuery = Mobil::query()
            // kalau di blade kamu akses relasi (mis. $mobil->user), aktifkan eager load ini:
            // ->with(['user'])
            ->where('user_id', $seller->id)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('merk_mobil', 'like', "%{$q}%")
                    ->orWhere('model_mobil', 'like', "%{$q}%")
                    ->orWhere('jenis_kendaraan', 'like', "%{$q}%")
                    ->orWhere('jenis_mobil', 'like', "%{$q}%");
                });
            })
            ->when(is_numeric($min), fn ($query) => $query->where('harga_mobil', '>=', (int) $min))
            ->when(is_numeric($max), fn ($query) => $query->where('harga_mobil', '<=', (int) $max))
            ->when(!empty($transmissions), fn ($query) => $query->whereIn('transmisi_mobil', $transmissions))
            ->when(!empty($seats), fn ($query) => $query->whereIn('tempat_duduk_mobil', $seats))
            ->when(!empty($fuels), fn ($query) => $query->whereIn('jenis_bahan_bakar_mobil', $fuels))
            ->orderByDesc('created_at');

        // =========================
        // MOTOR QUERY (dengan eager load jika perlu)
        // =========================
        $motorsQuery = Motor::query()
            // kalau di blade kamu akses relasi (mis. $motor->user), aktifkan eager load ini:
            // ->with(['user'])
            ->where('user_id', $seller->id)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('merk_motor', 'like', "%{$q}%")
                    ->orWhere('model_motor', 'like', "%{$q}%")
                    ->orWhere('jenis_kendaraan_motor', 'like', "%{$q}%")
                    ->orWhere('jenis_motor', 'like', "%{$q}%");
                });
            })
            ->when(is_numeric($min), fn ($query) => $query->where('harga_motor', '>=', (int) $min))
            ->when(is_numeric($max), fn ($query) => $query->where('harga_motor', '<=', (int) $max))
            ->when(!empty($transmissions), fn ($query) => $query->whereIn('transmisi_motor', $transmissions))
            ->when(!empty($seats), fn ($query) => $query->whereIn('tempat_duduk_motor', $seats))
            ->when(!empty($fuels), fn ($query) => $query->whereIn('jenis_bahan_bakar_motor', $fuels))
            ->orderByDesc('created_at');

        // =========================
        // AMBIL DATA SECARA RINGAN (TAKE LIMIT)
        // =========================
        $carItems = $carsQuery->take($limit)->get()->map(fn ($m) => (object) [
            'type' => 'mobil',
            'data' => $m,
            'created_at' => $m->created_at,
        ]);

        $motorItems = $motorsQuery->take($limit)->get()->map(fn ($m) => (object) [
            'type' => 'motor',
            'data' => $m,
            'created_at' => $m->created_at,
        ]);


        $merged = $carItems
            ->concat($motorItems)
            ->sortByDesc('created_at')
            ->values();

        $paginatedItems = $merged
            ->slice(($page - 1) * $perPage, $perPage)
            ->values();

        // NOTE: variable tetap $cars supaya view seller.profile tidak perlu banyak perubahan
        $cars = new LengthAwarePaginator(
            $paginatedItems,
            $merged->count(),
            $perPage,
            $page,
            [
                'path'  => $request->url(),
                'query' => $request->query(),
            ]
        );

        // =========================
        // POSTINGS (tetap)
        // =========================
        $postings = KebutuhanPosting::where('user_id', $seller->id)
            ->latest()
            ->paginate(9)
            ->withQueryString();

        // =========================
        // STATS
        // =========================
        $produkCount =
            Mobil::where('user_id', $seller->id)->count()
            + Motor::where('user_id', $seller->id)->count();

        $pengikut = 0;
        $penilaian = 4.9;

        $likedMobilIds = [];
        $likedMotorIds = [];

        if ($request->user()) {
            $likedMobilIds = $request->user()
                ->likedMobils()
                ->allRelatedIds()
                ->toArray();

            $likedMotorIds = $request->user()
                ->likedMotors()
                ->allRelatedIds()
                ->toArray();
        }

        return view('seller.profile', compact(
            'seller',
            'cars',
            'postings',
            'tab',
            'produkCount',
            'pengikut',
            'penilaian',
            'likedMobilIds',
            'likedMotorIds'
        ));
    }
}
