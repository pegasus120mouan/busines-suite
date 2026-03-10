<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CustomerController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Customer::query()
            ->with(['invoices', 'quotes'])
            ->when($request->search, fn ($q, $search) => $q->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            }))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->type, fn ($q, $type) => $q->where('type', $type))
            ->orderBy($request->sort_by ?? 'created_at', $request->sort_order ?? 'desc');

        return CustomerResource::collection($query->paginate($request->per_page ?? 15));
    }

    public function store(CustomerRequest $request): JsonResponse
    {
        $customer = Customer::create($request->validated());

        return response()->json([
            'message' => 'Customer created successfully',
            'data' => new CustomerResource($customer),
        ], 201);
    }

    public function show(Customer $customer): CustomerResource
    {
        return new CustomerResource($customer->load(['invoices', 'quotes']));
    }

    public function update(CustomerRequest $request, Customer $customer): JsonResponse
    {
        $customer->update($request->validated());

        return response()->json([
            'message' => 'Customer updated successfully',
            'data' => new CustomerResource($customer),
        ]);
    }

    public function destroy(Customer $customer): JsonResponse
    {
        $customer->delete();

        return response()->json([
            'message' => 'Customer deleted successfully',
        ]);
    }

    public function restore(int $id): JsonResponse
    {
        $customer = Customer::withTrashed()->findOrFail($id);
        $customer->restore();

        return response()->json([
            'message' => 'Customer restored successfully',
            'data' => new CustomerResource($customer),
        ]);
    }
}
