<?php

namespace App\Filament\Resources\PlanAuditLogs\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PlanAuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->dateTime('d M Y H:i')->sortable(),
                TextColumn::make('actor.name')->label('By')->placeholder('system'),
                TextColumn::make('action')->badge()->formatStateUsing(fn ($s) => str_replace('_', ' ', (string) $s)),
                TextColumn::make('subject_type')->label('Subject')->formatStateUsing(fn ($s) => class_basename((string) $s)),
                TextColumn::make('subject_id')->label('ID'),
            ])
            ->filters([]);
    }
}
