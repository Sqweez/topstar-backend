<?php

use App\Http\Controllers\api\v1\{ClientController,
    ClubController,
    DashboardController,
    EconomyController,
    PenaltyController,
    RequestController,
    RestoredServiceController,
    RoleController,
    SaleController,
    ServiceController,
    SessionController,
    UserController};
use Illuminate\Support\Facades\Route;


Route::group([
    'prefix' => 'v1',
    //'middleware' => 'auth:api'
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
    Route::get('economy/graphs', [EconomyController::class, 'getGraphReports']);
    Route::get('economy', [EconomyController::class, 'index']);
    // Штрафное списание услуги
    Route::apiResource('penalty', PenaltyController::class);
    // Восстановление услуги
    Route::post('restored/{service}', [RestoredServiceController::class, 'store']);
    Route::patch('restored/{restored}', [RestoredServiceController::class, 'update']);
    // Получение списка запросов
    Route::group(['prefix' => 'requests'], function () {
        Route::get('/penalties', [RequestController::class, 'getPenaltiesRequests']);
        Route::get('/restored', [RequestController::class, 'getRestoredServiceRequests']);
    });
    // Получение данных для дэшборда
    Route::get('dashboard/in-gym-clients', [DashboardController::class, 'getInGymClients']);

});

require __DIR__ . '/auth.php';

