<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('transactions', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
        //     $table->string('transaction_type', 255)->default('CREDIT');
        //     $table->string('transaction_status', 255)->default('PENDING');
        //     $table->decimal('amount', 30, 2);
        //     $table->string('currency', 30)->default('₦');
        //     $table->string('reference', 255)->nullable();
        //     $table->string('pssp', 255)->default('PAYSTACK');
        //     $table->string('payment_channel', 255)->nullable();
        //     $table->text('description')->nullable();
        //     $table->string('wallet_id', 255)->nullable();
        //     $table->string('object_id', 255)->nullable();
        //     $table->string('object_class', 255)->nullable();
        //     $table->string('payment_category', 255)->nullable();
        //     $table->json('pssp_meta_data')->nullable()->default(json_encode([]));
            
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
        // Schema::dropIfExists('transactions');
    }
}
