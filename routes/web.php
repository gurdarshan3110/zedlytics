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
use App\Http\Controllers\TransactionLogController;
use App\Http\Controllers\MarginLimitMarketController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\TwoFactorController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckMacAddress;
use Laravel\Fortify\Features;
use App\Http\Controllers\WithdrawRequestController;
use App\Http\Controllers\LedgerLogController;
use App\Http\Controllers\ChartsController;
use App\Http\Controllers\RiskManagementController;
use App\Http\Controllers\ComingSoonController;
use App\Http\Controllers\UserDeviceController;

Route::get('/coming-soon', [ComingSoonController::class, 'index'])->name('coming-soon.index');

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->intended('/dashboard');
    }

    return view('auth/login');
});

Route::get('/fetch-withdraw-requests', [WithdrawRequestController::class, 'pushWithdrawRequestsToDB']);
Route::get('/fetch-base-currency-data', [WithdrawRequestController::class, 'fetchBaseCurrencyData']);
Route::get('/fetch-open-positions', [WithdrawRequestController::class, 'fetchOpenPositions']);

Route::get('/fetch-save-clients', [WithdrawRequestController::class, 'fetchAndSaveClientRecords']);

Route::get('/fetch-insert-clients', [WithdrawRequestController::class, 'fetchAndInsertClientRecords']);

Route::get('/run-job', [WithdrawRequestController::class, 'fetchNewClients']);

Route::get('/new-dealers-job', [WithdrawRequestController::class, 'fetchNewDealers']);

Route::get('/new-transaction-job', [WithdrawRequestController::class, 'fetchTransactionLog']);
Route::get('/update-base-currencies', [WithdrawRequestController::class, 'fetchBaseCurrencies']);

Route::get('/dispatch-client-currency-policies', [WithdrawRequestController::class, 'fetchClientCurrencyPolicies']);
Route::get('/dispatch-client-generic-policies', [WithdrawRequestController::class, 'fetchClientGenericPolicies']);
Route::get('/dispatch-robo-dealer-policies', [WithdrawRequestController::class, 'fetchRoboDealerPolicies']);
Route::get('/dispatch-account-mirroring-policies', [WithdrawRequestController::class, 'fetchAccountMirroringPolicies']);
Route::get('/dispatch-agent-commission-policies', [WithdrawRequestController::class, 'fetchAgentCommissionPolicies']);
Route::get('/dispatch-transfer-user', [WithdrawRequestController::class, 'fetchTransferUser']);

Route::get('/user-devices-job', [WithdrawRequestController::class, 'fetchDeviceTypes']);




Route::get('/ledger-logs/list', [LedgerLogController::class, 'list'])->name('ledger-logs.list');
Route::get('/ledger-logs', [LedgerLogController::class, 'index'])->name('ledger-logs.index');

Route::group(['middleware' => ['auth:web',CheckMacAddress::class]], function ($router) {

    Route::get('/financial-details/{day}/{brand}', [DashboardController::class, 'finDetails'])->name('financial-details');

    Route::get('/segregate-positions/{currency}', [DashboardController::class, 'segregatePositions'])->name('segregate-positions');

    Route::get('/employee-dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::resource('dashboard', DashboardController::class);

    Route::get('/employees/list', [EmployeeController::class, 'list'])->name('employees.list');
    Route::delete('/employees/two-factor-authentication/{employee}', [EmployeeController::class, 'twofactor'])->name('employees.twofactor');
    Route::get('/employees/{employee}/reset', [EmployeeController::class, 'reset'])->name('employees.reset');
    Route::put('/employees/{employee}/password', [EmployeeController::class, 'resetpassword'])->name('employees.password');
    Route::get('/employees/{employee}/mac', [EmployeeController::class, 'mac'])->name('employees.mac');
    Route::put('/employees/{employee}/macaddress', [EmployeeController::class, 'macaddress'])->name('employees.macaddress');
    Route::resource('employees', EmployeeController::class);

    // web.php
    Route::get('/permissions/{parentId}', [RoleController::class, 'getPermissionsByParent']);


    Route::get('/roles/list', [RoleController::class, 'list'])->name('roles.list');
    Route::resource('roles', RoleController::class);

    Route::get('/clients/list', [ClientController::class, 'list'])->name('clients.list');
    Route::get('/clients/{client}/transfer', [ClientController::class, 'transfer'])->name('clients.transfer');
    Route::put('/clients/{client}/transfered', [ClientController::class, 'transfered'])->name('clients.transfered');
    Route::post('/clients/notes', [ClientController::class, 'addNotes'])->name('clients.notes');
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

    Route::get('/transactions/list', [TransactionLogController::class, 'list'])->name('transactions.list');
    Route::resource('transactions', TransactionLogController::class);

    Route::post('/generate-excel-report', [ReportController::class, 'generateExcelReport']);
    Route::get('/financial-report', [ReportController::class, 'findex'])->name('financial-report.index');
    Route::resource('report', ReportController::class);

    Route::get('/aggregate/list', [AggregateController::class, 'list'])->name('aggregate.list');
    Route::resource('aggregate', AggregateController::class);

    Route::get('/margin-limit-menu/list', [MarginLimitMarketController::class, 'list'])->name('margin-limit-menu.list');
    Route::resource('margin-limit-menu', MarginLimitMarketController::class);

    Route::get('/trx-logs', [RiskManagementController::class, 'getTrxLogs'])->name('trx-logs');
    Route::get('/risk-management/list', [RiskManagementController::class, 'list'])->name('risk-management.list');
    Route::get('/more-wl', [RiskManagementController::class, 'moreWL'])->name('moreWL');
    Route::get('/scripts', [RiskManagementController::class, 'scripts'])->name('scripts');
    Route::get('/more-parents', [RiskManagementController::class, 'moreParents'])->name('more-parents');
    Route::get('/market-details/{id}', [RiskManagementController::class, 'marketDetails'])->name('market-details');
    Route::get('/client-details/{id}', [RiskManagementController::class, 'clientDetails'])->name('client-details');
    Route::get('/child-details/{id}', [RiskManagementController::class, 'childDetails'])->name('child-details');
    Route::resource('risk-management', RiskManagementController::class);

    Route::get('/charts', [ChartsController::class, 'index'])->name('charts.index');
    Route::get('/financial-calendar', [ChartsController::class, 'findex'])->name('financial-calendar.index');

    
    Route::resource('two-factor',TwoFactorController::class);

    Route::get('/user-devices/list', [UserDeviceController::class, 'list'])->name('user-devices.list');
    Route::get('/device-details/{id}', [UserDeviceController::class, 'deviceDetail'])->name('device.details');
    Route::post('/update-blacklist', [UserDeviceController::class, 'update'])->name('update.blacklist');
    Route::resource('user-devices',UserDeviceController::class);
    
    

});
