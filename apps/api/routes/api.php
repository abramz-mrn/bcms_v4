<?php
use App\Http\Controllers\Public\InvoicePublicController;

Route::get('public/invoices/by-token/{token}', [InvoicePublicController::class, 'showByToken']);