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
        Schema::create('field_collections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedInteger('user_id'); // Sales Agent
            $table->unsignedInteger('person_id'); // Customer

            $table->string('invoice_id')->nullable(); // External Invoice Reference
            $table->decimal('amount', 12, 4)->default(0);
            $table->string('payment_mode')->default('cash'); // cash, check, online_transfer
            $table->string('transaction_id')->nullable();
            $table->string('proof_image')->nullable(); // Path to image
            $table->timestamp('collected_at')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('person_id')->references('id')->on('persons')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('field_collections');
    }
};
