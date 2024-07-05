<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
// use App\Filament\Resources\OrderResource\RelationManagers\OrderTimelineRelationManager;
use App\Models\Order;
use App\Models\OrderTimeline;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManager;
use App\Filament\Resources\OrderResource\RelationManagers\OrderTimelineRelationManager;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\Placeholder::make('customer_full_name')
                ->label('Customer Name')
                ->content(fn ($record) => $record->customer ? $record->customer->display_name : 'N/A')
                ->columnSpanFull(),
                Forms\Components\Placeholder::make('rider_full_name')
                ->label('Rider Name')
                ->content(fn ($record) => $record->rider ? $record->rider->display_name : 'N/A')
                ->columnSpanFull(),
            Forms\Components\Placeholder::make('business_name')
                ->label('Business Name')
                ->content(fn ($record) => $record->business ? $record->business->display_name : 'N/A')
                ->columnSpanFull(),
            Forms\Components\Placeholder::make('vehicle_name')
                ->label('Vehicle')
                ->content(fn ($record) => $record->vehicle->name)
                ->columnSpanFull(),

            Forms\Components\TextInput::make('order_id') ->disabled(),
            Forms\Components\TextInput::make('status')->disabled(),
            Forms\Components\TextInput::make('payment_method')->disabled(),
            Forms\Components\TextInput::make('payment_by')->disabled(),
            Forms\Components\TextInput::make('order_by')->disabled(),
            Forms\Components\TextInput::make('payment_by')->disabled(),
            Forms\Components\TextInput::make('pickup_number')->disabled(),
            Forms\Components\TextInput::make('pickup_contact_name')->disabled(),
            Forms\Components\TextInput::make('pickup_location')->disabled(),
            Forms\Components\TextInput::make('pickup_name')->disabled(),
            Forms\Components\TextInput::make('pickup_location_longitude')->disabled(),
            Forms\Components\TextInput::make('pickup_location_latitude')->disabled(),
            Forms\Components\TextInput::make('delivery_number')->disabled(),
            Forms\Components\TextInput::make('delivery_contact_name')->disabled(),
            Forms\Components\TextInput::make('delivery_location')->disabled(),
            Forms\Components\TextInput::make('delivery_name')->disabled(),
            Forms\Components\TextInput::make('delivery_location_longitude')->disabled(),
            Forms\Components\TextInput::make('total_amount')->disabled(),
            Forms\Components\TextInput::make('fele_amount')->disabled(),
            Forms\Components\TextInput::make('tip_amount')->disabled(),
            Forms\Components\TextInput::make('distance')->disabled(),
            Forms\Components\TextInput::make('duration')->disabled(),
            Forms\Components\Toggle::make('paid')->disabled(),
            Forms\Components\Toggle::make('paid_fele')->disabled(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_id'),
                Tables\Columns\TextColumn::make('status')->color(fn (string $state): string => match ($state) {
                    'PENDING' => 'gray',
                    'PROCESSING_ORDER' => 'gray',
                    'PENDING_RIDER_CONFIRMATION' => 'info',
                    'RIDER_ACCEPTED_ORDER' => 'info',
                    'RIDER_AT_PICK_UP' => 'info',
                    'RIDER_PICKED_UP_ORDER' => 'info',
                    'ORDER_ARRIVED' => 'success',
                    'ORDER_DELIVERED' => 'success',
                    'ORDER_COMPLETED' => 'success',
                    'ORDER_CANCELLED' => 'warning',
                    default => 'gray',
                }),
                Tables\Columns\TextColumn::make('customer.display_name'),
                Tables\Columns\TextColumn::make('business.display_name'),
                Tables\Columns\TextColumn::make('rider.display_name'),
                // Tables\Columns\TextColumn::make('user.first_name')->label('Customer first name'),

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
            OrderTimelineRelationManager::class,
        ];
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
