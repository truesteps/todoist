<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('todolist_items', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('todolist_id');

            $table->string('name')->index();
            $table->text('description');

            $table->dateTime('finished_at')->nullable()->index();

            $table->timestamps();

            $table->foreign('todolist_id')
                ->references('id')
                ->on('todolists')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('todolist_items');
    }
};
