<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClassificationRequest;
use App\Http\Requests\UpdateClassificationRequest;
use App\Models\Classification;
use App\Models\SubClassification;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Cache;

class ClassificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
{
    $search = $request->search;

    $data = Classification::with('subClassifications')
        ->search($search)
        ->paginate(10)
        ->appends(['search' => $search]);

    return view('pages.reference.classification', [
        'data' => $data,
        'search' => $search,
    ]);
}

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreClassificationRequest $request
     * @return RedirectResponse
     */
    public function store(StoreClassificationRequest $request): RedirectResponse
{
    try {
        Classification::create($request->validated());

        // ❌ Bersihkan cache klasifikasi form
        Cache::forget('form_classifications');

        return back()->with('success', __('menu.general.success'));
    } catch (\Throwable $exception) {
        return back()->with('error', $exception->getMessage());
    }
}

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateClassificationRequest $request
     * @param Classification $classification
     * @return RedirectResponse
     */
    public function update(UpdateClassificationRequest $request, Classification $classification): RedirectResponse
{
    try {
        $classification->update($request->validated());

        // ❌ Bersihkan cache klasifikasi form
        Cache::forget('form_classifications');

        return back()->with('success', __('menu.general.success'));
    } catch (\Throwable $exception) {
        return back()->with('error', $exception->getMessage());
    }
}

    /**
     * Remove the specified resource from storage.
     *
     * @param Classification $classification
     * @return RedirectResponse
     */
    public function destroy(Classification $classification): RedirectResponse
{
    try {
        $classification->delete();

        // ❌ Bersihkan cache klasifikasi form
        Cache::forget('form_classifications');

        return back()->with('success', __('menu.general.success'));
    } catch (\Throwable $exception) {
        return back()->with('error', $exception->getMessage());
    }
}

  public function storeSub(Request $request): RedirectResponse
{
    try {
        $data = $request->validate([
            'classification_id' => 'required|exists:classifications,id',
            'code' => [
                'required',
                'string',
                Rule::unique('sub_classifications')->where(function ($query) use ($request) {
                    return $query->where('classification_id', $request->classification_id);
                }),
            ],
            'description' => 'required|string',
        ]);

        SubClassification::create($data);

        // ❌ Bersihkan cache klasifikasi dan sub-klasifikasi terkait
        Cache::forget('form_classifications');
        Cache::forget('sub_classifications_of_' . $data['classification_id']);

        return back()->with('success', __('menu.general.success'));
    } catch (\Throwable $exception) {
        return back()->with('error', $exception->getMessage());
    }
}

public function getSubClassifications($classification_id)
{
    $subClassifications = \App\Models\SubClassification::where('classification_id', $classification_id)->get();

    return response()->json($subClassifications);
}

}