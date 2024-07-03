<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehicleResource\Pages;
use App\Filament\Resources\VehicleResource\RelationManagers;
use App\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\ImageColumn;
use App\Helpers\S3Uploader;
use Filament\Forms\Set;
use Illuminate\Support\Facades\Log;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('status')->required(),
                Forms\Components\TextInput::make('note'),
                Forms\Components\TextInput::make('base_fare'),
                Forms\Components\TextInput::make('km_5_below_fare'),
                Forms\Components\TextInput::make('km_5_above_fare'),
                Forms\Components\TextInput::make('price_per_minute'),
                Forms\Components\DatePicker::make('start_date'),
                Forms\Components\DatePicker::make('end_date'),
                Forms\Components\TextInput::make('file_url'),
                // Forms\Components\FileUpload::make('vehicle_image')->label('upload vehicle image'), # get the imae upload to s3 and save to file_url field
                // Forms\Components\FileUpload::make('vehicle_image')
                // ->afterStateUpdated(function (Set $set) {
                //     Log::info('Uploaded file details:', $set->get('vehicle_image'));

                //     $set('note', 'Blog Postsss'); 
            
                // })
                
                ]);
            // ->configure(function (Set $set) {
                // Add custom onSave callback to manipulate form data before saving
            //     return [
            //         'onSave' => function (Vehicle $vehicle, array $data) {
            //             $vehicle->note = "test it";
            //             // Perform calculations or transformations
            //             if (isset($data['vehicle_image'])) {

            //                 $file = $data('vehicle_image');
            //                 $keyname = 'uploads/' . $file->getClientOriginalName();
                    
            //                 $s3Uploader = new S3Uploader('/available-vehicles');
            //                 $fileUrl = $s3Uploader->uploadFileObject($file, $keyname);
            //                 $vehicle->file_url = $fileUrl;
            //             }
            //         },
            //     ];
            // });

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('status'),
                ImageColumn::make('file_url')->circular(),            ])
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
            'index' => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            'edit' => Pages\EditVehicle::route('/{record}/edit'),
        ];
    }
}
