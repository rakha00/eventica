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
use App\Models\Event;
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
                    ->live(debounce: 500)
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        if ($state) {
                            $set('start_valid', Event::find($state)->start_event);
                            $set('end_valid', Event::find($state)->end_event);
                        } else {
                            $set('start_valid', null);
                            $set('end_valid', null);
                        }
                    }),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(50)
                    ->autocomplete(false)
                    ->live(debounce: 500)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                Forms\Components\DateTimePicker::make('start_valid')
                    ->required()
                    ->minDate(fn (Get $get): ?string => $get('event_id') ? Event::find($get('event_id'))->start_event : null)
                    ->maxDate(fn (Get $get): ?string => $get('event_id') ? Event::find($get('event_id'))->end_event : null)
                    ->beforeOrEqual('end_valid')
                    ->live(debounce: 500),
                Forms\Components\DateTimePicker::make('end_valid')
                    ->required()
                    ->minDate(fn (Get $get): ?string => $get('event_id') ? Event::find($get('event_id'))->start_event : null)
                    ->maxDate(fn (Get $get): ?string => $get('event_id') ? Event::find($get('event_id'))->end_event : null)
                    ->afterOrEqual('start_valid'),
                Forms\Components\TextInput::make('description')
                    ->required()
                    ->autocomplete(false)
                    ->maxLength(100),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->prefix('Rp'),
                Forms\Components\TextInput::make('capacity')
                    ->required()
                    ->numeric()
                    ->mask('9999999999')
                    ->minValue(1)
                    ->live(debounce: 500)
                    ->readOnly(fn (?EventPackage $record): bool => $record ? true : false)
                    ->afterStateUpdated(function (Set $set, ?string $state, ?EventPackage $record) {
                        if (!$record) {
                            $set('remaining', $state);
                        }
                    }),
                Forms\Components\TextInput::make('remaining')
                    ->readOnly(),
                Forms\Components\TextInput::make('slug')
                    ->readOnly(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('event.title')
                    ->sortable(),
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
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('end_valid')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                        ->beforeFormFilled(function ($record) {
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