<?php

use App\Http\Controllers\api\v1\{ClientController,
    ClubController,
    EconomyController,
    PenaltyController,
    RoleController,
    SaleController,
    ServiceController,
    SessionController,
    UserController};
use Illuminate\Support\Facades\Route;


Route::group([
    'prefix' => 'v1',
    'middleware' => 'auth:api'
], function () {
    Route::apiResource('clubs', ClubController::class)->only(['index']);
    Route::apiResource('roles', RoleController::class)->only(['index']);
    Route::post('users/upload/{user}', [UserController::class, 'uploadPhoto']);
    Route::apiResource('users', UserController::class);
    // Пополнение баланса
    Route::post('clients/{client}/top-up', [ClientController::class, 'topUpClientAccount']);
    Route::get('clients/search', [ClientController::class, 'search']);
    Route::apiResource('clients', ClientController::class);
    // Активация купленной услуги
    Route::post('services/activate/{service}', [ServiceController::class, 'activateService']);
    // Типы услуг
    Route::get('services/types', [ServiceController::class, 'getServiceTypes']);
    Route::apiResource('services', ServiceController::class);
    // Продажа услуги/бара
    Route::post('sale', [SaleController::class, 'create']);
    // Списание визита по услуге
    Route::get('session/finish/{client}', [SessionController::class, 'finish']);
    Route::post('session/attach/{client}', [SessionController::class, 'attach']);
    Route::post('session/solarium', [SessionController::class, 'writeOffSolarium']);
    Route::post('session/write-off', [SessionController::class, 'writeOffVisit']);
    // получение статистических данных
    Route::get('economy/balance', [EconomyController::class, 'getClientsBalance']);
    Route::get('economy', [EconomyController::class, 'index']);
    // Штрафное списание услуги
    Route::apiResource('penalty', PenaltyController::class);

});

require __DIR__ . '/auth.php';

