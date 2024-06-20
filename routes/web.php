<?php

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\PartyController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AggregateController;
use App\Http\Controllers\PoolController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckMacAddress;


Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->intended('/dashboard');
    }

    return view('auth/login');
});
Auth::routes();


Route::group(['middleware' => ['auth:web',CheckMacAddress::class]], function ($router) {

    Route::get('/financial-details/{day}/{brand}', [DashboardController::class, 'finDetails'])->name('financial-details');
    Route::get('/employee-dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::resource('dashboard', DashboardController::class);

    Route::get('/employees/list', [EmployeeController::class, 'list'])->name('employees.list');
    Route::get('/employees/{employee}/reset', [EmployeeController::class, 'reset'])->name('employees.reset');
    Route::put('/employees/{employee}/password', [EmployeeController::class, 'resetpassword'])->name('employees.password');
    Route::get('/employees/{employee}/mac', [EmployeeController::class, 'mac'])->name('employees.mac');
    Route::put('/employees/{employee}/macaddress', [EmployeeController::class, 'macaddress'])->name('employees.macaddress');
    Route::resource('employees', EmployeeController::class);

    Route::get('/roles/list', [RoleController::class, 'list'])->name('roles.list');
    Route::resource('roles', RoleController::class);

    Route::get('/clients/list', [ClientController::class, 'list'])->name('clients.list');
    Route::resource('clients', ClientController::class);

    Route::get('/banks/list', [BankController::class, 'list'])->name('banks.list');
    Route::resource('banks', BankController::class);

    Route::get('/parties/list', [PartyController::class, 'list'])->name('parties.list');
    Route::resource('parties', PartyController::class);

    Route::get('/brands/list', [BrandController::class, 'list'])->name('brands.list');
    Route::resource('brands', BrandController::class);

    Route::get('/expenses/list', [ExpenseController::class, 'list'])->name('expenses.list');
    Route::resource('expenses', ExpenseController::class);

    Route::post('/save-ledger', [LedgerController::class, 'saveLedger'])->name('save.ledger');
    Route::get('/ledger/create/{id}', [LedgerController::class, 'create'])->name('ledger.create');
    Route::get('/ledger/list', [LedgerController::class, 'list'])->name('ledger.list');
    Route::get('/ledger/data/{date}/{bank_id}', [LedgerController::class, 'fetchdata'])->name('ledger.data');
    Route::get('/hints', [LedgerController::class, 'hints']);
    Route::resource('ledger', LedgerController::class);

    Route::post('/save-pool', [PoolController::class, 'saveLedger'])->name('save.pool');
    Route::get('/pool/create/{id}', [PoolController::class, 'create'])->name('pool.create');
    Route::get('/pool/list', [PoolController::class, 'list'])->name('pool.list');
    Route::get('/pool/data/{date}/{bank_id}', [PoolController::class, 'fetchdata'])->name('ledger.data');
    Route::resource('pool', PoolController::class);

    Route::get('/debug-user', function () {
        return Auth::user(); // This should now return the user object
    });
    Route::get('/timeline/list', [ActivityLogController::class, 'list'])->name('timeline.list');
    Route::resource('timeline', ActivityLogController::class);

    Route::post('/generate-excel-report', [ReportController::class, 'generateExcelReport']);
    Route::resource('report', ReportController::class);

    Route::get('/aggregate/list', [AggregateController::class, 'list'])->name('aggregate.list');
    Route::resource('aggregate', AggregateController::class);

    Route::get('/enable-two-factor-authentication',[LoginController::class, 'enable2Fa'])->name('two.factor');

    Route::get('/two-factor-authentication',[LoginController::class, 'twoFactor'])->name('twofactor.authentication');
     


});
