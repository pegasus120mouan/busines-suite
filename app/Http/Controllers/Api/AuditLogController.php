<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuditLogResource;
use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AuditLogController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = AuditLog::query()
            ->with(['user:id,name,email'])
            ->when($request->user_id, fn ($q, $id) => $q->where('user_id', $id))
            ->when($request->event, fn ($q, $event) => $q->where('event', $event))
            ->when($request->auditable_type, fn ($q, $type) => $q->where('auditable_type', $type))
            ->when($request->from_date, fn ($q, $date) => $q->where('created_at', '>=', $date))
            ->when($request->to_date, fn ($q, $date) => $q->where('created_at', '<=', $date))
            ->orderBy('created_at', 'desc');

        return AuditLogResource::collection($query->paginate($request->per_page ?? 50));
    }

    public function show(AuditLog $auditLog): AuditLogResource
    {
        return new AuditLogResource($auditLog->load('user'));
    }

    public function statistics(Request $request): JsonResponse
    {
        $fromDate = $request->from_date ?? now()->subDays(30)->format('Y-m-d');
        $toDate = $request->to_date ?? now()->format('Y-m-d');

        $byEvent = AuditLog::whereBetween('created_at', [$fromDate, $toDate])
            ->selectRaw('event, COUNT(*) as count')
            ->groupBy('event')
            ->get();

        $byUser = AuditLog::whereBetween('created_at', [$fromDate, $toDate])
            ->whereNotNull('user_id')
            ->join('users', 'audit_logs.user_id', '=', 'users.id')
            ->selectRaw('users.name, COUNT(*) as count')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        $byModel = AuditLog::whereBetween('created_at', [$fromDate, $toDate])
            ->selectRaw('auditable_type, COUNT(*) as count')
            ->groupBy('auditable_type')
            ->get()
            ->map(function ($item) {
                $item->model = class_basename($item->auditable_type);
                return $item;
            });

        $daily = AuditLog::whereBetween('created_at', [$fromDate, $toDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'data' => [
                'by_event' => $byEvent,
                'by_user' => $byUser,
                'by_model' => $byModel,
                'daily' => $daily,
                'total' => AuditLog::whereBetween('created_at', [$fromDate, $toDate])->count(),
            ],
        ]);
    }
}
