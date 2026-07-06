<?php
// GameController.php
namespace App\Controllers;

use App\Constants\Application;
use App\Core\Auth;
use App\Core\BaseController;
use App\Core\Csrf;
use App\Models\GameModel;
use App\Models\GameRuleSetModel;
use App\Services\GameService;
use LogicException;

class GameController extends BaseController {
    private GameService $gameService; 

    public function __construct() { 
        $this->gameService = new GameService(); 
    } 

    /**
     * Generic game action handler
     */
    private function executeGameActions(string $game_id, callable $action, string $redirect) {
        $game = GameModel::findById($game_id);
        $user = Auth::user();

        $success = $action($game, $user);
        if (!$success) {
            $this->render(
                'game/error', 
                [
                    'message' => 'Action not allowed.' 
                ]
            ); 
        }
        $this->redirect($redirect);
    }
    
    /**
     * Controller via Models to Views
     */
    // Lobby leads to game creation, games list and back
    public function lobby() {
        // Only for logged in users (secured through middleware)
        $user = Auth::user();
        $this->render(
            'game/lobby', 
            ['user' => $user]
        );
    }

    // Game creation form
    public function create() {
        $rule_set = (new GameRuleSetModel())->initializeDefaultRuleSet();

        $this->render(
            'game/create', 
            [
                'rule_set' => $rule_set, 
                'rule_set_presets' => GameRuleSetModel::getPresetsAsArray(), 
            ], 
            '', 
            [], 
            ['app/forms/game_ruleset'], 
        );
    }

    // Create a new game via POST
    public function store() {
        if (!Csrf::validate($_POST['_csrf_token'] ?? null)) {
            http_response_code(403);
            die('Invalid CSRF token');
        }

        if ($_SERVER[Application::REQUEST_METHOD] === Application::REQUEST_METHOD_POST && $_POST[Application::GAME_NAME] !== '') {
            $game_options = [
                Application::IS_PRIVATE => ($_POST[Application::IS_PRIVATE]), 
                Application::IS_LOCKED => ($_POST[Application::IS_LOCKED]), 
            ];

            $rule_set = [
                Application::ALLOW_BOTS => ($_POST[Application::ALLOW_BOTS]),
                Application::ALL_FIGURES_START_AT_HOME => ($_POST[Application::ALL_FIGURES_START_AT_HOME]), 
                Application::START_FIELD_MUST_BE_CLEARED => ($_POST[Application::START_FIELD_MUST_BE_CLEARED]),
                Application::LEAVE_HOME_ATTEMPT => ($_POST[Application::LEAVE_HOME_ATTEMPT]), 
                Application::LEAVE_HOME_ATTEMPTS_MAX => ($_POST[Application::LEAVE_HOME_ATTEMPTS_MAX]), 
                Application::EXTRA_ROLL_ON_SIX_LIMIT => ($_POST[Application::EXTRA_ROLL_ON_SIX_LIMIT]),
                Application::FORCE_LEAVING_HOME_ON_SIX => ($_POST[Application::FORCE_LEAVING_HOME_ON_SIX]), 
                Application::FORCE_CAPTURE_ENEMY_FIGURES => ($_POST[Application::FORCE_CAPTURE_ENEMY_FIGURES]), 
                Application::FORCE_EXTRA_LAP_ON_OVERFLOW => ($_POST[Application::FORCE_EXTRA_LAP_ON_OVERFLOW]),
                Application::ALLOW_STACK_OWN_FIGURES => ($_POST[Application::ALLOW_STACK_OWN_FIGURES]),
                Application::STRICT_GOAL_ORDER => ($_POST[Application::STRICT_GOAL_ORDER]),
            ];
            
            $game_id = (new GameModel())->create($_SESSION[Application::USER_ID], $_POST[Application::GAME_NAME], $game_options, $rule_set);

            header("Location: /game/detail/$game_id");
            exit;
        }
        $rule_set = (new GameRuleSetModel())->initializeDefaultRuleSet();

        $this->render(
            'game/create', 
            ['rule_set' => $rule_set]
        );
    }

    // Game edit form
    public function edit(string $game_id) {
        $game = GameModel::findById($game_id);
        $user = Auth::user();

        if ($game->isWaiting()) {
            $this->render(
                'game/edit', 
                [
                    'game' => $game, 
                    'user' => $user, 
                    'rule_set_presets' => GameRuleSetModel::getPresetsAsArray(), 
                    'rule_set_original' => $game->getRuleSetModel()->getAllRulesAsArray(), 
                ], 
                '', 
                [], 
                ['app/forms/game_ruleset'], 
            );
            exit;
        }
        header("Location: /game/detail/$game_id");
        exit;
    }

    // Game update via POST
    public function update() {
        if (!Csrf::validate($_POST['_csrf_token'] ?? null)) {
            http_response_code(403);
            die('Invalid CSRF token');
        }

        if ($_SERVER[Application::REQUEST_METHOD] === Application::REQUEST_METHOD_POST && $_POST[Application::GAME_ID] !== '') {
            $game = GameModel::findById($_POST[Application::GAME_ID]);
            if (!$game) {
                http_response_code(404);
                die('Game not found');
            }

            $game_name = $_POST[Application::GAME_NAME];

            $game_options = [
                Application::IS_PRIVATE => ($_POST[Application::IS_PRIVATE]), 
                Application::IS_LOCKED => ($_POST[Application::IS_LOCKED]), 
            ];

            $rule_set = [
                Application::ALLOW_BOTS => ($_POST[Application::ALLOW_BOTS]),
                Application::ALL_FIGURES_START_AT_HOME => ($_POST[Application::ALL_FIGURES_START_AT_HOME]), 
                Application::START_FIELD_MUST_BE_CLEARED => ($_POST[Application::START_FIELD_MUST_BE_CLEARED]),
                Application::LEAVE_HOME_ATTEMPT => ($_POST[Application::LEAVE_HOME_ATTEMPT]), 
                Application::LEAVE_HOME_ATTEMPTS_MAX => ($_POST[Application::LEAVE_HOME_ATTEMPTS_MAX]), 
                Application::EXTRA_ROLL_ON_SIX_LIMIT => ($_POST[Application::EXTRA_ROLL_ON_SIX_LIMIT]),
                Application::FORCE_LEAVING_HOME_ON_SIX => ($_POST[Application::FORCE_LEAVING_HOME_ON_SIX]), 
                Application::FORCE_CAPTURE_ENEMY_FIGURES => ($_POST[Application::FORCE_CAPTURE_ENEMY_FIGURES]), 
                Application::FORCE_EXTRA_LAP_ON_OVERFLOW => ($_POST[Application::FORCE_EXTRA_LAP_ON_OVERFLOW]),
                Application::ALLOW_STACK_OWN_FIGURES => ($_POST[Application::ALLOW_STACK_OWN_FIGURES]),
                Application::STRICT_GOAL_ORDER => ($_POST[Application::STRICT_GOAL_ORDER]),
            ];

            $game->update($game_name, $game_options, $rule_set);

            header('Location: /game/edit/' . $game->getId());
            exit;
        }
    }

    // Game - Games list overview - open games
    public function list(): void {
        $user = Auth::user();
        if ($user->isAdmin()) {
            $games = GameModel::getAllGames();
        } else {
            $games = GameModel::getAllOpenGames();
        }
        
        $this->render(
            'game/list', 
            [
                'games' => $games 
            ]
        );
    }

    // Game - Show list with user involved
    public function usersGames(): void {
        $user = Auth::user();
        $games = GameModel::getAllGamesWithUserInvolved($user->getId());

        //var_dump($games);
        
        $this->render(
            'game/list', 
            [
                'games' => $games 
            ]
        );
    }

    // Game - Detail view
    public function show(string $game_id) {
        // View an existing game
        $game = GameModel::findById($game_id);
        $user = Auth::user();

        if (!$game) {
            http_response_code(404);
            die('Game not found');
        }

        $this->render(
            'game/show', 
            [
                'game' => $game, 
                'user' => $user
            ]
        );
    }

    // Join game  
    public function join(string $game_id): void {
        $this->executeGameActions(
            $game_id, 
            fn($game, $user) => $this->gameService->join($game, $user), 
            "/game/detail/$game_id"
        ); 
    }

    // Leave game
    public function leave(string $game_id) {
        $this->executeGameActions(
            $game_id, 
            fn($game, $user) => $this->gameService->leave($game, $user), 
            "/game/detail/$game_id"
        ); 
    }

    // Start game
    public function start() {
        $game_id = $_POST[Application::GAME_ID] ?? null; 
        if (!$game_id) {
            $this->redirect('/game/list'); 
            return; 
        }

        $this->executeGameActions(
            $game_id, 
            fn($game, $user) => $this->gameService->start($game, $user), 
            "/game/detail/$game_id"
        ); 
    }

    // Pause game
    public function pause() {
        $game_id = $_POST[Application::GAME_ID] ?? null; 
        if (!$game_id) {
            $this->redirect('/game/list'); 
            return; 
        }
        
        $this->executeGameActions(
            $game_id, 
            fn($game, $user) => $this->gameService->pause($game, $user), 
            "/game/detail/$game_id"
        ); 
    }

    // Reset game
    public function reset(): void {
        $game_id = $_POST[Application::GAME_ID] ?? null; 
        if (!$game_id) {
            $this->redirect('/game/list'); 
            return; 
        }

        $this->executeGameActions(
            $game_id, 
            fn($game, $user) => $this->gameService->reset($game, $user), 
            "/game/detail/$game_id"
        ); 
    }

    // Cancel game
    public function cancel(): void {
        $game_id = $_POST[Application::GAME_ID] ?? null; 
        if (!$game_id) {
            $this->redirect('/game/list'); 
            return; 
        }

        $this->executeGameActions(
            $game_id, 
            fn($game, $user) => $this->gameService->cancel($game, $user), 
            "/game/detail/$game_id"
        ); 
    }

    // Delete game
    public function delete(): void {
        $game_id = $_POST[Application::GAME_ID] ?? null; 
        if (!$game_id) {
            $this->redirect('/game/list'); 
            return; 
        }

        $this->executeGameActions(
            $game_id, 
            fn($game, $user) => $this->gameService->delete($game, $user), 
            "/game/detail/$game_id"
        ); 
    }

    // Play game
    public function play(string $game_id) {
        $game = GameModel::findById($game_id);
        $user = Auth::user();
        $moves = [];

        // Start the game if it hasn't started yet
        if ($game->isWaiting()) {
            $game->startGame();
        }

        // Only if a dice value exists can possible moves be determined
        $current_roll = $game->getStateModel()->getCurrentDiceRoll();
        if ($current_roll !== null) {
            if ($game->isPlayersTurn($user)) {
                $moves = $game->getAvailableMoves($_SESSION[Application::USER_ID], $current_roll);
            }

            // If no moves are available, offer at least one passing move
            if (empty($moves)) {
                $moves[] = [
                    Application::DTO_IS_PASS => true,
                    Application::DTO_FIGURE_INDEX => null,
                    Application::DTO_TO => null,
                ];
            }
        }

        $this->render(
            'game/play', 
            [
                'game' => $game, 
                'moves' => $moves, 
                'user' => $user
            ], 
            $game->getName(), 
            ['game'] 
        );
    }

    /**
     * The following is for testing purpose in static view
     */
    // Start a Solo TEST game
    public function createSoloTest() {
        if (!Csrf::validate($_POST['_csrf_token'] ?? null)) {
            http_response_code(403);
            die('Invalid CSRF token');
        }

        $game = null;

        if ($_SERVER[Application::REQUEST_METHOD] === Application::REQUEST_METHOD_POST && $_POST[Application::GAME_ID] !== '') {
            $game = GameModel::findById($_POST[Application::GAME_ID]);
            $game_id = $game->cloneGameWithOnePlayer();
            $game = GameModel::findById($game_id);
            $user = Auth::user();

            $game->join($user->getId());

            header("Location: /game/detail/$game_id");
            exit;
        }

        header('Location: /game/list');
        exit;
    }

    // Start a given solo test game
    public function startSoloTest() {
        $game_id = $_POST[Application::GAME_ID];
        $game = GameModel::findById($game_id);
        $user = Auth::user();
        $moves = [];

        $game->startGame();

        if ($game->getStateModel()->getCurrentDiceRoll() !== null) {
            $moves = $game->getAvailableMoves($_SESSION[Application::USER_ID], $game->getStateModel()->getCurrentDiceRoll());
        }

        $this->redirect("/game/detail/$game_id");
    }

    // Roll dice
    public function roll() {
        $user = Auth::user();
        $game_id = $_POST[Application::GAME_ID];
        $game = GameModel::findById($game_id);

        // Check if it's users turn
        if ($game->getCurrentPlayer()->getUserId() !== $user->getId()) {
            throw new LogicException('Not your turn');
        }

        // Roll the dice and store the value in the database
        $game->rollDice($user->getId());

        $this->redirect("/game/play/$game_id");
    }

    // Move figure
    public function move() {
        $user = Auth::user();
        $game_id = $_POST[Application::GAME_ID];
        $move = json_decode($_POST[Application::DTO_MOVE], true);
        $game = GameModel::findById($game_id);

        // Check if it's users turn
        if ($game->getCurrentPlayer()->getUserId() !== $user->getId()) {
            throw new LogicException('Not your turn');
        }

        // Check if a move is skipped
        if (!empty($move[Application::DTO_IS_PASS])) {
            // Turn may end directly
            $game->passTurn($_SESSION[Application::USER_ID]);
        } else {
            $game->applyMove($_SESSION[Application::USER_ID], $move);
        }

        $this->redirect("/game/play/$game_id");
    }

    // Game State for Frontend
    public function state(string $game_id) {
        if (!$game_id) {
            return $this->json([
                'success' => false, 
                'error' => 'Missing game_id'
            ]);
        }
        $game = GameModel::findById($game_id);

        return $this->json([
            'success' => true, 
            'state' => $game->getGameStateSnapshot()
        ]);
    }
}
