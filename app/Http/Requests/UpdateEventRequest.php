<?php

namespace App\Http\Requests;

use App\Models\Event;
use App\Models\EventType;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEventRequest extends FormRequest
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
            'title' => ['required', 'string', Rule::unique(Event::class, 'title')
                ->ignore($this->route('event'))
                ->where(
                    function ($query) {
                        return $query->where('title', "!=", $this->route('event')->title);
                    }
                )],
            'event_type_id' => ['required', 'integer', Rule::exists(EventType::class, 'id')],
            'user_uuid' => ['required', 'uuid', Rule::exists(User::class, 'uuid')],
            'location' => ['required', 'string', 'min:5'],
            'description' => ['required', 'string', 'min:5'],
            'start_date' => ['required', 'date', 'after:today'],
            'end_date' => ['required', 'date', 'after:start_date'],
        ];
    }
}
