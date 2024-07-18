<?php

namespace App\Observers;

use App\Models\Client;
use App\Models\ClientLog;
use App\Models\Brand;
use Illuminate\Support\Facades\Auth;

class ClientObserver
{
    public function updated(Client $client)
    {
        $changes = $client->getChanges();
        $original = $client->getOriginal();

        foreach ($changes as $field => $newValue) {
            if (in_array($field, $client->getFillable()) && $newValue!='') {
                $fieldName = $field;
                $new_Value = $newValue;
                if($field=='brand_id'){
                    $fieldName= 'Brand';
                    $new_Value = Brand::where('id',$newValue)->first()->name;
                }else if($field=='city'){
                    $fieldName= 'City';
                }else if($field=='district'){
                    $fieldName= 'District';
                }else if($field=='state'){
                    $fieldName= 'State';
                }else if($field=='first_language'){
                    $fieldName= 'Prefered Language 1';
                }else if($field=='second_language'){
                    $fieldName= 'Prefered Language 2';
                }else if($field=='third_language'){
                    $fieldName= 'Prefered Language 3';
                }
                ClientLog::create([
                    'client_id' => $client->id,
                    'user_id' => Auth::id() ?: 1,
                    'field_name' => $fieldName,
                    'old_value' => $original[$field],
                    'new_value' => $newValue,
                    'note' => $fieldName.' changed from '.(($original[$field]=='')?'-(blank)':$original[$field]).' to '.$new_Value,
                    'log_type' => 'update',
                ]);
            }
        }
    }
}
