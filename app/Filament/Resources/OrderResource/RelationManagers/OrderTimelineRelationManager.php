<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\OrderTimeline;
use Illuminate\Support\HtmlString;

class OrderTimelineRelationManager extends RelationManager
{
    protected static string $relationship = 'order_timeline';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('image')
                    ->required()
                    ->maxLength(255)
                    ->disabled(),

                Forms\Components\Placeholder::make('Proof')
                ->content(fn (OrderTimeline $record)=> $record && $record->proof_url ? new HtmlString("<img src='{$record->proof_url}'>") : 'No proof available'),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('order_id')
            ->columns([
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\ImageColumn::make('proof_url'),
                Tables\Columns\TextColumn::make('created_at')->date(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->visible(fn () => false), // Disable create action
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

        // Define the schema for the show view
        public static function getDetailsSchema(): array
        {
            return [
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255),
                // Forms\Components\Image::make('proof_url')
                //     ->required(),
                Forms\Components\TextInput::make('created_at')
                    ->disabled(),
            ];
        }
    
        public function getTableDetailViewSchema(): array
        {
            return self::getDetailsSchema();
        }
    
}
