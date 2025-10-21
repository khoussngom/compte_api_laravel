<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Client;
use App\Models\Compte;
use Illuminate\Support\Str;
use App\Traits\RestResponse;
use Illuminate\Http\Request;
use App\Mail\NewClientCredentials;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Notifications\SmsClientCode;
use Illuminate\Support\Facades\Mail;
use App\Http\Resources\CompteResource;
use App\Http\Requests\StoreCompteRequest;
use App\Http\Requests\CreateCompteRequest;

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

    public function show(Request $request, Compte $compte)
    {
        // access control enforced by middleware EnsureCompteAccess
        return $this->success(new CompteResource($compte));
    }

    /**
     * Update client information for a specific compte (Admin only)
     */
    public function update(Request $request, Compte $compte)
    {
        // ensure user is admin
        $user = $request->user();
        if (!$user || ($user->role ?? null) !== 'Admin') {
            return response()->json(['success' => false, 'error' => ['code' => 'FORBIDDEN', 'message' => 'Accès refusé']], 403);
        }

        $req = app(\App\Http\Requests\UpdateClientRequest::class);
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $req->rules());
        $req->withValidator($validator);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => ['code' => 'VALIDATION_ERROR', 'message' => 'Les données fournies sont invalides', 'details' => $validator->errors()->messages()]], 400);
        }

        $data = $request->all();

        $client = $compte->client;
        if (!$client) {
            return response()->json(['success' => false, 'error' => ['code' => 'COMPTE_NOT_FOUND', 'message' => 'Client du compte introuvable']], 404);
        }

        // Update titulaire
        if (isset($data['titulaire'])) {
            $client->titulaire = $data['titulaire'];
        }

        if (!empty($data['informationsClient']) && is_array($data['informationsClient'])) {
            $info = $data['informationsClient'];
            if (isset($info['telephone'])) $client->telephone = $info['telephone'];
            if (isset($info['email'])) $client->email = $info['email'];
            if (isset($info['password'])) $client->mot_de_passe = bcrypt($info['password']);
            if (isset($info['nci'])) $client->nci = $info['nci'];
        }

        $client->version = ($client->version ?? 1) + 1;
        $client->save();

        // return updated compte resource
        return $this->success(new CompteResource($compte), 'Compte mis à jour avec succès', 201);
    }

    public function store(CreateCompteRequest $request)
    {
        $payload = $request->validated();

        $clientData = $payload['client'];

        $client = null;
        if (!empty($clientData['id'])) {
            $client = Client::find($clientData['id']);
        }

        if (!$client) {
            $client = Client::where('email', $clientData['email'])
                ->orWhere('telephone', $clientData['telephone'])
                ->first();
        }

        $generatedPassword = null;
        $generatedCode = null;
        if (!$client) {
            $generatedPassword = Str::random(10);
            $generatedCode = random_int(100000, 999999);

            $client = Client::create([
                'nom' => $clientData['titulaire'],
                'prenom' => null,
                'email' => $clientData['email'],
                'mot_de_passe' => bcrypt($generatedPassword),
                'telephone' => $clientData['telephone'],
                'adresse' => $clientData['adresse'],
                'nci' => $clientData['nci'] ?? null,
                'security_code' => (string) $generatedCode,
                'require_code_on_login' => true,
            ]);

            try {
                Mail::to($client->email)->send(new NewClientCredentials($client, $generatedPassword));
            } catch (\Exception $e) {
                Log::warning('Failed to send new client email: '.$e->getMessage());
            }

            try {
                $client->notify(new SmsClientCode($generatedCode));
            } catch (\Exception $e) {
                Log::warning('Failed to send SMS code: '.$e->getMessage());
            }
        }

        $compte = Compte::create([
            'client_id' => $client->id,
            'type_compte' => ucfirst(strtolower($payload['type'])),
            'solde' => $payload['soldeInitial'],
            'devise' => $payload['devise'],
            'date_creation' => now(),
            'statut_compte' => 'Actif',
            'titulaire_compte' => $clientData['titulaire'],
        ]);

        $data = [
            'id' => $compte->id,
            'numeroCompte' => $compte->numero_compte ?? null,
            'titulaire' => $compte->titulaire_compte,
            'type' => strtolower($compte->type_compte),
            'solde' => (float) $compte->solde,
            'devise' => $compte->devise,
            'dateCreation' => optional($compte->date_creation)->toIso8601String(),
            'statut' => $compte->statut_compte,
            'metadata' => [
                'derniereModification' => optional($compte->updated_at)->toIso8601String(),
                'version' => 1,
            ],
        ];

        return $this->success($data, 'Compte créé avec succès', 201);
    }
}
