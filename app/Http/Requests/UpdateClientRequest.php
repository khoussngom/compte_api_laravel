<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\ValidPhone;
use App\Rules\ValidNCI;

class UpdateClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Will be checked by middleware (Admin only)
        return true;
    }

    public function rules(): array
    {
        $clientId = $this->route('compte') ? $this->route('compte')->client_id ?? null : null;

        return [
            'titulaire' => ['sometimes', 'string', 'max:255'],
            'informationsClient.telephone' => ['sometimes', new ValidPhone(), Rule::unique('clients', 'telephone')->ignore($clientId, 'id')],
            'informationsClient.email' => ['sometimes', 'email', Rule::unique('clients', 'email')->ignore($clientId, 'id')],
            'informationsClient.password' => ['sometimes', 'string', 'min:8'],
            'informationsClient.nci' => ['sometimes', new ValidNCI(), Rule::unique('clients', 'nci')->ignore($clientId, 'id')],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $data = $this->all();
            $hasField = false;
            if (!empty($data['titulaire'])) $hasField = true;
            if (!empty($data['informationsClient']) && is_array($data['informationsClient'])) {
                foreach ($data['informationsClient'] as $val) {
                    if ($val !== null && $val !== '') { $hasField = true; break; }
                }
            }
            if (!$hasField) {
                $v->errors()->add('payload', 'Au moins un champ doit être fourni.');
            }
        });
    }
}
