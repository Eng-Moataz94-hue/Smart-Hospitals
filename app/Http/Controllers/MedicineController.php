<?php

namespace App\Http\Controllers;

use App\Medicine;
use App\Patients;
use App\Prescription;
use App\Appointment;
//use Illuminate\Support\Facades\Storage;
use App\Prescription_Medicine;
use App\MedicineStock;
//use App\Appointment;
//use File;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

//use stdClass;
//use Carbon\Carbon;
//use Auth;

class MedicineController extends Controller
{
    //

    public function markIssued(Request $request){
        try {
            $pres_med=Prescription_Medicine::find($request->medid);
            if (!$pres_med) {
                return response()->json([
                    "code"=>400,
                    "prescription"=>$request->medid,
                ]);
            }
            $med=Medicine::find($pres_med->medicine_id);
            $picked=DB::transaction(function () use ($pres_med) {
                $need=1;
                $batches=MedicineStock::where('medicine_id', $pres_med->medicine_id)
                    ->usable()
                    ->orderByRaw('ISNULL(expiry_date), expiry_date ASC')
                    ->lockForUpdate()
                    ->get();
                $picked=[];
                foreach ($batches as $batch) {
                    $take=min($batch->quantity, $need);
                    $batch->decrement('quantity', $take);
                    $need-=$take;
                    $picked[]=['batch_id'=>$batch->id, 'batch_number'=>$batch->batch_number, 'taken'=>$take, 'remaining'=>($batch->quantity-$take)];
                    if ($need<=0) {
                        break;
                    }
                }
                if ($need>0) {
                    throw new \Exception('OUT_OF_STOCK');
                }
                return $picked;
            });
            $pres_med->issued="YES";
            $pres_med->save();
            $med->qty+=1;
            $med->save();
            // Log Activity
            activity()->performedOn($pres_med)->withProperties(['Medicine ID' => $med->id, 'Batches' => $picked])->log('Medicine Issued with Stock Deduction');
            return response()->json([
                "code"=>200,
                "prescription"=>$request->medid,
            ]);
        } catch (\Throwable $th) {
            $reason='ERROR';
            if ($th->getMessage()==='OUT_OF_STOCK' && isset($pres_med) && $pres_med) {
                $hasExpired=MedicineStock::where('medicine_id', $pres_med->medicine_id)
                    ->where('quantity', '>', 0)
                    ->whereNotNull('expiry_date')
                    ->where('expiry_date', '<', now()->toDateString())
                    ->exists();
                $reason=$hasExpired ? 'EXPIRED_ONLY' : 'OUT_OF_STOCK';
            }
            return response()->json([
                "code"=>400,
                "prescription"=>$request->medid,
                "reason"=>$reason,
            ]);
        }
        
    }

    public function medIssueSave(Request $request){
        try {
            $presc=Prescription::find($request->presid);
            $presc->medicine_issued="YES";
            $presc->save();
            $medicines=Prescription_Medicine::where('prescription_id',$request->presid)->get();
            return view('medicine.receipt',compact('presc','medicines'));
        } catch (\Throwable $th) {
           return redirect()->back()->with('error',__("Unkown Error Occured"));
        }
        
    }

    public function searchSuggestion(Request $request)
    {
        $keyword = $request->keyword;
        return response()->json([
            "sugestion" => ["shakthi", "sachinta", "blov"],
        ], 200);
    }

    public function getherbs()
    {
        $herbs = DB::table('medicines')->get();
        // dd($herbs);
        return response()->json($herbs);
    }

    public function issueMedicine($presid){
        $pmedicines=Prescription_Medicine::where('prescription_id',$presid)->get();
        $title="Issue Medicine ($presid)";
        $prescription=Prescription::find($presid);
        // dd($pmedicines);
        return view('patient.show',compact('pmedicines','title','presid','prescription'));
    }

    public function issueMedicineView()
    {
        $user = Auth::user();
        return view('patient.issueMedicineView',
        ['title' => "Issue MedicineN"]);
    }

    public function issueMedicineValid(Request $request)
    {
        $num = $request->pNum;
        $numlength = strlen((string) $num);
        
        if ($numlength < 7) {  //if appointemnt number have been given
            $app=Appointment::whereRaw('date(created_at)=CURDATE()')
                            ->where('number',$num)
                            ->orderBy('created_at','DESC')
                            ->first();
          
            if ($app) {
                $rec=Prescription::where('appointment_id',$app->id)->first();
                return response()->json([
                    "exist" => true,
                    "name" => $rec->patient->name,
                    "appNum" => $app->number,
                    "pNUM" => $rec->patient_id,
                    "pres_id"=>$rec->id,
                ]);
            } else {
                return response()->json([
                    "exist" => false,
                ]);
            }
        } 
        else { //if patient registration number have been given
            $app=Appointment::whereRaw('date(created_at)=CURDATE()')
                            ->where('patient_id',$num)
                            ->orderBy('created_at','DESC')
                            ->first();

            if ($app) {

                $rec=Prescription::where('appointment_id',$app->id)->first();

                return response()->json([
                    "exist" => true,
                    "name" => $rec->patient->name,
                    "appNum" => $app->number,
                    "pNUM" => $rec->patient_id,
                    "pres_id"=>$rec->id,
                ]);
            } else {
                return response()->json([
                    "exist" => false,
                ]);
            }
        }

        
    }

    public function stockIndex(){
        $title="Medicine Stock";
        $today=now()->toDateString();
        $soon=now()->addDays(30)->toDateString();
        $lowThreshold=10;
        $medicines=Medicine::orderBy('name_english')->get();
        $rows=[];
        $outCount=0;
        $lowCount=0;
        foreach ($medicines as $medicine) {
            $total=$medicine->getTotalStock();
            $batches=$medicine->stocks()->orderByRaw('ISNULL(expiry_date), expiry_date ASC')->get();
            if ($total<=0) {
                $outCount++;
            } elseif ($total<$lowThreshold) {
                $lowCount++;
            }
            $rows[]=['medicine'=>$medicine, 'total'=>$total, 'batches'=>$batches];
        }
        $expiringCount=MedicineStock::available()->whereNotNull('expiry_date')->whereBetween('expiry_date', [$today, $soon])->count();
        return view('medicine.stocks', compact('title','rows','outCount','lowCount','expiringCount','lowThreshold','today','soon'));
    }

  
}

