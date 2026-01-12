<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('internet_services', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('router_id')->constrained('routers');

            $table->string('profile')->nullable();
            $table->string('rate_limit')->nullable(); // 5M/5M
            $table->string('limit_at')->nullable();   // 3M/3M
            $table->string('priority')->nullable();   // 8/8

            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();

            $table->integer('auto_soft_limit')->default(3); // days after due date
            $table->integer('auto_suspend')->default(7); // days after due date

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['product_id', 'router_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internet_services');
    }
};