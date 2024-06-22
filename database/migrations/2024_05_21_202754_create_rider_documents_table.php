<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRiderDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('rider_document', function (Blueprint $table) {
        //     $table->string('id', 60)->primary();
        //     $table->enum('type', [
        //         'address_verification',
        //         'driver_license',
        //         'insurance_certificate',
        //         'government_id',
        //         'guarantor_letter',
        //         'passport_photo',
        //         'vehicle_registration',
        //         'vehicle_photo',
        //         'certificate_of_vehicle_registration',
        //         'authorization_letter'
        //     ]);
        //     $table->string('number', 50)->nullable();
        //     $table->string('file_url', 550)->nullable();
        //     $table->foreignId('rider_id')->constrained('riders')->onDelete('cascade');
        //     $table->boolean('verified')->default(false);

        //     $table->timestamps();
        //     $table->softDeletes();
        //     $table->enum('state', ['ACTIVE', 'DELETED'])->default('ACTIVE');
        //     $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('cascade');
        //     $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('cascade');
        //     $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('cascade');

        //     $table->index('rider_id');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('rider_document');
    }
}
