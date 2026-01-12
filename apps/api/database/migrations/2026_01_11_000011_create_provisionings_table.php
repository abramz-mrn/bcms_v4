<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('provisionings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('subscriptions_id')->index();
            $table->unsignedBigInteger('routers_id')->index();
            $table->string('device_brand')->nullable();
            $table->string('device_type_device_sn')->nullable();
            $table->string('device_mac')->nullable()->index();
            $table->string('device_conn')->index(); // PPPoE|Static-IP
            $table->string('pppoe_name')->nullable()->index();
            $table->string('pppoe_password')->nullable();
            $table->string('static_ip')->nullable()->index();
            $table->string('static_gateway')->nullable();
            $table->date('activation_date')->nullable()->index();
            $table->string('technisian_name')->nullable();
            $table->string('document_speedtest')->nullable();
            $table->text('technisian_notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('subscriptions_id')->references('id')->on('subscriptions');
            $table->foreign('routers_id')->references('id')->on('routers');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provisionings');
    }
};