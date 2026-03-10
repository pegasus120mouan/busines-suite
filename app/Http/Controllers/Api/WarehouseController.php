<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WarehouseRequest;
use App\Http\Resources\WarehouseResource;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class WarehouseController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Warehouse::query()
            ->with('manager')
            ->withCount('stocks')
            ->when($request->search, fn ($q, $search) => $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            }))
            ->when($request->is_active !== null, fn ($q) => $q->where('is_active', $request->boolean('is_active')))
            ->orderBy($request->sort_by ?? 'name', $request->sort_order ?? 'asc');

        return WarehouseResource::collection($query->paginate($request->per_page ?? 15));
    }

    public function store(WarehouseRequest $request): JsonResponse
    {
        $warehouse = Warehouse::create($request->validated());

        return response()->json([
            'message' => 'Warehouse created successfully',
            'data' => new WarehouseResource($warehouse),
        ], 201);
    }

    public function show(Warehouse $warehouse): WarehouseResource
    {
        return new WarehouseResource($warehouse->load(['manager', 'stocks.product']));
    }

    public function update(WarehouseRequest $request, Warehouse $warehouse): JsonResponse
    {
        $warehouse->update($request->validated());

        return response()->json([
            'message' => 'Warehouse updated successfully',
            'data' => new WarehouseResource($warehouse),
        ]);
    }

    public function destroy(Warehouse $warehouse): JsonResponse
    {
        if ($warehouse->stocks()->exists()) {
            return response()->json([
                'message' => 'Cannot delete warehouse with existing stock',
            ], 422);
        }

        $warehouse->delete();

        return response()->json([
            'message' => 'Warehouse deleted successfully',
        ]);
    }
}
