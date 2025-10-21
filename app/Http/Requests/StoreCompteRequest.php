<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulaire_compte' => ['required', 'string', 'max:255'],
            'type_compte' => ['required', 'in:Epargne,Cheque'],
            'solde' => ['nullable', 'numeric'],
            'date_creation' => ['nullable', 'date'],
            'statut_compte' => ['nullable', 'in:Bloqué,Actif'],
            'client_id' => ['required', 'uuid', 'exists:clients,id'],
        ];
    }
}
