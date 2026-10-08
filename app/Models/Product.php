<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code', 'name', 'category_id', 'price', 'cost',
        'stock', 'min_stock', 'unit', 'description', 'image', 'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function isLowStock(): bool
    {
        return $this->stock <= $this->min_stock;
    }

    /**
     * Adjust stock and record movement.
     */
    public function adjustStock(int $quantity, string $type, int $userId, ?string $reference = null, ?string $notes = null): void
    {
        $before = $this->stock;
        $this->stock = max(0, $this->stock + $quantity);
        $this->save();

        StockMovement::create([
            'product_id' => $this->id,
            'user_id' => $userId,
            'type' => $type,
            'quantity' => $quantity,
            'stock_before' => $before,
            'stock_after' => $this->stock,
            'reference' => $reference,
            'notes' => $notes,
        ]);
    }
}
