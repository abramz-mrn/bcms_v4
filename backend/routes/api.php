<?php

// ... keep existing use statements
use App\Http\Controllers\Api\WhatsappTestController;

// ... inside auth:sanctum group add:
Route::post('/tools/whatsapp/send', [WhatsappTestController::class, 'send']);