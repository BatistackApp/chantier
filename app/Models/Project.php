<?php

namespace App\Models;

use App\Enums\ProjectStatus;
use App\Observers\FinancialObserver;
use App\Observers\ProjectObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[ObservedBy([ProjectObserver::class])]
#[ObservedBy([FinancialObserver::class])]
class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'title',
        'reference',
        'address',
        'geo_lat',
        'geo_long',
        'status',
        'quoted_amount',
        'estimated_cost',
        'planned_start_date',
        'planned_end_date',
        'started_at',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function preparation(): HasOne
    {
        return $this->hasOne(ProjectPreparation::class);
    }

    public function costs(): HasMany
    {
        return $this->hasMany(ProjectCost::class);
    }

    public function fabrications(): HasMany
    {
        return $this->hasMany(Fabrication::class);
    }

    public function quincaillerie(): HasMany
    {
        return $this->hasMany(FabricationItem::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(ProjectReport::class);
    }

    protected function casts(): array
    {
        return [
            'planned_start_date' => 'date',
            'planned_end_date' => 'date',
            'status' => ProjectStatus::class,
            'quoted_amount' => 'decimal:2',
            'estimated_cost' => 'decimal:2',
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    /**
     * Calcul du Déboursé Réel
     * Somme des coûts directs + Fabrication (Atelier) + Quincaillerie
     */
    public function getActualDebourseAttribute()
    {
        $directCosts = $this->costs()->sum('amount');
        $fabCosts = $this->fabrications()->get()->sum(fn ($f) => $f->quantity * $f->unit_cost_cents);
        $itemCosts = $this->quincaillerie()->sum('unit_cost');

        return $directCosts + $fabCosts + $itemCosts;
    }
}
