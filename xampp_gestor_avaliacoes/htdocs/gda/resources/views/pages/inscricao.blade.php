@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<p style="float: right; margin-right: 20px;">Época: {{ $currentyearandseason->nome_epoca }} | Ano-letivo: {{ $currentyearandseason->anoLetivo }}</p>
<h1>Inscrição</h1>

<form action="{{route('insc.store')}}" method="POST">
    {{ csrf_field() }}

    <div class="form-group row">
        <label for="aval_input" class="col-md-2 col-form-label text-md-right">{{ __('Avaliação') }}</label>

        <div class="col-md-2">
            <div>
                <select class="form-control input-lg dynamic" id="aval_input" name="aval_input" style="width:auto">
                    <option disabled selected value> -- Selecione uma opção -- </option>
                    @foreach ($inscs as $insc)
                    <option value="{{ $insc->id_avaliacao }}" id="active">{{ $insc->nome_avl  }} | UC: {{ $insc->nome_uc }}
                        | Época: {{ $insc->nome_epoca }} | Data: {{ $insc->data_aval }} | Tipo: {{ $insc->nome_tipoaval }}</option>
                    @endforeach
                </select>
                <a href="#" onclick="clearSelected();">Clear</a>
            </div>
            <button class="btn btn-primary" type="submit" id="searchbtn">Submit</button>

            @if($errors->any())
            <div class="alert alert-danger" style="width:300%">
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

<script type="text/javascript">
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#aval_input').select2();

        jobs = '{{ $autoSelectValue }}';

        flag = null;

        $('#aval_input option').each(function() {
            if (this.value == jobs) {
                flag = this.value;
                return false;
            } else {
                flag = null;
            }
        })
        $('#aval_input').val(flag).trigger('change');
    })

    function clearSelected() {
        $('#aval_input').val(null).trigger('change');
    }
</script>
@endsection