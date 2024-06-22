<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferralUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('referral_user', function (Blueprint $table) {
        //     $table->string('id', 60)->primary();
        //     $table->foreignId('referred_by')->constrained('users')->onDelete('cascade');
        //     $table->foreignId('referred_user')->constrained('users')->onDelete('cascade');
        //     $table->string('referral_code', 30)->nullable();
           
        //     $table->timestamps();
        //     $table->softDeletes();
        //     $table->enum('state', ['ACTIVE', 'DELETED'])->default('ACTIVE');
        //     $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('cascade');
        //     $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('cascade');
        //     $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('cascade');

        //     $table->index(['referred_by', 'referred_user']);
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('referral_user');
    }
}
