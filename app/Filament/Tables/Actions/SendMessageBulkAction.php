<?php

namespace App\Filament\Tables\Actions;

use Filament\Tables\Actions\BulkAction;
use Filament\Forms;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationService;


class SendMessageBulkAction extends BulkAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->name('send_message') // Ensure the name is unique
            ->form([
                Forms\Components\TextInput::make('title')
                    ->label('Title')
                    ->required(),
                Forms\Components\TextInput::make('message')
                    ->label('Message')
                    ->required(),

            ])
            ->action(function (Collection $records, array $data) {
                $this->handle($records, $data);
            });
    }

    public function handle(Collection $records, array $data): void
    {
        $message = $data['message'];
        $title = $data['title'];
        $notificationService = app()->make(NotificationService::class);
        $notificationService->sendCollectivePushNotification($records, $title, $message);
        $this->success('Message sent successfully!');
    }
}
