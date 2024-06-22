<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('address', function (Blueprint $table) {
        //     $table->string('id', 60)->primary();
        //     $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
        //     $table->string('formatted_address', 255);
        //     $table->string('longitude', 50);
        //     $table->string('latitude', 50);
        //     $table->string('state', 100)->nullable();
        //     $table->string('country', 100)->nullable();
        //     $table->string('landmark', 100)->nullable();
        //     $table->string('direction', 255)->nullable();
        //     $table->string('label', 255)->nullable();
        //     $table->json('meta_data')->nullable();
        //     $table->boolean('save_address')->default(false);
            
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
        // Schema::dropIfExists('address');
    }
}
