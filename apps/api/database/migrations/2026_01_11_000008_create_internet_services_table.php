<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('internet_services', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('products_id')->index();
            $table->unsignedBigInteger('routers_id')->index();
            $table->string('profile')->nullable();
            $table->string('rate_limit')->nullable(); // 5M/5M
            $table->string('limit_at')->nullable(); // 3M/3M
            $table->string('priority')->nullable(); // 8/8
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->unsignedInteger('auto_soft_limit')->default(0); // days after due date
            $table->unsignedInteger('auto_suspend')->default(0); // days after due date
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('products_id')->references('id')->on('products');
            $table->foreign('routers_id')->references('id')->on('routers');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internet_services');
    }
};