<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('provisionings', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignId('subscription_id')->constrained('subscriptions');
            $table->string('subscription_no')->nullable()->index(); // optional cache
            $table->foreignId('router_id')->constrained('routers');

            $table->string('device_brand')->nullable();
            $table->string('device_type')->nullable();
            $table->string('device_sn')->nullable();
            $table->string('device_mac')->nullable();

            $table->string('device_conn')->index(); // PPPoE|Static-IP

            $table->string('pppoe_name')->nullable();
            $table->string('pppoe_password')->nullable();
            $table->string('initial_name')->nullable();

            $table->string('static_ip')->nullable();
            $table->string('static_gateway')->nullable();

            $table->date('activation_date')->nullable();
            $table->string('technisian_name')->nullable();
            $table->string('document_speedtest')->nullable();
            $table->text('technisian_notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users');

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['subscription_id']); // 1 provisioning per subscription (starter)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provisionings');
    }
};