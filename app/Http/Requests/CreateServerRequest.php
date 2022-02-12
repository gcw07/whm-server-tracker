<?php

namespace App\Http\Requests;

use App\Enums\ServerTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\Enum\Laravel\Rules\EnumRule;

class CreateServerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:191'],
            'address' => ['required', 'string', 'max:191'],
            'port' => ['required', 'numeric'],
            'server_type' => ['required', new EnumRule(ServerTypeEnum::class)],
            'notes' => ['nullable', 'string'],
            'token' => ['nullable', 'string'],
        ];
    }
}
