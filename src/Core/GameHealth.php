<?php
// Core/GameHealth.php
namespace App\Core;

use App\Constants\Application;
use App\Controllers\ApiGameController;
use App\Models\GameModel;
/*
use App\Models\GameRuleSetModel;
use App\Models\GameStateFigureModel;
use App\Models\GameStateModel;
use App\Models\GameStatePlayerModel;
*/
use Throwable;

final class GameHealth {
    // Overall Game Health
    public static function getStatus(): string {
        return Application::GENERAL_OFF; 
    }

    // Game engine aka game models available 
    public static function isGameEngineAvailable(): bool {
        try {
            $game = new GameModel(); 
            return $game !== null;
        } catch (Throwable $e) {
            return false; 
        }
        /*
        return
            class_exists(GameModel::class) 
            && class_exists(GameRuleSetModel::class) 
            && class_exists(GameStateModel::class) 
            && class_exists(GameStatePlayerModel::class) 
            && class_exists(GameStateFigureModel::class); 
        */
    }

    // Game API status
    public static function isApiControllerAvailable(): bool {
        return
            class_exists(ApiGameController::class) 
            && method_exists(ApiGameController::class, 'state') 
            && method_exists(ApiGameController::class, 'rollDice') 
            && method_exists(ApiGameController::class, 'getAvailableMoves')  
            && method_exists(ApiGameController::class, 'applyMove')  
            && method_exists(ApiGameController::class, 'passTurn')  
            && method_exists(ApiGameController::class, 'health'); 
    }

    // Game API available
    public static function isApiAvailable(): bool {
        $url = rtrim($_ENV['APP_URL'], '/') . '/api/game/health'; 

        $response = Http::callUrlSimple($url); 
        if ($response === false) {
            return false; 
        }
        $data = json_decode($response, true); 

        return
            isset($data['status']) 
            && $data['status'] === 'ok';
    }

    /**
     * Helper
     */
    // Helper - Is game overall healthy
    public static function isHealthy(): bool {
        return 
            self::isGameEngineAvailable() 
            && self::isApiAvailable();
    }
}