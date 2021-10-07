<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Calendario;
use App\Inscricao;
use SebastianBergmann\Environment\Console;

class ClassController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user()->name;
        $useridget = DB::table('docente')->select('docente.id_docente')->where('docente.nome_docente', "=", $user)->first('id_docente');
        $userid = $useridget->id_docente;

        $avalIsReal = $request->input('aval_class_input');
        $alunoIsReal = $request->input('aluno_class_input');
        $valorIsReal = $request->input('valor_class_input');

        if ($avalIsReal == null) {
            return redirect()->back()->withErrors('Selecione uma avaliação por favor');
        } else if ($alunoIsReal == null) {
            return redirect()->back()->withErrors('Selecione um aluno por favor');
        } else if ($valorIsReal == null) {
            return redirect()->back()->withErrors('Para avaliar o aluno tem que atribuir um valor');
        } else {
            $checkDB = DB::table('inscricao')->select('*')
                ->join('avaliacoes', 'avaliacoes.id_avaliacao', '=', 'inscricao.id_avaliacao')
                ->join('uc_funcionamento', 'uc_funcionamento.id_ucFuncionamento', '=', 'avaliacoes.id_ucFuncionamento')
                ->where("uc_funcionamento.id_docente", "=", $userid)
                ->where("inscricao.id_avaliacao", "=", $request->input('aval_class_input'))
                ->where("inscricao.id_aluno", "=", $request->input('aluno_class_input'))
                ->first()
                ->{'avaliado'};
            $getInscrID = DB::table('inscricao')->select('*')
                ->join('avaliacoes', 'avaliacoes.id_avaliacao', '=', 'inscricao.id_avaliacao')
                ->join('uc_funcionamento', 'uc_funcionamento.id_ucFuncionamento', '=', 'avaliacoes.id_ucFuncionamento')
                ->where("uc_funcionamento.id_docente", "=", $userid)
                ->where("inscricao.id_avaliacao", "=", $request->input('aval_class_input'))
                ->where("inscricao.id_aluno", "=", $request->input('aluno_class_input'))
                ->first()
                ->{'id_inscricao'};
            if ($checkDB == 'Não') {
                $valorArray = array('valor' => $request->input('valor_class_input'));
                $avaliadoArray = array('avaliado' => 'Sim');
                DB::table('inscricao')->where('id_inscricao', '=', $getInscrID)->update($valorArray);
                DB::table('inscricao')->where('id_inscricao', '=', $getInscrID)->update($avaliadoArray);

                return redirect()->back()->with('message', 'Classificação registada com sucesso!');;
            } else {
                return redirect()->back()->withErrors('Classificação já existente');
            }
        }
    }

    public function classificacoes()
    {
        $dt = date('Y-m-d');

        $currentyearandseason = DB::table('epocaeanoletivoatual')
            ->join('epoca', 'epoca.id_epoca', '=', 'epocaeanoletivoatual.id_epoca')
            ->join('anoletivo', 'anoletivo.id_anoLetivo', '=', 'epocaeanoletivoatual.id_anoLetivo')
            ->select('epoca.nome_epoca', 'anoletivo.anoLetivo')
            ->first();

        if ((auth()->user()->type) == "Docente") {
            $avals = DB::table('inscricao')
                ->join('aluno', 'aluno.id_aluno', '=', 'inscricao.id_aluno')
                ->join('avaliacoes', 'avaliacoes.id_avaliacao', '=', 'inscricao.id_avaliacao')
                ->join('uc_funcionamento', 'uc_funcionamento.id_ucFuncionamento', '=', 'avaliacoes.id_ucFuncionamento')
                ->join('uc', 'uc.id_uc', '=', 'uc_funcionamento.id_uc')
                ->join('aluno_inscrito_ucfuncionamento', 'aluno_inscrito_ucfuncionamento.id_ucFuncionamento', '=', 'uc_funcionamento.id_ucFuncionamento')
                ->where('aluno_inscrito_ucfuncionamento.id_aluno', '=', 'aluno.id_aluno')
                ->join('epocaeanoletivoatual', 'epocaeanoletivoatual.id_epocaEAnoLetivoAtual', '=', 'uc_funcionamento.id_epocaEAnoLetivoAtual')
                ->join('epoca', 'epoca.id_epoca', '=', 'epocaeanoletivoatual.id_epoca')
                ->join('tipo_avaliacao', 'tipo_avaliacao.id_tipoaval', '=', 'avaliacoes.id_tipoaval')
                ->select('avaliacoes.id_avaliacao', 'avaliacoes.nome_avl', 'epoca.nome_epoca', 'uc.nome_uc', 'avaliacoes.data_aval', 'epoca.id_epoca', 'epoca.nome_epoca', 'tipo_avaliacao.nome_tipoaval')
                ->where('avaliacoes.data_aval', '<=', $dt)
                ->where('inscricao.avaliado', '=', 'Não')
                ->get();


            return view('pages/classificacoesprof', ['avals' => $avals, 'currentyearandseason' => $currentyearandseason]);
        } else if ((auth()->user()->type) == "Aluno") {
            $user = auth()->user()->name;
            $useridget = DB::table('aluno')->select('aluno.id_aluno')->where('aluno.nome_aluno', "=", $user)->first('id_aluno');
            $userid = $useridget->id_aluno;
            $class = DB::table('inscricao')
                ->join('avaliacoes', 'avaliacoes.id_avaliacao', '=', 'inscricao.id_avaliacao')
                ->join('tipo_avaliacao', 'tipo_avaliacao.id_tipoaval', '=', 'avaliacoes.id_tipoaval')
                ->join('uc_funcionamento', 'uc_funcionamento.id_ucFuncionamento', '=', 'avaliacoes.id_ucFuncionamento')
                ->join('aluno_inscrito_ucfuncionamento', 'aluno_inscrito_ucfuncionamento.id_ucFuncionamento', '=', 'uc_funcionamento.id_ucFuncionamento')
                ->where('aluno_inscrito_ucfuncionamento.id_aluno', '=', $userid)
                ->join('docente', 'docente.id_docente', '=', 'uc_funcionamento.id_docente')
                ->join('epocaeanoletivoatual', 'epocaeanoletivoatual.id_epocaEAnoLetivoAtual', '=', 'uc_funcionamento.id_epocaEAnoLetivoAtual')
                ->join('epoca', 'epoca.id_epoca', '=', 'epocaeanoletivoatual.id_epoca')
                ->join('aluno', 'aluno.id_aluno', '=', 'inscricao.id_aluno')
                ->join('uc', 'uc.id_uc', '=', 'uc_funcionamento.id_uc')
                ->select('uc.nome_uc', 'docente.nome_docente', 'avaliacoes.nome_avl', 'inscricao.avaliado', 'tipo_avaliacao.nome_tipoaval', 'epoca.nome_epoca', 'avaliacoes.data_aval', 'inscricao.valor')->where('aluno.nome_aluno', "=", $user)
                ->get();
            return view('pages/classificacoesaln', ['class' => $class, 'currentyearandseason' => $currentyearandseason]);
        }
    }

    public function fetch(Request $request)
    {
        $value = $request->get('value');
        $aluns = DB::table('aluno')
            ->join('inscricao', 'inscricao.id_aluno', '=', 'aluno.id_aluno')
            ->join('avaliacoes', 'avaliacoes.id_avaliacao', '=', 'inscricao.id_avaliacao')
            ->join('uc_funcionamento', 'uc_funcionamento.id_ucFuncionamento', '=', 'avaliacoes.id_ucFuncionamento')
            ->join('aluno_inscrito_ucfuncionamento', 'aluno_inscrito_ucfuncionamento.id_ucFuncionamento', '=', 'uc_funcionamento.id_ucFuncionamento')
            ->where('aluno_inscrito_ucfuncionamento.id_aluno', '=', 'aluno.id_aluno')
            ->join('epocaeanoletivoatual', 'epocaeanoletivoatual.id_epocaEAnoLetivoAtual', '=', 'uc_funcionamento.id_epocaEAnoLetivoAtual')
            ->join('epoca', 'epoca.id_epoca', '=', 'epocaeanoletivoatual.id_epoca')
            ->select('aluno.id_aluno', 'aluno.nome_aluno')
            ->where('inscricao.id_avaliacao', '=', $value)
            ->where('inscricao.avaliado', '=', 'Não')
            ->get();
        return ['aluns' => $aluns];
    }
}
