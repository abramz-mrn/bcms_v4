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

            $table->string('type')->index(); // Internet Services|Additional Services|Other Services|Equipments
            $table->text('description')->nullable();

            $table->string('market_segment')->nullable()->index(); // Residensial|SOHO/UMKM|Corporate|Others
            $table->string('billing_cycle')->index(); // One time charge|Weekly|Monthly|Quarterly|Semi-annually|Annually

            $table->bigInteger('price'); // store as integer rupiah
            $table->decimal('tax_rate', 5, 2)->default(0);
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