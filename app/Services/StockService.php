<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductStock;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function getOrCreateStock(Product $product, Warehouse $warehouse): ProductStock
    {
        return ProductStock::firstOrCreate(
            [
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
            ],
            [
                'tenant_id' => auth()->user()->tenant_id,
                'quantity' => 0,
                'reserved_quantity' => 0,
            ]
        );
    }

    public function adjustStock(Product $product, Warehouse $warehouse, int $quantity, ?string $reason = null): StockMovement
    {
        return DB::transaction(function () use ($product, $warehouse, $quantity, $reason) {
            $stock = $this->getOrCreateStock($product, $warehouse);
            $quantityBefore = $stock->quantity;

            $stock->update(['quantity' => $quantity]);

            return StockMovement::create([
                'tenant_id' => auth()->user()->tenant_id,
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'user_id' => auth()->id(),
                'type' => 'adjustment',
                'quantity' => $quantity - $quantityBefore,
                'quantity_before' => $quantityBefore,
                'quantity_after' => $quantity,
                'reason' => $reason,
            ]);
        });
    }

    public function receiveStock(
        Product $product,
        Warehouse $warehouse,
        int $quantity,
        ?float $unitCost = null,
        ?string $reference = null,
        ?string $reason = null
    ): StockMovement {
        return DB::transaction(function () use ($product, $warehouse, $quantity, $unitCost, $reference, $reason) {
            $stock = $this->getOrCreateStock($product, $warehouse);
            $quantityBefore = $stock->quantity;

            $stock->addStock($quantity);

            return StockMovement::create([
                'tenant_id' => auth()->user()->tenant_id,
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'user_id' => auth()->id(),
                'type' => 'in',
                'quantity' => $quantity,
                'quantity_before' => $quantityBefore,
                'quantity_after' => $stock->fresh()->quantity,
                'unit_cost' => $unitCost,
                'reference' => $reference,
                'reason' => $reason,
            ]);
        });
    }

    public function removeStock(
        Product $product,
        Warehouse $warehouse,
        int $quantity,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $reason = null
    ): StockMovement {
        return DB::transaction(function () use ($product, $warehouse, $quantity, $referenceType, $referenceId, $reason) {
            $stock = $this->getOrCreateStock($product, $warehouse);
            $quantityBefore = $stock->quantity;

            if ($stock->available_quantity < $quantity) {
                throw new \Exception('Insufficient stock available');
            }

            $stock->removeStock($quantity);

            return StockMovement::create([
                'tenant_id' => auth()->user()->tenant_id,
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'user_id' => auth()->id(),
                'type' => 'out',
                'quantity' => -$quantity,
                'quantity_before' => $quantityBefore,
                'quantity_after' => $stock->fresh()->quantity,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'reason' => $reason,
            ]);
        });
    }

    public function transferStock(
        Product $product,
        Warehouse $fromWarehouse,
        Warehouse $toWarehouse,
        int $quantity,
        ?string $reason = null
    ): array {
        return DB::transaction(function () use ($product, $fromWarehouse, $toWarehouse, $quantity, $reason) {
            $fromStock = $this->getOrCreateStock($product, $fromWarehouse);

            if ($fromStock->available_quantity < $quantity) {
                throw new \Exception('Insufficient stock available for transfer');
            }

            $fromQuantityBefore = $fromStock->quantity;
            $fromStock->removeStock($quantity);

            $toStock = $this->getOrCreateStock($product, $toWarehouse);
            $toQuantityBefore = $toStock->quantity;
            $toStock->addStock($quantity);

            $outMovement = StockMovement::create([
                'tenant_id' => auth()->user()->tenant_id,
                'product_id' => $product->id,
                'warehouse_id' => $fromWarehouse->id,
                'user_id' => auth()->id(),
                'type' => 'transfer',
                'quantity' => -$quantity,
                'quantity_before' => $fromQuantityBefore,
                'quantity_after' => $fromStock->fresh()->quantity,
                'reason' => "Transfer to {$toWarehouse->name}" . ($reason ? ": {$reason}" : ''),
            ]);

            $inMovement = StockMovement::create([
                'tenant_id' => auth()->user()->tenant_id,
                'product_id' => $product->id,
                'warehouse_id' => $toWarehouse->id,
                'user_id' => auth()->id(),
                'type' => 'transfer',
                'quantity' => $quantity,
                'quantity_before' => $toQuantityBefore,
                'quantity_after' => $toStock->fresh()->quantity,
                'reason' => "Transfer from {$fromWarehouse->name}" . ($reason ? ": {$reason}" : ''),
            ]);

            return [$outMovement, $inMovement];
        });
    }
}
