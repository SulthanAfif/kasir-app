<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = ['code', 'name', 'address', 'phone', 'is_active', 'is_main'];

    protected $casts = ['is_active' => 'boolean', 'is_main' => 'boolean'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public static function generateCode(): string
    {
        return 'BR-' . str_pad((string) (static::count() + 1), 3, '0', STR_PAD_LEFT);
    }
}
