<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('bank_account', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
        //     $table->string('account_number', 30)->nullable();
        //     $table->string('account_name', 100)->nullable();
        //     $table->string('bank_code', 50)->nullable();
        //     $table->string('bank_name', 100)->nullable();
        //     $table->string('recipient_code', 50)->nullable();
        //     $table->json('meta')->nullable();
        //     $table->boolean('save_account')->default(false);
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
        // Schema::dropIfExists('bank_account');
    }
}
