<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locations = Location::orderBy('created_at', 'desc')->take(5)->get();

        return view('praktikum.soal2', compact('locations'));
    }

    public function filter(Request $request)
    {
        // Mendapatkan semua kecamatan
        $allKecamatan = Location::select('kecamatan')
                        ->distinct()->pluck('kecamatan');

        // Mengambil kecamatan yang dipilih dari request
        // default nya 'semua'
        $selectedKecamatan = $request->get('kecamatan', 'semua');

        // Mengambil data berdasarkan kecamatan yang dipilih
        if ($selectedKecamatan == 'semua') {
            $locations = Location::all();
        } else {
            $locations = Location::where('kecamatan', $selectedKecamatan)->get();
        }

        return view('praktikum.soal3', compact(
            'locations',
            'allKecamatan',
            'selectedKecamatan'
        ));
    }

    public function search(Request $request)
    {
        $desa = $request->get('desa');
        $desanya = collect();

        if ($desa) {
            $desanya = Location::where('desa', 'like', '%' . $desa . '%')->get();
        }

        return view('praktikum.soal5', compact('desa', 'desanya'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Location $location)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Location $location)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Location $location)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location)
    {
        //
    }
}
