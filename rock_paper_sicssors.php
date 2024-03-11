<?php

class RockPaperScissors {

    public function __construct(
        public int $number_of_games = 1, 
        public int $number_of_players = 2, 
        public array $playerResults = [], 
    ) {
        if ($number_of_games < 1){
            throw new Exception("The number of games cannot be less than one");
        }

        if ($number_of_players < 2){
            throw new Exception("The number of players cannot be less than two");
        }
        $this->initPlayerResults();
    }

    /**
     * Función para iniciar el juego
     *
     * @return void
     */
    public function startGame(){
        for ($index = 1; $index <= $this->number_of_games; $index++){
            $this->playRound();
        }
    }

    public function playRound(): void {
        $playerResults = $this->playerResults;
        foreach ($this->playerResults as $player1Data) {
            foreach ($playerResults as $player2Data) {
                if ($player1Data->id !== $player2Data->id) {
                    $result = $this->playSingelRound($player1Data, $player2Data);
                    $this->updatePlayerResult($result);
                }
            }

            unset($playerResults[$player1Data->id]);
        }
    }

    /**
     * Actualiza los datos del jugador
     *
     * @param integer $result
     * @return void
     */
    public function updatePlayerResult($result): void {
        if (is_array($result)) {
            $player = $this->playerResults[$result[0]];
            $player1 = $this->playerResults[$result[1]];

            $player->draws++;
            $player1->draws++;
        } else {
            $player = $this->playerResults[$result];
            $player->wins++;
        }
    }

    /**
     * Muestra el resultado de los jugadores
     *
     * @return void
     */
    public function displayGameResults() {
        foreach ($this->playerResults as $playerId) {
            echo "Player {$playerId->id} Wins : {$playerId->wins}, Draws: {$playerId->draws}\n";
        }
    }

    /**
     * Función que define qué jugador ha ganado, si la partida empata devuelve 0
     *
     * @param Player $player1
     * @param Player $player2
     */
    public function playSingelRound(Player $player1, Player $player2) {
        $player1Choice = !empty($player1->defaultChoice) ? new ElementChoice($player1->defaultChoice) : ElementChoice::randomChoice();
        $player2Choice = !empty($player2->defaultChoice) ? new ElementChoice($player2->defaultChoice) : ElementChoice::randomChoice();

        if ($player1Choice->name === $player2Choice->name) {
            # Empate
            return [$player1->id, $player2->id];
        } elseif ($player1Choice->name == $player2Choice->weakness) {
            return $player1->id;
        } elseif ($player2Choice->name == $player1Choice->weakness) {
            return $player2->id;
        }
    }

    /**
     * Generamos los jugadores según el número especificado al empezar el juego e inicializamos sus valores por defecto
     * 
     * @return void
     */
    public function initPlayerResults(): void{
        $player_datas = [];

        # El primer jugador siempre elige piedra
        $setDefaultChoiceForPlayer1 = 'rock';

        for ($index = 1; $index <= $this->number_of_players; $index++){
            if ($index == 1){
                $player_datas[$index] = new Player($index, $setDefaultChoiceForPlayer1);
                continue;
            }
            $player_datas[$index] = new Player($index);
        }
        $this->playerResults = $player_datas;
    }
}

class Player{
    public function __construct(
        public int $id, 
        public string $defaultChoice = '',
        public int $draws = 0, 
        public int $wins = 0, 
    ) {
        if ($id == 0){
            throw new Exception("Player ID is incorrect");
        }
    }
}

class ElementChoice {
    public function __construct(
        public string $name, 
        public string $weakness = '', 
    ) {
        if ($name == 'rock'){
            $this->weakness = 'paper';
        }elseif ($name == 'paper'){
            $this->weakness = 'sicssors';
        }elseif ($name == 'sicssors'){
            $this->weakness = 'rock';
        }
    }

    /**
     * Función para obtener de forma aleatoria una opción a jugar
     *
     * @return ElementChoice
     */
    public static function randomChoice(): ElementChoice{
        $choices = self::getElementsChoices();
        return $choices[array_rand($choices)];
    }

    /**
     * Función para obtener las variantes de elección que tiene cada jugador
     *
     * @return array
     */
    public static function getElementsChoices(): array{
        return [new ElementChoice('rock'), new ElementChoice('paper'), new ElementChoice('sicssors')];
    }
}

# Primer parámetro es el número de rondas, el segundo parámetro es el número de jugadores
$game = new RockPaperScissors(2, 3);
$game->startGame();

echo "--- Players Results ---\n";

$game->displayGameResults();