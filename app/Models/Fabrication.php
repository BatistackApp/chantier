<?php

namespace App\Models;

use App\Enums\FabricationType;
use App\Observers\FabricationObserver;
use App\Observers\FinancialObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy([FabricationObserver::class])]
#[ObservedBy([FinancialObserver::class])]
class Fabrication extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'type',
        'label',
        'dimensions',
        'quantity',
        'color_code',
        'time_realized',
        'unit_cost',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(FabricationItem::class);
    }

    protected function casts(): array
    {
        return [
            'type' => FabricationType::class,
            'quantity' => 'decimal:2',
            'unit_cost' => 'decimal:2',
        ];
    }
}
