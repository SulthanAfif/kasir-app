<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'phone', 'email', 'address', 'notes', 'is_active'
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public static function generateCode(): string
    {
        $last = static::count() + 1;
        return 'CST-' . str_pad($last, 5, '0', STR_PAD_LEFT);
    }
}
