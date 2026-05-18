<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Medicamento;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MedicamentoController extends Controller
{
    public function index(): JsonResponse
    {
        $medicamentos = Medicamento::orderBy('nombre')->get();
        return response()->json($medicamentos);
    }

    public function store(Request $request): JsonResponse
    {
        // Solo admin o veterinarios pueden agregar medicamentos
        $persona = $request->user();
        if (! ($persona->esAdministrador() || $persona->esVeterinario())) {
            return response()->json(['error' => 'No tienes permiso para crear medicamentos.'], 403);
        }

        $data = $request->validate([
            'nombre'      => 'required|string|max:100|unique:medicamentos,nombre',
            'descripcion' => 'nullable|string',
        ]);

        $medicamento = Medicamento::create($data);
        return response()->json($medicamento, 201);
    }

    public function destroy(Request $request, Medicamento $medicamento): JsonResponse
    {
        if (! $request->user()->esAdministrador()) {
            return response()->json(['error' => 'Solo el administrador puede eliminar medicamentos.'], 403);
        }

        $medicamento->delete();
        return response()->json(['message' => 'Medicamento eliminado.']);
    }
}