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

class OutgoingLetterController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = Letter::outgoing();

        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        return view('pages.transaction.outgoing.index', [
            'data' => $query->render($request->search),
            'search' => $request->search,
        ]);
    }

    public function agenda(Request $request): View
    {
        return view('pages.transaction.outgoing.agenda', [
            'data' => Letter::outgoing()->agenda($request->since, $request->until, $request->filter)->render($request->search),
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
        $letter = __('menu.agenda.outgoing_letter');
        $title = App::getLocale() == 'id' ? "$agenda $letter" : "$letter $agenda";

        return view('pages.transaction.outgoing.print', [
            'data' => Letter::outgoing()->agenda($request->since, $request->until, $request->filter)->get(),
            'search' => $request->search,
            'since' => $request->since,
            'until' => $request->until,
            'filter' => $request->filter,
            'config' => Config::pluck('value','code')->toArray(),
            'title' => $title,
        ]);
    }

    public function create(): View
{
    $classifications = Classification::with('subClassifications')->get();
    $reference_number = $this->generateReferenceNumber();

    return view('pages.transaction.outgoing.create', compact('classifications', 'reference_number'));
}

    public function store(StoreLetterRequest $request): RedirectResponse
    {
        try {
            $user = auth()->user();
            if ($request->type != LetterType::OUTGOING->type()) {
                throw new \Exception(__('menu.transaction.outgoing_letter'));
            }

            $newLetter = $request->validated();
            $newLetter['user_id'] = $user->id;
            $newLetter['reference_number'] = $this->getNextLetterNumber($newLetter['letter_date']);

            $letter = Letter::create($newLetter);

            if ($request->hasFile('attachments')) {
                foreach ($request->attachments as $attachment) {
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

            return redirect()->route('transaction.outgoing.index')->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function show(Letter $outgoing): View
    {
        return view('pages.transaction.outgoing.show', [
            'data' => $outgoing->load(['classification', 'user', 'attachments']),
        ]);
    }

    public function edit(Letter $outgoing): View
    {
        return view('pages.transaction.outgoing.edit', [
            'data' => $outgoing,
            'classifications' => Classification::all(),
        ]);
    }

    public function update(UpdateLetterRequest $request, Letter $outgoing): RedirectResponse
    {
        try {
            $outgoing->update($request->validated());

            if ($request->hasFile('attachments')) {
                foreach ($request->attachments as $attachment) {
                    $extension = $attachment->getClientOriginalExtension();
                    if (!in_array($extension, ['png', 'jpg', 'jpeg', 'pdf'])) continue;

                    $filename = time() . '-' . str_replace(' ', '-', $attachment->getClientOriginalName());
                    $attachment->storeAs('public/attachments', $filename);

                    Attachment::create([
                        'filename' => $filename,
                        'extension' => $extension,
                        'user_id' => auth()->id(),
                        'letter_id' => $outgoing->id,
                    ]);
                }
            }

            return back()->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function destroy(Letter $outgoing): RedirectResponse
    {
        try {
            $outgoing->delete();
            return redirect()->route('transaction.outgoing.index')->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function previewReferenceNumber(Request $request)
{
    $letterDate = $request->input('letter_date');
    if (!$letterDate) {
        return response()->json([
            'reference_number' => '',
            'remaining' => null,
            'limit' => null,
        ]);
    }

    $referenceNumber = $this->generateReferenceNumber($letterDate);
    $remaining = LetterNumberPool::where('date', $letterDate)->where('is_used', false)->count();

    // Tambahkan ini untuk menentukan limit berdasarkan bulan
    $date = \Carbon\Carbon::parse($letterDate);
    $limit = $date->month % 2 === 0 ? 40 : 30;

    return response()->json([
        'reference_number' => $referenceNumber,
        'remaining' => $remaining,
        'limit' => $limit,
    ]);
}



    private function generateReferenceNumber($date = null): string
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();
        $limit = $date->month % 2 === 0 ? 40 : 30;
        $startDate = Carbon::create(2024, 6, 1);
        $daysSinceStart = $startDate->diffInDays($date);
        $startNumber = $daysSinceStart * $limit + 1;

        if (!LetterNumberPool::whereDate('date', $date)->exists()) {
            for ($i = 0; $i < $limit; $i++) {
                LetterNumberPool::create([
                    'date' => $date,
                    'number' => str_pad($startNumber + $i, 4, '0', STR_PAD_LEFT),
                    'is_used' => false,
                ]);
            }
        }

        $pool = LetterNumberPool::where('date', $date)->where('is_used', false)->orderBy('number')->first();

        return $pool ? 'WIM.2-' . $date->format('Ymd') . '/' . $pool->number : 'WIM.2-' . $date->format('Ymd') . '/HABIS';
    }

    private function getNextLetterNumber($letterDate): string
    {
        return DB::transaction(function () use ($letterDate) {
            $date = Carbon::parse($letterDate);
            $limit = $date->month % 2 === 0 ? 40 : 30;
            $startDate = Carbon::create(2024, 6, 1);
            $daysSinceStart = $startDate->diffInDays($date);
            $startNumber = $daysSinceStart * $limit + 1;

            if (!LetterNumberPool::where('date', $date)->exists()) {
                for ($i = 0; $i < $limit; $i++) {
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

            if (!$poolNumber) throw new \Exception('Nomor surat untuk tanggal ini telah habis.');

            $poolNumber->update(['is_used' => true]);

            return 'WIM.2-' . $date->format('Ymd') . '/' . $poolNumber->number;
        });
    }
}