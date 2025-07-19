<?php

namespace App\Http\Controllers;

use App\Enums\Config as ConfigEnum;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Config;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use TheSeer\Tokenizer\Exception;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
{
    $query = User::query();

    // Search
    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%')
              ->orWhere('email', 'like', '%' . $request->search . '%')
              ->orWhere('phone', 'like', '%' . $request->search . '%');
        });
    }

    // Filter tanggal
    if ($request->filled('since') && $request->filled('until') && $request->filled('filter')) {
        $field = $request->filter;
        $query->whereBetween($field, [
            date('Y-m-d 00:00:00', strtotime($request->since)),
            date('Y-m-d 23:59:59', strtotime($request->until)),
        ]);
    }

    // Ambil data
    $data = $query->latest()->paginate(10);

    // Kirim ke view
    return view('pages.user', [
        'data' => $data,
        'search' => $request->search,
        'since' => $request->since,
        'until' => $request->until,
        'filter' => $request->filter,
        'query' => http_build_query([
            'search' => $request->search,
            'since' => $request->since,
            'until' => $request->until,
            'filter' => $request->filter,
        ]),
    ]);
}


public function print()
{
    $data = \App\Models\User::all();
    $title = 'Laporan Data Pengguna';

    // Jika kamu pakai $config di layout cetakmu
    $config = [
        'institution_name' => 'Nama Instansi',
        'institution_address' => 'Alamat Instansi',
    ];

    return view('user.print', compact('data', 'title', 'config'));
}






    /**
     * Store a newly created resource in storage.
     *
     * @param StoreUserRequest $request
     * @return RedirectResponse
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        try {
            $newUser = $request->validated();
            $newUser['password'] = Hash::make(Config::getValueByCode(ConfigEnum::DEFAULT_PASSWORD));
            User::create($newUser);
            return back()->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUserRequest $request
     * @param User $user
     * @return RedirectResponse
     */
   public function update(UpdateUserRequest $request, User $user): RedirectResponse
{
    try {
        $newUser = $request->validated();
        $newUser['is_active'] = isset($newUser['is_active']);

        // Ganti password manual jika user isi field 'new_password'
        if ($request->filled('new_password')) {
            $newUser['password'] = Hash::make($request->input('new_password'));
        }

        // Kalau ada checkbox reset_password, ganti dengan password default
        if ($request->reset_password) {
            $newUser['password'] = Hash::make(Config::getValueByCode(ConfigEnum::DEFAULT_PASSWORD));
        }

        $user->update($newUser);
        return back()->with('success', __('menu.general.success'));
    } catch (\Throwable $exception) {
        return back()->with('error', $exception->getMessage());
    }
}

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return RedirectResponse
     * @throws \Exception
     */
   public function destroy(User $user): RedirectResponse
{
    // Cek apakah user masih dipakai di surat
    if ($user->letters()->exists()) {
        return back()->with('error', 'Pengguna tidak dapat dihapus karena masih terhubung dengan data surat.');
    }

    try {
        $user->delete();
        return back()->with('success', __('menu.general.success'));
    } catch (\Throwable $exception) {
        return back()->with('error', $exception->getMessage());
    }
}

}
