<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\View\View;
use PhpParser\Node\Stmt\TryCatch;

class MainController extends Controller
{

    private $app_data;

    public function __construct() {

        // carregar arquivo app_data.php

        $this->app_data=require(app_path('app_data.php'));
    }

    public function startGame():View {

        return view('home');
    }

    public function prepareGame(Request $request) {

        $request->validate([

            'total_questions' => 'required|integer|min:3|max:30'
        ],
        [
            'total_questions.required' => 'O número de questões é obrigatório.',
            'total_questions.integer' => 'O número de questões deve ser um valor inteiro.',
            'total_questions.min' => 'O número mínimo de questões é :min.',
            'total_questions.max' => 'O número máximo de questões é :max.',
        ]
    );

    // todas questoes
    $total_questions = intval($request->input('total_questions'));

    // preparar a estrutura do quiz
    $quiz = $this->prepareQuiz($total_questions);

    // colocar o jogo na sessão

    session()->put([
        'quiz' => $quiz,
        'total_questions' => $total_questions,
        'current_question' => 0, 
        'correct_answers' => 0,
        'wrong_answers' => 0

    ]);
    return redirect()->route('game');
}

    private function prepareQuiz($total_questions) {

        $questions = [];
        $total_countries = count($this->app_data);

        //criar array de indices países para nao ter repetição

        $indexes= range(0, $total_countries -1);

        shuffle($indexes); // mistura arrays sem repetir valores

        $indexes = array_slice($indexes, 0, $total_questions);

        // criar array de questoes
        $question_number = 1;
        foreach($indexes as $index) {

            $question['question_number'] = $question_number++;
            $question['country'] = $this->app_data[$index]['country'];
            $question['correct_answer'] =  $this->app_data[$index]['capital'];

            // respostas erradas
            $other_capitals = array_column($this->app_data, 'capital');

            //remover resposta correta
            $other_capitals = array_diff($other_capitals, [$question['correct_answer']]);

            // misturar as respostas das outras capitais
            shuffle($other_capitals);

            $question['wrong_answer'] = array_slice($other_capitals, 0, 3);

            // checar se a mensagem está correta
            $question['correct'] = null;

            $questions[] = $question;

        }
        return $questions;
    }

    public function game():View {

        $quiz = session('quiz');
        $total_questions = session ('total_questions');
        $current_question = session ('current_question');

        // preparar perguntas para aparecer na view

        $answers = $quiz[$current_question]['wrong_answer'];
        $answers[] = $quiz[$current_question]['correct_answer'];
        
        shuffle($answers);
        
        return view('game')->with([
            'country' => $quiz[$current_question]['country'],
            'totalQuestions' => $total_questions,
            'currentQuestion' => $current_question,
            'answers' => $answers
        ]);        
    }

    public function answer($enc_answer) {

        try {
           $answer = Crypt::decryptString($enc_answer);
            
        } catch (\Exception $e) {

            return redirect()->route('game');
        }

        // logica do jogo

        $quiz = session("quiz");
        $current_question = session('current_question');
        $correct_answer = $quiz[$current_question]['correct_answer'];
        $correct_answers = session('correct_answers');
        $wrong_answers = session('wrong_answers');

        if($answer == $correct_answer) {
            $correct_answers++;
            $quiz[$current_question]['correct'] = true;
        } else {
            $wrong_answers++;
            $quiz[$current_question]['correct'] = false;
        }

        //update session

        session()->put([
            'quiz' => $quiz,
            'correct_answers' => $correct_answers,
            'wrong_answers' => $wrong_answers
        ]);
        
        // mostrar a resposta correta

        $data = [
            'country' =>$quiz[$current_question]['country'],
            'correct_answer' => $correct_answer,
            'choice_answer' => $answer,
            'currentQuestion' => $current_question,
            'totalQuestions' => session('total_questions')
        ];
        return view('answer_result')->with($data);
    }
    public function nextQuestion() {
        $current_question = session('current_question');
        $total_questions = session('total_questions');

        // checar se o jogo acabou/ se chegou ao fim

        if($current_question < $total_questions - 1) {
            $current_question++;
            session()->put('current_question', $current_question);
            return redirect()->route('game');
        } else {

            // acaba o jogo
            return redirect()->route('show_results');
        }
    }
    public function showResults() {

        $correct_answers = session('correct_answers');
        $wrong_answers = session('wrong_answers');
        $total_questions = session('total_questions');
    
        return view ('final_results')->with([
            'correct_answers' => $correct_answers,
            'wrong_answers' => $wrong_answers,
            'total_questions' => $total_questions,
            'percentage' => round($correct_answers / $total_questions * 100, 2)
        ]);
    }
}