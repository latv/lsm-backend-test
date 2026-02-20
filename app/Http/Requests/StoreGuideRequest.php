<?php

namespace App\Http\Requests;

use App\Enums\Channel;
use App\Models\Guide;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Validator;

class StoreGuideRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:100'],
            'channel_nr' => ['required', 'integer', 'in:'.implode(',', Channel::values())],
            'starts_at' => ['required', 'date_format:Y-m-d H:i:s'],
            'ends_at' => ['required', 'date_format:Y-m-d H:i:s', 'after:starts_at'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $startsAt = Carbon::parse($this->starts_at);
                $endsAt = Carbon::parse($this->ends_at);

                $overlaps = Guide::where('channel_nr', $this->channel_nr)
                    ->where(function ($query) use ($startsAt, $endsAt) {
                        $query->where('starts_at', '<', $endsAt)
                            ->where('ends_at', '>', $startsAt);
                    })
                    ->exists();

                if ($overlaps) {
                    $validator->errors()->add(
                        'starts_at',
                        'The time range overlaps with an existing entry on this channel.'
                    );
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'channel_nr.in' => 'Channel number must be one of: '.implode(', ', Channel::values()).'.',
            'ends_at.after' => 'The end time must be after the start time.',
        ];
    }
}
