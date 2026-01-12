<?php
// ...inside class...

    public function ensureSimpleQueue(string $queueName, string $targetCidr, ?string $maxLimit): void
    {
        $rows = $this->talk(['/queue/simple/print', '?name='.$queueName]);
        if (!empty($rows)) {
            // exists, do nothing
            return;
        }

        // Create queue
        $words = [
            '/queue/simple/add',
            '=name='.$queueName,
            '=target='.$targetCidr,
        ];

        if ($maxLimit !== null && $maxLimit !== '') {
            $words[] = '=max-limit='.$maxLimit;
        }

        $this->talk($words);
    }