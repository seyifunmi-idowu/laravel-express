<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RiderResource\Pages;
use App\Filament\Resources\RiderResource\RelationManagers;
use App\Models\Rider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\ImageColumn;

class RiderResource extends Resource
{
    protected static ?string $model = Rider::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Placeholder::make('user_full_name')
                    ->label('Rider Name')
                    ->content(fn ($record) => $record->user->first_name . ' ' . $record->user->last_name)
                    ->columnSpanFull(),
                Forms\Components\Placeholder::make('vehicle_name')
                    ->label('Vehicle')
                    ->content(fn ($record) => $record->vehicle->name),
    
                Forms\Components\TextInput::make('status'),
                Forms\Components\TextInput::make('vehicle_make'),
                Forms\Components\TextInput::make('vehicle_model'),
                Forms\Components\TextInput::make('vehicle_plate_number'),
                Forms\Components\TextInput::make('vehicle_color'),
                Forms\Components\TextInput::make('city'),
                Forms\Components\TextInput::make('avatar_url'),
                Forms\Components\Textarea::make('rider_info'),
                Forms\Components\Toggle::make('on_duty'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.first_name')->label('Rider first name'),
                Tables\Columns\TextColumn::make('vehicle.name')->label('Vehicle name'),
                Tables\Columns\TextColumn::make('vehicle_type'),
                Tables\Columns\ToggleColumn::make('on_duty'),
                ImageColumn::make('avatar_url')->circular()

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListRiders::route('/'),
            'create' => Pages\CreateRider::route('/create'),
            'edit' => Pages\EditRider::route('/{record}/edit'),
        ];
    }
}
