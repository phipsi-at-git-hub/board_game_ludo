<?php
// GameController.php
namespace App\Controllers;

use App\Constants\Application;
use App\Constants\Database\DbColumnConstants;
use App\Constants\Game\GameConstants;
use App\Constants\HttpMethodConstants;
use App\Core\Application\App;
use App\Core\Auth\Auth;
use App\Core\BaseController;
use App\Core\Dto\Game\GameFilterContext;
use App\Core\Logging\Logger;
use App\Models\Game\GameModel;
use App\Models\Game\GameRuleSetModel;
use App\Services\Game\GameFilterService;
use App\Services\Game\GameService;
use LogicException;

/**
 * GameController
 */
class GameController extends BaseController
{
    private GameService $gameService;

    /**
     * __construct
     *
     * @param App $app
     * @return void
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->gameService = $app->resolve(GameService::class);
    }

    /**
     * Generic game action handler 
     * 
     * executeGameActions
     *
     * @param string $game_id
     * @param callable $action
     * @param string $redirect
     * @return void
     */
    private function executeGameActions(string $game_id, callable $action, string $redirect): void
    {
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
     * View 
     * Lobby leads to game creation, games list and back
     *
     * lobby
     *
     * @return void
     */
    public function lobby(): void
    {
        // Only for logged in users (secured through middleware)
        $user = Auth::user();

        // Logging
        Logger::app()->debug('Lobby', ['user_id' => $user->getId()]);

        $this->render(
            'game/lobby',
            ['user' => $user]
        );
    }

    /**
     * View 
     * Game creation form 
     * 
     * create
     *
     * @return void
     */
    public function create(): void
    {
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

    /** 
     * Store 
     * Create a new game via POST 
     * 
     * store
     *
     * @return void
     */
    public function store(): void
    {
        if ($_SERVER[HttpMethodConstants::SERVER_REQUEST_METHOD] === HttpMethodConstants::POST && $_POST[GameConstants::GAME_NAME] !== '') {
            $game_options = [
                DbColumnConstants::IS_PRIVATE => ($_POST[GameConstants::IS_PRIVATE]),
            ];

            $rule_set = [
                DbColumnConstants::ALLOW_BOTS => ($_POST[GameConstants::ALLOW_BOTS]),
                DbColumnConstants::ALL_FIGURES_START_AT_HOME => ($_POST[GameConstants::ALL_FIGURES_START_AT_HOME]),
                DbColumnConstants::START_FIELD_MUST_BE_CLEARED => ($_POST[GameConstants::START_FIELD_MUST_BE_CLEARED]),
                DbColumnConstants::LEAVE_HOME_ATTEMPT => ($_POST[GameConstants::LEAVE_HOME_ATTEMPT]),
                DbColumnConstants::LEAVE_HOME_ATTEMPTS_MAX => ($_POST[GameConstants::LEAVE_HOME_ATTEMPTS_MAX]),
                DbColumnConstants::EXTRA_ROLL_ON_SIX_LIMIT => ($_POST[GameConstants::EXTRA_ROLL_ON_SIX_LIMIT]),
                DbColumnConstants::FORCE_LEAVING_HOME_ON_SIX => ($_POST[GameConstants::FORCE_LEAVING_HOME_ON_SIX]),
                DbColumnConstants::FORCE_CAPTURE_ENEMY_FIGURES => ($_POST[GameConstants::FORCE_CAPTURE_ENEMY_FIGURES]),
                DbColumnConstants::FORCE_EXTRA_LAP_ON_OVERFLOW => ($_POST[GameConstants::FORCE_EXTRA_LAP_ON_OVERFLOW]),
                DbColumnConstants::ALLOW_STACK_OWN_FIGURES => ($_POST[GameConstants::ALLOW_STACK_OWN_FIGURES]),
                DbColumnConstants::STRICT_GOAL_ORDER => ($_POST[GameConstants::STRICT_GOAL_ORDER]),
            ];

            $game_id = (new GameModel())->create($_SESSION[GameConstants::USER_ID], $_POST[GameConstants::GAME_NAME], $game_options, $rule_set);

            // Update user statistics 
            $user = Auth::user();
            $user->getUserStatistics()->incrementGamesCreated();

            // Logging
            Logger::app()->info('Game created: ' . $game_id, ['user_id' => Auth::user()->getId(), 'game_id' => $game_id]);

            header("Location: /game/detail/$game_id");
            exit;
        }
        $rule_set = (new GameRuleSetModel())->initializeDefaultRuleSet();

        $this->render(
            'game/create',
            ['rule_set' => $rule_set]
        );
    }

    /** 
     * View 
     * Game edit form 
     * 
     * edit
     *
     * @param string $game_id
     * @return void
     */
    public function edit(string $game_id): void
    {
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

    /** 
     * Update
     * Game update via POST 
     * 
     * update
     *
     * @return void
     */
    public function update(): void
    {
        if ($_SERVER[HttpMethodConstants::SERVER_REQUEST_METHOD] === HttpMethodConstants::POST && $_POST[GameConstants::GAME_ID] !== '') {
            $game = GameModel::findById($_POST[DbColumnConstants::GAME_ID]);
            if (!$game) {
                http_response_code(404);
                die('Game not found');
            }

            $game_name = $_POST[GameConstants::GAME_NAME];

            $game_options = [
                DbColumnConstants::IS_PRIVATE => ($_POST[GameConstants::IS_PRIVATE]),
                DbColumnConstants::IS_LOCKED => (int)$game->isLocked(),
            ];

            $rule_set = [
                DbColumnConstants::ALLOW_BOTS => ($_POST[GameConstants::ALLOW_BOTS]),
                DbColumnConstants::ALL_FIGURES_START_AT_HOME => (isset($_POST[GameConstants::ALL_FIGURES_START_AT_HOME])) ? ($_POST[GameConstants::ALL_FIGURES_START_AT_HOME]) : (int)$game->getRuleSetModel()->getAllFiguresStartAtHome(),
                DbColumnConstants::START_FIELD_MUST_BE_CLEARED => ($_POST[GameConstants::START_FIELD_MUST_BE_CLEARED]),
                DbColumnConstants::LEAVE_HOME_ATTEMPT => ($_POST[GameConstants::LEAVE_HOME_ATTEMPT]),
                DbColumnConstants::LEAVE_HOME_ATTEMPTS_MAX => ($_POST[GameConstants::LEAVE_HOME_ATTEMPTS_MAX]),
                DbColumnConstants::EXTRA_ROLL_ON_SIX_LIMIT => ($_POST[GameConstants::EXTRA_ROLL_ON_SIX_LIMIT]),
                DbColumnConstants::FORCE_LEAVING_HOME_ON_SIX => ($_POST[GameConstants::FORCE_LEAVING_HOME_ON_SIX]),
                DbColumnConstants::FORCE_CAPTURE_ENEMY_FIGURES => ($_POST[GameConstants::FORCE_CAPTURE_ENEMY_FIGURES]),
                DbColumnConstants::FORCE_EXTRA_LAP_ON_OVERFLOW => ($_POST[GameConstants::FORCE_EXTRA_LAP_ON_OVERFLOW]),
                DbColumnConstants::ALLOW_STACK_OWN_FIGURES => ($_POST[GameConstants::ALLOW_STACK_OWN_FIGURES]),
                DbColumnConstants::STRICT_GOAL_ORDER => ($_POST[GameConstants::STRICT_GOAL_ORDER]),
            ];

            $game->update($game_name, $game_options, $rule_set);

            // Logging
            Logger::app()->info('Game updated: ' . $game->getId(), ['user_id' => Auth::user()->getId(), 'game_id' => $game->getId()]);

            header('Location: /game/edit/' . $game->getId());
            exit;
        }
    }

    /**
     * View 
     * Games list overview - open game 
     * 
     * list
     *
     * @return void
     */
    public function list(): void
    {
        $user = Auth::user();
        $games = GameModel::getAllOpenGamesNew();
        $games = GameService::gamesArrayForUser($games, $user);

        // set filter preset
        $statuses = GameFilterService::getStatusesByGamesArray($games);
        $userRelations = [];
        $filter = new GameFilterContext($statuses, $userRelations, null, $games);

        // Logging
        Logger::app()->debug('List all games', ['user_id' => $user->getId()]);

        $this->render(
            'game/list',
            [
                'games' => $games,
                'filter' => $filter,
            ]
        );
    }

    /**
     * View 
     * Show list with user involved 
     * usersGames
     *
     * @return void
     */
    public function usersGames(): void
    {
        $user = Auth::user();
        $games = GameModel::getAllGamesWithUserInvolvedNew($user->getId());

        // set filter preset
        $statuses = GameFilterService::getStatusesByGamesArray($games);
        $userRelations = GameFilterService::getUserRelationsByGamesArrayAndUser($games, $user);
        $filter = new GameFilterContext($statuses, $userRelations, null, $games);

        // Logging
        Logger::app()->debug('List user games', ['user_id' => $user->getId()]);

        $this->render(
            'game/list',
            [
                'games' => $games,
                'filter' => $filter,
            ]
        );
    }

    /**
     * View 
     * Game detail view 
     * 
     * show
     *
     * @param string $game_id
     * @return void
     */
    public function show(string $game_id): void
    {
        // View an existing game
        $game = GameModel::findById($game_id);
        $user = Auth::user();

        if (!$game) {
            http_response_code(404);
            die('Game not found');
        }

        // Logging
        Logger::app()->debug('Show game ' . $game->getId(), ['user_id' => $user->getId(), 'game_id' => $game->getId()]);

        $this->render(
            'game/show',
            [
                'game' => $game,
                'user' => $user
            ]
        );
    }

    /** 
     * Join game 
     * 
     * join
     *
     * @param string $game_id
     * @return void
     */
    public function join(string $game_id): void
    {
        $this->executeGameActions(
            $game_id,
            fn($game, $user) => $this->gameService->join($game, $user),
            "/game/detail/$game_id"
        );

        // Logging
        Logger::app()->info('Join game', ['user_id' => Auth::user()->getId(), 'game_id' => $game_id]);
    }

    /** 
     * Leave game 
     * 
     * leave
     *
     * @param string $game_id
     * @return void
     */
    public function leave(string $game_id): void
    {
        $this->executeGameActions(
            $game_id,
            fn($game, $user) => $this->gameService->leave($game, $user),
            "/game/detail/$game_id"
        );

        // Logging
        Logger::app()->info('Leave game', ['user_id' => Auth::user()->getId(), 'game_id' => $game_id]);
    }

    /** 
     * Start game 
     * 
     * start
     *
     * @return void
     */
    public function start(): void
    {
        $game_id = $_POST[GameConstants::GAME_ID] ?? null;
        if (!$game_id) {
            $this->redirect('/game/list');
            return;
        }

        $this->executeGameActions(
            $game_id,
            fn($game, $user) => $this->gameService->start($game, $user),
            "/game/detail/$game_id"
        );

        // Logging
        Logger::app()->info('Start game', ['user_id' => Auth::user()->getId(), 'game_id' => $game_id]);
    }

    // ToDo: remove, since pausing a game is obsolete
    /** 
     * Pause game 
     * 
     * pause
     *
     * @return void
     */
    public function pause(): void
    {
        $game_id = $_POST[GameConstants::GAME_ID] ?? null;
        if (!$game_id) {
            $this->redirect('/game/list');
            return;
        }

        $this->executeGameActions(
            $game_id,
            fn($game, $user) => $this->gameService->pause($game, $user),
            "/game/detail/$game_id"
        );

        // Logging
        Logger::app()->info('Pause game', ['user_id' => Auth::user()->getId(), 'game_id' => $game_id]);
    }

    /** 
     * Reset game 
     * 
     * reset
     *
     * @return void
     */
    public function reset(): void
    {
        $game_id = $_POST[GameConstants::GAME_ID] ?? null;
        if (!$game_id) {
            $this->redirect('/game/list');
            return;
        }

        $this->executeGameActions(
            $game_id,
            fn($game, $user) => $this->gameService->reset($game, $user),
            "/game/detail/$game_id"
        );

        // Logging
        Logger::app()->info('Reset game', ['user_id' => Auth::user()->getId(), 'game_id' => $game_id]);
    }

    /** 
     * Cancel game 
     * 
     * cancel
     *
     * @return void
     */
    public function cancel(): void
    {
        $game_id = $_POST[GameConstants::GAME_ID] ?? null;
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

    /** 
     * Delete game 
     * 
     * delete
     *
     * @return void
     */
    public function delete(): void
    {
        $game_id = $_POST[GameConstants::GAME_ID] ?? null;
        if (!$game_id) {
            $this->redirect('/game/list');
            return;
        }

        $this->executeGameActions(
            $game_id,
            fn($game, $user) => $this->gameService->delete($game, $user),
            "/game/list"
        );

        // Logging
        Logger::app()->info('Delete game', ['user_id' => Auth::user()->getId(), 'game_id' => $game_id]);
    }

    /** 
     * Play game 
     * 
     * play
     *
     * @param string $game_id
     * @return void
     */
    public function play(string $game_id): void
    {
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
                $moves = $game->getAvailableMoves($_SESSION[GameConstants::USER_ID], $current_roll);
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

        // Logging
        Logger::app()->info('Play game', ['user_id' => Auth::user()->getId(), 'game_id' => $game_id]);

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
    /** 
     * Start a Solo TEST game 
     * 
     * createSoloTest
     *
     * @return void
     */
    public function createSoloTest(): void
    {
        $game = null;
        if ($_SERVER[HttpMethodConstants::SERVER_REQUEST_METHOD] === HttpMethodConstants::POST && $_POST[GameConstants::GAME_ID] !== '') {
            $game = GameModel::findById($_POST[GameConstants::GAME_ID]);
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
    public function startSoloTest(): void
    {
        $game_id = $_POST[GameConstants::GAME_ID];
        $game = GameModel::findById($game_id);
        $user = Auth::user();
        $moves = [];

        $game->startGame();

        if ($game->getStateModel()->getCurrentDiceRoll() !== null) {
            $moves = $game->getAvailableMoves($_SESSION[GameConstants::USER_ID], $game->getStateModel()->getCurrentDiceRoll());
        }

        $this->redirect("/game/detail/$game_id");
    }

    // Roll dice
    public function roll()
    {
        $user = Auth::user();
        $game_id = $_POST[GameConstants::GAME_ID];
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
    public function move()
    {
        $user = Auth::user();
        $game_id = $_POST[GameConstants::GAME_ID];
        $move = json_decode($_POST[Application::DTO_MOVE], true);
        $game = GameModel::findById($game_id);

        // Check if it's users turn
        if ($game->getCurrentPlayer()->getUserId() !== $user->getId()) {
            throw new LogicException('Not your turn');
        }

        // Check if a move is skipped
        if (!empty($move[Application::DTO_IS_PASS])) {
            // Turn may end directly
            $game->passTurn($_SESSION[GameConstants::USER_ID]);
        } else {
            $game->applyMove($_SESSION[GameConstants::USER_ID], $move);
        }

        $this->redirect("/game/play/$game_id");
    }

    // Game State for Frontend
    public function state(string $game_id)
    {
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
