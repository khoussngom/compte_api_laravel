<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $clientId = $this->route('client')?->id ?? null;

        return [
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:clients,email'.($clientId ? ",$clientId" : '')],
            'mot_de_passe' => [$this->isMethod('post') ? 'required' : 'nullable', 'string', 'min:8'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'adresse' => ['nullable', 'string', 'max:500'],
            'role' => ['nullable', 'in:Admin,Client'],
        ];
    }
}
