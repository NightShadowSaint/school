<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use SebastianBergmann\Environment\Console;

class InscricaoController extends Controller
{
    public function index()
    {
        $user = auth()->user()->name;
        $useridget = DB::table('aluno')->select('aluno.id_aluno')->where('aluno.nome_aluno', "=", $user)->first('id_aluno');
        $userid = $useridget->id_aluno;
        $dt = date('Y-m-d');

        $currentyearandseason = DB::table('epocaeanoletivoatual')
            ->join('epoca', 'epoca.id_epoca', '=', 'epocaeanoletivoatual.id_epoca')
            ->join('anoletivo', 'anoletivo.id_anoLetivo', '=', 'epocaeanoletivoatual.id_anoLetivo')
            ->select('epoca.nome_epoca', 'anoletivo.anoLetivo')
            ->first();

        $inscrIDs = DB::table('inscricao')->pluck('id_avaliacao')->all();
        $inscrID2s = DB::table('epocaEAnoLetivoAtual')->pluck('id_epoca')->first();

        $inscs = DB::table('avaliacoes')
            ->join('uc_funcionamento', 'uc_funcionamento.id_ucFuncionamento', '=', 'avaliacoes.id_ucFuncionamento')
            ->join('aluno_inscrito_ucfuncionamento', 'aluno_inscrito_ucfuncionamento.id_ucFuncionamento', '=', 'uc_funcionamento.id_ucFuncionamento')
            ->where('aluno_inscrito_ucfuncionamento.id_aluno', '=', $userid)
            ->join('uc', 'uc.id_uc', '=', 'uc_funcionamento.id_uc')
            ->join('epocaEAnoLetivoAtual', 'epocaEAnoLetivoAtual.id_epocaEAnoLetivoAtual', '=', 'uc_funcionamento.id_epocaEAnoLetivoAtual')
            ->join('epoca', 'epoca.id_epoca', '=', 'avaliacoes.id_epoca')
            ->join('tipo_avaliacao', 'tipo_avaliacao.id_tipoaval', '=', 'avaliacoes.id_tipoaval')
            ->select('uc_funcionamento.id_ucFuncionamento', 'uc.nome_uc', 'avaliacoes.id_avaliacao', 'avaliacoes.nome_avl', 'avaliacoes.data_aval', 'epoca.id_epoca', 'epoca.nome_epoca', 'tipo_avaliacao.nome_tipoaval')
            ->where('avaliacoes.data_aval', '>', $dt)
            ->whereNotIn('id_avaliacao', $inscrIDs)
            ->where('avaliacoes.id_epoca', '=', $inscrID2s)
            ->get();

        return view('pages/inscricao', ['inscs' => $inscs, 'currentyearandseason' => $currentyearandseason, 'autoSelectValue' => null]);
    }

    public function store(Request $request)
    {
        $user = auth()->user()->name;
        $useridget = DB::table('aluno')->select('aluno.id_aluno')->where('aluno.nome_aluno', "=", $user)->first('id_aluno');
        $userid = $useridget->id_aluno;
        $avaliado = 'Não';

        $checkDB = DB::table('inscricao')->select('*')
            ->where("inscricao.id_aluno", "=", $userid)
            ->where("inscricao.id_avaliacao", "=", $request->input('aval_input'))->first();
        if ($checkDB == null) {
            $avalInputContent = $request->input('aval_input');
            if ($avalInputContent == null) {
                return redirect()->back()->withErrors('Para se inscrever tem que selecionar pelo menos uma opção!');
            } else {
                $inscricaodata = array(
                    'id_aluno' => $userid,
                    'id_avaliacao' => $request->input('aval_input'),
                    'avaliado' => $avaliado,
                    'valor' => null
                );
                DB::table('inscricao')->insert($inscricaodata);

                return redirect()->back()->with('message', 'Inscrito com sucesso!');;
            }
        } else {
            return redirect()->back()->withErrors('Já se inscreveu a esta avaliação');
        }
    }

    public function autoSelect($avl)
    {
        $user = auth()->user()->name;
        $useridget = DB::table('aluno')->select('aluno.id_aluno')->where('aluno.nome_aluno', "=", $user)->first('id_aluno');
        $userid = $useridget->id_aluno;
        $dt = date('Y-m-d');

        $currentyearandseason = DB::table('epocaeanoletivoatual')
            ->join('epoca', 'epoca.id_epoca', '=', 'epocaeanoletivoatual.id_epoca')
            ->join('anoletivo', 'anoletivo.id_anoLetivo', '=', 'epocaeanoletivoatual.id_anoLetivo')
            ->select('epoca.nome_epoca', 'anoletivo.anoLetivo')
            ->first();

        $inscrIDs = DB::table('inscricao')->pluck('id_avaliacao')->all();
        $inscrID2s = DB::table('epocaEAnoLetivoAtual')->pluck('id_epoca')->first();

        $inscs = DB::table('avaliacoes')
            ->join('uc_funcionamento', 'uc_funcionamento.id_ucFuncionamento', '=', 'avaliacoes.id_ucFuncionamento')
            ->join('aluno_inscrito_ucfuncionamento', 'aluno_inscrito_ucfuncionamento.id_ucFuncionamento', '=', 'uc_funcionamento.id_ucFuncionamento')
            ->where('aluno_inscrito_ucfuncionamento.id_aluno', '=', $userid)
            ->join('uc', 'uc.id_uc', '=', 'uc_funcionamento.id_uc')
            ->join('epocaEAnoLetivoAtual', 'epocaEAnoLetivoAtual.id_epocaEAnoLetivoAtual', '=', 'uc_funcionamento.id_epocaEAnoLetivoAtual')
            ->join('epoca', 'epoca.id_epoca', '=', 'avaliacoes.id_epoca')
            ->join('tipo_avaliacao', 'tipo_avaliacao.id_tipoaval', '=', 'avaliacoes.id_tipoaval')
            ->select('uc_funcionamento.id_ucFuncionamento', 'uc.nome_uc', 'avaliacoes.id_avaliacao', 'avaliacoes.nome_avl', 'avaliacoes.data_aval', 'epoca.id_epoca', 'epoca.nome_epoca', 'tipo_avaliacao.nome_tipoaval')
            ->where('avaliacoes.data_aval', '>', $dt)
            ->whereNotIn('id_avaliacao', $inscrIDs)
            ->where('avaliacoes.id_epoca', '=', $inscrID2s)
            ->get();

        return view('pages/inscricao', ['inscs' => $inscs, 'currentyearandseason' => $currentyearandseason, 'autoSelectValue' => $avl]);
    }
}
