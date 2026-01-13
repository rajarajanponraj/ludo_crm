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
        Schema::create('field_routes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedInteger('user_id');
            $table->date('date');
            $table->string('name')->nullable();
            $table->string('status')->default('draft'); // draft, active, completed
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('field_route_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('field_route_id');
            $table->unsignedInteger('person_id');
            $table->time('target_time')->nullable();
            $table->string('status')->default('pending'); // pending, visited, missed
            $table->timestamps();

            $table->foreign('field_route_id')->references('id')->on('field_routes')->onDelete('cascade');
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
        Schema::dropIfExists('field_route_items');
        Schema::dropIfExists('field_routes');
    }
};
