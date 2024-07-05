<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DateTimePicker;
use App\Filament\Resources\EventResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\EventResource\RelationManagers;
use App\Models\Event;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        $provinces = json_decode(file_get_contents('https://emsifa.github.io/api-wilayah-indonesia/api/provinces.json'), true);

        $provinceOptions = collect($provinces)->mapWithKeys(function ($province) {
            return [$province['name'] => $province['name']];
        });

        return $form
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->autocomplete(false)
                    ->maxLength(100)
                    ->live(debounce: 500)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                Select::make('location')
                    ->options($provinceOptions),
                Select::make('event_category_id')
                    ->relationship('category', 'title')
                    ->required(),
                FileUpload::make('image')
                    ->required()
                    ->image()
                    ->openable()
                    ->maxSize(1024)
                    ->directory('events')
                    ->imageCropAspectRatio('16:9'),
                DateTimePicker::make('start_event')
                    ->required()
                    ->native(false)
                    ->seconds(false),
                DateTimePicker::make('end_event')
                    ->required()
                    ->native(false)
                    ->seconds(false),
                RichEditor::make('description')
                    ->required(),
                RichEditor::make('highlight')
                    ->required(),
                Hidden::make('slug'),
            ])->model(Event::class);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->size(64),
                TextColumn::make('title')
                    ->description(fn (Event $record) => Str::limit(strip_tags($record->description), 50))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('location')
                    ->sortable(),
                TextColumn::make('start_event')
                    ->dateTime('F, j Y')
                    ->sortable(),
                TextColumn::make('end_event')
                    ->dateTime('F, j Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'unpublished' => 'danger',
                    })
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
                        ->color(fn (Event $record) => $record->status === 'published' ? 'info' : 'success'),
                    Tables\Actions\EditAction::make()
                        ->color('warning'),
                    Tables\Actions\DeleteAction::make()
                        ->color('danger'),
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
}