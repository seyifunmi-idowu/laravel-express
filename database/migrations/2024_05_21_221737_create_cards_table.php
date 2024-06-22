<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('card', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
        //     $table->string('card_type', 30)->nullable();
        //     $table->string('card_auth', 30)->nullable();
        //     $table->string('last_4', 30)->nullable();
        //     $table->string('exp_month', 30)->nullable();
        //     $table->string('exp_year', 30)->nullable();
        //     $table->string('country_code', 30)->nullable();
        //     $table->string('brand', 30)->nullable();
        //     $table->string('first_name', 50)->nullable();
        //     $table->string('last_name', 50)->nullable();
        //     $table->boolean('reusable')->default(true);
        //     $table->string('customer_code', 50)->nullable();

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
        // Schema::dropIfExists('card');
    }
}
