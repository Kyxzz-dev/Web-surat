<?php

namespace App\Http\Controllers;

use App\Models\SlotAllocation;
use Illuminate\Http\Request;

class SlotAllocationController extends Controller
{
    public function index()
    {
        $slots = SlotAllocation::orderBy('date')->get();
        return view('pages.slot_allocations.index', compact('slots'));
    }

    public function create()
    {
        return view('pages.slot_allocations.create');
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'date' => 'required|date|unique:slot_allocations,date',
        'start_number' => 'required|integer|min:1',
        'end_number' => 'required|integer|gt:start_number',
    ]);

    // Format tanggal secara eksplisit
    $validated['date'] = \Carbon\Carbon::parse($validated['date'])->format('Y-m-d');

    SlotAllocation::create($validated);

    return redirect()->route('reference.slot-allocations.index')
                     ->with('success', 'Slot berhasil ditambahkan.');
}

    public function edit(SlotAllocation $slot_allocation)
    {
        return view('pages.slot_allocations.edit', [
            'slot' => $slot_allocation
        ]);
    }

    public function update(Request $request, SlotAllocation $slot_allocation)
    {
        $validated = $request->validate([
            'date' => 'required|date|unique:slot_allocations,date,' . $slot_allocation->id,
            'start_number' => 'required|integer|min:1',
            'end_number' => 'required|integer|gt:start_number',
        ]);

        $slot_allocation->update($validated);

        return redirect()->route('reference.slot-allocations.index')
                         ->with('success', 'Slot berhasil diupdate.');
    }

    public function destroy(SlotAllocation $slot_allocation)
    {
        $slot_allocation->delete();
        return back()->with('success', 'Slot berhasil dihapus.');
    }
}
