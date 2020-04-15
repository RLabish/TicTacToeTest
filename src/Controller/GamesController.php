<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Game\ComputerPlayer;
use App\Game\TicTacToe;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Rest\Controller\RestController;

/**
 * Games Controller
 *
 * @property \App\Model\Table\GamesTable $Games
 *
 * @method \App\Model\Entity\Game[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class GamesController extends RestController
{

    public function __construct(ServerRequest $request = null, Response $response = null, $name = null, $eventManager = null, $components = null)
    {
        define("x", 1);
        define("o", 2);
        define("_", null);
        parent::__construct($request, $response, $name, $eventManager, $components);

    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $games = $this->paginate($this->Games);
         $this->set(compact('games'));
    }

    /**
     * View method
     *
     * @param string|null $id Game id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $game = $this->Games->get($id, [
            'contain' => [],
        ]);

        $this->set('game', $game);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $game = $this->Games->newEntity();
        if ($this->request->is('post')) {
            $game = $this->Games->patchEntity($game, $this->request->getData());
            if ($game = $this->Games->save($game)) {
                $this->set(['location' => $game['id']]);
            }
        }else{
            $this->set(['reason' => 'Invalid request']);
        }


    }

    /**
     * Edit method
     *
     * @param string|null $id Game id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $game = $this->Games->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->moveBoard();
            $game = $this->Games->patchEntity($game, $data);
//            var_dump($game);die();
            if ($game = $this->Games->save($game)) {
                $this->set(compact('game'));
            }

        }else{
            $this->set(['reason' => 'Invalid request']);
        }
    }
    private function moveBoard(){
        $board = $this->convertBoardToArray($this->request->getData('board'));
        $comp = new ComputerPlayer();
        $tic = new TicTacToe($board);
        if(!$tic->isGameCompleted()){
            $position = $comp->computerMove($board, o);
            $new_board = $this->makeMove($this->request->getData('board'),$position);
            $tic = new TicTacToe($this->convertBoardToArray($new_board));
            $status = "RUNNING";
            if($tic->isGameCompleted()){
                $winner = $tic->getWinner();
                if($winner == 1){
                    $status = 'X_WON';
                }
                else if($winner == 2){
                    $status = 'O_WON';
                }
            }
            return ['status' => $status,'board' => $new_board];
        }else{
            $winner = $tic->getWinner();
            if($winner == 1){
                $status = 'X_WON';
            }
            else if($winner == 2){
                $status = 'O_WON';
            }else if($winner == null){
                $status = "DRAW";
            }
            return ['status' => $status,'board' => $this->request->getData('board')];
        }

    }
    private function convertBoardToArray($board){
        $board = str_split($board,3);
        foreach ($board as $key=>$item){
            $board[$key] = str_split($item,1);
            foreach ($board[$key] as $k=>$value){
                if($value == '-'){
                    $board[$key][$k] = NULL;
                }if($value == 'x'){
                    $board[$key][$k] = 1;
                }if($value == 'o'){
                    $board[$key][$k] = 2;
                }
            }
        }
        return $board;
    }
    private function makeMove($board,$position){
        $board = str_split($board);
        $board[$position -1] = 'o';
        return implode($board);

    }

    /**
     * Delete method
     *
     * @param string|null $id Game id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $game = $this->Games->get($id);

        if ($this->Games->delete($game)) {
            $this->set(['message' => 'Game successfully deleted']);
//            $this->Flash->success(__('The game has been deleted.'));
        } else {
            $this->set(['message' => 'Resource not found']);

        }

//        return $this->redirect(['action' => 'index']);
    }
}
