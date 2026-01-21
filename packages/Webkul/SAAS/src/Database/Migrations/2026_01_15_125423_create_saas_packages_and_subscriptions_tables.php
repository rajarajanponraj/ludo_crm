<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('saas_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('price', 12, 2)->default(0); // Base Price
            $table->integer('included_users')->default(1);
            $table->decimal('extra_user_price', 12, 2)->default(0);
            $table->json('features')->nullable(); // Feature flags
            $table->boolean('status')->default(1);
            $table->timestamps();
        });

        Schema::create('saas_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('package_id');
            $table->date('starts_at');
            $table->date('ends_at');
            $table->integer('max_users')->default(0);
            $table->string('status')->default('active'); // active, expired, cancelled
            $table->string('razorpay_subscription_id')->nullable();
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->timestamps();

            // $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            // $table->foreign('package_id')->references('id')->on('saas_packages')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('saas_subscriptions');
        Schema::dropIfExists('saas_packages');
    }
};
