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
        Schema::create('tickets', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->foreignId("ticket_category_id");
            $table->foreignUuid("user_uuid");
            $table->foreignUuid("price");
            $table->string('name');
            $table->boolean("validated")->default(0);
            $table->foreignUuid("validated_by_uuid");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
