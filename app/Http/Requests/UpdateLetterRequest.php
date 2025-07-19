<?php

namespace App\Http\Requests;

use App\Enums\LetterType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLetterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    public function attributes(): array
    {
        return [
            'agenda_number' => __('model.letter.agenda_number'),
            'from' => __('model.letter.from'),
            'to' => __('model.letter.to'),
            'reference_number' => __('model.letter.reference_number'),
            'received_date' => __('model.letter.received_date'),
            'letter_date' => __('model.letter.letter_date'),
            'description' => __('model.letter.description'),
            'note' => __('model.letter.note'),
            'letter_nature' => __('model.letter.letter_nature'),
            'classification_code' => __('model.letter.classification_code'),
            'letter_code' => __('model.letter_code'),
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
{
    $letter = $this->route('incoming') ?? $this->route('outgoing');

    $common = [
        'reference_number' => ['required', Rule::unique('letters')->ignore($letter?->id)],
        'letter_date' => ['required'],
        'letter_nature' => ['required', Rule::in(['Segera', 'Sangat Segera', 'Biasa', 'Rahasia', 'Sangat Rahasia'])],
        'description' => ['required'],
        'note' => ['nullable'],
    ];

    if ($this->type === LetterType::INCOMING->type()) {
        return array_merge($common, [
            'from' => ['required'],
            'letter_code' => ['required'],
        ]);
    }

    if ($this->type === LetterType::OUTGOING->type()) {
        return array_merge($common, [
            'to' => ['required'],
            'agenda_number' => ['nullable'],
            'classification_id' => ['nullable', 'exists:classifications,id'],
            'sub_classification_id' => ['nullable', 'exists:sub_classifications,id'],
            'received_date' => ['nullable'],
        ]);
    }

    return $common;
}
}
