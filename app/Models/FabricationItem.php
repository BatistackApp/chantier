<?php

namespace App\Models;

use App\Enums\FabricationItemType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FabricationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'fabrication_id',
        'type',
        'label',
        'quantity',
        'unit_cost',
    ];

    public function fabrication(): BelongsTo
    {
        return $this->belongsTo(Fabrication::class);
    }

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_cost' => 'decimal:2',
            'type' => FabricationItemType::class,
        ];
    }
}
