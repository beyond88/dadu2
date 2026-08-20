<!-- ========== Left Sidebar Start ========== -->
<div class="left side-menu">
    <div class="slimscroll-menu" id="remove-scroll">
        <!--- Side Menu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu" id="side-menu">
                <li class="menu-title">{{ __('custom.main') }}</li>
                @can('Dashboard')
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="">
                        <i class="flaticon-dashboard"></i><span> {{ __('custom.dashboard') }} </span>
                    </a>
                </li>
                @endcan
                <li class="menu-title">{{ __('custom.components') }}</li>


                @canany(['Product', 'Product Category', 'Brand', 'Manufacturer','Stock Out Report'])
                <li>
                    <a href="#" class=""><i class="flaticon-new-product"></i><span>
                            {{ __('custom.product') }}
                            <span class="float-right menu-arrow">
                                <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="10 15 15 20 20 15"></polyline>
                                    <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                                </svg>
                            </span>
                        </span></a>
                    <ul class="submenu">
                        @can('Product')
                        <li class="{{ request()->is('admin/products/*') ? 'mm-active' : '' }}"><a
                                href="{{ route('admin.products.index') }}">{{__('custom.product')}}</a></li>
                        @endcan
                        {{-- Product Category, Brand, Manufacturer menu items hidden per request
                        @can('Product Category')
                        <li class="{{ request()->is('admin/product-categories/*') ? 'mm-active' : '' }}"><a
                                href="{{ route('admin.product-categories.index') }}">{{__('custom.product_category')}}</a>
                        </li>
                        @endcan
                        @can('Brand')
                        <li class="{{ request()->is('admin/brands/*') ? 'mm-active' : '' }}"><a
                                href="{{ route('admin.brands.index') }}">{{__('custom.brand')}}</a></li>
                        @endcan
                        @can('Manufacturer')
                        <li class="{{ request()->is('admin/manufacturers/*') ? 'mm-active' : '' }}"><a
                                href="{{ route('admin.manufacturers.index') }}">{{__('custom.manufacturer')}}</a></li>
                        @endcan
                        --}}
                        @can('Stock Out Report')
                        <li class="{{ request()->is('admin/report/stock-out') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.report.stock-out') }}">{{__('custom.stock_out_list')}}</a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcanany



                @canany(['Weight Unit', 'Measurement Unti', 'Attribute'])
                <li class="d-none">
                    <a href="#" class=""><i class="flaticon-pamphlet"></i><span>
                            {{ __('custom.catalog') }}
                            <span class="float-right menu-arrow">
                                <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="10 15 15 20 20 15"></polyline>
                                    <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                                </svg>
                            </span>
                        </span></a>
                    <ul class="submenu">

                        @can('Weight Unit')
                        <li class="{{ request()->is('admin/weight-units/*') ? 'mm-active' : '' }}"><a
                                href="{{ route('admin.weight-units.index') }}">{{__('custom.weight_unit')}}</a></li>
                        @endcan
                        @can('Measurement Unit')
                        <li class="{{ request()->is('admin/measurement-units/*') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.measurement-units.index') }}">{{__('custom.measurement_unit')}}</a>
                        </li>
                        @endcan
                        @can('Attribute')
                        <li class="{{ request()->is('admin/attributes/*') ? 'mm-active' : '' }}"><a
                                href="{{ route('admin.attributes.index') }}">{{__('custom.attribute')}}</a></li>
                        @endcan

                    </ul>
                </li>
                @endcanany
                @canany(['Invoice', 'Draft Invoice'])
                    <li>
                        <a href="#" class=""><i class="flaticon-bill"></i><span>
                            {{ __('custom.invoice_manage') }}
                            <span class="float-right menu-arrow">
                                <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="10 15 15 20 20 15"></polyline>
                                    <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                                </svg>
                            </span>
                        </span></a>
                        <ul class="submenu">
                            @can('Invoice')
                            <li class="{{ request()->is('admin/invoices') || request()->is('admin/invoices/*') ? 'mm-active' : '' }}">
                                <a href="{{ route('admin.invoices.index') }}">{{ __('custom.invoice_list') }}</a>
                            </li>
                            @endcan
                            @can('Draft Invoice')
                            <li class="{{ request()->is('admin/draft-invoices') || request()->is('admin/draft-invoices/*') ? 'mm-active' : '' }}">
                                <a href="{{ route('admin.draft-invoices.index') }}">{{ __('custom.draft_invoice_list') }}</a>
                            </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                {{-- Purchase menu moved up (right after POS/Invoice Manage) per request --}}
                @canany('Purchase')
                <li>
                    <a href="#" class=""><i class="flaticon-shopping-bag-1"></i><span>
                            {{ __t('purchases') }}
                            <span class="float-right menu-arrow">
                                <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="10 15 15 20 20 15"></polyline>
                                    <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                                </svg>
                            </span>
                        </span></a>
                    <ul class="submenu">
                        @can('Purchase')
                        <li><a
                                href="{{ route('admin.purchases.index') }}">{{ __t('purchases') }}</a></li>
                        @endcan
                        @can('Purchase Receive List')
                        <li><a href="{{ route('admin.purchases.receive-list') }}">{{ __t('purchase_receive_list') }}</a>
                        </li>
                        @endcan
                        @can('Purchase Return List')
                        <li><a href="{{ route('admin.purchases.return.list') }}">{{ __t('purchase_return_list') }}</a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcanany


                @can('Sale Return')
                    <li>
                        <a href="#" class=""><i class="flaticon-expenses"></i><span>
                            {{ __('custom.sale_return') }}
                            <span class="float-right menu-arrow">
                                <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                                     xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <polyline points="10 15 15 20 20 15"></polyline>
                                    <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                                </svg>
                            </span>
                        </span></a>
                        <ul class="submenu">
                            @can('Return Sale Return')
                                {{-- Hidden per request: admin/sales-return-create (createable list) --}}
                                {{--
                                <li>
                                    <a href="{{ route('admin.sales-return.createable_list') }}">{{__('custom.sale_return')}}</a>
                                </li>
                                --}}
                            @endcan
                            @can('Sale Return List')
                                <li>
                                    <a href="{{ route('admin.sales-return.index') }}">{{__('custom.sale_return_list')}}</a>
                                </li>
                            @endcan
                            @can('Sale Return Request List')
                                <li class="{{ request()->is('admin/products-return-request/*') ? 'mm-active' : '' }}">
                                    <a href="{{ route('admin.sales-return.requests') }}">{{__('custom.return_requests')}} ({{@$total_pending_req}})</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan


                @canany(['Coupon','Add Coupon','Edit Coupon','Delete Coupon'])
                    <li class="d-none">
                        <a href="#" class=""><i class="mdi mdi-percent"></i><span>
                            {{ __t('Marketing') }}
                            <span class="float-right menu-arrow">
                                <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                                     xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <polyline points="10 15 15 20 20 15"></polyline>
                                    <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                                </svg>
                            </span>
                        </span></a>
                        <ul class="submenu">
                            @can('Coupon')
                           <li class="{{ request()->is('admin/coupons/*') || request()->is('admin/coupon-products/*') ? 'mm-active' : '' }}"><a
                                   href="{{ route('admin.coupons.index') }}">{{ __t('coupon') }}</a></li>
                           @endcan
                        </ul>
                    </li>
                @endcanany

                @can('Customer')
                <li class="{{ request()->is('admin/customers/*') ? 'mm-active': '' }}">
                    <a class="{{ request()->is('admin/customers/*') ? 'mm-active' : '' }}"
                        href="{{ route('admin.customers.index') }}" class="">
                        <i class="flaticon-conversation"></i><span> {{ __('custom.customers') }} </span>
                    </a>
                </li>
                @endcan
                @can('Supplier')
                <li class="{{ request()->is('admin/suppliers/*') ? 'mm-active': '' }}">
                    <a class="{{ request()->is('admin/suppliers/*') ? 'mm-active' : '' }}"
                        href="{{ route('admin.suppliers.index') }}" class="">
                        <i class="flaticon-conversation"></i><span> {{ __('custom.suppliers') }} </span>
                    </a>
                </li>
                @endcan
                @can('Bank List')
                <li class="{{ request()->is('admin/banks/*') ? 'mm-active': '' }}">
                    <a class="{{ request()->is('admin/banks/*') ? 'mm-active' : '' }}"
                        href="{{ route('admin.banks.index') }}" class="">
                        <i class="mdi mdi-bank"></i><span> {{ __('custom.banks') }} </span>
                    </a>
                </li>
                @endcan

                @canany(['Expenses Category', 'Expenses'])
                <li>
                    <a href="#" class=""><i class="flaticon-expenses"></i><span>
                            {{ __('custom.expenses') }}
                            <span class="float-right menu-arrow">
                                <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="10 15 15 20 20 15"></polyline>
                                    <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                                </svg>
                            </span>
                        </span></a>
                    <ul class="submenu">
                        @can('Expenses Category')
                        <li class="{{ request()->is('admin/expenses-categories/*') ? 'mm-active' : '' }}">
                            <a
                                href="{{ route('admin.expenses-categories.index') }}">{{__('custom.expenses_category')}}</a>
                        </li>
                        @endcan
                        @can('Expenses')
                        <li class="{{ request()->is('admin/expenses/*') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.expenses.index') }}">{{__('custom.expenses')}}</a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcanany



                @can('Reports')
                <li>
                    <a href="#" class=""><i class="flaticon-report"></i><span>
                            {{ __('custom.reports') }}
                            <span class="float-right menu-arrow">
                                <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="10 15 15 20 20 15"></polyline>
                                    <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                                </svg>
                            </span>
                        </span></a>
                    <ul class="submenu">
                        @can('Customer Ledger Report')
                        <li>
                            <a href="{{ route('admin.report.customer-ledger') }}">{{__('custom.customer_ledger_report')}}</a>
                        </li>
                        @endcan
                        @can('Expenses Report')
                        <li>
                            <a href="{{ route('admin.reports.expenses') }}">{{__('custom.expenses_report')}}</a>
                        </li>
                        @endcan
                        @can('Sales Report')
                        <li>
                            <a href="{{ route('admin.reports.sales') }}">{{__('custom.sales_report')}}</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.reports.balance') }}">{{__('custom.balance_report')}}</a>
                        </li>
                        @endcan
                        @can('Expired Products Report')
                        <li>
                            <a href="{{ route('admin.report.expired-products') }}">{{__('custom.expired_product_report')}}</a>
                        </li>
                        @endcan
                        @can('Stock Out Report')
                        <li>
                            <a href="{{ route('admin.report.stock-out') }}">{{__('custom.stock_out_report')}}</a>
                        </li>
                        @endcan
                        @can('Purchases Report')
                        <li>
                            <a href="{{ route('admin.reports.purchases') }}">{{__('custom.purchases_report')}}</a>
                        </li>
                        @endcan
                        @can('Payments Report')
                        <li>
                            <a href="{{ route('admin.reports.payments') }}">{{__('custom.payments_report')}}</a>
                        </li>
                        @endcan
                        @can('Payments Report')
                        <li>
                            <a href="{{ route('admin.report.warehouse-stock') }}">{{__('custom.warehouse_stock_report')}}</a>
                        </li>
                        @endcan
                        @can('Warehouse Selling Price Report')
                        <li>
                            <a href="{{ route('admin.report.warehouse-price') }}">{{__('custom.warehouse_selling_price_report')}}</a>
                        </li>
                        @endcan

                        @can('Payments Report')
                        <li>
                            <a href="{{ route('admin.report.loss-profit') }}">{{__('custom.loss_profit_report')}}</a>
                        </li>
                        @endcan
                        @can('Supplier Payment History')
                        <li>
                            <a href="{{ route('admin.report.supplier-payment-history') }}">{{__('custom.supplier_payment_history')}}</a>
                        </li>
                        @endcan

                    </ul>
                </li>
                @endcan

                {{-- Warehouse menu moved here (after Reports) per request --}}
                @can('Warehouse')
                <li>
                    <a href="#" class="">
                        <i class="ti-home"></i>
                        <span>
                            {{ __('custom.warehouse') }}
                            <span class="float-right menu-arrow">
                                <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="10 15 15 20 20 15"></polyline>
                                    <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                                </svg>
                            </span>
                        </span>
                    </a>
                    <ul class="submenu">
                        <li class="{{ request()->is('admin/warehouses') || (request()->is('admin/warehouses/*') && !request()->is('admin/warehouse-transfers*')) ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.warehouses.index') }}">{{ __('custom.warehouse') }}</a>
                        </li>
                        <li class="{{ request()->is('admin/warehouse-transfers*') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.warehouse-transfers.create') }}">Transfer</a>
                        </li>
                    </ul>
                </li>
                @endcan
               

                @canany(['Account List', 'Transaction List'])
                <li class="menu-title">{{ __('custom.account_management') }}</li>
                @can('Account List')
                <li class="{{ request()->is('admin/accounts/*') ? 'mm-active': '' }}">
                    <a class="{{ request()->is('admin/accounts/*') ? 'mm-active' : '' }}"
                        href="{{ route('admin.accounts.index') }}" class="">
                        <i class="mdi mdi-bank"></i><span> {{ __('custom.accounts') }} </span>
                    </a>
                </li>
                @endcan
                @can('Transaction List')
                <li class="{{ request()->is('admin/transactions/*') ? 'mm-active': '' }}">
                    <a class="{{ request()->is('admin/transactions/*') ? 'mm-active' : '' }}"
                        href="{{ route('admin.transactions.index') }}" class="">
                        <i class="mdi mdi-swap-horizontal"></i><span> {{ __('custom.transactions') }} </span>
                    </a>
                </li>
                @endcan
                @endcanany

                {{-- Loan Management menu hidden per request --}}
                {{--
                @canany(['Loan List', 'Loan Create', 'Loan Transaction History'])
                <li class="menu-title">{{ __('custom.loan_management') }}</li>
                <li>
                    <a href="#" class=""><i class="mdi mdi-bank-transfer"></i><span>
                            {{ __('custom.loan_management') }}
                            <span class="float-right menu-arrow">
                                <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="10 15 15 20 20 15"></polyline>
                                    <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                                </svg>
                            </span>
                        </span></a>
                    <ul class="submenu">
                        @can('Loan List')
                        <li class="{{ request()->is('admin/loans') || request()->is('admin/loans/*') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.loans.index') }}">{{ __('custom.loan_list') }}</a>
                        </li>
                        @endcan
                        @can('Loan Create')
                        <li class="{{ request()->is('admin/loans/create') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.loans.create') }}">{{ __('custom.add_loan') }}</a>
                        </li>
                        @endcan
                        @can('Loan Transaction History')
                        <li class="{{ request()->is('admin/loans-transactions') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.loans.transactions') }}">{{ __('custom.loan_transaction_history') }}</a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcanany
                --}}

                @canany(['Capital List', 'Capital Create', 'Capital Transaction History'])
                <li class="menu-title">{{ __('custom.capital_management') }}</li>
                <li>
                    <a href="#" class=""><i class="mdi mdi-cash-multiple"></i><span>
                            {{ __('custom.capital_management') }}
                            <span class="float-right menu-arrow">
                                <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="10 15 15 20 20 15"></polyline>
                                    <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                                </svg>
                            </span>
                        </span></a>
                    <ul class="submenu">
                        @can('Capital List')
                        <li class="{{ request()->is('admin/capitals') || request()->is('admin/capitals/*') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.capitals.index') }}">{{ __('custom.capital_list') }}</a>
                        </li>
                        @endcan
                        @can('Capital Create')
                        <li class="{{ request()->is('admin/capitals/create') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.capitals.create') }}">{{ __('custom.add_capital') }}</a>
                        </li>
                        @endcan
                        @can('Capital Transaction History')
                        <li class="{{ request()->is('admin/capitals-transactions') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.capitals.transactions') }}">{{ __('custom.capital_transaction_history') }}</a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcanany

                <li class="menu-title">{{ __('custom.lc_management') }}</li>
                <li>
                    <a href="#" class=""><i class="mdi mdi-file-document-box-multiple"></i><span>
                            {{ __('custom.lc_management') }}
                            <span class="float-right menu-arrow">
                                <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="10 15 15 20 20 15"></polyline>
                                    <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                                </svg>
                            </span>
                        </span></a>
                    <ul class="submenu">
                        <li class="{{ request()->is('admin/lcs') || request()->is('admin/lcs/*') && !request()->is('admin/lcs/create') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.lcs.index') }}">{{ __('custom.lc_list') }}</a>
                        </li>
                        <li class="{{ request()->is('admin/lcs/create') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.lcs.create') }}">{{ __('custom.add_lc') }}</a>
                        </li>
                    </ul>
                </li>

                @if(auth()->check() && auth()->user()->hasRole('Super Admin'))
                <li class="menu-title">{{ __('custom.db_management') }}</li>
                <li>
                    <a href="#" class=""><i class="mdi mdi-database"></i><span>
                            {{ __('custom.db_management') }}
                            <span class="float-right menu-arrow">
                                <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="10 15 15 20 20 15"></polyline>
                                    <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                                </svg>
                            </span>
                        </span></a>
                    <ul class="submenu">
                        <li class="{{ request()->is('admin/database') || request()->is('admin/database/*') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.database.index') }}">{{ __('custom.db_export_import') }}</a>
                        </li>
                    </ul>
                </li>
                @endif

                @can('Settings')
{{--                <li>--}}
{{--                    <a href="{{ route('admin.system-settings.edit') }}" class="">--}}
{{--                        <i class="ti-settings"></i><span> {{ __('custom.settings') }} </span>--}}
{{--                    </a>--}}
{{--                </li>--}}
                    <li>
                        <a href="#" class=""><i class="ti-settings"></i><span>
                            {{ __('custom.settings') }}
                                <span class="float-right menu-arrow">
                                <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                                     xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <polyline points="10 15 15 20 20 15"></polyline>
                                    <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                                </svg>
                            </span>

                        </span></a>
                        <ul class="submenu">
                            @can('Settings')
                                <li>
                                    <a href="{{ route('admin.system-settings.edit') }}">{{ __('custom.settings') }}</a>
                                </li>
                            @endcan
                            <li>
                                <a href="{{ route('admin.countries.index') }}">{{__('custom.country')}}</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.states.index') }}">{{__('custom.state')}}</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.cities.index') }}">{{__('custom.city')}}</a>
                            </li>

                        </ul>
                    </li>
                @endcan

                {{-- Administration menu moved to bottom per request --}}
                @canany(['User', 'Role'])
                <li>
                    <a href="#" class=""><i class="flaticon-working"></i><span> {{
                            __('custom.administration') }}
                            <span class="float-right menu-arrow">
                                <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="10 15 15 20 20 15"></polyline>
                                    <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                                </svg>
                            </span>
                        </span>
                    </a>
                    <ul class="submenu">
                        @can('User')
                        <li class="{{ request()->is('admin/users/*') ? 'mm-active' : '' }}"><a
                                href="{{ route('admin.users.index') }}">{{ __('custom.users') }}</a>
                        </li>
                        @endcan
                        @can('Role')
                        <li class="{{ request()->is('admin/roles/*') ? 'mm-active' : '' }}"><a
                                href="{{ route('admin.roles.index') }}">{{ __('custom.roles') }}</a></li>
                        @endcan
                    </ul>
                </li>
                @endcanany

            </ul>
        </div>
        <!-- Sidebar -->
        <div class="clearfix"></div>

    </div>
    <!-- Sidebar -left -->

</div>
<!-- Left Sidebar End -->
