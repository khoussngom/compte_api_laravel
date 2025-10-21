<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Client;

class Compte extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'comptes';

    protected $fillable = [
        'id',
        'numero_compte',
        'titulaire_compte',
        'type_compte',
        'solde',
        'devise',
        'date_creation',
        'statut_compte',
        'motif_blocage',
        'version',
        'client_id',
    ];

    protected static function booted()
    {
        // generate uuid on creating if not present
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) \Illuminate\Support\Str::uuid();
            }
        });

        // auto-generate numero_compte if not provided
        static::creating(function ($model) {
            if (empty($model->numero_compte)) {
                // generate an account number ACC + 8 digits
                $model->numero_compte = 'ACC' . sprintf('%08d', random_int(0, 99999999));
            }
            if (empty($model->date_creation)) {
                $model->date_creation = now()->toDateString();
            }
            if (!isset($model->statut_compte)) {
                $model->statut_compte = 'Actif';
            }
        });
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }
}
