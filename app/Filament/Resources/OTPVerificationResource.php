<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OTPVerificationResource\Pages;
use App\Filament\Resources\OTPVerificationResource\RelationManagers;
use App\Models\OtpVerification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OTPVerificationResource extends Resource
{
    protected static ?string $model = OtpVerification::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\TextInput::make('email'),
            Forms\Components\TextInput::make('phone_number'),
            Forms\Components\TextInput::make('otp'),
            Forms\Components\DatePicker::make('expiration_time'),
            Forms\Components\TextInput::make('trials'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('phone_number'),
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
            'index' => Pages\ListOTPVerifications::route('/'),
            'create' => Pages\CreateOTPVerification::route('/create'),
            'edit' => Pages\EditOTPVerification::route('/{record}/edit'),
        ];
    }
}
