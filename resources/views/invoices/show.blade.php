@extends('template.main')

@section('title', $invoice->invoice_number)
@section('content_title', __('Invoice') . ' ' . $invoice->invoice_number)
@section('content_description', __('Invoice details'))
@section('breadcrumbs')
<ol class="breadcrumb">
    <li><a href="{{route('dash')}}"><i class="fas fa-tachometer-alt"></i>{{__('Dashboard')}}</a></li>
    <li><a href="{{route('invoices.index')}}">{{__('Invoices')}}</a></li>
    <li class="active">{{$invoice->invoice_number}}</li>
</ol>
@endsection

@section('main_content')
<div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-10">
        @if(session('success'))<div class="alert alert-success">{{session('success')}}</div>@endif

        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    {{__('Invoice')}} {{$invoice->invoice_number}}
                    @if($invoice->status=='paid')<span class="badge bg-green">{{__('Paid')}}</span>
                    @elseif($invoice->status=='partial')<span class="badge bg-yellow">{{__('Partial')}}</span>
                    @else<span class="badge bg-red">{{__('Unpaid')}}</span>@endif
                </h3>
                <div class="pull-right">
                    <a href="{{route('invoices.pdf', $invoice->id)}}" class="btn btn-danger" target="_blank">
                        <i class="fas fa-file-pdf"></i> {{__('Export PDF')}}
                    </a>
                    <a href="{{route('invoices.index')}}" class="btn btn-default">{{__('Back')}}</a>
                </div>
            </div>
            <div class="box-body">
                <h4>{{__('Patient')}}: {{$invoice->patient->name ?? '-'}} ({{$invoice->patient->id ?? '-'}})</h4>
                <h4>{{__('Date')}}: {{$invoice->created_at->format('Y-m-d H:i')}}</h4>
                @if($invoice->appointment)<h4>{{__('Appointment Number')}}: {{$invoice->appointment->number}}</h4>@endif
            </div>
        </div>

        <div class="box">
            <div class="box-header"><h3 class="box-title">{{__('Items')}}</h3></div>
            <div class="box-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>{{__('Description')}}</th>
                            <th>{{__('Quantity')}}</th>
                            <th>{{__('Unit Price')}}</th>
                            <th>{{__('Total')}}</th>
                            <th>{{__('Actions')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoice->items as $item)
                        <tr>
                            <td>{{$item->description}}</td>
                            <td>{{$item->quantity}}</td>
                            <td>{{number_format($item->unit_price, 2)}} YER</td>
                            <td>{{number_format($item->total, 2)}} YER</td>
                            <td>
                                <form action="{{route('invoices.removeItem', $item->id)}}" method="POST" style="display:inline" onsubmit="return confirm('{{__('Are you sure?')}}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="box box-success">
            <div class="box-header"><h3 class="box-title">{{__('Add Item')}}</h3></div>
            <div class="box-body">
                <form method="POST" action="{{route('invoices.addItem', $invoice->id)}}" class="form-inline">
                    @csrf
                    <input type="text" name="description" class="form-control" placeholder="{{__('Description')}}" required>
                    <input type="number" name="quantity" class="form-control" value="1" min="1" required>
                    <input type="number" step="0.01" name="unit_price" class="form-control" placeholder="{{__('Unit Price')}}" required>
                    <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> {{__('Add')}}</button>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="box box-warning">
                    <div class="box-header"><h3 class="box-title">{{__('Record Payment')}}</h3></div>
                    <div class="box-body">
                        <form method="POST" action="{{route('invoices.recordPayment', $invoice->id)}}">
                            @csrf
                            <div class="form-group">
                                <label>{{__('Amount')}}</label>
                                <input type="number" step="0.01" name="amount" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>{{__('Payment Method')}}</label>
                                <select name="method" class="form-control">
                                    <option value="cash">{{__('Cash')}}</option>
                                    <option value="card">{{__('Card')}}</option>
                                    <option value="transfer">{{__('Transfer')}}</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">{{__('Record Payment')}}</button>
                        </form>
                        <form method="POST" action="{{route('invoices.markPaid', $invoice->id)}}" style="margin-top:10px">
                            @csrf
                            <button type="submit" class="btn btn-success">{{__('Mark as Paid')}}</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="box box-info">
                    <div class="box-header"><h3 class="box-title">{{__('Summary')}}</h3></div>
                    <div class="box-body">
                        <h4>{{__('Total Amount')}}: <strong>{{number_format($invoice->total_amount, 2)}} YER</strong></h4>
                        <h4>{{__('Paid Amount')}}: <strong class="text-success">{{number_format($invoice->paid_amount, 2)}} YER</strong></h4>
                        <h4>{{__('Remaining')}}: <strong class="text-danger">{{number_format($invoice->total_amount - $invoice->paid_amount, 2)}} YER</strong></h4>
                        <hr>
                        <h5>{{__('Payment History')}}</h5>
                        <table class="table table-sm">
                            @foreach ($invoice->payments as $p)
                            <tr>
                                <td>{{$p->created_at->format('Y-m-d H:i')}}</td>
                                <td>{{number_format($p->amount, 2)}} YER</td>
                                <td>{{$p->method}}</td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-1"></div>
</div>
@endsection
