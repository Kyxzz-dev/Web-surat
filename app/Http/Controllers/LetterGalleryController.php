<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Letter;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LetterGalleryController extends Controller
{
  public function incoming(Request $request): View
{
    $user = auth()->user();

    $query = Attachment::with('letter')->incoming();

    // Admin hanya bisa lihat yang bidang-nya sama
    if ($user->role === 'admin') {
        $query->whereHas('letter', function ($q) use ($user) {
            $q->where('bidang', $user->bidang);
        });
    }

    // Super-admin atau user lain, bisa akses semua
    // (Boleh tambahkan else {} kalau mau batasan juga)

    return view('pages.gallery.incoming', [
        'data' => $query->render($request->search),
        'search' => $request->search,
    ]);
}

   public function outgoing(Request $request): View
{
    $user = auth()->user();

    $query = Attachment::outgoing();

    // Jika bukan admin, filter hanya file yang diupload oleh user ini
    if ($user->role !== 'admin') {
        $query->where('user_id', $user->id);
    }

    return view('pages.gallery.outgoing', [
        'data' => $query->render($request->search),
        'search' => $request->search,
    ]);
}

}
