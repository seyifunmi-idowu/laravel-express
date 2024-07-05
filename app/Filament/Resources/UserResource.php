<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\FileUpload;
use App\Filament\Tables\Actions\SendMessageBulkAction;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('first_name')
                    ->required()
                    ->label('First name'),
                Forms\Components\TextInput::make('last_name')
                    ->required()
                    ->label('Last name'),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->label('Email'),
                // Forms\Components\TextInput::make('password')
                //     ->password()
                //     ->required()
                //     ->label('Password'),
                Forms\Components\TextInput::make('phone_number')
                    ->label('Phone'),            
                Forms\Components\TextInput::make('referral_code'),
                Forms\Components\TextInput::make('street_address'),
                Forms\Components\TextInput::make('city'),
                Forms\Components\TextInput::make('state_of_residence'),
                Forms\Components\TextInput::make('country'),
                Forms\Components\TextInput::make('avatar_url'), 
                Forms\Components\Placeholder::make('wallet_balance')
                    ->label('Wallet balance')
                    ->content(fn ($record) => $record->wallet->balance)
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('email_verified')
                    ->label('Email Verification Status')
                    ->disabled(),
                Forms\Components\Toggle::make('phone_verified')
                    ->label('Phone Number Verification Status')
                    ->disabled(),
                Forms\Components\Toggle::make('is_superuser')
                    ->label('Is Superuser'),
                Forms\Components\Toggle::make('is_deactivated'),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name'),
                Tables\Columns\TextColumn::make('last_name'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('phone_number'),
                Tables\Columns\TextColumn::make('user_type'),
                Tables\Columns\BooleanColumn::make('is_superuser'),
                ImageColumn::make('avatar_url')->circular()
                
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Tables\Actions\Action::make('view')
                // ->label('View')
                // ->url(fn ($record) => route('filament.resources.users.view', $record)),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    SendMessageBulkAction::make('send_message'),

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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            // 'view' => Pages\ViewUser::route('/{record}'),
        ];
    }

    public static function getUserName(User $user): string
    {
        return $user->first_name . ' ' . $user->last_name;
    }
}
