<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LoadModelById
{
    public function handle(Request $request, Closure $next, $modelClass = null, $param = 'id')
    {
        // $modelClass should be a full class name like App\\Models\\Compte
        if ($request->route($param) || $request->has($param)) {
            $id = $request->route($param) ?? $request->get($param);
            if (!$modelClass) {
                return $next($request);
            }
            if (!class_exists($modelClass)) {
                return $next($request);
            }
            $model = $modelClass::find($id);
            if (!$model) {
                return response()->json(['success' => false, 'message' => 'Ressource introuvable'], 404);
            }
            // inject the model instance into the request
            $request->attributes->set(Str::snake(class_basename($modelClass)), $model);
        }

        return $next($request);
    }
}
