@extends('template.main')

@section('title', __('New Invoice'))
@section('content_title', __('New Invoice'))
@section('content_description', __('Create a new invoice'))

@section('main_content')
<div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-10">
        <form method="POST" action="{{route('invoices.store')}}">
            @csrf
            <div class="box box-info">
                <div class="box-header"><h3 class="box-title">{{__('Invoice Details')}}</h3></div>
                <div class="box-body">
                    <div class="form-group">
                        <label>{{__('Patient')}}</label>
                        <select name="patient_id" class="form-control" required>
                            <option value="">{{__('Select Patient')}}</option>
                            @foreach ($patients as $p)
                                <option value="{{$p->id}}">{{$p->name}} ({{$p->id}})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{__('Notes')}}</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
            </div>

            <div class="box box-success">
                <div class="box-header">
                    <h3 class="box-title">{{__('Items')}}</h3>
                    <button type="button" class="btn btn-sm btn-success pull-right" onclick="addRow()">
                        <i class="fas fa-plus"></i> {{__('Add Item')}}
                    </button>
                </div>
                <div class="box-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{__('Description')}}</th>
                                <th>{{__('Quantity')}}</th>
                                <th>{{__('Unit Price')}}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="items-body">
                            <tr>
                                <td><input type="text" name="items[0][description]" class="form-control" required></td>
                                <td><input type="number" name="items[0][quantity]" class="form-control" value="1" min="1" required></td>
                                <td><input type="number" step="0.01" name="items[0][unit_price]" class="form-control" required></td>
                                <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()"><i class="fas fa-trash"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary btn-lg">{{__('Save')}}</button>
                    <a href="{{route('invoices.index')}}" class="btn btn-default btn-lg">{{__('Cancel')}}</a>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-1"></div>
</div>

<script>
    var rowIdx = 1;
    function addRow() {
        var html = '<tr>' +
            '<td><input type="text" name="items['+rowIdx+'][description]" class="form-control" required></td>' +
            '<td><input type="number" name="items['+rowIdx+'][quantity]" class="form-control" value="1" min="1" required></td>' +
            '<td><input type="number" step="0.01" name="items['+rowIdx+'][unit_price]" class="form-control" required></td>' +
            '<td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest(\'tr\').remove()"><i class="fas fa-trash"></i></button></td>' +
            '</tr>';
        document.getElementById('items-body').insertAdjacentHTML('beforeend', html);
        rowIdx++;
    }
</script>
@endsection
