<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\EventResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\EventResource\RelationManagers;
use App\Models\Event;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Event';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        $provinces = json_decode(file_get_contents('https://emsifa.github.io/api-wilayah-indonesia/api/provinces.json'), true);

        $provinceOptions = collect($provinces)->mapWithKeys(function ($province) {
            return [$province['name'] => $province['name']];
        });

        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->autocomplete(false)
                    ->maxLength(100)
                    ->live(debounce: 500)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                Forms\Components\Select::make('location')
                    ->required()
                    ->options($provinceOptions),
                Forms\Components\Select::make('event_category_id')
                    ->relationship('eventCategory', 'title')
                    ->required(),
                Forms\Components\FileUpload::make('image')
                    ->required()
                    ->image()
                    ->openable()
                    ->maxSize(1024)
                    ->directory('events')
                    ->imageCropAspectRatio('16:9'),
                Forms\Components\DateTimePicker::make('start_event')
                    ->required()
                    ->native(false)
                    ->seconds(false)
                    ->minDate(now())
                    ->beforeOrEqual('end_event')
                    ->live()
                    ->closeOnDateSelection(),
                Forms\Components\DateTimePicker::make('end_event')
                    ->required()
                    ->native(false)
                    ->seconds(false)
                    ->minDate(fn (Get $get) => $get('start_event'))
                    ->afterOrEqual('start_event')
                    ->closeOnDateSelection(),
                Forms\Components\RichEditor::make('description')
                    ->required(),
                Forms\Components\RichEditor::make('highlight')
                    ->required(),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->disabled()
                    ->default('unpublished')
                    ->dehydrated(),
            ])->model(Event::class);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->size(64),
                Tables\Columns\TextColumn::make('title')
                    ->description(fn (Event $record) => Str::limit(strip_tags($record->description), 50))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('location')
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_event')
                    ->dateTime('F, j Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_event')
                    ->dateTime('F, j Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'unpublished' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('packages_sum_capacity')
                    ->label('Capacity')
                    ->sum('packages', 'capacity')
                    ->sortable(),
                Tables\Columns\TextColumn::make('packages_sum_remaining')
                    ->label('Remaining')
                    ->sum('packages', 'remaining')
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('Publish/Unpublish')
                        ->label(fn (Event $record) => $record->status === 'published' ? 'Unpublish' : 'Publish')
                        ->action(function (Event $record) {
                            $record->status = $record->status === 'published' ? 'unpublished' : 'published';
                            $record->save();
                        })
                        ->icon('heroicon-c-globe-alt')
                        ->color(fn (Event $record) => $record->status === 'published' ? 'gray' : 'success'),
                    Tables\Actions\Action::make('Event Packages')
                        ->label('Event Packages')
                        ->color('info')
                        ->icon('heroicon-c-link')
                        ->url(fn (Event $record): string => url('admin/event-packages?tableFilters[event_id][value]=' . $record->id)),
                    Tables\Actions\EditAction::make()
                        ->color('warning'),
                    Tables\Actions\DeleteAction::make()
                        ->color('danger')
                        ->before(function ($action, $record) {
                            if ($record->status === 'published') {
                                Notification::make()
                                    ->danger()
                                    ->title('Something went wrong')
                                    ->body('The event cannot be deleted because it is published.')
                                    ->send();
                                $action->halt();
                            }
                            if ($record->packages()->exists()) {
                                Notification::make()
                                    ->danger()
                                    ->title('Something went wrong')
                                    ->body('The event cannot be deleted because it has related packages.')
                                    ->send();
                                $action->halt();
                            }
                        })
                ]),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title'];
    }
}