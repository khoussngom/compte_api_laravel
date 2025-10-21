<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Compte;
use App\Traits\RestResponse;
use App\Http\Requests\StoreCompteRequest;
use Illuminate\Http\Request;
use App\Http\Resources\CompteResource;

class CompteController extends Controller
{
    use RestResponse;

    public function index(Request $request)
    {
        $user = $request->user();

        $query = Compte::query();

        // si client, filtrer seulement ses comptes
        if ($user && ($user->role ?? null) !== 'Admin') {
            // assuming relation between user and client: user->id == client.user_id or email
            $query->where('client_id', $user->id);
        }

        if ($request->has('type')) $query->where('type_compte', $request->type);
        if ($request->has('statut')) $query->where('statut_compte', $request->statut);
        if ($request->has('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('numero_compte', 'like', "%{$s}%")
                    ->orWhere('titulaire_compte', 'like', "%{$s}%");
            });
        }

        if ($request->has('sort') && $request->has('order')) {
            $query->orderBy($request->sort, $request->order);
        }

        $perPage = (int) ($request->limit ?? 10);
        $comptes = $query->paginate($perPage);

        return $this->success(CompteResource::collection($comptes));
    }

    public function show(Request $request, $id)
    {
        $compte = Compte::find($id);
        if (!$compte) return $this->error('Compte introuvable', 404);
        return $this->success(new CompteResource($compte));
    }

    public function store(StoreCompteRequest $request)
    {
        $data = $request->validated();
        $compte = Compte::create($data);
        return $this->success(new CompteResource($compte), 'Compte créé', 201);
    }
}
