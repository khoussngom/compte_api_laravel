<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompteResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'numeroCompte' => $this->numero_compte,
            'titulaire' => $this->titulaire_compte,
            'type' => strtolower($this->type_compte),
            'solde' => $this->solde,
            'devise' => $this->devise ?? null,
            'dateCreation' => optional($this->date_creation)->toIso8601String(),
            'statut' => $this->statut_compte,
            'motifBlocage' => $this->motif_blocage ?? null,
            'metadata' => [
                'derniereModification' => optional($this->updated_at)->toIso8601String(),
                'version' => 1,
            ],
        ];
    }
}
