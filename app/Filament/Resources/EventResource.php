<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Event;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\EventResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\EventResource\RelationManagers;
use Filament\Forms\Components\Select;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Event';

    protected static ?int $navigationSort = 1;


    public static function form(Form $form): Form
    {
        $provinces = json_decode(file_get_contents('https://emsifa.github.io/api-wilayah-indonesia/api/provinces.json'), true);

        $provinceOptions = collect($provinces)->mapWithKeys(function ($province) {
            return [$province['name'] => $province['name']];
        });

        return $form
            ->schema([
                Forms\Components\Select::make('event_category_id')
                    ->relationship('eventCategory', 'title')
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->live(debounce: 500)
                    ->afterStateUpdated(fn (Set $set, $state) => $set('slug', Str::slug($state))),
                Forms\Components\DateTimePicker::make('start_event')
                    ->required()
                    ->minDate(now())
                    ->live(debounce: 500),
                Forms\Components\DateTimePicker::make('end_event')
                    ->required()
                    ->minDate(fn (Get $get) => $get('start_event')),
                Forms\Components\Select::make('location')
                    ->required()
                    ->options($provinceOptions),
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->required()
                    ->imageCropAspectRatio('16:9')
                    ->maxSize(1024),
                Forms\Components\RichEditor::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\RichEditor::make('highlight')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->required()
                    ->options([
                        'unpublished' => 'Unpublished',
                        'published' => 'Published',
                    ])
                    ->default('unpublished')
                    ->disableOptionWhen(function (string $value, ?Event $record): bool {
                        if ($record) {
                            return false;
                        }
                        return $value === 'published';
                    })
                    ->selectablePlaceholder(false),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->readOnly(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('eventCategory.title')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->description(fn (Event $record): string => Str::limit($record->description, 20))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_event')
                    ->dateTime('M j, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_event')
                    ->dateTime('M j, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('location'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'unpublished' => 'danger',
                        'published' => 'success',
                    }),
                Tables\Columns\TextColumn::make('slug')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\Filter::make('event_date')
                    ->form([
                        Forms\Components\DatePicker::make('start_event'),
                        Forms\Components\DatePicker::make('end_event'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_event'],
                                fn (Builder $query, $date): Builder => $query->whereDate('start_event', '>=', $date),
                            )
                            ->when(
                                $data['end_event'],
                                fn (Builder $query, $date): Builder => $query->whereDate('end_event', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['start_event']) {
                            return null;
                        }

                        return \Carbon\Carbon::parse($data['start_event'])->toFormattedDateString() . ' to ' . \Carbon\Carbon::parse($data['end_event'])->toFormattedDateString();
                    }),
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('eventCategory', 'title'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'unpublished' => 'Unpublished',
                        'published' => 'Published',
                    ]),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageEvents::route('/'),
        ];
    }

    protected static ?string $recordTitleAttribute = 'title';

    public static function getGloballySearchableAttributes(): array
    {
        return ['title'];
    }
}