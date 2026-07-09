@extends('admin.layouts.master')

@section('content')
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">{{ __('custom.add_transaction') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#" class="ic-javascriptVoid">{{ __('custom.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.transactions.index') }}">{{ __('custom.transactions') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('custom.add_transaction') }}</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card ic-txn-entry">
                <div class="card-body">
                    <h4 class="mt-0 header-title">{{ __('custom.transaction_information') }}</h4>

                    {{-- ===== Entry row ===== --}}
                    <div class="form-row align-items-end ic-entry-row">
                        <div class="form-group col-6 col-md-1">
                            <label>{{ __('custom.transaction_no') }}</label>
                            <input type="text" id="transaction_no" class="form-control form-control-sm text-primary font-weight-bold"
                                   value="{{ $nextTransactionNo }}" readonly>
                        </div>
                        <div class="form-group col-6 col-md-2">
                            <label>{{ __('custom.account_code') }} <span class="text-danger">*</span></label>
                            <div class="ic-combo">
                                <input type="text" id="account_code" class="form-control form-control-sm" autocomplete="off"
                                       placeholder="{{ __('custom.account_code') }}" autofocus>
                                <ul class="ic-combo-list" id="account_code_list">
                                    @foreach($accounts as $account)
                                        @if($account->code)
                                            <li class="ic-combo-item" data-code="{{ $account->code }}" data-search="{{ strtolower($account->code.' '.$account->name) }}">
                                                <span class="ic-combo-code">{{ $account->code }}</span>
                                                <span class="ic-combo-name">{{ $account->name }}</span>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="form-group col-12 col-md-4">
                            <label>{{ __('custom.description') }}</label>
                            <input type="text" id="description" class="form-control form-control-sm" autocomplete="off"
                                   placeholder="{{ __('custom.description') }}">
                        </div>
                        <div class="form-group col-4 col-md-2">
                            <label>{{ __('custom.date') }}</label>
                            <input type="date" id="trans_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                        </div>
                        <div class="form-group col-4 col-md-1-5">
                            <label>{{ __('custom.debit') }}</label>
                            <input type="number" step="0.01" min="0" id="debit" class="form-control form-control-sm text-right" placeholder="0.00">
                        </div>
                        <div class="form-group col-4 col-md-1-5">
                            <label>{{ __('custom.credit') }}</label>
                            <input type="number" step="0.01" min="0" id="credit" class="form-control form-control-sm text-right" placeholder="0.00">
                        </div>
                    </div>

                    <input type="hidden" id="account_id" value="">

                    {{-- ===== Transactions grid ===== --}}
                    <div class="table-responsive ic-grid-wrap">
                        <table class="table table-sm table-bordered mb-0 ic-txn-grid">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width:70px;">{{ __('custom.transaction_no') }}</th>
                                    <th style="width:150px;">{{ __('custom.account_no') }}</th>
                                    <th>{{ __('custom.description') }}</th>
                                    <th style="width:110px;">{{ __('custom.date') }}</th>
                                    <th style="width:130px;" class="text-right">{{ __('custom.debit') }}</th>
                                    <th style="width:130px;" class="text-right">{{ __('custom.credit') }}</th>
                                </tr>
                            </thead>
                            <tbody id="txn-grid-body">
                                <tr class="ic-grid-empty">
                                    <td colspan="6" class="text-center text-muted py-3">
                                        {{ __('custom.load_account_first') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- ===== Account summary ===== --}}
                    <div class="ic-summary mt-3">
                        <div class="form-row">
                            <div class="form-group col-12 col-md-3">
                                <label>{{ __('custom.account_no') }}</label>
                                <input type="text" id="account_no" class="form-control form-control-sm ic-readbox" readonly>
                            </div>
                            <div class="form-group col-12 col-md-6">
                                <label>{{ __('custom.bank_name') }}</label>
                                <input type="text" id="bank_name" class="form-control form-control-sm ic-readbox" readonly>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-12 col-md-6">
                                <label>{{ __('custom.name') }}</label>
                                <input type="text" id="acc_name" class="form-control form-control-sm ic-readbox" readonly>
                            </div>
                            <div class="form-group col-6 col-md-3">
                                <label>{{ __('custom.current_balance') }}</label>
                                <input type="text" id="current_balance" class="form-control form-control-sm ic-readbox text-right font-weight-bold" value="0.00" readonly>
                            </div>
                            <div class="form-group col-6 col-md-3">
                                <label>{{ __('custom.new_balance') }}</label>
                                <input type="text" id="new_balance" class="form-control form-control-sm ic-readbox text-right font-weight-bold text-success" value="0.00" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="mt-2 text-right">
                        <button type="button" id="btn-clear" class="btn btn-secondary btn-sm">
                            <i class="mdi mdi-eraser"></i> {{ __('custom.clear') }}
                        </button>
                        <a href="{{ route('admin.transactions.index') }}" class="btn btn-primary btn-sm">
                            <i class="mdi mdi-exit-run"></i> {{ __('custom.exit') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .ic-txn-entry .card-body { padding: 14px 16px; }
        .ic-txn-entry .header-title { margin-bottom: 10px; font-size: 1rem; }
        .ic-txn-entry label { margin-bottom: 1px; font-weight: 600; font-size: .72rem; color: #495057; text-transform: uppercase; letter-spacing: .3px; }
        .ic-txn-entry .form-group { margin-bottom: 8px; }

        /* Clearly-bordered, compact inputs */
        .ic-txn-entry .form-control {
            height: 30px;
            padding: 2px 8px;
            font-size: .85rem;
            border: 1px solid #b0b7c3;
            border-radius: 4px;
            background: #fff;
        }
        .ic-txn-entry .form-control:focus {
            border-color: #4a7dff;
            box-shadow: 0 0 0 2px rgba(74, 125, 255, .18);
            background: #fbfcff;
        }
        /* Debit/Credit get a stronger cue when focused */
        #debit:focus { border-color: #dc3545 !important; box-shadow: 0 0 0 2px rgba(220,53,69,.18) !important; }
        #credit:focus { border-color: #28a745 !important; box-shadow: 0 0 0 2px rgba(40,167,69,.18) !important; }

        /* Account Code combobox: text input + custom styled dropdown */
        .ic-combo { position: relative; }
        #account_code {
            padding-right: 20px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%23667'%3E%3Cpath d='M5 7l5 6 5-6z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 5px center;
            background-size: 11px;
        }
        .ic-combo-list {
            position: absolute; top: 100%; left: 0; z-index: 30;
            min-width: 100%; width: max-content; max-width: 320px;
            margin: 2px 0 0; padding: 0; list-style: none;
            max-height: 260px; overflow-y: auto;
            background: #fff; border: 1px solid #b0b7c3; border-radius: 4px;
            box-shadow: 0 8px 20px rgba(24, 39, 75, .14);
            display: none;
        }
        .ic-combo-list.show { display: block; }
        .ic-combo-item {
            display: flex; align-items: baseline; gap: 8px;
            padding: 7px 10px; cursor: pointer;
            border-bottom: 1px solid #e3e7ec; font-size: .85rem;
        }
        .ic-combo-item:last-child { border-bottom: 0; }
        .ic-combo-item:hover, .ic-combo-item.active { background: #eef4ff; }
        .ic-combo-item.d-none { display: none; }
        .ic-combo-code { font-weight: 700; color: #2f57d6; min-width: 34px; }
        .ic-combo-name { color: #444; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        .ic-entry-row { padding: 8px 6px; background: #f4f6f9; border: 1px solid #dbe0e6; border-radius: 6px; }
        /* custom 12.5% columns for debit/credit so the entry row fits on one line */
        @media (min-width: 768px) {
            .col-md-1-5 { flex: 0 0 12.5%; max-width: 12.5%; }
        }
        .ic-grid-wrap { border: 1px solid #cfd5dd; border-radius: 6px; max-height: 240px; overflow-y: auto; margin-top: 10px; }
        .ic-txn-grid thead th { position: sticky; top: 0; z-index: 1; font-size: .74rem; padding: 5px 8px; background: #eef1f5; }
        .ic-txn-grid td { font-size: .82rem; padding: 4px 8px; }
        /* Non-editable (read-only) fields get a light gray background */
        .ic-txn-entry .form-control[readonly] { background: #e9ecef; cursor: default; }
        .ic-txn-entry .form-control[readonly]:focus { box-shadow: none; border-color: #b0b7c3; background: #e9ecef; }
        .ic-readbox { background: #e9ecef; font-weight: 600; border-color: #c7ccd4; }
        .ic-summary { margin-top: 10px !important; }
        .ic-summary label { color: #6c757d; }

        /* Compact SweetAlert confirm dialog */
        .ic-swal-compact { padding: 14px 16px 16px !important; border-radius: 10px !important; }
        .ic-swal-compact .swal2-title { font-size: 1rem !important; padding: 0 0 6px !important; }
        .ic-swal-compact .swal2-html-container { margin: 4px 0 0 !important; font-size: .9rem !important; }
        .ic-swal-compact .ic-confirm-amount { font-size: 1.15rem; font-weight: 700; }
        .ic-swal-compact .ic-confirm-name { color: #666; font-size: .82rem; margin-top: 2px; }
        .ic-swal-compact .swal2-actions { margin-top: 12px !important; gap: 8px; }
        .ic-swal-compact .swal2-styled { font-size: .85rem !important; padding: .35em 1.1em !important; margin: 0 !important; }
        /* Compact toast */
        .ic-toast { padding: 8px 12px !important; }
        .ic-toast .swal2-title { font-size: .85rem !important; margin: 0 !important; }
        .ic-toast .swal2-icon { width: 1.4em !important; height: 1.4em !important; margin: 0 8px 0 0 !important; }

        /* Green success toast */
        .ic-toast:has(.swal2-success) { background: #1cbb8c !important; }
        .ic-toast:has(.swal2-success) .swal2-title { color: #fff !important; }
        .ic-toast:has(.swal2-success) .swal2-timer-progress-bar { background: rgba(255, 255, 255, .6) !important; }
        .ic-toast .swal2-success-ring { border-color: rgba(255, 255, 255, .7) !important; }
        .ic-toast .swal2-success-line-tip,
        .ic-toast .swal2-success-line-long { background-color: #fff !important; }
        .ic-toast .swal2-success [class^='swal2-success-circular-line'],
        .ic-toast .swal2-success-fix { background: transparent !important; }
    </style>
@endpush

@push('script')
    <script>
        $(function () {
            var CSRF = $('meta[name="csrf-token"]').attr('content');
            var routes = {
                byCode: "{{ route('admin.transactions.account-by-code') }}",
                store:  "{{ route('admin.transactions.quick-store') }}"
            };
            var lang = {
                loadFirst:   @json(__('custom.load_account_first')),
                enterAmount: @json(__('custom.enter_debit_or_credit')),
                onlyOne:     @json(__('custom.enter_only_one_debit_credit')),
                confirm:     @json(__('custom.confirm_transaction')),
                yes:         @json(__('custom.yes')),
                no:          @json(__('custom.no')),
                debit:       @json(__('custom.debit')),
                credit:      @json(__('custom.credit'))
            };

            function money(v) {
                return (parseFloat(v) || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            }

            // Small, unobtrusive toast for feedback (top-right, auto-dismiss)
            var Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1600,
                timerProgressBar: true,
                customClass: { popup: 'ic-toast' }
            });

            function resetSummary() {
                $('#account_id').val('');
                $('#account_no, #bank_name, #acc_name').val('');
                $('#current_balance, #new_balance').val('0.00');
                $('#txn-grid-body').html(
                    '<tr class="ic-grid-empty"><td colspan="6" class="text-center text-muted py-3">' + lang.loadFirst + '</td></tr>'
                );
            }

            function rowHtml(t) {
                return '<tr>' +
                    '<td>' + (t.id || '') + '</td>' +
                    '<td>' + (t.accno || '') + '</td>' +
                    '<td>' + $('<div>').text(t.note || '').html() + '</td>' +
                    '<td>' + (t.date || '') + '</td>' +
                    '<td class="text-right">' + (t.debit || '') + '</td>' +
                    '<td class="text-right">' + (t.credit || '') + '</td>' +
                    '</tr>';
            }

            // ---- Account Code lookup (typed + Enter, or chosen from dropdown) ----
            var lastCode = null;

            function lookupAccount(code) {
                code = $.trim(code || '');
                if (!code) { lastCode = null; resetSummary(); return; }
                lastCode = code;

                $.ajax({
                    url: routes.byCode, method: 'GET', data: { code: code }
                }).done(function (res) {
                    if (!res.success) { resetSummary(); return; }
                    var a = res.account;
                    $('#account_id').val(a.id);
                    $('#account_no').val(a.account_number || '');
                    $('#bank_name').val(a.bank_name || '');
                    $('#acc_name').val(a.name || '');
                    $('#current_balance').val(a.current_balance_formatted);
                    $('#new_balance').val('0.00');
                    // Do NOT load previously saved transactions. The grid only
                    // shows entries made in this session (since page load); a
                    // page reload starts blank.
                    $('#debit').focus();
                }).fail(function (xhr) {
                    lastCode = null;
                    resetSummary();
                    Toast.fire({ icon: 'error', title: (xhr.responseJSON && xhr.responseJSON.message) || 'Error' });
                });
            }

            // ---- Custom combobox dropdown for Account Code ----
            var $code = $('#account_code');
            var $list = $('#account_code_list');

            function filterList(q) {
                q = $.trim((q || '').toLowerCase());
                $list.children('.ic-combo-item').each(function () {
                    var match = !q || ($(this).data('search') + '').indexOf(q) !== -1;
                    $(this).toggleClass('d-none', !match).removeClass('active');
                });
            }
            function openList() { filterList($code.val()); $list.addClass('show'); }
            function closeList() { $list.removeClass('show').children().removeClass('active'); }

            $code.on('focus click', openList);
            $code.on('input', openList);

            $code.on('keydown', function (e) {
                if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                    e.preventDefault();
                    if (!$list.hasClass('show')) openList();
                    var items = $list.children('.ic-combo-item:not(.d-none)');
                    if (!items.length) return;
                    var idx = items.index(items.filter('.active'));
                    idx = e.key === 'ArrowDown' ? Math.min(idx + 1, items.length - 1) : Math.max(idx - 1, 0);
                    items.removeClass('active');
                    var $sel = items.eq(idx < 0 ? 0 : idx).addClass('active');
                    $sel[0].scrollIntoView({ block: 'nearest' });
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    var $active = $list.children('.ic-combo-item.active:not(.d-none)');
                    if ($active.length) $code.val($active.data('code'));
                    closeList();
                    lookupAccount($code.val());
                } else if (e.key === 'Escape') {
                    closeList();
                }
            });

            // Pick an item with the mouse (mousedown keeps input focus)
            $list.on('mousedown', '.ic-combo-item', function (e) {
                e.preventDefault();
                $code.val($(this).data('code'));
                closeList();
                lookupAccount($code.val());
            });

            // Click outside closes the dropdown
            $(document).on('mousedown', function (e) {
                if (!$(e.target).closest('.ic-combo').length) closeList();
            });

            // ---- Debit / Credit -> Enter: confirm + save ----
            function submitTransaction() {
                if (!$('#account_id').val()) {
                    Toast.fire({ icon: 'warning', title: lang.loadFirst });
                    $('#account_code').focus();
                    return;
                }
                var debit  = parseFloat($('#debit').val())  || 0;
                var credit = parseFloat($('#credit').val()) || 0;

                if (debit <= 0 && credit <= 0) {
                    Toast.fire({ icon: 'warning', title: lang.enterAmount });
                    return;
                }
                if (debit > 0 && credit > 0) {
                    Toast.fire({ icon: 'warning', title: lang.onlyOne });
                    return;
                }

                var isDebit = debit > 0;
                var amount = isDebit ? debit : credit;
                var color = isDebit ? '#dc3545' : '#28a745';
                var label = (isDebit ? lang.debit : lang.credit) + ' ' + money(amount);

                Swal.fire({
                    title: lang.confirm,
                    html: '<div class="ic-confirm-amount" style="color:' + color + '">' + label + '</div>' +
                          '<div class="ic-confirm-name">' + $('<div>').text($('#acc_name').val()).html() + '</div>',
                    showCancelButton: true,
                    confirmButtonText: lang.yes,
                    cancelButtonText: lang.no,
                    confirmButtonColor: color,
                    reverseButtons: true,
                    focusConfirm: true,
                    width: 320,
                    customClass: { popup: 'ic-swal-compact' }
                }).then(function (result) {
                    if (!result.value) return;

                    $.ajax({
                        url: routes.store, method: 'POST',
                        headers: { 'X-CSRF-TOKEN': CSRF },
                        data: {
                            account_id:  $('#account_id').val(),
                            debit:       isDebit ? amount : 0,
                            credit:      isDebit ? 0 : amount,
                            description: $('#description').val(),
                            date:        $('#trans_date').val()
                        }
                    }).done(function (res) {
                        if (!res.success) return;
                        // Prepend the new row to the grid
                        $('#txn-grid-body .ic-grid-empty').remove();
                        $('#txn-grid-body').prepend(rowHtml(res.transaction));
                        // Update balances instantly
                        $('#current_balance').val(res.new_balance_formatted);
                        $('#new_balance').val(res.new_balance_formatted);
                        // Advance transaction no, reset entry inputs
                        $('#transaction_no').val((parseInt(res.transaction.id, 10) || 0) + 1);
                        $('#debit, #credit, #description').val('');
                        Toast.fire({ icon: 'success', title: res.message });
                        $('#debit').focus();
                    }).fail(function (xhr) {
                        Toast.fire({ icon: 'error', title: (xhr.responseJSON && xhr.responseJSON.message) || 'Error' });
                    });
                });
            }

            $('#debit, #credit').on('keydown', function (e) {
                if (e.key !== 'Enter') return;
                e.preventDefault();
                submitTransaction();
            });

            // ---- Clear ----
            $('#btn-clear').on('click', function () {
                lastCode = null;
                $('#description, #debit, #credit').val('');
                $code.val('');
                resetSummary();
                $code.focus();
            });

            // ---- Put cursor on Account Code as soon as the page loads ----
            $code.focus();
        });
    </script>
@endpush
