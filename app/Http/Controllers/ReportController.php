<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Clinic;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function viewclinicreport()
    {
        $user = Auth::user();
        $data = Clinic::all();
        return view('reports/clinic_reports', ['title' => $user->name, 'clinic' => $data]);
    }

    public function printclinicreport(Request $data)
    {

        $user = Auth::user();

        return view('reports/print_clinic_report', ['name' => $user->name]);
    }
    public function view_mobile_clinic_report()
    {
        $user = Auth::user();
        return view('reports/mobile_clinic_reports', ['title' => $user->name]);
    }
    public function view_monthly_static_report()
    {
        $user = Auth::user();

        $start_date = date('Y/m/00');
        $end_date = date('Y/m/31');
        $no_of_employees = DB::table('attendances')
            ->whereBetween('attendances.start', [$start_date, $end_date])
            ->count(DB::raw('DISTINCT user_id'));
        $patient_count = DB::table('appointments')
            ->count(DB::raw('distinct number'));
        $avg_patient = ceil($patient_count / 30);

        $ward_count = DB::table('wards')
            ->count(DB::raw('distinct ward_no'));
        $bed_count = DB::table('wards')
            ->sum('beds');
        $inpatient_count = DB::table('inpatients')
            ->whereBetween('created_at', [$start_date, $end_date])
            ->count(DB::raw('discharged'));
        $discharged_patinet_count = DB::table('inpatients')
            ->where('discharged', '=', 'YES')
            ->whereBetween('created_at', [$start_date, $end_date])
            ->count(DB::raw('discharged'));

        $admindaycnt = DB::table('attendances')
            ->join('users', 'users.id', '=', 'attendances.user_id')
            ->whereBetween('attendances.start', [$start_date, $end_date])
            ->where('users.user_type', '=', 'admin')
            ->count(DB::raw('start'));

        $doctordaycnt = DB::table('attendances')
            ->join('users', 'users.id', '=', 'attendances.user_id')
            ->whereBetween('attendances.start', [$start_date, $end_date])
            ->where('users.user_type', '=', 'doctor')
            ->count(DB::raw('start'));

        $appointmentcnt = DB::table('appointments')
            ->whereBetween('created_at', [$start_date, $end_date])
            ->count(DB::raw('id'));
        $distinctappcnt = DB::table('appointments')
            ->whereBetween('created_at', [$start_date, $end_date])
            ->count(DB::raw('distinct patient_id'));
        $patientsecondarrival = $appointmentcnt - $distinctappcnt;

        return view('reports/monthly_static_report', [
            'title' => $user->name,
            'noemp' => $no_of_employees,
            'avgpatient' => $avg_patient,
            'wardcnt' => $ward_count,
            'bedcnt' => $bed_count,
            'inpcnt' => $inpatient_count,
            'dispcnt' => $discharged_patinet_count,
            'admindaycnt' => $admindaycnt,
            'doctordaycnt' => $doctordaycnt,
            'fa' => $distinctappcnt,
            'sa' => $patientsecondarrival,
            'total' => $appointmentcnt
        ]);
    }
    public function view_out_patient_report()
    {
        $user = Auth::user();
        return view('reports/out_patient_report', ['title' => $user->name]);
    }
    public function view_attendance_report()
    {
        $user = Auth::user();
        return view('reports/attendance_reports', ['title' => $user->name]);
    }

    public function view_ward_report()
    {
        $user = Auth::user();
        return view('reports/ward_reports', ['title' => $user->name]);
    }
    public function gen_att_reports(Request $request)
    {
        $user = Auth::user();
        $start_date = date_format(date_create($request->start), "Y/m/d");
        $end_date = date_format(date_create($request->end), "Y/m/d");

        if ($request->type == "All") {
            $data = DB::table('attendances')
                ->join('users', 'attendances.user_id', '=', 'users.id')
                ->select(
                    'users.id as id',
                    'attendances.start as start',
                    'attendances.end as end',
                    'users.name as name',
                    'users.user_type as type',
                    DB::raw('count(CASE WHEN HOUR(TIMEDIFF(attendances.end, attendances.start )) > 7 THEN 1 ELSE NULL END) AS attended'),
                    DB::raw('count(CASE WHEN HOUR(TIMEDIFF(attendances.end, attendances.start )) < 5 THEN 1 ELSE NULL END) AS shortleave')
                )
                ->whereBetween('attendances.start', [$start_date, $end_date])
                ->groupBy('id')
                ->get();
        }


        elseif ($request->type == "My Attendance") {

            $data = DB::table('attendances')
                ->join('users', 'attendances.user_id', '=', 'users.id')
                ->select(
                    'users.id as id',
                    'attendances.start as start',
                    'attendances.end as end',
                    'users.name as name',
                    'users.user_type as type',
                    DB::raw('count(CASE WHEN HOUR(TIMEDIFF(attendances.end, attendances.start )) > 7 THEN 1 ELSE NULL END) AS attended'),
                    DB::raw('count(CASE WHEN HOUR(TIMEDIFF(attendances.end, attendances.start )) < 5 THEN 1 ELSE NULL END) AS shortleave')
                )
                ->whereBetween('attendances.start', [$start_date, $end_date])
                ->where('attendances.user_id', $user->id)
                ->groupBy('id')
                ->get();
            // $data=User::find($user->id);

        }


        elseif ($request->type == "Doctors") {
            $data = DB::table('attendances')
                ->join('users', 'attendances.user_id', '=', 'users.id')
                ->select(
                    'users.id as id',
                    'attendances.start as start',
                    'attendances.end as end',
                    'users.name as name',
                    'users.user_type as type',
                    DB::raw('count(CASE WHEN HOUR(TIMEDIFF(attendances.end, attendances.start )) > 7 THEN 1 ELSE NULL END) AS attended'),
                    DB::raw('count(CASE WHEN HOUR(TIMEDIFF(attendances.end, attendances.start )) < 5 THEN 1 ELSE NULL END) AS shortleave')
                )
                ->whereBetween('attendances.start', [$start_date, $end_date])
                ->where('users.user_type', 'doctor')
                ->groupBy('id')
                ->get();
        }


        elseif ($request->type == "General Staff") {
            $data = DB::table('attendances')
                ->join('users', 'attendances.user_id', '=', 'users.id')
                ->select(
                    'users.id as id',
                    'attendances.start as start',
                    'attendances.end as end',
                    'users.name as name',
                    'users.user_type as type',
                    DB::raw('count(CASE WHEN HOUR(TIMEDIFF(attendances.end, attendances.start )) > 7 THEN 1 ELSE NULL END) AS attended'),
                    DB::raw('count(CASE WHEN HOUR(TIMEDIFF(attendances.end, attendances.start )) < 5 THEN 1 ELSE NULL END) AS shortleave')
                )
                ->whereBetween('attendances.start', [$start_date, $end_date])
                ->whereIn('users.user_type', ['pharmacist', 'general'])
                ->groupBy('id')
                // ->where('users.user_type','pharmacist')
                ->get();
        } else {
            $data = collect();
        }

        return view('reports/attendance-reports/all_attendance_report', ['title' => $user->name, 'details' => $data, 'start' => $request->start, 'end' => $request->end, 'type' => $request->type]);
    }

    public function all_print_preview(Request $request)
    {

        $start_date = date_format(date_create($request->start), "Y/m/d");
        $end_date = date_format(date_create($request->end), "Y/m/d");

        $user = Auth::user();
        //get the attendance of all type
        if ($request->type == "All") {
            $data = DB::table('attendances')
                ->join('users', 'attendances.user_id', '=', 'users.id')
                ->select(
                    'users.id as id',
                    'attendances.start as start',
                    'users.name as name',
                    'users.user_type as type',
                    DB::raw('count(CASE WHEN HOUR(TIMEDIFF(attendances.end, attendances.start )) > 7 THEN 1 ELSE NULL END) AS attended'),
                    DB::raw('count(CASE WHEN HOUR(TIMEDIFF(attendances.end, attendances.start )) < 5 THEN 1 ELSE NULL END) AS shortleave')
                )
                ->whereBetween('attendances.start', [$start_date, $end_date])
                ->groupBy('id')
                ->orderBy('type')
                ->get();
        }

        //get the attendance of mine
        if ($request->type == "My Attendance") {
            $data = DB::table('attendances')
                ->join('users', 'attendances.user_id', '=', 'users.id')
                ->select(
                    'users.id as id',
                    'attendances.start as start',
                    'users.name as name',
                    'users.user_type as type',
                    DB::raw('count(CASE WHEN HOUR(TIMEDIFF(attendances.end, attendances.start )) > 7 THEN 1 ELSE NULL END) AS attended'),
                    DB::raw('count(CASE WHEN HOUR(TIMEDIFF(attendances.end, attendances.start )) < 5 THEN 1 ELSE NULL END) AS shortleave')
                )
                ->whereBetween('attendances.start', [$start_date, $end_date])
                ->where('attendances.user_id', $user->id)
                ->groupBy('id')
                ->orderBy('type')
                ->get();
        }

        //get the attendance of doctor
        if ($request->type == "Doctors") {
            $data = DB::table('attendances')
                ->rightJoin('users', 'attendances.user_id', '=', 'users.id')
                ->select(
                    'users.id as id',
                    'attendances.start as start',
                    'users.name as name',
                    'users.user_type as type',
                    DB::raw('count(CASE WHEN HOUR(TIMEDIFF(attendances.end, attendances.start )) > 7 THEN 1 ELSE NULL END) AS attended'),
                    DB::raw('count(CASE WHEN HOUR(TIMEDIFF(attendances.end, attendances.start )) < 5 THEN 1 ELSE NULL END) AS shortleave')
                )
                ->whereBetween('attendances.start', [$start_date, $end_date])
                ->where('users.user_type', 'doctor')
                ->groupBy('id')
                ->orderBy('type')
                ->get();
        }

        //get the attendance of staff
        if ($request->type == "General Staff") {
            $data = DB::table('attendances')
                ->join('users', 'attendances.user_id', '=', 'users.id')
                ->select(
                    'users.id as id',
                    'attendances.start as start',
                    'users.name as name',
                    'users.user_type as type',
                    DB::raw('count(CASE WHEN HOUR(TIMEDIFF(attendances.end, attendances.start )) > 7 THEN 1 ELSE NULL END) AS attended'),
                    DB::raw('count(CASE WHEN HOUR(TIMEDIFF(attendances.end, attendances.start )) < 5 THEN 1 ELSE NULL END) AS shortleave')
                )
                ->whereBetween('attendances.start', [$start_date, $end_date])
                ->whereIn('users.user_type', ['pharmacist', 'general'])
                ->groupBy('id')
                ->orderBy('type')
                // ->where('users.user_type','pharmacist')
                ->get();
        }

        //return to printing page view
        return view('reports/attendance-reports/all_print_preview', ['title' => $user->name, 'details' => $data]);
    }

    public function clinicPdf(Request $request)
    {
        $query = DB::table('clinic_patient')
            ->join('patients', 'patients.id', '=', 'clinic_patient.patients_id')
            ->leftJoin('prescriptions', 'prescriptions.patient_id', '=', 'patients.id')
            ->select(
                'patients.id as patient_id',
                'patients.name as name',
                'prescriptions.created_at as date',
                'prescriptions.diagnosis as diagnosis'
            );
        if ($request->filled('clinic_id')) {
            $query->where('clinic_patient.clinic_id', $request->clinic_id);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('prescriptions.created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('prescriptions.created_at', '<=', $request->end_date);
        }
        $data = $query->orderBy('patients.id')->get();
        $clinicName = $request->clinic_name ?? 'عيادة عامة';
        if ($request->filled('clinic_id')) {
            $clinic = Clinic::find($request->clinic_id);
            if ($clinic) {
                $clinicName = $clinic->name_eng;
            }
        }
        $pdf = Pdf::loadView('reports.pdf.clinic', [
            'title' => 'تقرير العيادة',
            'data' => $data,
            'clinic_name' => $clinicName,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);
        return $pdf->download('clinic-report-' . date('Y-m-d') . '.pdf');
    }

    public function attendancePdf(Request $request)
    {
        $start = $request->start_date ?? now()->startOfMonth()->format('Y/m/d');
        $end = $request->end_date ?? now()->format('Y/m/d');
        $data = DB::table('attendances')
            ->join('users', 'attendances.user_id', '=', 'users.id')
            ->select(
                'users.name as name',
                'users.user_type as type',
                DB::raw('count(CASE WHEN HOUR(TIMEDIFF(attendances.end, attendances.start )) > 7 THEN 1 ELSE NULL END) AS attended'),
                DB::raw('count(CASE WHEN HOUR(TIMEDIFF(attendances.end, attendances.start )) < 5 THEN 1 ELSE NULL END) AS shortleave')
            )
            ->whereBetween('attendances.start', [$start, $end])
            ->groupBy('id')
            ->get();
        $pdf = Pdf::loadView('reports.pdf.attendance', [
            'title' => 'تقرير الحضور',
            'data' => $data,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);
        return $pdf->download('attendance-report-' . date('Y-m-d') . '.pdf');
    }

    public function inpatientPdf(Request $request)
    {
        $reportDate = $request->report_date ?? now()->toDateString();
        $data = DB::table('inpatients')
            ->join('patients', 'patients.id', '=', 'inpatients.patient_id')
            ->leftJoin('wards', 'wards.id', '=', 'inpatients.ward_id')
            ->select(
                'inpatients.patient_id as patient_id',
                'patients.name as name',
                'wards.ward_no as ward',
                'inpatients.house_doctor as doctor',
                'inpatients.created_at as date',
                'inpatients.discharged as status'
            )
            ->whereDate('inpatients.created_at', $reportDate)
            ->orderBy('inpatients.id')
            ->get();
        $pdf = Pdf::loadView('reports.pdf.inpatient', [
            'title' => 'تقرير المرضى المنوّمين',
            'data' => $data,
            'report_date' => $reportDate,
        ]);
        return $pdf->download('inpatient-report-' . date('Y-m-d') . '.pdf');
    }

    public function monthlyPdf(Request $request)
    {
        $year = (int) ($request->year ?? now()->year);
        $month = (int) ($request->month ?? now()->month);
        $monthStart = Carbon::create($year, $month, 1)->startOfDay();
        $monthEnd = (clone $monthStart)->endOfMonth();
        $start = $monthStart->format('Y/m/d');
        $end = $monthEnd->format('Y/m/d');
        $noemp = DB::table('attendances')
            ->whereBetween('attendances.start', [$start, $end])
            ->count(DB::raw('DISTINCT user_id'));
        $appcnt = DB::table('appointments')
            ->whereBetween('created_at', [$start, $end])
            ->count(DB::raw('id'));
        $distinctapp = DB::table('appointments')
            ->whereBetween('created_at', [$start, $end])
            ->count(DB::raw('distinct patient_id'));
        $daysInMonth = $monthStart->daysInMonth;
        $avg = $daysInMonth > 0 ? (int) ceil($appcnt / $daysInMonth) : 0;
        $wardcnt = DB::table('wards')->count(DB::raw('distinct ward_no'));
        $bedcnt = DB::table('wards')->sum('beds');
        $inpcnt = DB::table('inpatients')
            ->whereBetween('created_at', [$start, $end])
            ->count(DB::raw('discharged'));
        $dispcnt = DB::table('inpatients')
            ->where('discharged', '=', 'YES')
            ->whereBetween('created_at', [$start, $end])
            ->count(DB::raw('discharged'));
        $doctordaycnt = DB::table('attendances')
            ->join('users', 'users.id', '=', 'attendances.user_id')
            ->whereBetween('attendances.start', [$start, $end])
            ->where('users.user_type', '=', 'doctor')
            ->count(DB::raw('start'));
        $stats = [
            'عدد الموظفين الحاضرين' => $noemp,
            'متوسط المرضى يومياً' => $avg,
            'عدد الأجنحة' => $wardcnt,
            'عدد الأسرّة' => $bedcnt,
            'المرضى المنوّمون' => $inpcnt,
            'المرضى المُخرجون' => $dispcnt,
            'أيام الأطباء' => $doctordaycnt,
            'مرضى أول مرة' => $distinctapp,
            'مرضى العودة' => $appcnt - $distinctapp,
            'إجمالي المواعيد' => $appcnt,
        ];
        $pdf = Pdf::loadView('reports.pdf.monthly', [
            'title' => "التقرير الشهري - $month/$year",
            'year' => $year,
            'month' => $month,
            'stats' => $stats,
        ]);
        return $pdf->download("monthly-report-{$year}-{$month}.pdf");
    }
}
