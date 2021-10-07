@extends('layouts.app')

@section('content')
<p style="float: right; margin-right: 20px;">Época: {{ $currentyearandseason->nome_epoca }} | Ano-letivo: {{ $currentyearandseason->anoLetivo }}</p>
<h1>Avaliações/Classificações</h1>

<table id="avalsTable" class="table table-bordered">
    <thead>
        <th>Unidade Curricular</th>
        <th>Docente</th>
        <th>Avaliação</th>
        <th>Época</th>
        <th>Tipo</th>
        <th>Data</th>
        <th>Avaliado</th>
        <th>Valor</th>
    </thead>
    <tbody>
        @foreach ($class as $class)
        <tr>
            <td>{{ $class->nome_uc }}</td>
            <td>{{ $class->nome_docente }}</td>
            <td>{{ $class->nome_avl }}</td>
            <td>{{ $class->nome_epoca }}</td>
            <td>{{ $class->nome_tipoaval }}</td>
            <td>{{ $class->data_aval }}</td>
            <td>{{ $class->avaliado }}</td>
            <td>{{ $class->valor }}</td>
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
</script>
@endsection