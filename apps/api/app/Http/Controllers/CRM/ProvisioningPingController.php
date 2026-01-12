<?php
// ...snip...

            if (!$active || empty($active['address'])) {
                return response()->json([
                    'message' => 'PPPoE session not active',
                    'code' => 'PPPOE_NOT_ACTIVE',
                    'pppoe_name' => $resolved['pppoe_name'],
                ], 404);
            }

// ...snip...