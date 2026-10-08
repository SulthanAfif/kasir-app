<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'code', 'name', 'contact_person', 'phone', 'email', 'address', 'notes', 'is_active'
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public static function generateCode(): string
    {
        return 'SUP-' . str_pad((string) (static::count() + 1), 4, '0', STR_PAD_LEFT);
    }
}
