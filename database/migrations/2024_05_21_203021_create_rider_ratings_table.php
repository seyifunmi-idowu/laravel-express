<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRiderRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('rider_rating', function (Blueprint $table) {
        //     $table->string('id', 60)->primary();
        //     $table->unsignedTinyInteger('rating')->nullable();
        //     $table->text('remark')->nullable();
        //     $table->foreignId('rider_id')->constrained('riders')->onDelete('cascade');
        //     $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            
        //     $table->timestamps();
        //     $table->softDeletes();
        //     $table->enum('state', ['ACTIVE', 'DELETED'])->default('ACTIVE');
        //     $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('cascade');
        //     $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('cascade');
        //     $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('cascade');

        //     $table->index(['rider_id', 'customer_id']);
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('rider_rating');
    }
}