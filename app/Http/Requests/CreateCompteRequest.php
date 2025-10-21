<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidPhone;
use App\Rules\ValidNCI;

class CreateCompteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // auth handled by middleware
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:cheque,epargne,Cheque,Epargne'],
            'soldeInitial' => ['required', 'numeric', 'min:10000'],
            'devise' => ['required', 'string', 'max:10'],
            'solde' => ['required', 'numeric', 'min:0'],
            'client' => ['required', 'array'],
            'client.id' => ['nullable', 'uuid', 'exists:clients,id'],
            'client.titulaire' => ['required', 'string', 'max:255'],
            'client.nci' => ['required', 'string', new ValidNCI(), 'unique:clients,nci'],
            'client.email' => ['required', 'email', 'unique:clients,email'],
            'client.telephone' => ['required', new ValidPhone(), 'unique:clients,telephone'],
            'client.adresse' => ['required', 'string', 'max:512'],
        ];
    }

    public function messages(): array
    {
        return [
            'client.email.unique' => 'Cet email est déjà utilisé.',
            'client.telephone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'client.nci.unique' => 'Ce NCI est déjà enregistré.',
            'soldeInitial.min' => 'Le solde initial doit être au moins :min.',
        ];
    }
}
