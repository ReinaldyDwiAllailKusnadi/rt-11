<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resident;
use App\Models\Payment;
use Illuminate\Support\Facades\Schema;

class ResidentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $perPage = $request->input('per_page', 10);

        $query = Resident::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('no_rumah', 'like', "%{$search}%");
            });
        }

        // Sort by street prefix then house number numerically
        $residents = $query->get()->sortBy(function($resident) {
            $parts = explode('.', $resident->no_rumah);
            $prefix = $parts[0] ?? '';
            $num = isset($parts[1]) ? (int)$parts[1] : 0;
            return [$prefix, $num];
        });

        // Manual pagination
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $col = collect($residents);
        $slice = $col->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginatedResidents = new \Illuminate\Pagination\LengthAwarePaginator(
            $slice, 
            $col->count(), 
            $perPage, 
            $currentPage, 
            ['path' => route('residents.index'), 'query' => $request->query()]
        );

        return view('residents.index', compact('paginatedResidents', 'search', 'perPage'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'no_rumah' => 'required|string|max:50',
        ]);

        $name = strtoupper($request->name);
        
        // Normalize house number format (e.g. J19 -> J. 19, j.19 -> J. 19)
        $noRumah = strtoupper($request->no_rumah);
        $noRumah = preg_replace('/^([A-Z])\.?\s*(\d+)$/', '$1. $2', $noRumah);

        // Check if name already exists
        if (Resident::where('name', $name)->exists()) {
            return back()->withErrors(['name' => 'Nama warga sudah terdaftar.'])->withInput();
        }

        Resident::create([
            'name' => $name,
            'no_rumah' => $noRumah
        ]);

        return redirect()->route('residents.index')->with('success', 'Warga berhasil ditambahkan.');
    }

    public function update(Request $request, Resident $resident)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'no_rumah' => 'required|string|max:50',
        ]);

        $name = strtoupper($request->name);
        $noRumah = strtoupper($request->no_rumah);
        $noRumah = preg_replace('/^([A-Z])\.?\s*(\d+)$/', '$1. $2', $noRumah);

        // Check unique name excluding current resident
        if (Resident::where('name', $name)->where('id', '!=', $resident->id)->exists()) {
            return back()->withErrors(['name' => 'Nama warga sudah terdaftar.']);
        }

        $resident->update([
            'name' => $name,
            'no_rumah' => $noRumah
        ]);

        return redirect()->route('residents.index')->with('success', 'Data warga berhasil diupdate.');
    }

    public function destroy(Resident $resident)
    {
        $resident->delete(); // Cascading delete will remove payments too
        return redirect()->route('residents.index')->with('success', 'Warga berhasil dihapus.');
    }

    public function reset(Request $request)
    {
        Schema::disableForeignKeyConstraints();
        Payment::whereNotNull('resident_id')->delete();
        Resident::truncate();
        Schema::enableForeignKeyConstraints();

        // Seed default residents
        $seeder = new \Database\Seeders\ResidentSeeder();
        $seeder->run();

        return redirect()->route('residents.index')->with('success', 'Data warga berhasil direset ke default.');
    }
}
