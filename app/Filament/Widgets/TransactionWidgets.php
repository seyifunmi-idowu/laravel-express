<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Transaction;

class TransactionWidgets extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = "full";

    public function table(Table $table): Table
    {
        return $table
        ->query(Transaction::query())
        ->columns([
            Tables\Columns\TextColumn::make('id')->label('Transaction ID'),
            Tables\Columns\TextColumn::make('user.display_name')->label('User'),
            Tables\Columns\TextColumn::make('amount')->label('Amount'),
            Tables\Columns\TextColumn::make('transaction_type')->label('Type'),
            Tables\Columns\TextColumn::make('transaction_status')->label('Status')->color(fn (string $state): string => match ($state) {
                'PENDING' => 'info',
                'FAILED' => 'warning',
                'SUCCESS' => 'success',
                'CANCELLED' => 'warning',
                default => 'gray',
            }),
            Tables\Columns\TextColumn::make('created_at')->label('Date'),
        ]);
    }
}
