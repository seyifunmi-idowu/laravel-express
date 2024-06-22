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
        Schema::table('user', function (Blueprint $table) {
            $table->boolean('is_superuser')->default(false)->change();
            $table->boolean('email_verified')->nullable()->change();
            $table->boolean('phone_verified')->nullable()->change();
            $table->boolean('is_staff')->nullable()->change();
            $table->boolean('is_deactivated')->nullable()->change();
            $table->string('bio')->nullable()->change();
            $table->string('state')->nullable()->change();           
            $table->string('receive_email_promotions')->nullable()->change();
            $table->string('user_type')->nullable()->change();

            $table->boolean('new_pass')->default(false);

            $table->rememberToken();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
