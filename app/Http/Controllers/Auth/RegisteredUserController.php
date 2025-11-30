<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $locations = Location::select('id', 'desa', 'kecamatan')->get();
        return view('auth.register', compact('locations'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'telepon' => ['required', 'string', 'max:20', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'kecamatan' => ['required', 'string'],
            'desa' => ['required', 'string'],
            'alamat' => ['required', 'string'],
        ]);

        // Cari id_lokasi berdasarkan kecamatan dan desa
        $lokasi = Location::where('kecamatan', $request->kecamatan)
                          ->where('desa', $request->desa)
                          ->first();

        if (!$lokasi) {
            return back()->withErrors(['desa' => 'Lokasi tidak ditemukan'])->withInput();
        }

        $user = User::create([
            'nama' => $request->nama,
            'telepon' => $request->telepon,
            'password' => Hash::make($request->password),
            'id_lokasi' => $lokasi->id,
            'alamat' => $request->alamat,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('landing', absolute: false))
            ->with('success', 'Registrasi berhasil! Selamat datang, ' . $user->nama);
    }
}
