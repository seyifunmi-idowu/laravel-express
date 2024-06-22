<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('order', function (Blueprint $table) {
        //     $table->string('id', 60)->primary();
        //     $table->foreignId('customer_id')->nullable()->constrained()->onDelete('cascade');
        //     $table->foreignId('rider_id')->nullable()->constrained()->onDelete('cascade');
        //     $table->foreignId('business_id')->nullable()->constrained()->onDelete('cascade');
        //     $table->foreignId('vehicle_id')->nullable()->constrained()->onDelete('cascade');
        //     $table->string('order_id', 20);
        //     $table->string('chat_id', 20)->nullable();
        //     $table->string('status', 50)->default('PENDING');
        //     $table->string('payment_method', 50)->nullable();
        //     $table->string('payment_by', 50)->nullable();
        //     $table->string('order_by', 50)->default('CUSTOMER');
        //     $table->boolean('paid')->default(false);
        //     $table->string('pickup_number', 50)->nullable();
        //     $table->string('pickup_contact_name', 100)->nullable();
        //     $table->string('pickup_location', 255)->nullable();
        //     $table->string('pickup_name', 255)->nullable();
        //     $table->string('pickup_location_longitude', 255)->nullable();
        //     $table->string('pickup_location_latitude', 255)->nullable();
        //     $table->string('delivery_number', 50)->nullable();
        //     $table->string('delivery_contact_name', 100)->nullable();
        //     $table->string('delivery_location', 255)->nullable();
        //     $table->string('delivery_name', 255)->nullable();
        //     $table->string('delivery_location_longitude', 255)->nullable();
        //     $table->string('delivery_location_latitude', 255)->nullable();
        //     $table->dateTime('delivery_time')->nullable();
        //     $table->json('order_stop_overs_meta_data')->nullable();
        //     $table->decimal('total_amount', 10, 2)->default(0.0);
        //     $table->decimal('fele_amount', 10, 2)->default(0.0);
        //     $table->boolean('paid_fele')->default(false);
        //     $table->decimal('tip_amount', 10, 2)->nullable();
        //     $table->json('order_meta_data')->nullable();
        //     $table->string('distance', 20);
        //     $table->string('duration', 20);
            
        //     $table->timestamps();
        //     $table->softDeletes();
        //     $table->enum('state', ['ACTIVE', 'DELETED'])->default('ACTIVE');
        //     $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('cascade');
        //     $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('cascade');
        //     $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('cascade');

        //     $table->index(['order_id']);
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('order');
    }
}
