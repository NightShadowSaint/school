@extends('layouts.app')

@section('content')
<p style="float: right; margin-right: 20px;">Época: {{ $currentyearandseason->nome_epoca }} | Ano-letivo: {{ $currentyearandseason->anoLetivo }}</p>
<h1>Calendário</h1>

<table id="avalsTable" class="table table-bordered">
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
            <td>
                <a class="text-primary" href="./inscricao/{{ $avl->id_avaliacao }}"> {{ $avl->nome_avl }} </a>
            </td>
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