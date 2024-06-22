<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Schema::create('user', function (Blueprint $table) {
        //     $table->string('id', 60)->primary();

        //     $table->string('first_name', 100)->nullable()->default(null);
        //     $table->string('last_name', 100)->nullable()->default(null);
        //     $table->string('email', 100)->unique()->nullable()->default(null);
        //     $table->boolean('email_verified')->default(false);
        //     $table->string('phone_number', 100)->nullable()->default(null);
        //     $table->boolean('phone_verified')->default(false);
        //     $table->boolean('is_staff')->default(false);
        //     $table->string('password', 128)->default(null);
        //     $table->string('avatar_url', 550)->nullable()->default(null);
        //     $table->string('street_address', 100)->nullable()->default(null);
        //     $table->string('city', 100)->nullable()->default(null);
        //     $table->string('state_of_residence', 100)->nullable()->default(null);
        //     $table->string('country', 100)->nullable()->default('Nigeria');
        //     $table->text('bio')->nullable()->default('');
        //     $table->enum('user_type', ['RIDER', 'CUSTOMER', 'BUSINESS', 'ADMIN']);
        //     $table->date('date_of_birth')->nullable()->default(null);
        //     $table->string('last_login_user_type', 150)->nullable()->default(null);
        //     $table->boolean('is_deactivated')->default(false);
        //     $table->text('deactivated_reason')->nullable()->default(null);
        //     $table->boolean('receive_email_promotions')->default(false);
        //     $table->string('referral_code', 30)->nullable()->default(null);

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
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
