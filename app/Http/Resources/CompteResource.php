<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompteResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'numero_compte' => $this->numero_compte,
            'titulaire_compte' => $this->titulaire_compte,
            'type_compte' => $this->type_compte,
            'solde' => $this->solde,
            'date_creation' => $this->date_creation,
            'statut_compte' => $this->statut_compte,
            'client_id' => $this->client_id,
        ];
    }
}
