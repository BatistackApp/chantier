<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'siret',
        'address',
        'postal_code',
        'city',
        'country',
        'is_professional',
    ];

    protected function casts(): array
    {
        return [
            'is_professional' => 'boolean',
        ];
    }
}
