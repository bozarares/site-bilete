<?php

namespace App\Http\Requests;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStaffRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'user_uuid' => ['required', 'uuid', Rule::exists(User::class, 'uuid')],
            // 'event_uuid' => ['required', 'uuid', Rule::exists(Event::class, 'uuid')],
            'can_validate' => ['required', 'boolean'],
            'can_edit' => ['required', 'boolean']
        ];
    }
}
