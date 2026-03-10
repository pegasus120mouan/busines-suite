<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NumberSequence;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NumberSequenceController extends Controller
{
    public function index(): JsonResponse
    {
        $sequences = NumberSequence::orderBy('type')->get();

        return response()->json([
            'data' => $sequences,
        ]);
    }

    public function show(NumberSequence $numberSequence): JsonResponse
    {
        return response()->json([
            'data' => $numberSequence,
        ]);
    }

    public function update(Request $request, NumberSequence $numberSequence): JsonResponse
    {
        $request->validate([
            'prefix' => ['nullable', 'string', 'max:20'],
            'suffix' => ['nullable', 'string', 'max:20'],
            'next_number' => ['nullable', 'integer', 'min:1'],
            'padding' => ['nullable', 'integer', 'min:1', 'max:10'],
            'reset_yearly' => ['nullable', 'boolean'],
        ]);

        $numberSequence->update($request->only([
            'prefix', 'suffix', 'next_number', 'padding', 'reset_yearly'
        ]));

        return response()->json([
            'message' => 'Number sequence updated successfully',
            'data' => $numberSequence,
        ]);
    }
}
