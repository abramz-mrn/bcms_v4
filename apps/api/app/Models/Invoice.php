<?php
// ...
public function subscription()
{
    return $this->belongsTo(Subscription::class, 'subscriptions_id');
}