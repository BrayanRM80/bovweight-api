<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\EstadoAnimal;
use Illuminate\Http\JsonResponse;

class EstadoAnimalController extends Controller
{
    public function index(): JsonResponse
    {
        $estados = EstadoAnimal::orderBy('nombre_estado')->get();
        return response()->json($estados);
    }
}