@extends('template.main')

@section('title', __('Invoice Report'))
@section('content_title', __('Invoice Report'))
@section('content_description', $month . '/' . $year)

@section('main_content')
<div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-10">
        <div class="box box-info">
            <div class="box-body">
                <form method="GET" class="form-inline">
                    <input type="number" name="year" value="{{$year}}" class="form-control" min="2020" max="2100">
                    <input type="number" name="month" value="{{$month}}" class="form-control" min="1" max="12">
                    <button type="submit" class="btn btn-primary">{{__('Show')}}</button>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="info-box bg-aqua">
                    <span class="info-box-icon"><i class="fas fa-file-invoice"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{__('Total Invoices')}}</span>
                        <span class="info-box-number">{{$totalInvoices}}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-green">
                    <span class="info-box-icon"><i class="fas fa-money-bill"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{__('Total Amount')}}</span>
                        <span class="info-box-number">{{number_format($totalAmount, 0)}} YER</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-yellow">
                    <span class="info-box-icon"><i class="fas fa-hand-holding-usd"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{__('Paid Amount')}}</span>
                        <span class="info-box-number">{{number_format($totalPaid, 0)}} YER</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-red">
                    <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{__('Remaining')}}</span>
                        <span class="info-box-number">{{number_format($totalRemaining, 0)}} YER</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="box">
            <div class="box-header"><h3 class="box-title">{{__('Invoices')}}</h3></div>
            <div class="box-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>{{__('Invoice Number')}}</th>
                            <th>{{__('Patient')}}</th>
                            <th>{{__('Total Amount')}}</th>
                            <th>{{__('Paid Amount')}}</th>
                            <th>{{__('Status')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoices as $inv)
                        <tr>
                            <td><a href="{{route('invoices.show', $inv->id)}}">{{$inv->invoice_number}}</a></td>
                            <td>{{$inv->patient->name ?? '-'}}</td>
                            <td>{{number_format($inv->total_amount, 2)}} YER</td>
                            <td>{{number_format($inv->paid_amount, 2)}} YER</td>
                            <td>{{$inv->status}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-1"></div>
</div>
@endsection
