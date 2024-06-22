<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('customer', function (Blueprint $table) {
        //     $table->string('id', 60)->primary();
        //     $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        //     $table->enum('customer_type', ['INDIVIDUAL', 'BUSINESS']);
        //     $table->string('business_name', 100)->nullable();
        //     $table->string('business_address', 100)->nullable();
        //     $table->string('business_category', 100)->nullable();
        //     $table->integer('delivery_volume')->nullable();
            
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
        // Schema::dropIfExists('customer');
    }
}
