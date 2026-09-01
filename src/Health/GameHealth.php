<?php
// Health/GameHealth.php
namespace App\Health;

use App\Constants\Application;
use App\Controllers\Api\ApiGameController;
use App\Controllers\Api\ApiGameEngineController;
use App\Core\Http\Http;
use App\Models\Game\GameModel;
use Throwable;

final class GameHealth {
    // Overall Game Health
    public static function getStatus(): string {
        if (self::isHealthy()) {
            return Application::GENERAL_HEALTHY; 
        }
        if (self::isGameEngineAvailable() && self::isApiGameEngineControllerAvailable()) {
            return Application::GENERAL_WARNING; 
        }
        return Application::GENERAL_CRITICAL; 
    }

    // Game engine aka game models available 
    public static function isGameEngineAvailable(): bool {
        try {
            $game = new GameModel(); 
            return $game !== null;
        } catch (Throwable $e) {
            return false; 
        }
    }

    // Game API status
    public static function isApiGameControllerAvailable(): bool {
        return
            class_exists(ApiGameController::class) 
            && method_exists(ApiGameController::class, 'join') 
            && method_exists(ApiGameController::class, 'leave') 
            && method_exists(ApiGameController::class, 'start')  
            && method_exists(ApiGameController::class, 'pause')  
            && method_exists(ApiGameController::class, 'reset')  
            && method_exists(ApiGameController::class, 'delete'); 
    }

    // Game Engine API status
    public static function isApiGameEngineControllerAvailable(): bool {
        return
            class_exists(ApiGameEngineController::class) 
            && method_exists(ApiGameEngineController::class, 'state') 
            && method_exists(ApiGameEngineController::class, 'rollDice') 
            && method_exists(ApiGameEngineController::class, 'getAvailableMoves')  
            && method_exists(ApiGameEngineController::class, 'applyMove')  
            && method_exists(ApiGameEngineController::class, 'passTurn')  
            && method_exists(ApiGameEngineController::class, 'health'); 
    }

    // Game API available
    public static function isApiAvailable(): bool {
        $url = rtrim($_ENV['APP_URL'], '/') . API_PATH . 'health'; 

        $response = Http::callUrlSimple($url); 
        if ($response === false) {
            return false; 
        }
        $data = json_decode($response, true); 

        return
            isset($data['status']) 
            && $data['status'] === 'ok';
    }

    // Game API - get details
    public static function getApiHealthDetails(): array {
        $url = rtrim($_ENV['APP_URL'], '/') . API_PATH . 'health'; 
        $response = Http::callUrlDetailed($url, 3); 

        if ($response === null) {
            return []; 
        }
        $decoded = null; 

        if (isset($response['body'])) {
            $decoded = json_decode($response['body'] ?? '', true); 
        }
        $response['valid_json'] = is_array($decoded); 
        $response['status_ok'] = ($decoded['status'] ?? null) === 'ok'; 
        $response['api'] = self::isApiGameEngineControllerAvailable(); 
        $response['engine'] = self::isGameEngineAvailable(); 
        // ToDo: implement health checks for all important api resources / calls
        $response['resources'] = []; 

        return $response; 
    }

    /**
     * Helper
     */
    // Helper - Is game overall healthy
    public static function isHealthy(): bool {
        return 
            self::isGameEngineAvailable() 
            && self::isApiGameEngineControllerAvailable()
            && self::isApiAvailable();
    }
}