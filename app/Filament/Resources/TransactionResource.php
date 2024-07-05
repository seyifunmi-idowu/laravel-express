<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Placeholder::make('user_full_name')
                ->label('User')
                ->content(fn ($record) => $record->user->display_name),

                Forms\Components\TextInput::make('currency')->disabled(),
                Forms\Components\TextInput::make('amount')->disabled(),
                Forms\Components\TextInput::make('transaction_type')->disabled(),
                Forms\Components\TextInput::make('transaction_status')->disabled(),
                Forms\Components\TextInput::make('reference')->disabled(),
                Forms\Components\TextInput::make('payment_channel')->disabled(),
                Forms\Components\TextInput::make('description')->disabled(),
                Forms\Components\TextInput::make('payment_category')->disabled(),
                Forms\Components\TextInput::make('created_at')->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.display_name'),
                Tables\Columns\TextColumn::make('amount'),
                Tables\Columns\TextColumn::make('reference'),
                Tables\Columns\TextColumn::make('created_at'),
                Tables\Columns\TextColumn::make('transaction_status')->color(fn (string $state): string => match ($state) {
                    'PENDING' => 'info',
                    'FAILED' => 'warning',
                    'SUCCESS' => 'success',
                    'CANCELLED' => 'warning',
                    default => 'gray',
                }),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(), 
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
