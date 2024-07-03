<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\Tables\Columns;
use Illuminate\Database\Eloquent\Model;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Placeholder::make('user_full_name')
                ->label('Customer Name')
                ->content(fn ($record) => $record->user->first_name . ' ' . $record->user->last_name)
                ->columnSpanFull(),

                Forms\Components\TextInput::make('business_name'),
                Forms\Components\TextInput::make('customer_type'),
                Forms\Components\TextInput::make('business_address'),
                Forms\Components\TextInput::make('business_category'),
                Forms\Components\TextInput::make('delivery_volume'), 
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.first_name')->label('Customer first name'),
                Tables\Columns\TextColumn::make('user.last_name')->label('Customer last name'),
                Tables\Columns\TextColumn::make('business_name'),
                Tables\Columns\TextColumn::make('customer_type'),

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
            'index' => Pages\ListCustomers::route('/'),
            // 'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
