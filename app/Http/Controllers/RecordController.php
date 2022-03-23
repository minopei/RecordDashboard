<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $record = DB::table('ProcessInstance')
                    ->select(DB::raw('count(*) as caseNumber,processInstanceName'))
                    // ->select('processInstanceName','createdTime')
                    ->where('currentState', '=', 1)
                    ->where(function($query){
                        $query->where('processInstanceName', 'like', '電腦%')
                              ->orWhere('processInstanceName', 'like', '%作廢%');
                    })
                    ->groupBy('processInstanceName')
                    ->get();

        return view('record', compact('record'));

    }

    public function getjson()
    {

        $record = DB::table('ProcessInstance')
                    ->select(DB::raw('count(*) as caseNumber,processInstanceName,createdTime'))
                    ->where('currentState', '=', 1)
                    // ->whereBetween('createdTime',[$startDate,$endDate])
                    ->where(function($query){
                        $query->where('processInstanceName', 'like', '電腦%')
                              ->orWhere('processInstanceName', 'like', '%作廢%');
                    })
                    ->groupBy('processInstanceName')
                    ->get();
        return json_encode($record);
    }
}
