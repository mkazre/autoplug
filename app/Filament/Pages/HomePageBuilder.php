<?php

namespace App\Filament\Pages;

use App\Support\Settings;
use BackedEnum;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class HomePageBuilder extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationLabel = 'Home page';

    protected static ?string $title = 'Home page builder';

    protected static ?int $navigationSort = 90;

    protected string $view = 'filament.pages.home-page-builder';

    public ?array $data = [];

    public function mount(): void
    {
        $blocks = json_decode(Settings::get('home_blocks', '[]'), true) ?: [];
        $this->form->fill(['blocks' => $blocks]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Builder::make('blocks')
                    ->label('Page blocks')
                    ->blocks([
                        Block::make('hero')->label('Hero')->icon('heroicon-o-bolt')->schema([
                            TextInput::make('headline')->default('Find a trusted garage near you'),
                            Textarea::make('subtext')->rows(2),
                            Toggle::make('show_search')->label('Show search form')->default(true),
                            ColorPicker::make('bg_color')->label('Background colour (used if no image)')->default('#7c3aed'),
                            FileUpload::make('bg_image')->label('Background image')->image()->disk('public')->directory('blocks'),
                            ColorPicker::make('overlay_color')->label('Overlay colour')->default('#7c3aed'),
                            TextInput::make('overlay_opacity')->label('Overlay opacity (0–100)')->numeric()->default(60),
                            TextInput::make('button1_text')->label('Button 1 text'),
                            TextInput::make('button1_url')->label('Button 1 link'),
                            TextInput::make('button2_text')->label('Button 2 text'),
                            TextInput::make('button2_url')->label('Button 2 link'),
                            TextInput::make('badge_label')->label('Trust badge')->placeholder('e.g. Trusted by 1000+ drivers'),
                            TextInput::make('badge_rating')->label('Rating')->placeholder('4.9'),
                        ]),
                        Block::make('features')->label('Feature grid')->icon('heroicon-o-squares-2x2')->schema([
                            TextInput::make('heading')->default('Why choose us'),
                            Select::make('cols_mobile')->label('Columns — mobile')->options([1 => '1', 2 => '2'])->default(1),
                            Select::make('cols_tablet')->label('Columns — tablet')->options([1 => '1', 2 => '2', 3 => '3', 4 => '4'])->default(2),
                            Select::make('cols_tablet_landscape')->label('Columns — tablet landscape')->options([1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6'])->default(3),
                            Select::make('cols_laptop')->label('Columns — laptop')->options([1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6'])->default(4),
                            Select::make('cols_desktop')->label('Columns — desktop')->options([1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6', 8 => '8'])->default(6),
                            Repeater::make('items')->schema([
                                TextInput::make('icon')->label('Icon (emoji — used if no image)')->default('⭐'),
                                FileUpload::make('image')->label('Image (optional)')->image()->disk('public')->directory('blocks'),
                                TextInput::make('image_size')->label('Image height (px)')->numeric()->default(48),
                                TextInput::make('title'),
                                Textarea::make('text')->rows(2),
                            ])->columns(2)->default([]),
                        ]),
                        Block::make('garages')->label('Garages grid')->icon('heroicon-o-building-storefront')->schema([
                            TextInput::make('heading')->default('Top-rated garages'),
                            Select::make('source')->options([
                                'top_rated' => 'Top rated',
                                'newest' => 'Newest',
                                'most_reviewed' => 'Most reviewed',
                            ])->default('top_rated'),
                            Select::make('count')->options([3 => '3', 6 => '6', 9 => '9', 12 => '12'])->default(6),
                            Toggle::make('show_view_all')->label('Show "View all" link')->default(true),
                        ]),
                        Block::make('services')->label('Popular services')->icon('heroicon-o-wrench-screwdriver')->schema([
                            TextInput::make('heading')->default('Popular services'),
                            Select::make('cols_mobile')->label('Columns — mobile')->options([1 => '1', 2 => '2', 3 => '3'])->default(2),
                            Select::make('cols_tablet')->label('Columns — tablet')->options([2 => '2', 3 => '3', 4 => '4'])->default(3),
                            Select::make('cols_tablet_landscape')->label('Columns — tablet landscape')->options([2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6'])->default(4),
                            Select::make('cols_laptop')->label('Columns — laptop')->options([2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6'])->default(6),
                            Select::make('cols_desktop')->label('Columns — desktop')->options([2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6', 8 => '8'])->default(6),
                            Repeater::make('cards')->label('Service cards (leave empty to auto-show all categories)')->schema([
                                Select::make('category')->options([
                                    'minor_service' => 'Minor Service',
                                    'major_service' => 'Major Service',
                                    'tyres' => 'Tyres',
                                    'brakes' => 'Brakes',
                                    'repair' => 'Repair',
                                    'other' => 'Other',
                                ])->required(),
                                TextInput::make('label')->placeholder('Optional display name'),
                                TextInput::make('icon')->label('Icon (emoji — used if no image)')->placeholder('🛠️'),
                                FileUpload::make('image')->label('Image (optional)')->image()->disk('public')->directory('blocks'),
                                TextInput::make('image_size')->label('Image height (px)')->numeric()->default(48),
                            ])->columns(2)->default([]),
                        ]),
                        Block::make('logos')->label('Logos strip')->icon('heroicon-o-photo')->schema([
                            TextInput::make('heading')->placeholder('e.g. Brands we service'),
                            Repeater::make('logos')->schema([
                                FileUpload::make('image')->image()->disk('public')->directory('blocks'),
                                TextInput::make('link')->url()->placeholder('https://… (optional)'),
                            ])->columns(2)->default([]),
                        ]),
                        Block::make('cta')->label('Call to action')->icon('heroicon-o-megaphone')->schema([
                            TextInput::make('heading')->default('Own a garage?'),
                            Textarea::make('subtext')->rows(2),
                            ColorPicker::make('bg_color')->default('#7c3aed'),
                            TextInput::make('button_text')->default('List your garage'),
                            TextInput::make('button_url')->default('/register'),
                        ]),
                        Block::make('richtext')->label('Rich text')->icon('heroicon-o-document-text')->schema([
                            RichEditor::make('content'),
                        ]),
                    ])
                    ->collapsible()
                    ->blockNumbers(false)
                    ->addActionLabel('Add a block'),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();
        Settings::set('home_blocks', json_encode($state['blocks'] ?? []));
        Settings::forgetCache();

        Notification::make()->title('Home page saved')->success()->send();
    }
}
