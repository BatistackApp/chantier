<?php

namespace App\Models;

use App\Enums\CostType;
use App\Observers\FinancialObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([FinancialObserver::class])]
class ProjectCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'cost_type',
        'label',
        'amount',
        'spent_at',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    protected function casts(): array
    {
        return [
            'spent_at' => 'date',
            'cost_type' => CostType::class,
            'amount' => 'decimal:2',
        ];
    }
}
