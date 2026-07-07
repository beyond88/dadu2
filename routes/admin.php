<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\Axios\AxiosController;
use App\Http\Controllers\Admin\Coupon\CouponController;
use App\Http\Controllers\Admin\Purchase\PurchaseController;
use App\Http\Controllers\Admin\Customer\CustomersController;
use App\Http\Controllers\Admin\Coupon\CouponProductController;
use App\Http\Controllers\Admin\Sale\SaleReturnRequestController;
use App\Http\Controllers\Admin\Purchase\PurchaseReturnController;
use App\Http\Controllers\Admin\Purchase\PurchaseReceiveController;
use App\Http\Controllers\Admin\Purchase\PurchasePaymentController;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\AddonController;
use App\Http\Controllers\Admin\LoanController;
use App\Http\Controllers\Admin\CapitalController;
use App\Http\Controllers\Admin\LcController;
use App\Http\Controllers\Admin\DatabaseController;
use App\Http\Controllers\Admin\Invoice\DraftInvoiceController;

Route::prefix('admin')->as('admin.')->middleware(['auth', 'isInstalled'])->group(function () {
    //................LOCATION
    //     Countries
    Route::resource('countries', Location\CountriesController::class);
    //     States
    Route::resource('states', Location\StatesController::class);
    //     City
    Route::resource('cities', Location\CitiesController::class);
});
Route::namespace('Admin')->prefix('admin')->as('admin.')->middleware(['auth', 'isInstalled'])->group(function () {


    Route::get('set-lang', 'DashboardController@setLang')->name('set-lang');
    // DASHBOARD
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
    // USER
    Route::resource('users', Administration\UsersController::class);
    // ROLE
    Route::resource('roles', Administration\RolesController::class);
    // WAREHOUSE
    Route::resource('warehouses', Warehouse\WarehousesController::class);
    Route::get('warehouses/{warehouse}/show-pdf', "Warehouse\WarehousesController@showPdf")->name('warehouses.show-pdf');
    Route::get('warehouses/{warehouse}/export-excel', "Warehouse\WarehousesController@exportExcel")->name('warehouses.export-excel');
    Route::get('warehouse/barcode/{id}', 'Warehouse\WarehousesController@barcodeDownload')->name('warehouse.barcode.download');

    Route::get('warehouses/{warehouse}/show-storage-store-and-out', 'Warehouse\WarehousesController@showStorageStoreAndOut')->name('warehouses.show-storage-store-and-out');

    // Warehouse Transfer
    Route::get('warehouse-transfers/create', 'Warehouse\WarehouseTransferController@create')->name('warehouse-transfers.create');
    Route::post('warehouse-transfers', 'Warehouse\WarehouseTransferController@store')->name('warehouse-transfers.store');

    // BRAND
    Route::resource('brands', Brand\BrandsController::class);
    Route::post('brands/import', 'Brand\BrandsController@import')->name('brands.import');
    // MANUFACTURER
    Route::resource('manufacturers', Manufacturer\ManufacturersController::class);
    Route::post('manufacturers/import', 'Manufacturer\ManufacturersController@import')->name('manufacturers.import');

    // BANKS
    Route::resource('banks', BankController::class);
    Route::put('banks/{bank}/toggle-status', 'BankController@toggleStatus')->name('banks.toggle-status');

    // ACCOUNTS
    Route::resource('accounts', AccountController::class);
    Route::put('accounts/{account}/toggle-status', 'AccountController@toggleStatus')->name('accounts.toggle-status');
    Route::get('accounts/{account}/balance', 'AccountController@getBalance')->name('accounts.balance');
    Route::get('accounts/type/{type}', 'AccountController@getAccountsByType')->name('accounts.by-type');

    // TRANSACTIONS
    Route::get('transactions/account/by-code', 'TransactionController@accountByCode')->name('transactions.account-by-code');
    Route::post('transactions/quick-store', 'TransactionController@quickStore')->name('transactions.quick-store');
    Route::resource('transactions', TransactionController::class);
    Route::get('transactions/transfer/form', 'TransactionController@transferForm')->name('transactions.transfer-form');
    Route::post('transactions/transfer', 'TransactionController@transfer')->name('transactions.transfer');
    Route::get('accounts/{account}/statement', 'TransactionController@accountStatement')->name('transactions.statement');
    Route::get('transactions/api/store', 'TransactionController@apiStore')->name('transactions.api-store');

    // LOAN MANAGEMENT
    Route::resource('loans', LoanController::class);
    Route::post('loans/{loan}/payment', [LoanController::class, 'storePayment'])->name('loans.payment.store');
    Route::delete('loans/{loan}/payment/{payment}', [LoanController::class, 'destroyPayment'])->name('loans.payment.destroy');
    Route::get('loans-transactions', [LoanController::class, 'transactionHistory'])->name('loans.transactions');

    // CAPITAL MANAGEMENT
    Route::resource('capitals', CapitalController::class);
    Route::post('capitals/{capital}/payment', [CapitalController::class, 'storePayment'])->name('capitals.payment.store');
    Route::delete('capitals/{capital}/payment/{payment}', [CapitalController::class, 'destroyPayment'])->name('capitals.payment.destroy');
    Route::post('capitals/{capital}/add-amount', [CapitalController::class, 'addAmount'])->name('capitals.add-amount');
    Route::get('capitals-transactions', [CapitalController::class, 'transactionHistory'])->name('capitals.transactions');

    // LC MANAGEMENT
    Route::resource('lcs', LcController::class);

    // DB MANAGEMENT (Super Admin only - enforced in controller)
    Route::get('database', [DatabaseController::class, 'index'])->name('database.index');
    Route::get('database/export', [DatabaseController::class, 'export'])->name('database.export');
    Route::post('database/import', [DatabaseController::class, 'import'])->name('database.import');
    Route::delete('database/log/{log}', [DatabaseController::class, 'destroyLog'])->name('database.log.destroy');

    // WEIGHT UNIT
    Route::resource('weight-units', WeightUnit\WeightUnitsController::class);
    // MEASUREMENT UNIT
    Route::resource('measurement-units', MeasurementUnit\MeasurementUnitsController::class);
    // PRODUCT CATEGORY
    Route::resource('product-categories', Product\ProductCategoriesController::class);
    Route::post('product-categories/import', 'Product\ProductCategoriesController@import')->name('product-categories.import');
    // ATTRIBUTE
    Route::resource('attributes', Attribute\AttributesController::class);
    // PRODUCT
    Route::resource('products', Product\ProductsController::class);
    Route::post('/products/import', "Product\ProductsController@import")->name('products.import');
    Route::group(['prefix' => 'addons'], function () {
        Route::get('installed', [AddonController::class, 'installAddons'])->name('admin.installed.addon');
        Route::get('available', [AddonController::class, 'availableAddons'])->name('admin.available.addons');
        Route::post('new-install', [AddonController::class, 'installNewAddon'])->name('install.new.addon');


    });
    Route::put('addon-status-change', [AddonController::class, 'statusChange'])->name('addon.status.change');

    Route::get('product/barcode/{id}', 'Product\ProductsController@barcodeDownload')->name('products.barcode.download');
    Route::post('product/barcode-zip', 'Product\ProductsController@barcodeDownloadZip')->name('products.barcode.download.zip');

    // PRODUCT STOCK
    Route::resource('product-stocks', Stock\ProductStocksController::class)->only(['update', 'edit']);

    Route::put('product-stocks.update-by-stock/{id}', 'Stock\ProductStocksController@updateByStock')->name('product-stocks.update-by-stock');

    Route::get('low-stock-products', 'Stock\ProductStocksController@index')->name('low-stock-products');
        // Company
    Route::resource('companies',Company\CompanyController::class);
    // CUSTOMER
    Route::resource('customers', Customer\CustomersController::class);
    Route::get('customers/{customer}/export-invoices-history', 'Customer\CustomersController@exportInvoicesHistory')
        ->name('customers.export-invoices-history');
    Route::get('customers/{customer}/export-topay-history', 'Customer\CustomersController@exportToPayHistory')
        ->name('customers.export-topay-history');
    Route::get('customers/{customer}/export-products-history', 'Customer\CustomersController@exportProductsHistory')
        ->name('customers.export-products-history');
    Route::get('customers/verify/{id}', [CustomersController::class, 'verifyUnverify'])->name('customers.verify');

    // Customer Payments
    Route::get('customers/{customer}/payment/create', [App\Http\Controllers\Admin\Invoice\InvoicePaymentController::class, 'createCustomerPayment'])
        ->name('customers.payment.create');
    Route::post('customers/{customer}/payment', [App\Http\Controllers\Admin\Invoice\InvoicePaymentController::class, 'storeCustomerPayment'])
        ->name('customers.payment.store');
    Route::get('customers/{customer}/payment/history', [App\Http\Controllers\Admin\Invoice\InvoicePaymentController::class, 'customerPaymentHistory'])
        ->name('customers.payment.history');

    // SUPPLIER
    Route::resource('suppliers', Supplier\SuppliersController::class);
    Route::get('suppliers/{supplier}/export-purchases-history', 'Supplier\SuppliersController@exportPurchasesHistory')
        ->name('suppliers.export-purchases-history');
    Route::get('suppliers/{supplier}/export-products-history', 'Supplier\SuppliersController@exportProductsHistory')
        ->name('suppliers.export-products-history');
    
    // Supplier Payments
    Route::get('suppliers/{supplier}/payment/create', [PurchasePaymentController::class, 'createSupplierPayment'])
        ->name('suppliers.payment.create');
    Route::post('suppliers/{supplier}/payment', [PurchasePaymentController::class, 'storeSupplierPayment'])
        ->name('suppliers.payment.store');
    Route::get('suppliers/{supplier}/payment/history', [PurchasePaymentController::class, 'supplierPaymentHistory'])
        ->name('suppliers.payment.history');
    // EXPENSES CATEGORY
    Route::resource('expenses-categories', Expenses\ExpensesCategoriesController::class);
    // EXPENSES
    Route::resource('expenses', Expenses\ExpensesController::class);
    Route::delete('expenses/file/delete/{file_id}', 'Expenses\ExpensesController@deleteFile')->name('expenses.deleteFile');
    Route::post('expenses/import', 'Expenses\ExpensesController@import')->name('expenses.import');
    // WithDrawal
    Route::resource('withdrawals', 'Withdraw\WithdrawalController');
    Route::get('withdrawals/download/{id}', 'Withdraw\WithdrawalController@download')->name('withdrawals.download');
    Route::get('withdrawals-print/{id}', 'Withdraw\WithdrawalController@print')->name('withdrawals.print');

    // INVOICE
    Route::resource('invoices', 'Invoice\InvoicesController');
    Route::get('invoices/download/{id}', 'Invoice\InvoicesController@download')->name('invoices.download');
    Route::get('invoices/delivered/{id}/{status}', 'Invoice\InvoicesController@deliveryStatusChange')->name('invoices.delivery.status.change');
    Route::post('invoices/payments', 'Invoice\InvoicesController@addPayment')->name('invoices.add_payment');
    Route::get('invoices/payments/{invoice_id}', 'Invoice\InvoicesController@getPayments')->name('invoices.get_payments');
    Route::post('invoices/payments/send', 'Invoice\InvoicesController@sendInvoice')->name('invoices.sendInvoice');
    Route::get('invoices/payments/delete/{id}', 'Invoice\InvoicesController@deletePayment')->name('invoices.delete_payment');
    Route::get('invoices/make-payment/{id}', 'Invoice\InvoicesController@makePayment')->name('invoices.makePayment');
    Route::post('invoices/make-payment/{id}', 'Invoice\InvoicesController@makePaymentPost')->name('invoices.makePaymentPost');
    Route::get('invoices/customer-email/{id}', 'Invoice\InvoicesController@invoiceCustomerEmail');
    Route::get('invoices-print/{id}', 'Invoice\InvoicesController@print')->name('invoice.print');

    // DRAFT INVOICE (Admin)
    Route::resource('draft-invoices', DraftInvoiceController::class)->only(['index', 'store', 'show', 'destroy']);
    Route::get('draft-invoices/{id}/download', [DraftInvoiceController::class, 'download'])->name('draft-invoices.download');
    Route::get('draft-to-invoice/{id}', [DraftInvoiceController::class, 'draftToInvoice'])->name('draft-invoices.draft-to-invoice');
    Route::post('invoices/store/from-draft/{id}', [DraftInvoiceController::class, 'storeDraftToInvoice'])->name('draft-invoices.store.from-draft');

    // SALE
    Route::resource('sales', 'Sale\SalesController');
    //Notifications
    Route::get('/markasread-all', [NotificationController::class, 'markAsReadAll'])->name('markasreadall');
    Route::post('/notifications/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    // SALE RETURN
    Route::resource('sales-return', 'Sale\SaleReturnController')->except(['create']);
    Route::get('sales-return/{sale_id}/create', 'Sale\SaleReturnController@create')
        ->name('sales-return.create');
    Route::get('sales-return-create', 'Sale\SaleReturnController@createList')
        ->name('sales-return.createable_list');
    Route::get('sales-return-requests', [SaleReturnRequestController::class, 'returnRequestList'])
        ->name('sales-return.requests');
    Route::get('products-return-request/{id}', [SaleReturnRequestController::class, 'returnRequestShow'])
        ->name('products-return-request.show');
    Route::get('products-return-request/accept/{id}', [SaleReturnRequestController::class, 'returnRequestAccept'])
        ->name('products-return-request.accept');
    Route::get('products-return-request/reject/{id}', [SaleReturnRequestController::class, 'returnRequestReject'])
        ->name('products-return-request.reject');

    // Purchase
    Route::resource('purchases', Purchase\PurchaseController::class);

    Route::get('purchases/{purchase}/cancel', [PurchaseController::class, 'cancelPurchase'])
        ->name('purchases.cancel');
    Route::post('purchases/{purchase}/cancel', [PurchaseController::class, 'storeCancelPurchase'])
        ->name('purchases.cancelPost');
    Route::get('purchases/{purchase}/confirm', [PurchaseController::class, 'confirmPurchase'])
        ->name('purchases.confirm');

    // Purchase Receive
    Route::get('purchases/{purchase}/receive', [PurchaseReceiveController::class, 'purchasesReceive'])
        ->name('purchases.receive');
    Route::post('purchases/{purchase}/receive', [PurchaseReceiveController::class, 'storePurchasesReceive'])
        ->name('purchases.receive.store');
    Route::get('purchases/receive/list', [PurchaseReceiveController::class, 'receives'])
        ->name('purchases.receive-list');
    Route::get('purchases/receive/show/{id}', [PurchaseReceiveController::class, 'receiveShow'])
        ->name('purchases.receive.show');
    Route::delete('purchases/receive/delete/{id}', [PurchaseReceiveController::class, 'receiveDelete'])
        ->name('purchases.receive.delete');

    // Purchase Return

    Route::get('purchases/{purchase}/return', [PurchaseReturnController::class, 'purchaseReturn'])
        ->name('purchases.return');
    Route::post('purchases/{purchase}/return', [PurchaseReturnController::class, 'storePurchaseReturn'])
        ->name('purchases.return.store');
    Route::get('purchases/return/list', [PurchaseReturnController::class, 'purchaseReturnList'])
        ->name('purchases.return.list');
    Route::get('purchases/return/show/{id}', [PurchaseReturnController::class, 'returnShow'])
        ->name('purchases.return.show');
    Route::delete('purchases/return/delete/{id}', [PurchaseReturnController::class, 'returnDelete'])
        ->name('purchases.return.delete');

    // Purchase Payments
    Route::get('purchases/{purchase}/payment/create', [PurchasePaymentController::class, 'create'])
        ->name('purchases.payment.create');
    Route::post('purchases/{purchase}/payment', [PurchasePaymentController::class, 'store'])
        ->name('purchases.payment.store');
    Route::get('purchases/{purchase}/payment/history', [PurchasePaymentController::class, 'history'])
        ->name('purchases.payment.history');
    Route::delete('purchases/payment/{payment}', [PurchasePaymentController::class, 'destroy'])
        ->name('purchases.payment.destroy');


    //COUPON
    Route::resource('coupons', Coupon\CouponController::class);
    Route::get('coupon-products/{id}', [CouponProductController::class, 'index'])->name('coupon.products');
    Route::post('coupon-products/store', [CouponProductController::class, 'store'])->name('coupon.product.store');
    Route::delete('coupon-products/destroy/{id}', [CouponProductController::class, 'destroy'])->name('coupon.product.destroy');

    // REPORTS
    Route::get('reports/expenses', 'Report\ReportsController@expenses')->name('reports.expenses');
    Route::get('reports/export/expenses', 'Report\ReportsController@exportExpenses')->name('reports.export.expenses');
    Route::get('reports/sales', 'Report\ReportsController@sales')->name('reports.sales');
    Route::get('reports/balance', 'Report\ReportsController@balance')->name('reports.balance');
    Route::get('reports/export/balance', 'Report\ReportsController@exportBalance')->name('reports.export.balance');
    Route::get('reports/export/sales', 'Report\ReportsController@exportSales')->name('reports.export.sales');
    Route::get('reports/purchases', 'Report\ReportsController@purchases')->name('reports.purchases');
    Route::get('reports/export/purchases', 'Report\ReportsController@exportPurchases')->name('reports.export.purchases');
    Route::get('reports/payments', 'Report\ReportsController@payments')->name('reports.payments');
    Route::get('reports/export/payments', 'Report\ReportsController@exportPayments')->name('reports.export.payments');
    Route::get('reports/stock', 'Report\ReportsController@stock')->name('reports.stock');
    Route::get('reports/withdraw-products', 'Report\ReportsController@generateWithdrawReport')->name('report.withdraw-products');
    Route::get('reports/withdraw-products', 'Report\ReportsController@generateWithdrawReport')->name('report.withdraw-products');
    Route::get('reports/stock-movements', 'Report\ReportsController@stockMovements')->name('report.stock-movements');
    Route::get('reports/stock-change-over-period', 'Report\ReportsController@stockChangeOverPeriod')->name('report.stock-change-over-period');


    Route::get('reports/warehouse-stock', 'Report\ReportsController@warehouseStock')->name('report.warehouse-stock');
    Route::get('reports/expired-products', 'Report\ReportsController@expiredProducts')->name('report.expired-products');
    Route::get('reports/warehouse-price', 'Report\ReportsController@warehousePrice')->name('report.warehouse-price');

    Route::get('reports/loss-profit', 'Report\ReportsController@lossProfit')->name('report.loss-profit');
    Route::get('reports/stock-out', 'Report\ReportsController@stockOutReport')->name('report.stock-out');

    // Supplier Payment History Report
    Route::get('reports/supplier-payment-history', 'Report\SupplierPaymentReportController@index')->name('report.supplier-payment-history');
    Route::get('reports/supplier-payment-history/export', 'Report\SupplierPaymentReportController@export')->name('report.supplier-payment-history.export');

    // SYSTEM SETTINGS
    Route::get('system-settings', 'Settings\SystemSettingsController@edit')->name('system-settings.edit');
    Route::post('system-settings', 'Settings\SystemSettingsController@update')->name('system-settings.update');

    // PROFILE
    Route::get('profile', 'Administration\UsersController@profile')->name('user.profile');
    Route::put('profile/{profile}', 'Administration\UsersController@updateProfile')->name('user.profile.update');

    // HANDLE AJAX
    Route::prefix('api')->group(function () {
        // Attribute items
        Route::get('attribute-items/{id}', 'Attribute\AttributesController@attributeItems');

        Route::get('/product/search/name-sku/{query}', [AxiosController::class, 'productSearchNameSku'])->name('search-product-name-sku');
        Route::get('/product-stock/search/name-sku/{query}', [AxiosController::class, 'productStockSearchNameSku'])->name('search-product-name-sku');
        Route::get('/product-stock/search/name-sku/{query}/{warehouse_id}', [AxiosController::class, 'productStockSearchByWarehouse']);
        Route::get('/purchase_item/delete/{query}', [AxiosController::class, 'purchaseItemDelete']);
    });

    // HANDLE API REQUEST
    Route::prefix('app/api')->as('app-api.')->group(function () {
        // TODO: Because this api only handle forntend API and ajax
        // TODO: request so added "app" at prefix. its done for future.
        // TODO: If user try to build full feature API they can use "/api" endpint
        // Thanks me later


        // Search product by sku
        Route::get('/products/skus/search/{query}', 'Product\ProductsController@productSkuSearch');
           // Search product by name and sku
        Route::get('/products/name-sku/search/{query}', 'Product\ProductsController@productSearchByNameSku');
        Route::get('/product-stocks/name-sku/search/{query}/{warehouse_id}', 'Product\ProductsController@productStockSearchByNameSku');
        Route::get('/products/warehouse/search/{query}', 'Product\ProductsController@productSearchByWarehouse');
        Route::get('/product-stocks/warehouse/search/{id}', 'Product\ProductsController@productStockSearchByWarehouse');
        // Get product by category
        Route::get('/products/category/{id}', 'Product\ProductsController@getByCategory');
        Route::get('/product-stocks/category/{id}/{warehouse_id}', 'Product\ProductsController@getProductStockByCategory');
        // Get product by barcode
        Route::get('/products/barcode/{barcode}', 'Product\ProductsController@getByBarcode');
        Route::get('/product-stocks/barcode/{barcode}', 'Product\ProductsController@getProductStockByBarcode');

        Route::get('/active-coupon/{code}', [CouponController::class, 'getActiveCouponByCode']);

        Route::get('/products/stack-update/{query}', 'Product\ProductsController@productQtyUpdate');


        // Dashboard product filter by (month, year, week)
        Route::get('top-product', 'DashboardController@getTopProduct')
            ->name('get-top-product');
    });
});
