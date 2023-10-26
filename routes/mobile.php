<?

use App\Http\Controllers\mobile\v1\AuthController;

Route::prefix('mobile')->group(function () {
    Route::prefix('v1')->group(function () {
        Route::prefix('auth')->group(function () {
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/login', [AuthController::class, 'login']);
        });
    });
});
