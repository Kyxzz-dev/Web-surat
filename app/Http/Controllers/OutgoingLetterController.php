<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Config;
use App\Models\Letter;
use App\Enums\LetterType;
use App\Models\Attachment;
use Illuminate\Http\Request;
use App\Models\Classification;
use App\Models\LetterNumberPool;
use App\Models\SubClassification;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreLetterRequest;
use App\Models\SlotAllocation;
use App\Http\Requests\UpdateLetterRequest;
use Illuminate\Support\Facades\Cache;

class OutgoingLetterController extends Controller
{
    public function index(Request $request): View
{
    return view('pages.transaction.outgoing.index', [
        'data' => Letter::outgoing()
            ->when(auth()->user()->role === 'staff', function ($query) {
                // Staff hanya melihat surat yang dia buat
                return $query->where('user_id', auth()->id());
            })
            ->when(auth()->user()->role === 'admin', function ($query) {
                // Admin lihat surat sesuai bidangnya
                return $query->bidang(auth()->user()->bidang);
            })
            // super-admin tidak difilter, biarkan semua data
            ->render($request->search),

        'search' => $request->search,
    ]);
}


    public function agenda(Request $request): View
{
    return view('pages.transaction.outgoing.agenda', [
        'data' => Letter::outgoing()
            ->when(auth()->user()->role === 'admin', function ($query) {
                return $query->bidang(auth()->user()->bidang);
            })
            ->agenda($request->since, $request->until, $request->filter, $request->search)

            ->render($request->search, $request->filter),

        'search' => $request->search,
        'since' => $request->since,
        'until' => $request->until,
        'filter' => $request->filter,
        'bidang' => $request->bidang,
        'query' => $request->getQueryString(),
    ]);
}

    


   public function print(Request $request): View
{
    $agenda = __('menu.agenda.menu');
    $letter = __('menu.agenda.outgoing_letter');
    $title  = App::getLocale() == 'id' ? "$agenda $letter" : "$letter $agenda";

    $letters = Letter::outgoing()
        ->when(auth()->user()->role === 'admin', function ($query) {
            return $query->bidang(auth()->user()->bidang); // filter berdasarkan bidang admin
        })
        ->agenda($request->since, $request->until, $request->filter)
        ->orderBy('letter_date')
        ->get()
        ->groupBy(function ($item) {
            return $item->letter_date->format('Y-m-d'); // Kelompokkan berdasarkan tanggal surat
        });

    $filledData = [];

    foreach ($letters as $date => $items) {
        $filledGroup = [];

        foreach ($items as $letter) {
            $filledGroup[] = $letter;
        }

        $missing = 30 - count($filledGroup);
        for ($i = 0; $i < $missing; $i++) {
            $filledGroup[] = null;
        }

        $filledData[$date] = $filledGroup;
    }

    return view('pages.transaction.outgoing.print', [
        'data'   => $filledData,
        'search' => $request->search,
        'since'  => $request->since,
        'until'  => $request->until,
        'filter' => $request->filter,
        'config' => Config::pluck('value', 'code')->toArray(),
        'title'  => $title,
    ]);
}


   public function create(): View
{
    $classifications = Cache::remember('form_classifications', 86400, function () {
        return Classification::select('id', 'code', 'type')->get();
    });

    return view('pages.transaction.outgoing.create', compact('classifications'));
}

   public function store(StoreLetterRequest $request): RedirectResponse
{
    try {
        $user = auth()->user();

        if ($request->type != LetterType::OUTGOING->type()) {
            throw new \Exception(__('menu.transaction.outgoing_letter'));
        }

        $date = $request->input('letter_date');

        // 🔧 Buat slot otomatis jika belum ada
        $slot = SlotAllocation::where('date', $date)->first();
        if (! $slot) {
            $lastSlot = SlotAllocation::orderBy('date', 'desc')->first();
            $startNumber = $lastSlot ? $lastSlot->end_number + 1 : 1;
            $slot = SlotAllocation::create([
                'date'         => $date,
                'start_number' => $startNumber,
                'end_number'   => $startNumber + 29,
            ]);
        }

        // 🔢 Hitung nomor agenda berdasarkan slot
        $used = Letter::where('letter_date', $date)
    ->where('type', LetterType::OUTGOING->type())
    ->count();
        $agendaNumber = $slot->start_number + $used;

        if ($agendaNumber > $slot->end_number) {
            throw new \Exception("Slot surat untuk tanggal $date sudah habis.");
        }

        $newLetter = $request->validated();
        $newLetter['user_id'] = $user->id;
        $newLetter['agenda_number'] = $agendaNumber;
        $newLetter['letter_date'] = $date;
        $newLetter['bidang'] = $user->bidang;

        $classification = Classification::findOrFail($request->classification_id);
        $sub = $request->sub_classification_id
    ? SubClassification::findOrFail($request->sub_classification_id)
    : null;

$reference_number = 'WIM.2-' . trim($classification->code);

if ($sub) {
    $reference_number .= '-' . $sub->code;
}

        $reference_number .= '-' . str_pad($agendaNumber, 3, '0', STR_PAD_LEFT);
        $newLetter['reference_number'] = $reference_number;
        $newLetter['classification_code'] = $classification->code;

        $letter = Letter::create($newLetter);

        // if ($request->hasFile('attachments')) {
        //     foreach ($request->attachments as $attachment) {
        //         $extension = $attachment->getClientOriginalExtension();
        //         if (! in_array($extension, ['png', 'jpg', 'jpeg', 'pdf'])) continue;

        //         $filename = time() . '-' . str_replace(' ', '-', $attachment->getClientOriginalName());
        //         $attachment->storeAs('public/attachments', $filename);

        //         Attachment::create([
        //             'filename' => $filename,
        //             'extension' => $extension,
        //             'user_id' => $user->id,
        //             'letter_id' => $letter->id,
        //         ]);
        //     }
        // }

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
        $subClassifications= SubClassification::all();
        $classifications = Classification::all();
        return view('pages.transaction.outgoing.edit', [
            'data'            => $outgoing,
        'classifications' => $classifications,
        'subClassifications' => $subClassifications,
        ]);
    }

   public function update(UpdateLetterRequest $request, Letter $outgoing): RedirectResponse
{
    try {
        $outgoing->update($request->validated());

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $attachment) {
                $extension = $attachment->getClientOriginalExtension();
                if (!in_array(strtolower($extension), ['png', 'jpg', 'jpeg', 'pdf'])) {
                    continue;
                }

                $filename = time() . '-' . str_replace(' ', '-', $attachment->getClientOriginalName());
                $path = $attachment->storeAs('public/attachments', $filename);

                Attachment::create([
                    'filename'  => $filename,
                    'extension' => $extension,
                    'user_id'   => auth()->id(),
                    'letter_id' => $outgoing->id,
                ]);
            }
        }

           return redirect()->route('transaction.outgoing.index')->with('success', __('menu.general.success'));
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

   public function getSubClassifications($classification_id)
{
    $cacheKey = 'sub_classifications_of_' . $classification_id;

    $subClassifications = Cache::remember($cacheKey, 86400, function () use ($classification_id) {
        return SubClassification::where('classification_id', $classification_id)->get();
    });

    return response()->json($subClassifications);
}

    // 

   public function getNextAgendaNumber(Request $request)
{
    $date = $request->input('letter_date') ?? now()->toDateString();

    // Ambil slot terakhir agar bisa menentukan start_number baru
    $latestSlot = SlotAllocation::orderBy('end_number', 'desc')->first();
    $lastEndNumber = $latestSlot ? $latestSlot->end_number : 0;

    // Buat slot baru jika belum ada untuk tanggal ini
    $slot = SlotAllocation::firstOrCreate(
        ['date' => $date],
        ['start_number' => $lastEndNumber + 1, 'end_number' => $lastEndNumber + 30]
    );

    // Hitung surat keluar (outgoing) yang sudah digunakan di tanggal ini
    $usedCount = Letter::whereDate('letter_date', $date)
        ->where('type', LetterType::OUTGOING->type()) // ✅ hanya surat keluar
        ->count();

    $nextNumber = $slot->start_number + $usedCount;

    if ($nextNumber > $slot->end_number) {
        return response()->json(['error' => 'Slot nomor surat sudah habis.'], 400);
    }

    return response()->json([
        'next_number' => str_pad($nextNumber, 3, '0', STR_PAD_LEFT),
        'slot_start'  => $slot->start_number,
        'slot_end'    => $slot->end_number,
        'used'        => $usedCount
    ]);
}



}