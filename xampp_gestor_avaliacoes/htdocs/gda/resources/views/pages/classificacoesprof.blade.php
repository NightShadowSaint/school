@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<p style="float: right; margin-right: 20px;">Época: {{ $currentyearandseason->nome_epoca }} | Ano-letivo: {{ $currentyearandseason->anoLetivo }}</p>
<h1>Classificações</h1>

<form action="{{route('class.store')}}" method="POST">
    {{ csrf_field() }}

    <div class="form-group row">
        <label for="aval_class_input" class="col-md-4 col-form-label text-md-right">{{ __('Avaliação') }}</label>

        <div class="col-md-6">
            <select class="form-control input-lg dynamic" id="aval_class_input" name="aval_class_input" data-dependent="aluno_class_input" style="display: table;">
                <option disabled selected value> -- Selecione uma opção -- </option>
                @foreach ($avals as $aval)
                <option value="{{ $aval->id_avaliacao }}">{{ $aval->nome_avl  }} | UC: {{ $aval->nome_uc }}
                        | Época: {{ $aval->nome_epoca }} | Data: {{ $aval->data_aval }} | Tipo: {{ $aval->nome_tipoaval }}</option>
                @endforeach
            </select>
            <a href="#" onclick="clearSelected('#aval_class_input');">Clear</a>
        </div>
    </div>

    <div class="form-group row">
        <label for="aluno_class_input" class="col-md-4 col-form-label text-md-right">{{ __('Aluno') }}</label>

        <div class="col-md-6">
            <select class="form-control input-lg" id="aluno_class_input" name="aluno_class_input">
                <option disabled selected value> -- Selecione uma opção -- </option>
            </select>
            <a href="#" onclick="clearSelected('#aluno_class_input');">Clear</a>
        </div>
    </div>

    <div class="form-group row">
        <label for="valor_class_input" class="col-md-4 col-form-label text-md-right">{{ __('Valor') }}</label>

        <div class="col-md-6">
            <input id="valor_class_input" type="decimal" class="form-control" name="valor_class_input" value="{{ old('valor_class_input') }}" placeholder="Dê um valor à classificação" style="width: 110%;">


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
    </div>
</form>

<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#aval_class_input').on("change", function() {
            $('#aluno_class_input').val(null).trigger('change');
        })
        $('.dynamic').change(function() {
            if ($(this).val() != '') {
                var select = $(this).attr("id");
                var value = $(this).val();
                var dependent = $(this).data('dependent');
                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url: "{{ route ('class.fetch') }}",
                    type: "POST",
                    data: {
                        'select': select,
                        'value': value,
                        '_token': _token,
                        'dependent': dependent
                    },
                    success: function(result) {
                        $('#aluno_class_input').empty();
                        $('#aluno_class_input').append('<option disabled selected value> -- Selecione uma opção -- </option>');
                        for (var i = 0; i < result.aluns.length; i++) {
                            $('#aluno_class_input').append('<option value="' + result.aluns[i].id_aluno + '">' + result.aluns[i].nome_aluno + '</option>');
                        }
                    }
                })
            }
        });

        $('.input-lg').select2({
            width: '110%'
        });
    })
    function clearSelected(selected) {
        $(selected).val(null).trigger('change');
    }
</script>
@endsection