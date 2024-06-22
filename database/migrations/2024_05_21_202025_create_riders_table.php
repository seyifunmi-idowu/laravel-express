<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('rider', function (Blueprint $table) {
        //     $table->string('id', 60)->primary();
        //     $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        //     $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->onDelete('cascade');
        //     $table->string('vehicle_type', 30)->nullable();
        //     $table->string('vehicle_make', 50)->nullable();
        //     $table->string('vehicle_model', 30)->nullable();
        //     $table->string('vehicle_plate_number', 10)->nullable();
        //     $table->string('vehicle_color', 30)->nullable();
        //     $table->text('rider_info')->nullable();
        //     $table->string('city', 50)->nullable();
        //     $table->string('avatar_url', 550)->nullable();
        //     $table->enum('status', ['APPROVED', 'UNAPPROVED', 'DISAPPROVED', 'SUSPENDED'])->default('UNAPPROVED');
        //     $table->json('status_updates')->nullable();
        //     $table->json('operation_locations')->nullable();
        //     $table->boolean('on_duty')->default(false);

        //     $table->timestamps();
        //     $table->softDeletes();
        //     $table->enum('state', ['ACTIVE', 'DELETED'])->default('ACTIVE');
        //     $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('cascade');
        //     $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('cascade');
        //     $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('cascade');
            
        //     $table->index('user_id');
        // });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('rider');
    }
}
