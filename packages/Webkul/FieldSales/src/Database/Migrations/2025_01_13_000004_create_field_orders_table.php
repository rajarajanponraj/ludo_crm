<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('field_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedInteger('user_id'); // Sales Agent
            $table->unsignedInteger('person_id'); // Customer

            $table->string('type')->default('primary'); // primary (distributor to company), secondary (retailer to distributor)
            $table->decimal('grand_total', 12, 4)->default(0);
            $table->string('status')->default('pending'); // pending, approved, dispatched, delivered, cancelled

            $table->unsignedInteger('dispatcher_id')->nullable(); // Assigned Dispatcher (User)

            $table->text('notes')->nullable();
            $table->date('delivery_date')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('person_id')->references('id')->on('persons')->onDelete('cascade');
            $table->foreign('dispatcher_id')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('field_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('field_order_id');
            $table->unsignedInteger('product_id');

            $table->integer('qty')->default(1);
            $table->decimal('price', 12, 4)->default(0);
            $table->decimal('total', 12, 4)->default(0);

            $table->timestamps();

            $table->foreign('field_order_id')->references('id')->on('field_orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('field_order_items');
        Schema::dropIfExists('field_orders');
    }
};
