<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TaxRate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaxRateController extends Controller
{
    public function index(): JsonResponse
    {
        $taxRates = TaxRate::orderBy('rate')->get();

        return response()->json([
            'data' => $taxRates,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'code' => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_default' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->is_default) {
            TaxRate::where('is_default', true)->update(['is_default' => false]);
        }

        $taxRate = TaxRate::create($request->all());

        return response()->json([
            'message' => 'Tax rate created successfully',
            'data' => $taxRate,
        ], 201);
    }

    public function show(TaxRate $taxRate): JsonResponse
    {
        return response()->json([
            'data' => $taxRate,
        ]);
    }

    public function update(Request $request, TaxRate $taxRate): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'code' => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_default' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->is_default && !$taxRate->is_default) {
            TaxRate::where('is_default', true)->update(['is_default' => false]);
        }

        $taxRate->update($request->all());

        return response()->json([
            'message' => 'Tax rate updated successfully',
            'data' => $taxRate,
        ]);
    }

    public function destroy(TaxRate $taxRate): JsonResponse
    {
        $taxRate->delete();

        return response()->json([
            'message' => 'Tax rate deleted successfully',
        ]);
    }
}
