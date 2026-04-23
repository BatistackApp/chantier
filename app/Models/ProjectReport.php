<?php

namespace App\Models;

use App\Enums\ProjectReportType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'type',
        'supports_conformity',
        'support_deviations',
        'access_ok',
        'electricity_ok',
        'is_completed',
        'cleaning_done',
        'reserves',
        'signed_at',
        'signatory_name',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    protected function casts(): array
    {
        return [
            'supports_conformity' => 'boolean',
            'access_ok' => 'boolean',
            'electricity_ok' => 'boolean',
            'is_completed' => 'boolean',
            'cleaning_done' => 'boolean',
            'reserves' => 'array',
            'signed_at' => 'datetime',
            'type' => ProjectReportType::class,
        ];
    }
}
