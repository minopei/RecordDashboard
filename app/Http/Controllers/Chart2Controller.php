<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Chart2Controller extends Controller
{
    public function getjson()
    {
        $value = "進行中";
        $record = DB::table('ProcessInstance')
                    // ->select(DB::raw('count(*) as caseNumber,processInstanceName'))
                    ->select('processInstanceName','createdTime',
                    DB::raw("CASE currentState 
                    WHEN 1 THEN '進行中' 
                    WHEN 3 THEN '已完成' 
                    ELSE '已終止' END AS currentState"))
                    // ->where('currentState', '=', 1)
                    ->where(function($query){
                        $query->where('processInstanceName', 'like', '電腦%')
                              ->orWhere('processInstanceName', 'like', '%作廢%');
                    })
                    // ->groupBy('processInstanceName')
                    ->get();
        return json_encode($record);
    }
}
