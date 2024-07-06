<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Models\EventPackage;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\EventPackageResource\Pages;
use App\Filament\Resources\EventPackageResource\RelationManagers;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Support\RawJs;

class EventPackageResource extends Resource
{
    protected static ?string $model = EventPackage::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Event';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('event_id')
                    ->relationship('event', 'title')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        if ($state === null) {
                            $set('start_valid', null);
                            $set('end_valid', null);
                        } else {
                            $set('start_event', \App\Models\Event::find($state)->start_event);
                            $set('start_valid', \App\Models\Event::find($state)->start_event);
                            $set('end_event', \App\Models\Event::find($state)->end_event);
                            $set('end_valid', \App\Models\Event::find($state)->end_event);
                        }
                    }),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(50)
                    ->live(debounce: 500)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                Forms\Components\DateTimePicker::make('start_valid')
                    ->required()
                    ->disabled(fn (Get $get): bool => !$get('event_id'))
                    ->native(false)
                    ->minDate(function (Get $get): ?string {
                        if ($get('start_event')) {
                            return $get('start_event');
                        }
                        if ($get('event_id')) {
                            $event = \App\Models\Event::find($get('event_id'));
                            return $event->start_event;
                        }
                        return null;
                    })
                    ->maxDate(function (Get $get): ?string {
                        if ($get('end_event')) {
                            return $get('end_event');
                        }
                        if ($get('event_id')) {
                            $event = \App\Models\Event::find($get('event_id'));
                            return $event->end_event;
                        }
                        return null;
                    })
                    ->beforeOrEqual('end_valid')
                    ->live(),
                Forms\Components\DateTimePicker::make('end_valid')
                    ->required()
                    ->disabled(fn (Get $get): bool => !$get('event_id'))
                    ->native(false)
                    ->minDate(fn (Get $get): ?string => $get('start_valid') ?? $get('start_event'))
                    ->maxDate(function (Get $get): ?string {
                        if ($get('end_event')) {
                            return $get('end_event');
                        }
                        if ($get('event_id')) {
                            $event = \App\Models\Event::find($get('event_id'));
                            return $event->end_event;
                        }
                        return null;
                    })
                    ->afterOrEqual('start_valid'),
                Forms\Components\TextInput::make('description')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\TextInput::make('capacity')
                    ->required()
                    ->integer()
                    ->mask('9999999999')
                    ->minValue(1)
                    ->live(debounce: 500)
                    ->disabled(fn (?EventPackage $record): bool => $record ? $record->capacity !== $record->remaining : false)
                    ->afterStateUpdated(function (Set $set, ?string $state, ?EventPackage $record) {
                        if ($record && $record->capacity !== $record->remaining) {
                            return;
                        }
                        $set('remaining', $state);
                    }),
                Forms\Components\TextInput::make('remaining')
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\TextInput::make('slug')
                    ->disabled()
                    ->dehydrated(),
            ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['slug'] = Str::slug($data['title']);
        $data['remaining'] = $data['capacity'];

        return $data;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('event.title')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->description(fn (EventPackage $record): string => $record->description)
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->sortable()
                    ->money('IDR', locale: 'id'),
                Tables\Columns\TextColumn::make('capacity')
                    ->sortable(),
                Tables\Columns\TextColumn::make('remaining')
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_valid')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_valid')
                    ->dateTime()
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
                Tables\Filters\SelectFilter::make('event_id')
                    ->label('Event')
                    ->relationship('event', 'title'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('event')
                        ->label('Event')
                        ->color('info')
                        ->icon('heroicon-c-link')
                        ->url(fn (EventPackage $record): string => url('/admin/events?tableSearch=' . $record->event->title)),
                    Tables\Actions\EditAction::make()
                        ->color('warning')
                        ->beforeFormFilled(function ($action, $record) {
                            if ($record->event->status == 'published') {
                                Notification::make()
                                    ->warning()
                                    ->title('Be Careful')
                                    ->body('You are editing package of an event that is published')
                                    ->send();
                            }
                        }),
                    Tables\Actions\DeleteAction::make()
                        ->color('danger'),
                ])
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('created_at', 'desc');;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageEventPackages::route('/'),
        ];
    }
}