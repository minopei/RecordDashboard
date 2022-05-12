<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{
    public function getjson()
    {
        $record = DB::table('ProcessInstance')
                    // ->select(DB::raw('count(*) as caseNumber,processInstanceName'))
                    ->select('processInstanceName','createdTime','currentState')
                    // ->where('currentState', '=', 1)
                    ->where(function($query){
                        $query->where('processInstanceName', 'like', '電腦需求%')
                              ->orWhere('processInstanceName', 'like', '電腦帳號%')
                            // ->orWhere('processInstanceName', 'like', '%作廢%');
                              ->orWhere('processInstanceName', 'like', '%請購單_採購單作廢%');
                    })
                    // ->groupBy('processInstanceName')
                    ->get();
        return json_encode($record);
    }
}
