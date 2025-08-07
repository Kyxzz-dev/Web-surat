<?php

namespace App\Http\Controllers;

use App\Helpers\GeneralHelper;
use App\Http\Requests\UpdateConfigRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Attachment;
use App\Models\Config;
use App\Models\Disposition;
use App\Models\Letter;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class PageController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user(); // Ambil user login
        $userId = $user->id;

        // Pengecekan role langsung via string
        $isAdmin = in_array($user->role, ['admin', 'super-admin']);

        if ($isAdmin) {
            $todayIncomingLetter = Letter::incoming()->today()->count();
            $todayOutgoingLetter = Letter::outgoing()->today()->count();
            $todayDispositionLetter = Disposition::today()->count();

            $yesterdayIncomingLetter = Letter::incoming()->yesterday()->count();
            $yesterdayOutgoingLetter = Letter::outgoing()->yesterday()->count();
            $yesterdayDispositionLetter = Disposition::yesterday()->count();
        } else {
            $todayIncomingLetter = Letter::incoming()->today()->where('user_id', $userId)->count();
            $todayOutgoingLetter = Letter::outgoing()->today()->where('user_id', $userId)->count();
            $todayDispositionLetter = Disposition::today()->where('user_id', $userId)->count();

            $yesterdayIncomingLetter = Letter::incoming()->yesterday()->where('user_id', $userId)->count();
            $yesterdayOutgoingLetter = Letter::outgoing()->yesterday()->where('user_id', $userId)->count();
            $yesterdayDispositionLetter = Disposition::yesterday()->where('user_id', $userId)->count();
        }

        $todayLetterTransaction = $todayIncomingLetter + $todayOutgoingLetter + $todayDispositionLetter;
        $yesterdayLetterTransaction = $yesterdayIncomingLetter + $yesterdayOutgoingLetter + $yesterdayDispositionLetter;

        return view('pages.dashboard', [
            'greeting' => GeneralHelper::greeting(),
            'currentDate' => Carbon::now()->isoFormat('dddd, D MMMM YYYY'),
            'todayIncomingLetter' => $todayIncomingLetter,
            'todayOutgoingLetter' => $todayOutgoingLetter,
            'todayDispositionLetter' => $todayDispositionLetter,
            'todayLetterTransaction' => $todayLetterTransaction,
            'activeUser' => User::active()->count(),
            'percentageIncomingLetter' => GeneralHelper::calculateChangePercentage($yesterdayIncomingLetter, $todayIncomingLetter),
            'percentageOutgoingLetter' => GeneralHelper::calculateChangePercentage($yesterdayOutgoingLetter, $todayOutgoingLetter),
            'percentageDispositionLetter' => GeneralHelper::calculateChangePercentage($yesterdayDispositionLetter, $todayDispositionLetter),
            'percentageLetterTransaction' => GeneralHelper::calculateChangePercentage($yesterdayLetterTransaction, $todayLetterTransaction),
        ]);
    }

    public function profile(Request $request): View
    {
        return view('pages.profile', [
            'data' => auth()->user(),
        ]);
    }

    public function profileUpdate(UpdateUserRequest $request): RedirectResponse
    {
        try {
        $newProfile = $request->validated();
        
        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $oldPicture = auth()->user()->profile_picture;
            if (str_contains($oldPicture, '/storage/avatars/')) {
                $url = parse_url($oldPicture, PHP_URL_PATH);
                Storage::delete(str_replace('/storage', 'public', $url));
            }

            $filename = time() . '-' . uniqid() . '.' . $request->file('profile_picture')->getClientOriginalExtension();
            $request->file('profile_picture')->storeAs('public/avatars', $filename);
            $newProfile['profile_picture'] = asset('storage/avatars/' . $filename);
        }

        // Handle password update
        if ($request->filled('new_password')) {
            $newProfile['password'] = Hash::make($request->new_password);
            
            // Log untuk debugging (hapus setelah testing)
            \Log::info('Password sedang diupdate untuk user: ' . auth()->user()->id);
        }

        // Remove fields yang tidak perlu disimpan ke database
        unset($newProfile['new_password']);
        unset($newProfile['new_password_confirmation']);

        // Update user
        auth()->user()->update($newProfile);
        
        // Log untuk debugging (hapus setelah testing)
        \Log::info('Profile updated successfully for user: ' . auth()->user()->id);
        
        return back()->with('success', __('menu.general.success'));
    } catch (\Throwable $exception) {
        // Log error untuk debugging
        \Log::error('Error updating profile: ' . $exception->getMessage());
        
        return back()->with('error', $exception->getMessage());
    }
    }

    public function deactivate(): RedirectResponse
    {
        try {
            auth()->user()->update(['is_active' => false]);
            Auth::logout();
            return back()->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function settings(Request $request): View
    {
        
        return view('pages.setting', [
            'configs' => Config::all(),
        ]);
    }

    public function settingsUpdate(UpdateConfigRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            foreach ($request->validated() as $code => $value) {
                Config::where('code', $code)->update(['value' => $value]);
            }
            DB::commit();
            return back()->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            DB::rollBack();
            return back()->with('error', $exception->getMessage());
        }
    }

    public function removeAttachment(Request $request): RedirectResponse
    {
        try {
            $attachment = Attachment::find($request->id);
            $oldPicture = $attachment->path_url;
            if (str_contains($oldPicture, '/storage/attachments/')) {
                $url = parse_url($oldPicture, PHP_URL_PATH);
                Storage::delete(str_replace('/storage', 'public', $url));
            }
            $attachment->delete();
            return back()->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }
}
