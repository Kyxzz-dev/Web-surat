<?php

namespace App\Http\Controllers;


use App\Jobs\StoreIncomingLetterJob;
use App\Enums\LetterType;
use App\Http\Requests\StoreLetterRequest;
use App\Http\Requests\UpdateLetterRequest;
use App\Models\Attachment;
use App\Models\Classification;
use App\Models\Config;
use App\Models\Letter;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class IncomingLetterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        return view('pages.transaction.incoming.index', [
            'data' => Letter::incoming()->render($request->search),
            'search' => $request->search,
        ]);
    }

    /**
     * Display a listing of the incoming letter agenda.
     *
     * @param Request $request
     * @return View
     */
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

    /**
     * @param Request $request
     * @return View
     */
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
            'config' => Config::pluck('value','code')->toArray(),
            'title' => $title,
        ]);
    }

    /**
     * Generate unique reference number for incoming letter
     * 
     * @return string
     */
    private function generateLetterCode(): string
{
    $year = now()->format('Y');
    $month = now()->format('m');

    return DB::transaction(function () use ($year, $month) {
        $count = Letter::incoming()
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->lockForUpdate()
            ->count();

        $sequence = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
        return "$year/$month/$sequence";
    });
}

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        $letter_code = $this->generateLetterCode();

        return view('pages.transaction.incoming.create', [
            'letter_code' => $letter_code,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreLetterRequest $request
     * @return RedirectResponse
     */
    
     public function store(StoreLetterRequest $request): RedirectResponse
     {
         try {
             $user = auth()->user();
     
             if ($request->type !== 'incoming') {
                 throw new \Exception(__('menu.transaction.incoming_letter') . ' - Invalid letter type');
             }
     
             // Validasi duplikat nomor surat
             $exists = Letter::where('reference_number', $request->reference_number)
                 ->where('type', 'incoming')
                 ->exists();
     
             if ($exists) {
                 throw new \Exception('Reference number already exists. Please try again.');
             }
     
             // Generate letter_code otomatis
             $letterCode = $this->generateLetterCode();
     
             // Siapkan data surat
             $data = [
                 'reference_number' => $request->reference_number,
                 'from' => $request->from,
                 'letter_date' => $request->letter_date,
                 'letter_nature' => $request->letter_nature,
                 'letter_code' => $letterCode,
                 'description' => $request->description,
                 'note' => $request->note,
                 'type' => 'incoming',
                 'user_id' => $user->id,
             ];
     
             // Ambil lampiran (jika ada)
             $attachments = $request->hasFile('attachments') ? $request->file('attachments') : [];
     
             // Kirim ke job queue (opsional)
             StoreIncomingLetterJob::dispatch($data, $user->id, $attachments);
     
             return redirect()
                 ->route('transaction.incoming.index')
                 ->with('success', 'Surat sedang diproses');
     
         } catch (\Exception $exception) {
             Log::error('Error queueing incoming letter: ' . $exception->getMessage(), [
                 'user_id' => auth()->id(),
                 'request_data' => $request->except(['attachments'])
             ]);
     
             return back()
                 ->withInput()
                 ->with('error', $exception->getMessage());
         }
     }
    /**
     * Handle file attachments
     * 
     * @param array $attachments
     * @param Letter $letter
     * @param $user
     * @return void
     */
    private function handleAttachments(array $attachments, Letter $letter, $user): void
    {
        $allowedExtensions = ['png', 'jpg', 'jpeg', 'pdf'];
        $maxFileSize = 2048; // 2MB in KB

        foreach ($attachments as $attachment) {
            $extension = strtolower($attachment->getClientOriginalExtension());
            $fileSize = $attachment->getSize() / 1024; // Convert to KB

            // Validasi ekstensi file
            if (!in_array($extension, $allowedExtensions)) {
                Log::warning('Invalid file extension attempted', [
                    'filename' => $attachment->getClientOriginalName(),
                    'extension' => $extension,
                    'user_id' => $user->id
                ]);
                continue;
            }

            // Validasi ukuran file
            if ($fileSize > $maxFileSize) {
                Log::warning('File size exceeds limit', [
                    'filename' => $attachment->getClientOriginalName(),
                    'size_kb' => $fileSize,
                    'user_id' => $user->id
                ]);
                continue;
            }

            // Generate unique filename
            $filename = time() . '-' . uniqid() . '-' . $attachment->getClientOriginalName();
            $filename = preg_replace('/[^a-zA-Z0-9\-_\.]/', '-', $filename); // Sanitize filename
            
            // Store file
            $path = $attachment->storeAs('public/attachments', $filename);
            
            if ($path) {
                // Create attachment record
                Attachment::create([
                    'filename' => $filename,
                    'extension' => $extension,
                    'user_id' => $user->id,
                    'letter_id' => $letter->id,
                ]);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Letter $incoming
     * @return View
     */
    public function show(Letter $incoming): View
    {
        return view('pages.transaction.incoming.show', [
            'data' => $incoming->load(['classification', 'user', 'attachments']),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Letter $incoming
     * @return View
     */
    public function edit(Letter $incoming): View
    {
        return view('pages.transaction.incoming.edit', [
            'data' => $incoming,
            'classifications' => Classification::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateLetterRequest $request
     * @param Letter $incoming
     * @return RedirectResponse
     */
    public function update(UpdateLetterRequest $request, Letter $incoming): RedirectResponse
    {
        try {
            return DB::transaction(function() use ($request, $incoming) {
                // Update letter data
                $incoming->update($request->validated());
                
                // Handle new attachments
                if ($request->hasFile('attachments')) {
                    $this->handleAttachments($request->file('attachments'), $incoming, auth()->user());
                }

                return back()->with('success', __('menu.general.success'));
            });

        } catch (\Exception $exception) {
            Log::error('Error updating incoming letter: ' . $exception->getMessage(), [
                'letter_id' => $incoming->id,
                'user_id' => auth()->id()
            ]);
            
            return back()
                ->withInput()
                ->with('error', $exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Letter $incoming
     * @return RedirectResponse
     */
    public function destroy(Letter $incoming): RedirectResponse
    {
        try {
            return DB::transaction(function() use ($incoming) {
                // Delete associated attachments files
                foreach ($incoming->attachments as $attachment) {
                    Storage::delete('public/attachments/' . $attachment->filename);
                }
                
                // Delete letter (cascading will handle attachments records)
                $incoming->delete();

                return redirect()
                    ->route('transaction.incoming.index')
                    ->with('success', __('menu.general.success'));
            });

        } catch (\Exception $exception) {
            Log::error('Error deleting incoming letter: ' . $exception->getMessage(), [
                'letter_id' => $incoming->id,
                'user_id' => auth()->id()
            ]);
            
            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Get new reference number via AJAX
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLetterCode(Request $request)
    {
        try {
            $letterCode = $this->generateLetterCode();
            
            return response()->json([
                'success' => true,
                'letter_code' => $letterCode
            ]);
        } catch (\Exception $e) {
            Log::error('Error generating reference number: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate reference number'
            ], 500);
        }
    }
}