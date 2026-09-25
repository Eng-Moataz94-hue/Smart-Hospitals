@extends('template.main')

@section('title', $title)

@section('content_title',__("Medicine Stock"))
@section('content_description',__("Stock levels by batch."))
@section('breadcrumbs')

<ol class="breadcrumb">
    <li><a href="{{route('dash')}}"><i class="fas fa-tachometer-alt"></i>{{__('Dashboard')}}</a></li>
    <li class="active">{{__('Here')}}</li>
</ol>
@endsection

@section('main_content')

<div class="row">
    <div class="col-md-4 col-sm-12">
        <div class="info-box bg-red">
            <span class="info-box-icon"><i class="fas fa-times-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">{{__('Out of stock medicines')}}</span>
                <span class="info-box-number">{{$outCount}}</span>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-12">
        <div class="info-box bg-yellow">
            <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">{{__('Low stock medicines')}} ({{__('below')}} {{$lowThreshold}})</span>
                <span class="info-box-number">{{$lowCount}}</span>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-12">
        <div class="info-box bg-aqua">
            <span class="info-box-icon"><i class="fas fa-calendar-times"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">{{__('Batches expiring within 30 days')}}</span>
                <span class="info-box-number">{{$expiringCount}}</span>
            </div>
        </div>
    </div>
</div>

<div style="padding:3%" class="row">
    <div class="col-md-1"></div>
    <div class="col-md-10">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">{{__('Medicine Stock Details')}}</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">

                    <br>
                    <table id="example1" class="table table-bordered table-striped dataTable" role="grid"
                        aria-describedby="example1_info">
                        <thead>
                            <tr>
                                <th>{{__('Medicine')}}</th>
                                <th>{{__('Batch No')}}</th>
                                <th>{{__('Expiry Date')}}</th>
                                <th>{{__('Received')}}</th>
                                <th>{{__('Remaining')}}</th>
                                <th>{{__('Status')}}</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $row)
                                @forelse ($row['batches'] as $batch)
                                <tr>
                                    <td>{{ucwords($row['medicine']->name_english)}}</td>
                                    <td>{{$batch->batch_number ?? '-'}}</td>
                                    <td>
                                        @if($batch->expiry_date && $batch->expiry_date < $today)
                                        <span class="badge bg-red">{{$batch->expiry_date}} ({{__('Expired')}})</span>
                                        @elseif($batch->expiry_date && $batch->expiry_date <= $soon)
                                        <span class="badge bg-yellow">{{$batch->expiry_date}}</span>
                                        @else
                                        {{$batch->expiry_date ?? '-'}}
                                        @endif
                                    </td>
                                    <td>{{$batch->initial_quantity}}</td>
                                    <td>{{$batch->quantity}}</td>
                                    <td>
                                        @if($row['total']<=0)
                                        <span class="badge bg-red">{{__('OUT')}}</span>
                                        @elseif($row['total']<$lowThreshold)
                                        <span class="badge bg-yellow">{{__('LOW')}} ({{$row['total']}})</span>
                                        @else
                                        <span class="badge bg-green">{{__('OK')}} ({{$row['total']}})</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td>{{ucwords($row['medicine']->name_english)}}</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>0</td>
                                    <td><span class="badge bg-red">{{__('OUT')}}</span></td>
                                </tr>
                                @endforelse
                            @endforeach
                        </tbody>
                        <tfoot>
                            <th>{{__('Medicine')}}</th>
                            <th>{{__('Batch No')}}</th>
                            <th>{{__('Expiry Date')}}</th>
                            <th>{{__('Received')}}</th>
                            <th>{{__('Remaining')}}</th>
                            <th>{{__('Status')}}</th>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-1"></div>
    <!-- /.box-body -->
</div>
@endsection
@section('optional_scripts')
<script>
    $(function () {

        $('#example1').DataTable({
            'paging': true,
            'lengthChange': true,
            'searching': true,
            'ordering': true,
            'info': true,
            'autoWidth': false
        })
    })

</script>
@endsection
