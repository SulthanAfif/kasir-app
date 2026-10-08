<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    protected $table = 'app_notifications';

    protected $fillable = [
        'user_id', 'type', 'title', 'message', 'link', 'is_read'
    ];

    protected $casts = ['is_read' => 'boolean'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function notify(?int $userId, string $type, string $title, string $message, ?string $link = null): self
    {
        return static::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
        ]);
    }

    public static function checkLowStock(): int
    {
        $count = 0;
        $products = Product::where('is_active', true)->whereColumn('stock', '<=', 'min_stock')->get();
        foreach ($products as $p) {
            // Hindari spam: cek notifikasi serupa hari ini
            $exists = static::where('type', 'low_stock')
                ->where('message', 'like', "%{$p->code}%")
                ->whereDate('created_at', today())
                ->exists();
            if (!$exists) {
                static::notify(
                    null,
                    'low_stock',
                    'Stok Menipis',
                    "Produk {$p->name} ({$p->code}) sisa {$p->stock} {$p->unit}",
                    '/products?low_stock=1'
                );
                $count++;
            }
        }
        return $count;
    }
}
