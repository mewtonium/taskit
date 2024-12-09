<?php

namespace App\Http\Requests;

use App\Enums\Priority;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class TaskStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'notes' => ['nullable'],
            'priority' => ['required', new Rules\Enum(Priority::class)],
            'start_at' => ['nullable', 'sometimes', 'date', 'date_format:Y-m-d', 'after_or_equal:today'],
        ];
    }
}
