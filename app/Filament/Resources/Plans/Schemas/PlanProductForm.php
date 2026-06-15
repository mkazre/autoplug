<?php

namespace App\Filament\Resources\Plans\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PlanProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            Select::make('type')
                ->options(['service' => 'Service Plan', 'maintenance' => 'Maintenance Plan'])
                ->required()->default('service')->live(),
            Textarea::make('description')->rows(2)->columnSpanFull(),
            TextInput::make('base_price')->numeric()->prefix('R')->default(0),
            TextInput::make('min_km_excl')->numeric()->label('Min km (exclusive)')->default(0),
            TextInput::make('max_km')->numeric()->label('Max km'),
            TextInput::make('max_age_years')->numeric()->label('Max age (years)'),
            Toggle::make('requires_full_history')->label('Requires full service history'),
            TextInput::make('terms_version')->default('v1')->maxLength(50),
            FileUpload::make('terms_doc_path')->label('Terms & Conditions (PDF)')
                ->disk('public')->directory('plan-terms')->acceptedFileTypes(['application/pdf'])->downloadable(),
            Toggle::make('is_active')->default(true),
            Repeater::make('pricingTiers')->relationship()->label('Pricing tiers')->schema([
                TextInput::make('vehicle_category')->required()->placeholder('e.g. Sedan'),
                TextInput::make('term_months')->numeric()->default(12),
                TextInput::make('installment_count')->numeric()->default(1),
                TextInput::make('monthly_price')->numeric()->prefix('R')->default(0),
                TextInput::make('upfront_price')->numeric()->prefix('R')->default(0),
            ])->columns(5)->columnSpanFull()->defaultItems(0)->collapsible(),
            Repeater::make('benefitItems')->relationship()->label('Covered benefit items')->schema([
                TextInput::make('item_name')->required()->placeholder('e.g. Brake pads'),
                TextInput::make('coverage_limit')->numeric()->label('Limit'),
                Select::make('coverage_unit')->options(['per_item' => 'Per item', 'per_year' => 'Per year', 'lifetime' => 'Lifetime'])->default('per_year'),
            ])->columns(3)->columnSpanFull()->defaultItems(0)->collapsible()
                ->visible(fn ($get) => $get('type') === 'maintenance'),
            Repeater::make('discounts')->relationship()->label('Plan-holder discounts')->schema([
                Select::make('discount_type')->options(['percentage' => 'Percentage', 'fixed' => 'Fixed (R)'])->default('percentage'),
                TextInput::make('discount_value')->numeric()->default(0),
                Select::make('applies_to')->options(['quote_total' => 'Quote total', 'labour_only' => 'Labour only', 'parts_only' => 'Parts only'])->default('labour_only'),
                Toggle::make('is_active')->default(true),
            ])->columns(4)->columnSpanFull()->defaultItems(0)->collapsible(),
        ]);
    }
}
