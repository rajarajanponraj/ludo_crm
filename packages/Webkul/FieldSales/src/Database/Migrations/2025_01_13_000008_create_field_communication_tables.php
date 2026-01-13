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
        // 1. Announcements (One-to-Many: Company Admin -> All Agents)
        Schema::create('field_announcements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();

            $table->string('title');
            $table->text('content');
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });

        // 2. Direct Messages (Many-to-Many: User <-> User)
        Schema::create('field_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();

            $table->unsignedInteger('sender_id');
            $table->unsignedInteger('receiver_id');

            $table->text('message');
            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('field_messages');
        Schema::dropIfExists('field_announcements');
    }
};
