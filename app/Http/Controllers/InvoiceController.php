<?php

namespace App\Http\Controllers;

use App\Invoice;
use App\InvoiceItem;
use App\InvoicePayment;
use App\Patients;
use App\Service;
use App\Appointment;
use App\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $q = Invoice::with('patient')->orderBy('id', 'desc');
        
        if ($request->filled('status') && $request->status !== 'all') { $q->where('status', $request->status); }
        if ($request->filled('type') && $request->type !== 'all') { $q->where('invoice_type', $request->type); }
        if ($request->filled('from')) {
            $q->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $q->whereDate('created_at', '<=', $request->to);
        }
        $invoices = $q->paginate(20)->appends($request->query());
        
        return view('invoices.index', compact('invoices'));
    }

    public function show($id)
    {
        $invoice = Invoice::with(['items', 'payments', 'patient', 'appointment'])->findOrFail($id);
        $services = Service::all();
        return view('invoices.show', compact('invoice', 'services'));
    }

    public function create()
    {
        $patients = Patients::orderBy('name')->limit(200)->get();
        $services = Service::all();
        return view('invoices.create', compact('patients', 'services'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'patient_id' => 'required|exists:patients,id',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateNumber(),
            'patient_id' => $request->patient_id,
            'issued_at' => now(),
            'notes' => $request->notes,
        ]);

        foreach ($request->items as $row) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $row['description'],
                'quantity' => $row['quantity'],
                'unit_price' => $row['unit_price'],
            ]);
        }

        $invoice->recalculate();

        activity()->performedOn($invoice)->log('Invoice Created');

        return redirect()->route('invoices.show', $invoice->id)->with('success', __('Invoice created successfully'));
    }

    public function addItem(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        $this->validate($request, [
            'description' => 'required|string',
            'quantity' => 'required|numeric|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => $request->description,
            'quantity' => $request->quantity,
            'unit_price' => $request->unit_price,
        ]);

        return response()->json([
            'success' => true,
            'total' => $invoice->fresh()->total_amount,
            'remaining' => $invoice->fresh()->total_amount - $invoice->fresh()->paid_amount,
        ]);
    }

    public function removeItem($itemId)
    {
        $item = InvoiceItem::findOrFail($itemId);
        $invoiceId = $item->invoice_id;
        $item->delete();
        return redirect()->route('invoices.show', $invoiceId)->with('success', __('Item removed'));
    }

    public function recordPayment(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        $this->validate($request, [
            'amount' => 'required|numeric|min:0.01',
            'method' => 'nullable|string|in:cash,card,transfer',
        ]);

        InvoicePayment::create([
            'invoice_id' => $invoice->id,
            'amount' => $request->amount,
            'method' => $request->method ?? 'cash',
            'received_by' => Auth::id(),
            'notes' => $request->notes,
        ]);

        activity()->performedOn($invoice)->withProperties(['amount' => $request->amount])->log('Payment Recorded');

        return redirect()->route('invoices.show', $id)->with('success', __('Payment recorded'));
    }

    public function markPaid($id)
    {
        $invoice = Invoice::findOrFail($id);
        $remaining = $invoice->total_amount - $invoice->paid_amount;
        if ($remaining > 0) {
            InvoicePayment::create([
                'invoice_id' => $invoice->id,
                'amount' => $remaining,
                'method' => 'cash',
                'received_by' => Auth::id(),
            ]);
        }
        return redirect()->route('invoices.show', $id)->with('success', __('Invoice marked as paid'));
    }

    public function pdf($id)
    {
        try {
            $invoice = Invoice::with(['items', 'payments', 'patient', 'appointment'])->findOrFail($id);
            $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
            return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('invoices.index')
                ->with('error', __('Invoice not found. It may have been deleted.'));
        } catch (\Throwable $e) {
            \Log::error('Invoice PDF failed: ' . $e->getMessage(), ['invoice_id' => $id]);
            return redirect()->route('invoices.show', $id)
                ->with('error', __('PDF generation failed. Please try again.'));
        }
    }

    public function report(Request $request)
    {
        $year = (int) ($request->year ?? now()->year);
        $month = (int) ($request->month ?? now()->month);
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = (clone $start)->endOfMonth();

        $invoices = Invoice::with('patient')
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('id', 'desc')
            ->get();

        $totalInvoices = $invoices->count();
        $totalAmount = $invoices->sum('total_amount');
        $totalPaid = $invoices->sum('paid_amount');
        $totalRemaining = $totalAmount - $totalPaid;
        $paidCount = $invoices->where('status', 'paid')->count();
        $partialCount = $invoices->where('status', 'partial')->count();
        $unpaidCount = $invoices->where('status', 'unpaid')->count();

        return view('invoices.report', compact(
            'year', 'month', 'invoices',
            'totalInvoices', 'totalAmount', 'totalPaid', 'totalRemaining',
            'paidCount', 'partialCount', 'unpaidCount'
        ));
    }

    // ============ دوال داخلية للربط التلقائي ============

    public function createForPatient($patientId, $appointmentId = null, $invoiceType = 'consultation')
    {
        return Invoice::create([
            'invoice_number' => Invoice::generateNumber(),
            'patient_id'     => $patientId,
            'appointment_id' => $appointmentId,
            'invoice_type'   => $invoiceType,
            'issued_at'      => now(),
        ]);
    }

    public function addConsultationItem($invoice)
    {
        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'كشف طبي',
            'quantity' => 1,
            'unit_price' => 5000.00,
        ]);
    }

    public function addMedicineItem($invoice, $medicineName)
    {
        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => $medicineName,
            'quantity' => 1,
            'unit_price' => 500.00,
        ]);
    }

    public function addAppointmentItem($invoice)
    {
        InvoiceItem::create(['invoice_id' => $invoice->id, 'description' => 'حجز موعد', 'quantity' => 1, 'unit_price' => 1000.00]);
    }

    public function addWardItem($invoice, $wardNo, $days = 1)
    {
        InvoiceItem::create(['invoice_id' => $invoice->id, 'description' => 'يومية سرير - جناح ' . $wardNo, 'quantity' => $days, 'unit_price' => 10000.00]);
    }
}
