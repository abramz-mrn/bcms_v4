<?php
// ...
public function customer()
{
    return $this->belongsTo(Customer::class, 'customers_id');
}