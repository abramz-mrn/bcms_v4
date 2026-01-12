<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('products_id')->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('start_date')->index();
            $table->date('end_date')->index();
            $table->decimal('discount', 14, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('products_id')->references('id')->on('products');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};