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

    $query = Attachment::incoming();

    // Jika bukan admin, filter hanya file yang diupload oleh user ini
    if ($user->role !== 'admin') {
        $query->where('user_id', $user->id);
    }

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
