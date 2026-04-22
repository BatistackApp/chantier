<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectPreparation extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'subcontractor_form_ok',
        'subcontractor_contract_ok',
        'logistics_status',
        'lifting_means',
        'lifting_count',
        'lifting_provider',
        'safety_nets_required',
        'safety_nets_provider',
        'observations',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    protected function casts(): array
    {
        return [
            'subcontractor_form_ok' => 'boolean',
            'subcontractor_contract_ok' => 'boolean',
            'logistics_status' => 'array',
            'safety_nets_required' => 'boolean',
        ];
    }
}
