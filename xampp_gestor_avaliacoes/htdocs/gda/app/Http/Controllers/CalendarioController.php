<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Calendario;
use SebastianBergmann\Environment\Console;

class CalendarioController extends Controller
{
    public function store(Request $request)
    {
        $nomeIsReal = $request->input('nome_aval_input');
        $ucIsReal = $request->input('uc_aval_input');
        $dataIsReal = $request->input('data_aval_input');
        $epocaIsReal = $request->input('epoca_aval_input');
        $tipoIsReal = $request->input('uc_tipoaval_input');

        if ($nomeIsReal == null) {
            return redirect()->back()->withErrors('Dê um nome à avaliação por favor');
        } else if ($ucIsReal == null) {
            return redirect()->back()->withErrors('Selecione a unidade curricular por favor');
        } else if ($dataIsReal == null) {
            return redirect()->back()->withErrors('Selecione a data por favor');
        } else if ($epocaIsReal == null) {
            return redirect()->back()->withErrors('Selecione a época por favor');
        } else if ($tipoIsReal == null) {
            return redirect()->back()->withErrors('Selecione o tipo de avaliação por favor');
        } else {

            if ((DB::table('avaliacoes')->select('id_avaliacao')->first()) == null) {
                $getidforavaliacao = 0;
            } else {
                $getidforavaliacao = (DB::table('avaliacoes')->latest('id_avaliacao')->first()->{'id_avaliacao'}) + 1;
            }

            $checkDB = DB::table('avaliacoes')->select('*')
                ->where("avaliacoes.nome_avl", "=", $request->input('nome_aval_input'))
                ->where("avaliacoes.data_aval", "=", $request->input('data_aval_input'))
                ->where("avaliacoes.id_ucFuncionamento", "=", $request->input('uc_aval_input'))
                ->where("avaliacoes.id_tipoaval", "=", $request->input('uc_tipoaval_input'))
                ->where("avaliacoes.id_epoca", "=", $request->input('epoca_aval_input'))
                ->first();
            if ($checkDB == null) {
                $avaliacaodata = array(
                    'id_avaliacao' => $getidforavaliacao,
                    'nome_avl' => $request->input('nome_aval_input'),
                    'data_aval' => $request->input('data_aval_input'),
                    'id_ucFuncionamento' => $request->input('uc_aval_input'),
                    'id_epoca' => $request->input('epoca_aval_input'),
                    'id_tipoaval' => $request->input('uc_tipoaval_input')
                );
                DB::table('avaliacoes')->insert($avaliacaodata);

                return redirect()->back()->with('message', 'Avaliação registada com sucesso!');;
            } else {
                return redirect()->back()->withErrors('Avaliação já existente');
            }
        }
    }

    public function calendario()
    {
        $dt = date('Y-m-d');

        $currentyearandseason = DB::table('epocaeanoletivoatual')
            ->join('epoca', 'epoca.id_epoca', '=', 'epocaeanoletivoatual.id_epoca')
            ->join('anoletivo', 'anoletivo.id_anoLetivo', '=', 'epocaeanoletivoatual.id_anoLetivo')
            ->select('epoca.nome_epoca', 'anoletivo.anoLetivo')
            ->first();

        if ((auth()->user()->type) == "Docente") {
            $user = auth()->user()->name;
            $useridget = DB::table('docente')->select('docente.id_docente')->where('docente.nome_docente', "=", $user)->first('id_docente');
            $userid = $useridget->id_docente;
            $selects = DB::table('uc_funcionamento')
                ->join('uc', 'uc.id_uc', '=', 'uc_funcionamento.id_uc')
                ->where('uc_funcionamento.id_docente', '=', $userid)
                ->select('uc.id_uc', 'uc.nome_uc')
                ->get();
            $select2s = DB::table('epoca')
                ->select('epoca.id_epoca', 'epoca.nome_epoca')
                ->get();
            $select3s = DB::table('tipo_avaliacao')
                ->select('tipo_avaliacao.id_tipoaval', 'tipo_avaliacao.nome_tipoaval')
                ->get();

            $avls = DB::table('avaliacoes')
                ->join('uc_funcionamento', 'uc_funcionamento.id_ucFuncionamento', '=', 'avaliacoes.id_ucFuncionamento')
                ->join('epocaeanoletivoatual', 'epocaeanoletivoatual.id_epocaEAnoLetivoAtual', '=', 'uc_funcionamento.id_epocaEAnoLetivoAtual')
                ->join('epoca', 'epoca.id_epoca', '=', 'avaliacoes.id_epoca')
                ->join('uc', 'uc.id_uc', '=', 'uc_funcionamento.id_uc')
                ->join('tipo_avaliacao', 'tipo_avaliacao.id_tipoaval', '=', 'avaliacoes.id_tipoaval')
                ->select('avaliacoes.nome_avl', 'avaliacoes.data_aval', 'epoca.nome_epoca', 'uc.nome_uc', 'tipo_avaliacao.nome_tipoaval')
                ->where('avaliacoes.data_aval', '>', $dt)
                ->get();
            return view('pages/calendario', ['selects' => $selects, 'select2s' => $select2s, 'select3s' => $select3s, 'currentyearandseason' => $currentyearandseason, 'avls' => $avls]);
        } else if ((auth()->user()->type) == "Aluno") {
            $user = auth()->user()->name;
            $useridget = DB::table('aluno')->select('aluno.id_aluno')->where('aluno.nome_aluno', "=", $user)->first('id_aluno');
            $userid = $useridget->id_aluno;


            $avls = DB::table('avaliacoes')
                ->join('uc_funcionamento', 'uc_funcionamento.id_ucFuncionamento', '=', 'avaliacoes.id_ucFuncionamento')
                ->join('aluno_inscrito_ucfuncionamento', 'aluno_inscrito_ucfuncionamento.id_ucFuncionamento', '=', 'uc_funcionamento.id_ucFuncionamento')
                ->where('aluno_inscrito_ucfuncionamento.id_aluno', '=', $userid)
                ->join('epocaeanoletivoatual', 'epocaeanoletivoatual.id_epocaEAnoLetivoAtual', '=', 'uc_funcionamento.id_epocaEAnoLetivoAtual')
                ->join('epoca', 'epoca.id_epoca', '=', 'avaliacoes.id_epoca')
                ->join('uc', 'uc.id_uc', '=', 'uc_funcionamento.id_uc')
                ->join('tipo_avaliacao', 'tipo_avaliacao.id_tipoaval', '=', 'avaliacoes.id_tipoaval')
                ->select('avaliacoes.id_avaliacao', 'avaliacoes.nome_avl', 'avaliacoes.data_aval', 'epoca.nome_epoca', 'uc.nome_uc', 'tipo_avaliacao.nome_tipoaval')
                ->where('avaliacoes.data_aval', '>', $dt)
                ->get();
            return view('pages/calendarioaln', ['avls' => $avls, 'currentyearandseason' => $currentyearandseason]);
        }
    }
}
