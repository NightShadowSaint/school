@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<p style="float: right; margin-right: 20px;">Época: {{ $currentyearandseason->nome_epoca }} | Ano-letivo: {{ $currentyearandseason->anoLetivo }}</p>
<h1>Calendário</h1>

<form action="{{route('clnd.store')}}" method="POST">
    {{ csrf_field() }}

    <div class="form-group row">
        <label for="nome_aval_input" class="col-md-4 col-form-label text-md-right">{{ __('Nome da Avaliação') }}</label>

        <div class="col-md-6">
            <input id="nome_aval_input" type="nome_aval_input" class="form-control" name="nome_aval_input" value="{{ old('nome_aval_input') }}" placeholder="Dê um nome à avaliação">
        </div>
    </div>

    <div class="form-group row">
        <label for="uc_aval_input" class="col-md-4 col-form-label text-md-right">{{ __('Unidade Curricular') }}</label>

        <div class="col-md-6">
            <select class="form-control input-lg dynamic" id="uc_aval_input" name="uc_aval_input">
                <option disabled selected value> -- Selecione uma opção -- </option>
                @foreach ($selects as $select1)
                <option value="{{ $select1->id_uc }}" id="active">{{ $select1->nome_uc }}</option>
                @endforeach
            </select>
            <a href="#" onclick="clearSelected('#uc_aval_input');">Clear</a>
        </div>
    </div>

    <div class="form-group row">
        <label for="data_aval_input" class="col-md-4 col-form-label text-md-right">Data</label>
        <div class="col-md-6">
            <input name="data_aval_input" class="form-control" type="datetime-local" value="{{ old('data_aval_input') }}" id="data_aval_input">
            <a href="#" onclick="clearSelected('#data_aval_input');">Clear</a>
        </div>
    </div>

    <div class="form-group row">
        <label for="epoca_aval_input" class="col-md-4 col-form-label text-md-right">{{ __('Época') }}</label>

        <div class="col-md-6">
            <select class="form-control input-lg dynamic" id="epoca_aval_input" name="epoca_aval_input">
                <option disabled selected value> -- Selecione uma opção -- </option>
                @foreach ($select2s as $select2)
                <option value="{{ $select2->id_epoca }}" id="active">{{ $select2->nome_epoca }}</option>
                @endforeach
            </select>
            <a href="#" onclick="clearSelected('#epoca_aval_input');">Clear</a>
        </div>
    </div>

    <div class="form-group row">
        <label for="uc_aval_input" class="col-md-4 col-form-label text-md-right">{{ __('Tipo de Avaliação') }}</label>

        <div class="col-md-6">
            <div>
                <select class="form-control input-lg dynamic" id="uc_tipoaval_input" name="uc_tipoaval_input">
                    <option disabled selected value> -- Selecione uma opção -- </option>
                    @foreach ($select3s as $select3)
                    <option value="{{ $select3->id_tipoaval }}" id="active">{{ $select3->nome_tipoaval }}</option>
                    @endforeach
                </select>
                <a href="#" onclick="clearSelected('#uc_tipoaval_input');">Clear</a>
            </div>
            <br>
            <button class="btn btn-primary" type="submit" id="submitbtn">Submit</button>

            @if($errors->any())
            <div class="alert alert-danger">
                {{$errors->first()}}
            </div>
            @endif
            @if(session()->has('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
            </div>
            @endif
        </div>
    </div>
</form>

<table id="avalsTable" class="table table-bordered table-sm">
    <thead>
        <th>Avaliação</th>
        <th>Data</th>
        <th>Época</th>
        <th>Tipo</th>
        <th>Unidade Curricular</th>
    </thead>
    <tbody>
        @foreach ($avls as $avl)
        <tr>
            <td>{{ $avl->nome_avl }}</td>
            <td>{{ $avl->data_aval }}</td>
            <td>{{ $avl->nome_epoca }}</td>
            <td>{{ $avl->nome_tipoaval }}</td>
            <td>{{ $avl->nome_uc }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<script type="text/javascript">
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.input-lg').select2({
            allowClear: true,
        });
        $('#avalsTable').DataTable({
            "pagingType": "full_numbers",
            "language": {
                "url": "{{ asset('tableLanguage/pt-pt.json') }}"
            },
            "pageLength": 5,
            "lengthMenu": [
                [5, 10, 20, -1],
                [5, 10, 20, 'Todos']
            ]
        })
        $('.dataTables_length').addClass('bs-select');
    })

    function clearSelected(selected) {
        $(selected).val(null).trigger('change');
    }
</script>
@endsection