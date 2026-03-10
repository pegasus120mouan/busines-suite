<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SupplierController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Supplier::query()
            ->withCount(['products', 'purchaseOrders'])
            ->when($request->search, fn ($q, $search) => $q->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                    ->orWhere('contact_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            }))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->orderBy($request->sort_by ?? 'created_at', $request->sort_order ?? 'desc');

        return SupplierResource::collection($query->paginate($request->per_page ?? 15));
    }

    public function store(SupplierRequest $request): JsonResponse
    {
        $supplier = Supplier::create($request->validated());

        return response()->json([
            'message' => 'Supplier created successfully',
            'data' => new SupplierResource($supplier),
        ], 201);
    }

    public function show(Supplier $supplier): SupplierResource
    {
        return new SupplierResource($supplier->loadCount(['products', 'purchaseOrders']));
    }

    public function update(SupplierRequest $request, Supplier $supplier): JsonResponse
    {
        $supplier->update($request->validated());

        return response()->json([
            'message' => 'Supplier updated successfully',
            'data' => new SupplierResource($supplier),
        ]);
    }

    public function destroy(Supplier $supplier): JsonResponse
    {
        $supplier->delete();

        return response()->json([
            'message' => 'Supplier deleted successfully',
        ]);
    }
}
