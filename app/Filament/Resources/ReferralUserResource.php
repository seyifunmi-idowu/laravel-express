<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReferralUserResource\Pages;
use App\Filament\Resources\ReferralUserResource\RelationManagers;
use App\Models\ReferralUser;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReferralUserResource extends Resource
{
    protected static ?string $model = ReferralUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Placeholder::make('referred_by_full_name')
                    ->label('Referred By')
                    ->content(fn ($record) => $record->referredBy->first_name . ' ' . $record->referredBy->last_name)
                    ->columnSpanFull(),
                Forms\Components\Placeholder::make('referred_user_full_name')
                    ->label('Referred User')
                    ->content(fn ($record) => $record->referredUser->first_name . ' ' . $record->referredUser->last_name)
                    ->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('referredBy.first_name')->label('Referred By first name'),
                Tables\Columns\TextColumn::make('referredBy.last_name')->label('Referred By last name'),
                Tables\Columns\TextColumn::make('referredUser.first_name')->label('Referred User last name'),
                Tables\Columns\TextColumn::make('referredUser.last_name')->label('Referred User last name'),
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
            'index' => Pages\ListReferralUsers::route('/'),
            'create' => Pages\CreateReferralUser::route('/create'),
            'edit' => Pages\EditReferralUser::route('/{record}/edit'),
        ];
    }
}
