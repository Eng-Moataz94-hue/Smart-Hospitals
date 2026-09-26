@extends('template.main')

@section('title', __('Invoices'))
@section('content_title', __('Invoices'))
@section('content_description', __('All invoices'))
@section('breadcrumbs')
<ol class="breadcrumb">
    <li><a href="{{route('dash')}}"><i class="fas fa-tachometer-alt"></i>{{__('Dashboard')}}</a></li>
    <li class="active">{{__('Invoices')}}</li>
</ol>
@endsection

@section('main_content')
<div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-10">
        @if(session('success'))
            <div class="alert alert-success">{{session('success')}}</div>
        @endif
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">{{__('Filter')}}</h3>
                <a href="{{route('invoices.create')}}" class="btn btn-success pull-right">
                    <i class="fas fa-plus"></i> {{__('New Invoice')}}
                </a>
            </div>
            <div class="box-body">
                <form method="GET" class="form-inline">
                    <select name="status" class="form-control">
                        <option value="all">{{__('All')}}</option>
                        <option value="unpaid" @if(request('status')=='unpaid') selected @endif>{{__('Unpaid')}}</option>
                        <option value="partial" @if(request('status')=='partial') selected @endif>{{__('Partial')}}</option>
                        <option value="paid" @if(request('status')=='paid') selected @endif>{{__('Paid')}}</option>
                    </select>
                    <input type="date" name="from" class="form-control" value="{{request('from')}}">
                    <input type="date" name="to" class="form-control" value="{{request('to')}}">
                    <button type="submit" class="btn btn-primary">{{__('Filter')}}</button>
                </form>
            </div>
        </div>
        <div class="box">
            <div class="box-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>{{__('Invoice Number')}}</th>
                            <th>{{__('Patient')}}</th>
                            <th>{{__('Date')}}</th>
                            <th>{{__('Total Amount')}}</th>
                            <th>{{__('Paid Amount')}}</th>
                            <th>{{__('Remaining')}}</th>
                            <th>{{__('Status')}}</th>
                            <th>{{__('Actions')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoices as $inv)
                        <tr>
                            <td>{{$inv->invoice_number}}</td>
                            <td>{{$inv->patient->name ?? '-'}}</td>
                            <td>{{$inv->created_at->format('Y-m-d')}}</td>
                            <td>{{number_format($inv->total_amount, 2)}} YER</td>
                            <td>{{number_format($inv->paid_amount, 2)}} YER</td>
                            <td>{{number_format($inv->total_amount - $inv->paid_amount, 2)}} YER</td>
                            <td>
                                @if($inv->status=='paid')<span class="badge bg-green">{{__('Paid')}}</span>
                                @elseif($inv->status=='partial')<span class="badge bg-yellow">{{__('Partial')}}</span>
                                @else<span class="badge bg-red">{{__('Unpaid')}}</span>@endif
                            </td>
                            <td><a href="{{route('invoices.show', $inv->id)}}" class="btn btn-sm btn-info">{{__('View')}}</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="text-center">{{$invoices->links()}}</div>
            </div>
        </div>
    </div>
    <div class="col-md-1"></div>
</div>
@endsection
