<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('vehicle', function (Blueprint $table) {
        //     $table->string('id', 60)->primary();
        //     $table->string('name', 50);
        //     $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('INACTIVE');
        //     $table->string('note', 550)->nullable();
        //     $table->dateTime('start_date')->nullable();
        //     $table->dateTime('end_date')->nullable();
        //     $table->string('file_url', 550)->nullable();
        //     $table->integer('base_fare');
        //     $table->integer('km_5_below_fare');
        //     $table->integer('km_5_above_fare');
        //     $table->integer('price_per_minute')->default(80);

        //     $table->timestamps();
        //     $table->softDeletes();
        //     $table->enum('state', ['ACTIVE', 'DELETED'])->default('ACTIVE');
        //     $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('cascade');
        //     $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('cascade');
        //     $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('cascade');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('vehicle');
    }
}
