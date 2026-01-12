<?php
// ...inside class...

    public function ensureSimpleQueue(string $queueName, string $targetCidr, ?string $maxLimit): void
    {
        // Check exists
        $check = $this->exec('/queue/simple/print where name="'.$queueName.'"');
        if (stripos($check, $queueName) !== false) {
            return;
        }

        $cmd = '/queue/simple/add name="'.$queueName.'" target="'.$targetCidr.'"';
        if ($maxLimit !== null && $maxLimit !== '') {
            $cmd .= ' max-limit="'.$maxLimit.'"';
        }

        $this->exec($cmd);
    }