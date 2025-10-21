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
        'date_creation',
        'statut_compte',
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
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }
}
