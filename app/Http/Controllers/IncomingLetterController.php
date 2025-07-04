<?php

namespace App\Http\Controllers;

use App\Enums\LetterType;
use App\Http\Requests\StoreLetterRequest;
use App\Http\Requests\UpdateLetterRequest;
use App\Models\Attachment;
use App\Models\Classification;
use App\Models\Config;
use App\Models\Letter;
use App\Models\LetterNumberPool;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IncomingLetterController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = Letter::incoming();

        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        return view('pages.transaction.incoming.index', [
            'data' => $query->render($request->search),
            'search' => $request->search,
        ]);
    }

    public function agenda(Request $request): View
    {
        return view('pages.transaction.incoming.agenda', [
            'data' => Letter::incoming()->agenda($request->since, $request->until, $request->filter)->render($request->search),
            'search' => $request->search,
            'since' => $request->since,
            'until' => $request->until,
            'filter' => $request->filter,
            'query' => $request->getQueryString(),
        ]);
    }

    public function print(Request $request): View
    {
        $agenda = __('menu.agenda.menu');
        $letter = __('menu.agenda.incoming_letter');
        $title = App::getLocale() == 'id' ? "$agenda $letter" : "$letter $agenda";

        return view('pages.transaction.incoming.print', [
            'data' => Letter::incoming()->agenda($request->since, $request->until, $request->filter)->get(),
            'search' => $request->search,
            'since' => $request->since,
            'until' => $request->until,
            'filter' => $request->filter,
            'config' => Config::pluck('value', 'code')->toArray(),
            'title' => $title,
        ]);
    }

    public function create(): View
    {
        return view('pages.transaction.incoming.create', [
            'classifications' => Classification::all(),
            'reference_number' => $this->generateReferenceNumber(),
        ]);
    }

    public function store(StoreLetterRequest $request): RedirectResponse
    {
        try {
            $user = auth()->user();

            if ($request->type != LetterType::INCOMING->type()) {
                throw new \Exception(__('menu.transaction.incoming_letter'));
            }

            $newLetter = $request->validated();
            $newLetter['user_id'] = $user->id;

            $letterDate = $newLetter['letter_date'] ?? Carbon::today()->toDateString();
            $number = $this->getNextLetterNumber($letterDate);
            $classificationCode = $newLetter['classification_code'];
            $newLetter['reference_number'] = $classificationCode . '.' . $number;




            $letter = Letter::create($newLetter);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $attachment) {
                    if (!$attachment->isValid()) continue;

                    $extension = $attachment->getClientOriginalExtension();
                    if (!in_array($extension, ['png', 'jpg', 'jpeg', 'pdf'])) continue;

                    $filename = time() . '-' . str_replace(' ', '-', $attachment->getClientOriginalName());
                    $attachment->storeAs('public/attachments', $filename);

                    Attachment::create([
                        'filename' => $filename,
                        'extension' => $extension,
                        'user_id' => $user->id,
                        'letter_id' => $letter->id,
                    ]);
                }
            }

            return redirect()
                ->route('transaction.incoming.index')
                ->with('success', __('menu.general.success'));

        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function show(Letter $incoming): View
    {
        $user = auth()->user();

        if ($user->role !== 'admin' && $incoming->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk melihat surat ini.');
        }

        return view('pages.transaction.incoming.show', [
            'data' => $incoming->load(['classification', 'user', 'attachments']),
        ]);
    }

    public function edit(Letter $incoming): View
    {
        return view('pages.transaction.incoming.edit', [
            'data' => $incoming,
            'classifications' => Classification::with('subClassifications')->get(),
        ]);
    }

    public function update(UpdateLetterRequest $request, Letter $incoming): RedirectResponse
    {
        try {
            $incoming->update($request->validated());

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $attachment) {
                    if (!$attachment->isValid()) continue;

                    $extension = $attachment->getClientOriginalExtension();
                    if (!in_array($extension, ['png', 'jpg', 'jpeg', 'pdf'])) continue;

                    $filename = time() . '-' . str_replace(' ', '-', $attachment->getClientOriginalName());
                    $attachment->storeAs('public/attachments', $filename);

                    Attachment::create([
                        'filename' => $filename,
                        'extension' => $extension,
                        'user_id' => auth()->id(),
                        'letter_id' => $incoming->id,
                    ]);
                }
            }

            return back()->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function destroy(Letter $incoming): RedirectResponse
    {
        try {
            $incoming->delete();

            return redirect()
                ->route('transaction.incoming.index')
                ->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function previewReferenceNumber(Request $request)
{
    $classificationCode = $request->input('classification_code');
    $letterDate = $request->input('letter_date');

    if (!$classificationCode || !$letterDate) {
        return response()->json(['reference_number' => '']);
    }

    $referenceNumber = $this->generateReferenceNumber($letterDate);
    return response()->json(['reference_number' => $referenceNumber]);
}


    /**
     * Generate automatic reference number for preview only (not marked used)
     */
    private function generateReferenceNumber($date = null): string
{
    $date = $date ? Carbon::parse($date) : Carbon::today();

    $startDate = Carbon::create(2024, 6, 1);
    $daysSinceStart = $startDate->diffInDays($date);

    $startNumber = $daysSinceStart * 30 + 1;

    // Buat pool jika belum
    $exists = LetterNumberPool::whereDate('date', $date)->exists();
    if (!$exists) {
        for ($i = 0; $i < 30; $i++) {
            LetterNumberPool::create([
                'date' => $date,
                'number' => str_pad($startNumber + $i, 4, '0', STR_PAD_LEFT),
                'is_used' => false,
            ]);
        }
    }

    $pool = LetterNumberPool::where('date', $date)
        ->where('is_used', false)
        ->orderBy('number')
        ->first();

    return $pool
        ? 'WIM.2-' . $date->format('Ymd') . '/' . $pool->number
        : 'WIM.2-' . $date->format('Ymd') . '/HABIS';
}



    /**
     * Ambil dan tandai nomor surat dari pool (used = true)
     */
    private function getNextLetterNumber($letterDate): string
{
    return DB::transaction(function () use ($letterDate) {
        $date = Carbon::parse($letterDate);
        $startDate = Carbon::create(2024, 6, 1);
        $daysSinceStart = $startDate->diffInDays($date);
        $startNumber = $daysSinceStart * 30 + 1;

        // Buat pool jika belum
        if (!LetterNumberPool::where('date', $date)->exists()) {
            for ($i = 0; $i < 30; $i++) {
                LetterNumberPool::create([
                    'date' => $date,
                    'number' => str_pad($startNumber + $i, 4, '0', STR_PAD_LEFT),
                    'is_used' => false,
                ]);
            }
        }

        $poolNumber = LetterNumberPool::where('date', $date)
            ->where('is_used', false)
            ->orderBy('number')
            ->lockForUpdate()
            ->first();

        if (!$poolNumber) {
            throw new \Exception('Nomor surat untuk tanggal ini telah habis.');
        }

        $poolNumber->update(['is_used' => true]);

        return 'WIM.2-' . $date->format('Ymd') . '/' . $poolNumber->number;
    });
}

}
