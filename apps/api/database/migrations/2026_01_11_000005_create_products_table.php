<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->unique();
            $table->string('name');
            $table->string('type')->index();
            $table->text('description')->nullable();
            $table->string('market_segment')->index();
            $table->string('billing_cycle')->index();
            $table->decimal('price', 14, 2)->default(0);
            $table->decimal('tax_rate', 6, 2)->default(0);
            $table->boolean('tax_included')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};