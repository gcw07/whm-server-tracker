<?php

namespace App\Http\Requests;

use App\Enums\ServerTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateServerRequest extends FormRequest
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
            'server_type' => ['required', new Enum(ServerTypeEnum::class)],
            'notes' => ['nullable', 'string'],
        ];
    }
}
