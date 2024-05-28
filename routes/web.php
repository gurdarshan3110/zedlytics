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
    Route::resource('ledger', LedgerController::class);
    Route::get('/debug-user', function () {
        return Auth::user(); // This should now return the user object
    });

});
