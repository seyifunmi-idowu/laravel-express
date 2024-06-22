<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('user_activity', function (Blueprint $table) {
        //     $table->string('id', 60)->primary();
        //     $table->string('category', 100);
        //     $table->string('action', 100);
        //     $table->json('context')->nullable()->default(json_encode(new stdClass));
        //     $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        //     $table->foreignId('rider_id')->nullable()->constrained('riders')->onDelete('cascade');
        //     $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('cascade');
        //     $table->foreignId('business_id')->nullable()->constrained('businesses')->onDelete('cascade');
        //     $table->enum('level', ['ERROR', 'SUCCESS', 'INFO', 'WARNING'])->default('INFO');
        //     $table->string('session_id', 60)->nullable();

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
        // Schema::dropIfExists('user_activity');
    }
}
