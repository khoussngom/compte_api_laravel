<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Compte;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Client extends Model
{
    use HasFactory;
    use Notifiable;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'clients';

    protected $fillable = [
        'id',
        'nom',
        'prenom',
        'titulaire',
        'email',
        'mot_de_passe',
        'nci',
        'security_code',
        'require_code_on_login',
        'telephone',
        'adresse',
        'role',
        'statut',
        'version',
    ];

    protected $hidden = [
        'mot_de_passe',
        'security_code',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function comptes()
    {
        return $this->hasMany(Compte::class, 'client_id', 'id');
    }
}
