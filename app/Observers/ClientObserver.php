<?php

namespace App\Observers;

use App\Models\Client;
use App\Models\ClientLog;
use Illuminate\Support\Facades\Auth;

class ClientObserver
{
    public function updated(Client $client)
    {
        $changes = $client->getChanges();
        $original = $client->getOriginal();

        foreach ($changes as $field => $newValue) {
            if (in_array($field, $client->getFillable())) {
                ClientLog::create([
                    'client_id' => $client->id,
                    'user_id' => Auth::id(),
                    'field_name' => $field,
                    'old_value' => $original[$field],
                    'new_value' => $newValue,
                    'note' => $original[$field].' changed to '.$newValue,
                    'log_type' => 'update',
                ]);
            }
        }
    }
}