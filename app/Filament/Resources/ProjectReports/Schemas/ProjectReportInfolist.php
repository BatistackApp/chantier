<?php

namespace App\Filament\Resources\ProjectReports\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProjectReportInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('project.title')
                    ->label('Project'),
                TextEntry::make('type')
                    ->badge(),
                IconEntry::make('supports_conformity')
                    ->boolean(),
                TextEntry::make('support_deviations')
                    ->placeholder('-')
                    ->columnSpanFull(),
                IconEntry::make('access_ok')
                    ->boolean(),
                IconEntry::make('electricity_ok')
                    ->boolean(),
                IconEntry::make('is_completed')
                    ->boolean(),
                IconEntry::make('cleaning_done')
                    ->boolean(),
                TextEntry::make('signed_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('signatory_name')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
