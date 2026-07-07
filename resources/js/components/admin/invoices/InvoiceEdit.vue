<template>
    <div class="row">

        <div class="col-12" v-if="isExistsValidationErrors">
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-danger alert-important alert-dismissible fade show mb-0" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <li v-for="(validationError, index) in validationErrors.errors" :key="index">
                            {{ validationError[0] }}
                        </li>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="row">
                <div class="col-lg-5 col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6 col-lg-6 col-md-6">
                                    <form @submit.prevent="submitBarcode()">
                                        <div class="form-group">
                                            <input v-focus v-model="search" type="text" class="form-control"
                                                placeholder="Search Name, SKU, Model, Parts No or Scan Barcode"
                                                @keyup="searchSelectSku($event)" />
                                        </div>
                                    </form>
                                </div>
                                <div class="col-sm-6 col-lg-6 col-md-6">
                                    <Select2 v-if="localCategories.length > 0"
                                        :key="'category-' + select2Key"
                                        v-model="selected_category"
                                        :options="localCategories"
                                        :settings="{ placeholder: 'Select Category' }"
                                        @select="selectCategory($event)">
                                    </Select2>
                                </div>
                            </div>
                            <div class="ic-product-head" v-if="showProduct">
                                <div v-for="(product_stock, index) in product_list" :key="index"
                                    class="product-items pt-0">
                                    <div class="product-item" @click="addNewItem(product_stock)">
                                        <div class="ic-images-out-of-stock">
                                            <img
                                                class="img-fluid list-image card-img-top"
                                                :src="product_stock.variation?.thumb_url || product_stock.product.thumb_url"
                                                alt="Product"
                                                />


                                        </div>
                                        <div class="product-item-body p-2">
                                            <label class="m-0">{{ product_stock.product.name }}</label>
                                            <p class="card-text p-0 m-0" v-if="product_stock.product.is_variant == 1">
                                                {{ __("custom.variant") }}: {{ product_stock.variation?.name ?? null }}
                                            </p>
                                            <p class="card-text p-0 m-0" v-if="product_stock.product.model">
                                                {{ __("custom.model") }}: {{ product_stock.product.model }}
                                            </p>
                                            <p class="card-text p-0 m-0">
                                                {{ __("custom.price") }}: {{ currency_symbol }} {{
                                                    product_stock.price_for_sale }}
                                            </p>
                                            <p class="card-text p-0 m-0"
                                                v-if="product_stock.product.is_weight_based == 1 && product_stock.product.kg_per_barrel > 0">
                                                {{ __("custom.selling_price") }} {{ __("custom.per_kg") }}: {{ currency_symbol }} {{
                                                    formatNumber(product_stock.price_for_sale / product_stock.product.kg_per_barrel, 2) }}
                                            </p>
                                            <p class="card-text p-0 m-0">
                                                {{ __("custom.stock") }}: {{ product_stock.quantity }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else>
                                <p class="text-white m-0">{{ __("custom.no_product_found") }}</p>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="col-lg-7 col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group mb-0">
                                <div>
                                    <label class="float-left"><span class="mr-1">{{ __("custom.customer") }} </span>
                                    </label>
                                    <div class="custom-control custom-checkbox float-right">
                                        <input v-model="formData.is_walkin_customer"
                                            class="form-check-input custom-control-input" type="checkbox"
                                            id="walkinCustomer" @click="btnWalkinCustomer" />
                                        <label class="form-check-label custom-control-label checkbox-label"
                                            for="walkinCustomer">
                                            {{ __("custom.walk_in_customer") }}
                                        </label>
                                    </div>
                                </div>
                                <Select2 v-if="!formData.is_walkin_customer && localCustomers.length > 0"
                                    :key="'customer-' + select2Key"
                                    v-model="formData.customer_id"
                                    :id="'customer-id'"
                                    :options="localCustomers"
                                    :settings="{ placeholder: 'Select Customer' }"
                                    @select="selectedCustomer($event)">
                                </Select2>
                            </div>
                            <div class="mb-3 w-100" v-if="formData.is_walkin_customer">
                                <div class="from-group">
                                    <label for="">{{ __("custom.name") }}</label>
                                    <input type="text" class="form-control"
                                        v-model="formData.walkin_customer.full_name" />
                                </div>
                                <div class="from-group">
                                    <label for="">{{ __("custom.phone") }}</label>
                                    <input type="text" class="form-control" v-model="formData.walkin_customer.phone" />
                                </div>
                            </div>
                            <div class="col-sm-12 p-0 mt-3">
                                <div class="col-sm-12 p-0">
                                    <label for="" class="text-muted w-100 mb-0">{{ __("custom.billing_info") }}
                                        <a class="float-right" href="#" data-toggle="modal"
                                            data-target=".billing-info-edit"><i class="fa fa-edit"></i></a></label>
                                </div>
                                <div class="col-sm-12 mt-1 p-0 float-left"
                                    v-if="isCustomerSelected || formData.is_walkin_customer">
                                    <p class="m-0">{{ formData.billing.name }}</p>
                                    <p class="m-0">{{ formData.billing.email }}</p>
                                    <p class="m-0">{{ formData.billing.phone }}</p>
                                    <p class="m-0">{{ billinAddressFull() }}</p>
                                </div>
                            </div>

                            <div class="col-sm-12 p-0 mt-3">
                                <div class="col-sm-12 p-0">
                                    <label for="" class="text-muted mb-0 w-100">
                                        {{ __("custom.shipping_info") }}
                                        <a class="float-right" href="#" data-toggle="modal"
                                            data-target=".shipping-info-edit"><i class="fa fa-edit"></i></a></label>

                                    <div class="custom-control custom-checkbox">
                                        <input v-model="is_shipping_same_billing"
                                            class="form-check-input custom-control-input" type="checkbox"
                                            id="shippingSameBilling" @change="shippingSameBilling($event)" />
                                        <label class="form-check-label custom-control-label checkbox-label"
                                            for="shippingSameBilling">
                                            {{ __("custom.same_as_billing") }}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-12 mt-1 p-0"
                                    v-if="isCustomerSelected || formData.is_walkin_customer">
                                    <p class="m-0">{{ formData.shipping.name }}</p>
                                    <p class="m-0">{{ formData.shipping.email }}</p>
                                    <p class="m-0">{{ formData.shipping.phone }}</p>
                                    <p class="m-0">{{ shippingAddressFull() }}</p>
                                </div>
                            </div>

                            <div class="from-group mt-3 mb-3">
                                <label for="">{{ __("custom.date") }}</label>
                                <datepicker input-class="form-control" v-model="formData.date" format="yyyy-MM-dd"
                                    :use-utc=true placeholder="Select date" v-model.trim="$v.formData.date.$model">
                                </datepicker>
                                <small class="error" v-if="!$v.formData.date.required">
                                    {{ __("custom.required") }}
                                </small>
                            </div>

                            <div class="from-group mt-3 mb-3">
                                <label for="">{{ __("custom.due_date") }}</label>
                                <datepicker input-class="form-control" v-model="formData.due_date" format="yyyy-MM-dd"
                                    :use-utc=true placeholder="Select due date"></datepicker>
                            </div>
                            <div class="table-responsive ic-table-responsive-heading">
                                <table class="
                table table-hover table-sm table-borderedless table-striped
              ">
                                    <thead>
                                        <tr>
                                            <th>{{ __("custom.item") }}</th>
                                            <th class="text-center">{{ __("custom.model") }}</th>
                                            <th class="text-center">{{ __("custom.price") }} ({{ currency_symbol }})
                                            </th>
                                            <th class="text-center">{{ __("custom.qty") }}</th>

                                            <th class="text-center">{{ __("custom.dis") }}</th>
                                            <th class="text-center">{{ __("custom.dis_type") }}</th>
                                            <th class="text-center">{{ __("custom.sub_total") }}</th>
                                            <th class="text-center">{{ __("custom.action") }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, index) in formData.items" :key="index">
                                            <td>
                                                <p class="p-0 m-0" v-if="item.variation">{{ item.name }}
                                                    ({{ __("custom.variant") }}: {{ item.variation.name }})</p>

                                                <p class="p-0 m-0" v-else>{{ item.name }}</p>
                                                <div v-if="item.is_weight_based && item.kg_per_barrel > 0" class="mt-1">
                                                    <select class="form-control form-control-sm border"
                                                        :value="item.unit" @change="changeUnit(index, $event.target.value)">
                                                        <option value="barrel">{{ item.barrel_label || 'Barrel' }}</option>
                                                        <option value="kg">{{ __("custom.kg") }}</option>
                                                    </select>
                                                    <small class="text-muted d-block mt-1">
                                                        {{ __("custom.available") }}:
                                                        {{ $formatNumber(item.stock) }} {{ item.barrel_label || 'Barrel' }}
                                                        / {{ $formatNumber(item.stock * item.kg_per_barrel) }} {{ __("custom.kg") }}
                                                    </small>
                                                </div>
                                            </td>
                                            <td class="text-center">{{ item.model || '-' }}</td>
                                            <td>
                                                <input :min="item.is_weight_based ? 0 : 1" :step="item.is_weight_based ? 'any' : 1"
                                                    type="number" v-model="item.price"
                                                    @input="updatePrice($event, index)"
                                                    class="form-control text-center" />
                                            </td>
                                            <td>
                                                <input :min="item.is_weight_based ? 0 : 1" :step="item.is_weight_based ? 'any' : 1"
                                                    type="number" v-model="item.quantity"
                                                    @input="updateQuantity($event, index)"
                                                    class="form-control text-center" />
                                            </td>
                                            <td>
                                                <input min="0" type="number" v-model="item.discount"
                                                    class="form-control text-center" />
                                            </td>
                                            <td>
                                                <select v-model="item.discount_type" class="form-control">
                                                    <option value="percent">%</option>
                                                    <option value="fixed">{{ __("custom.fixed") }}</option>
                                                </select>
                                            </td>
                                            <td>{{ currency_symbol }} {{ $formatNumber(calculateSubtotal(index)) }}</td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                    @click.prevent="deleteItem(index)">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="5">
                                                <b>{{ __("custom.sub_total") }}</b>
                                            </td>
                                            <td>
                                                <b>{{ currency_symbol }} {{ $formatNumber(calculateTotalWithOutTax())
                                                    }}</b>
                                            </td>
                                            <td colspan="2"></td>
                                        </tr>
                                        <tr v-if="cartNotEmpty">
                                            <td colspan="2">
                                                <b>{{ __("custom.discount") }}</b>
                                            </td>
                                            <td>
                                                <input type="number" v-model="formData.discount" class="form-control" />
                                            </td>
                                            <td colspan="1">
                                                <select v-model="formData.discount_type" class="form-control">
                                                    <option value="percent">%</option>
                                                    <option value="fixed">{{ __("custom.fixed") }}</option>
                                                </select>
                                            </td>
                                            <td>
                                                <b>{{ currency_symbol }} {{ $formatNumber(calculateGlobalDiscount())
                                                    }}</b>
                                            </td>
                                            <td colspan="2"></td>
                                        </tr>

                                        <tr v-if="cartNotEmpty">
                                            <td colspan="6">
                                                <b>{{ __("custom.total_discount") }}</b>
                                            </td>
                                            <td>
                                                <b>{{ currency_symbol }} {{ $formatNumber(calculateTotalDiscount())
                                                    }}</b>
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr v-if="cartNotEmpty">
                                            <td colspan="2">
                                                <b>{{ __("custom.additional_charge") }}</b>
                                            </td>
                                            <td colspan="4">
                                                <input type="text" v-model="formData.additional_charge_name"
                                                    class="form-control border" placeholder="Charge Name" />
                                            </td>
                                            <td>
                                                <input type="number" step="any" v-model="formData.additional_charge_amount"
                                                    class="form-control text-center border" placeholder="Amount" />
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="5">
                                                <b>{{ __("custom.total") }}</b>
                                            </td>
                                            <td>
                                                <b>{{ currency_symbol }} {{ $formatNumber(calculateTotalWithTax())
                                                    }}</b>
                                            </td>
                                            <td colspan="2"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <!-- ── Free Items ──────────────────────────────── -->
                            <div style="margin-top:6px;margin-bottom:8px;">
                                <div class="d-flex align-items-center" style="margin-bottom:4px;">
                                    <label class="switch mb-0 mr-2" style="transform:scale(.85);transform-origin:left;">
                                        <input type="checkbox" v-model="formData.enable_free_items" />
                                        <span class="slider"></span>
                                    </label>
                                    <span style="font-size:.8rem;font-weight:600;color:#495057;">Free Items</span>
                                </div>
                                <div v-if="formData.enable_free_items">
                                    <table class="table table-hover table-sm table-bordered mb-1" style="font-size:.8rem;">
                                        <thead>
                                            <tr>
                                                <th style="padding:4px 6px;font-weight:600;">Item</th>
                                                <th style="width:80px;padding:4px 6px;font-weight:600;text-align:center;">Qty</th>
                                                <th style="width:32px;padding:4px 6px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(fi, idx) in formData.free_items" :key="idx">
                                                <td style="padding:3px 4px;position:relative;">
                                                    <input type="text" v-model="fi.search"
                                                           @input="searchFreeProduct(idx)"
                                                           @blur="closeFreeDropdown(idx)"
                                                           class="form-control form-control-sm border"
                                                           style="font-size:.8rem;height:28px;padding:2px 6px;"
                                                           :placeholder="fi.product_name || 'Search by name or SKU...'" />
                                                    <div v-if="fi.show_dropdown && fi.results.length"
                                                         style="position:absolute;z-index:999;left:4px;right:4px;top:100%;background:#fff;border:1px solid #ced4da;border-radius:4px;max-height:160px;overflow-y:auto;box-shadow:0 2px 6px rgba(0,0,0,.12);">
                                                        <div v-for="r in fi.results" :key="r.id"
                                                             @mousedown.prevent="selectFreeProduct(idx, r)"
                                                             style="padding:4px 8px;cursor:pointer;font-size:.78rem;border-bottom:1px solid #f5f5f5;line-height:1.4;"
                                                             onmouseover="this.style.background='#f0f4ff'" onmouseout="this.style.background='#fff'">
                                                            {{ r.product.name }} <span style="color:#888;">({{ r.product.sku }})</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td style="padding:3px 4px;">
                                                    <input type="number" v-model.number="fi.quantity" min="1"
                                                           class="form-control form-control-sm text-center border"
                                                           style="font-size:.8rem;height:28px;padding:2px 4px;" />
                                                </td>
                                                <td style="padding:3px 4px;text-align:center;">
                                                    <button type="button"
                                                            style="border:1px solid #e74c3c;background:#fff;color:#e74c3c;border-radius:4px;width:24px;height:24px;padding:0;line-height:1;font-size:.7rem;cursor:pointer;"
                                                            @click="removeFreeItem(idx)">✕</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <button type="button"
                                            style="font-size:0.75rem;padding:3px 10px;border:1px solid var(--primary-color);background:var(--primary-color);color:#fff;border-radius:4px;cursor:pointer;"
                                            @click="addFreeItem">
                                        + Add Free Item
                                    </button>
                                </div>
                            </div>

                            <div class="col-sm-12 p-0">
                                <label class="w-100 font-weight-bold mb-2">{{ __("custom.payment") }}</label>

                                <!-- Cash -->
                                <div style="border:1px solid #dee2e6;border-radius:10px;padding:10px 12px;background:#fff;margin-bottom:8px;">
                                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                                        <img :src="asset('admin/images/cash.png')" style="height:22px;" alt="cash" />
                                        <span style="font-size:.8125rem;font-weight:500;color:#6c757d;">Cash</span>
                                    </div>
                                    <input v-model.number="formData.cash_amount" type="number" min="0" step="any"
                                           placeholder="0"
                                           style="width:100%;height:36px;border:1px solid #ced4da;border-radius:6px;padding:0 10px;font-size:.875rem;color:#495057;background:#fff;outline:none;box-sizing:border-box;" />
                                </div>

                                <!-- Bank -->
                                <div style="border:1px solid #dee2e6;border-radius:10px;padding:10px 12px;background:#fff;margin-bottom:8px;">
                                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                                        <img :src="asset('admin/images/bank.png')" style="height:22px;" alt="bank" />
                                        <span style="font-size:.8125rem;font-weight:500;color:#6c757d;">Bank</span>
                                    </div>
                                    <input v-model.number="formData.bank_amount" type="number" min="0" step="any"
                                           placeholder="0"
                                           style="width:100%;height:36px;border:1px solid #ced4da;border-radius:6px;padding:0 10px;font-size:.875rem;color:#495057;background:#fff;outline:none;box-sizing:border-box;" />

                                    <!-- Bank payment details (shown when a bank amount is entered) -->
                                    <div v-if="bankAmountEntered" class="ic-bank-fields">
                                        <div style="margin-bottom:8px;">
                                            <label style="font-size:.8125rem;font-weight:500;color:#495057;margin-bottom:4px;display:block;">Bank Name</label>
                                            <select v-model="formData.bank_info.bank_name"
                                                    style="width:100%;height:36px;border:1px solid #ced4da;border-radius:6px;padding:0 10px;font-size:.875rem;color:#495057;background:#fff;outline:none;box-sizing:border-box;">
                                                <option value="">Select Account</option>
                                                <option v-for="acc in bankAccounts" :key="acc.id" :value="acc.id">{{ acc.code ? '[' + acc.code + '] ' : '' }}{{ acc.name }}</option>
                                            </select>
                                        </div>
                                        <div style="margin-bottom:8px;">
                                            <label style="font-size:.8125rem;font-weight:500;color:#495057;margin-bottom:4px;display:block;">Account Number</label>
                                            <input v-model="formData.bank_info.ac_no" type="text"
                                                   style="width:100%;height:36px;border:1px solid #ced4da;border-radius:6px;padding:0 10px;font-size:.875rem;color:#495057;background:#fff;outline:none;box-sizing:border-box;" />
                                        </div>
                                        <div style="margin-bottom:8px;">
                                            <label style="font-size:.8125rem;font-weight:500;color:#495057;margin-bottom:4px;display:block;">Transaction No</label>
                                            <input v-model="formData.bank_info.t_no" type="text"
                                                   style="width:100%;height:36px;border:1px solid #ced4da;border-radius:6px;padding:0 10px;font-size:.875rem;color:#495057;background:#fff;outline:none;box-sizing:border-box;" />
                                        </div>
                                        <div>
                                            <label style="font-size:.8125rem;font-weight:500;color:#495057;margin-bottom:4px;display:block;">Transaction Date</label>
                                            <input v-model="formData.bank_info.date" type="date"
                                                   style="width:100%;height:36px;border:1px solid #ced4da;border-radius:6px;padding:0 10px;font-size:.875rem;color:#495057;background:#fff;outline:none;box-sizing:border-box;" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Current Balance as payment -->
                                <div v-if="selectedCustomerData && !formData.is_walkin_customer && customerCurrentBalance > 0"
                                     style="border:1.5px solid #28aaa9;border-radius:10px;padding:10px 12px;background:#f0fdf8;margin-bottom:8px;">
                                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                                        <span style="font-size:.8125rem;font-weight:600;color:#28aaa9;display:flex;align-items:center;gap:5px;">
                                            <i class="fas fa-wallet" style="font-size:.8rem;"></i> Current Balance
                                        </span>
                                        <span style="font-size:.75rem;color:#6c757d;">
                                            Available: {{ currency_symbol }}{{ $formatNumber(customerCurrentBalance) }}
                                        </span>
                                    </div>
                                    <input v-model.number="formData.balance_amount" type="number" min="0"
                                           :max="Math.max(0, customerCurrentBalance)" step="any" placeholder="0"
                                           style="width:100%;height:36px;border:1px solid #ced4da;border-radius:6px;padding:0 10px;font-size:.875rem;color:#495057;background:#fff;outline:none;box-sizing:border-box;" />
                                    <small v-if="formData.balance_amount > Math.max(0, customerCurrentBalance)" style="color:#dc3545;margin-top:4px;display:block;font-size:.75rem;">
                                        Cannot exceed available balance.
                                    </small>
                                </div>

                                <!-- Live breakdown -->
                                <div v-if="cartNotEmpty" style="border:1px solid #dee2e6;border-radius:10px;overflow:hidden;margin-top:10px;">
                                    <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 12px;background:#f8f9fa;border-bottom:1px solid #dee2e6;">
                                        <span style="font-size:.8125rem;color:#495057;">Invoice Total</span>
                                        <span style="font-size:.8125rem;font-weight:600;color:#212529;">{{ currency_symbol }}{{ $formatNumber(calculateTotalWithTax()) }}</span>
                                    </div>
                                    <div v-if="formData.cash_amount > 0" style="display:flex;justify-content:space-between;align-items:center;padding:7px 12px;background:#fff;border-bottom:1px solid #f3f3f3;">
                                        <span style="font-size:.8125rem;color:#6c757d;">Cash</span>
                                        <span style="font-size:.8125rem;font-weight:500;color:#28aaa9;">{{ currency_symbol }}{{ $formatNumber(formData.cash_amount) }}</span>
                                    </div>
                                    <div v-if="formData.bank_amount > 0" style="display:flex;justify-content:space-between;align-items:center;padding:7px 12px;background:#fff;border-bottom:1px solid #f3f3f3;">
                                        <span style="font-size:.8125rem;color:#6c757d;">Bank</span>
                                        <span style="font-size:.8125rem;font-weight:500;color:#28aaa9;">{{ currency_symbol }}{{ $formatNumber(formData.bank_amount) }}</span>
                                    </div>
                                    <div v-if="formData.balance_amount > 0" style="display:flex;justify-content:space-between;align-items:center;padding:7px 12px;background:#fff;border-bottom:1px solid #f3f3f3;">
                                        <span style="font-size:.8125rem;color:#6c757d;">Balance Used</span>
                                        <span style="font-size:.8125rem;font-weight:500;color:#28aaa9;">{{ currency_symbol }}{{ $formatNumber(formData.balance_amount) }}</span>
                                    </div>
                                    <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 12px;background:#eef2ff;border-top:1px solid #d8deff;border-bottom:1px solid #d8deff;">
                                        <span style="font-size:.8125rem;font-weight:600;color:#212529;">Total Paid</span>
                                        <span style="font-size:.8125rem;font-weight:600;color:#28aaa9;">{{ currency_symbol }}{{ $formatNumber(totalPaid) }}</span>
                                    </div>
                                    <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 12px;background:#fff5f5;border-bottom:1px solid #fce8e8;">
                                        <span style="font-size:.8125rem;color:#6c757d;">Due</span>
                                        <span style="font-size:.8125rem;font-weight:600;" :style="invoiceDue > 0 ? 'color:#dc3545' : 'color:#28aaa9'">{{ currency_symbol }}{{ $formatNumber(invoiceDue) }}</span>
                                    </div>
                                    <div v-if="selectedCustomerData && !formData.is_walkin_customer"
                                         style="display:flex;justify-content:space-between;align-items:center;padding:8px 12px;background:#fff;border-top:1px solid #dee2e6;">
                                        <span style="font-size:.8125rem;font-weight:600;color:#212529;">Remaining Balance</span>
                                        <span style="font-size:.8125rem;font-weight:600;" :style="balanceAfterInvoice >= 0 ? 'color:#28aaa9' : 'color:#dc3545'">{{ currency_symbol }}{{ $formatNumber(balanceAfterInvoice) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-grou">
                                <label for="">{{ __("custom.note") }}</label>
                                <textarea v-model="formData.notes" cols="30" rows="10" class="form-control"></textarea>
                            </div>
                            <div class="col-sm-12 p-0 mt-3">
                                <div class="text-center">
                                    <button v-if="cartNotEmpty" type="button" class="btn btn-dark btn-block"
                                        @click="submitInvoice" :disabled="isSubmitting">
                                        <span v-if="isSubmitting">
                                            <i class="fa fa-spinner fa-spin"></i>
                                        </span>
                                        <span v-else>
                                            {{ __("custom.confirm") }}
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Billing info edit modal -->
                <div class="modal fade billing-info-edit" tabindex="-1" role="dialog"
                    aria-labelledby="myLargeModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ __("custom.billing_info") }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label for="">{{ __("custom.name") }}</label>
                                        <input type="text" class="form-control" v-model="formData.billing.name" />
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="">{{ __("custom.email") }}</label>
                                        <input type="email" class="form-control" v-model="formData.billing.email" />
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="">{{ __("custom.phone") }}</label>
                                        <input type="text" class="form-control" v-model="formData.billing.phone" />
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="">{{ __("custom.address_line_1") }}</label>
                                        <input type="text" class="form-control"
                                            v-model="formData.billing.address_line_1" />
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="">{{ __("custom.address_line_2") }}</label>
                                        <input type="text" class="form-control"
                                            v-model="formData.billing.address_line_2" />
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="">{{ __("custom.city") }}</label>
                                        <input type="text" class="form-control" v-model="formData.billing.city" />
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="">{{ __("custom.state") }}</label>
                                        <input type="text" class="form-control" v-model="formData.billing.state" />
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="">{{ __("custom.zipcode") }}</label>
                                        <input type="text" class="form-control" v-model="formData.billing.zip" />
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="">{{ __("custom.country") }}</label>
                                        <input type="text" class="form-control" v-model="formData.billing.country" />
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer text-right">
                                <button type="button" class="btn btn-primary" data-dismiss="modal">
                                    {{ __("custom.save_and_close") }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipping info edit modal -->
                <div class="modal fade shipping-info-edit" tabindex="-1" role="dialog"
                    aria-labelledby="myLargeModalLabel1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ __("custom.shipping_info") }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label for="">{{ __("custom.name") }}</label>
                                        <input type="text" class="form-control" v-model="formData.shipping.name" />
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="">{{ __("custom.email") }}</label>
                                        <input type="email" class="form-control" v-model="formData.shipping.email" />
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="">{{ __("custom.phone") }}</label>
                                        <input type="text" class="form-control" v-model="formData.shipping.phone" />
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="">{{ __("custom.address_line_1") }}</label>
                                        <input type="text" class="form-control"
                                            v-model="formData.shipping.address_line_1" />
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="">{{ __("custom.address_line_2") }}</label>
                                        <input type="text" class="form-control"
                                            v-model="formData.shipping.address_line_2" />
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="">{{ __("custom.city") }}</label>
                                        <input type="text" class="form-control" v-model="formData.shipping.city" />
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="">{{ __("custom.state") }}</label>
                                        <input type="text" class="form-control" v-model="formData.shipping.state" />
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="">{{ __("post_code_or_zip_code") }}</label>
                                        <input type="text" class="form-control" v-model="formData.shipping.zip" />
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="">{{ __("custom.country") }}</label>
                                        <input type="text" class="form-control" v-model="formData.shipping.country" />
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer text-right">
                                <button type="button" class="btn btn-primary" data-dismiss="modal">
                                    {{ __("custom.save_and_close") }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import moment from "moment";
import axios from "axios";
import Datepicker from "vuejs-datepicker";
import { required } from "vuelidate/lib/validators";

export default {
    props: [
        "app_name",
        "product_stocks",
        "categories",
        "customers",
        "user",
        "default_tax",
        "invoice",
        "warehouse_id",
        "currency_symbol",
        "accounts",
        "invoice_free_items",
    ],
    components: {
        Datepicker,
    },
    data() {
        return {
            isSubmitting: false,
            validationErrors: [],
            isExistsValidationErrors: 0,
            product_list: {},
            selected_category: "all",
            search: "",
            char_limit: 200,
            is_shipping_same_billing: false,
            localCustomers: [],
            localCategories: [],
            select2Key: 0,
            selectedCustomerData: null,
            formData: {
                warehouse_id: this.warehouse_id,
                payment_type: "cash",
                is_walkin_customer: false,
                walkin_customer: {
                    full_name: "",
                    phone: "",
                },
                bank_info: {
                    bank_name: "",
                    ac_no: "",
                    t_no: "",
                    date: "",
                },
                cash_amount: 0,
                bank_amount: 0,
                balance_amount: 0,
                date: "",
                due_date: "",
                customer_id: "",
                billing: {
                    name: "",
                    email: "",
                    phone: "",
                    address_line_1: "",
                    address_line_2: "",
                    city: "",
                    state: "",
                    zip: "",
                    country: "",
                },
                shipping: {
                    name: "",
                    email: "",
                    phone: "",
                    address_line_1: "",
                    address_line_2: "",
                    city: "",
                    state: "",
                    zip: "",
                    country: "",
                },
                items: [],
                status: "",
                notes: "",
                tax: 0,
                discount: 0,
                discount_type: "percent",
                total_paid: 0,
                additional_charge_name: "",
                additional_charge_amount: 0,
                enable_free_items: false,
                free_items: [],
            },
        };
    },
    created() {
        // Initialize data as early as possible
        this.localCustomers = Array.isArray(this.customers) ? [...this.customers] : [];
        this.localCategories = Array.isArray(this.categories) ? [...this.categories] : [];

        // Set customer_id early so Select2 can find it
        if (this.invoice && this.invoice.customer_id) {
            this.formData.customer_id = this.invoice.customer_id;
        }
    },
    mounted() {
        console.log('hi',this.invoice.items_data);
        // Set old data
        this.formData.customer_id = this.invoice.customer_id;
        this.formData.billing = this.invoice.billing_info;
        this.formData.shipping = this.invoice.shipping_info;
        this.formData.date = this.invoice.date;
        this.formData.due_date = this.invoice.due_date;
        this.formData.items = this.invoice.items_data;

        this.formData.payment_type = this.invoice.payment_type;
        this.formData.total_paid = this.invoice.last_paid;
        this.formData.notes = this.invoice.notes;
        // Normalize bank_info into a complete object so the bank fields bind safely.
        const _bi = typeof this.invoice.bank_info === 'string'
            ? (JSON.parse(this.invoice.bank_info || '{}') || {})
            : (this.invoice.bank_info || {});
        this.formData.bank_info = {
            bank_name: _bi.bank_name || '',
            ac_no: _bi.ac_no || '',
            t_no: _bi.t_no || '',
            date: _bi.date || '',
        };
        this.formData.discount = this.invoice.global_discount;
        this.formData.discount_type = this.invoice.global_discount_type;
        this.formData.additional_charge_name = this.invoice.additional_charge_name || "";
        this.formData.additional_charge_amount = this.invoice.additional_charge_amount || 0;
        this.product_list = this.product_stocks;
        if (!this.invoice.customer_id) {
            this.formData.is_walkin_customer = true;
            this.formData.walkin_customer = this.invoice.customer;
        }

        // Initialize payment amounts from last_paid split by type
        const lastPaid = parseFloat(this.invoice.last_paid) || 0;
        const pt = this.invoice.payment_type;
        if (pt === 'bank') {
            this.formData.bank_amount = lastPaid;
        } else if (pt === 'balance') {
            this.formData.balance_amount = lastPaid;
        } else {
            this.formData.cash_amount = lastPaid;
        }

        // Set selectedCustomerData for balance card
        if (this.invoice.customer_id) {
            this.selectedCustomerData = this.localCustomers.find(c => c.id == this.invoice.customer_id) || null;
        }

        // Pre-populate free items from invoice
        const freeItems = Array.isArray(this.invoice_free_items) ? this.invoice_free_items : [];
        if (freeItems.length > 0) {
            this.formData.enable_free_items = true;
            this.formData.free_items = freeItems.map(fi => ({
                product_id: fi.product_id,
                product_stock_id: fi.product_stock_id,
                product_name: fi.product_name,
                search: fi.product_name,
                results: [],
                show_dropdown: false,
                quantity: fi.quantity,
            }));
        }

        // Force Select2 to reinitialize after DOM is ready and data is set
        this.$nextTick(() => {
            setTimeout(() => {
                this.select2Key++;
            }, 150);
        });
    },
    watch: {
        customers: {
            handler(newVal) {
                this.localCustomers = Array.isArray(newVal) ? [...newVal] : [];
                // Force Select2 to reinitialize
                this.$nextTick(() => {
                    this.select2Key++;
                });
            },
            immediate: true,
            deep: true
        },
        categories: {
            handler(newVal) {
                this.localCategories = Array.isArray(newVal) ? [...newVal] : [];
                // Force Select2 to reinitialize
                this.$nextTick(() => {
                    this.select2Key++;
                });
            },
            immediate: true,
            deep: true
        }
    },
    computed: {
        showProduct: function () {
            return this.product_stocks.length > 0 ? true : false;
        },
        cartNotEmpty: function () {
            return this.formData.items.length;
        },
        charactersLeft() {
            let char = this.form.note.length;
            return this.char_limit - char + " / " + this.char_limit;
        },
        isCustomerSelected: function () {
            if (this.formData.customer_id) {
                return true;
            } else {
                return false;
            }
        },
        isDataReady: function () {
            return this.localCustomers.length > 0 && this.localCategories.length > 0;
        },
        cashAccount: function () {
            return this.accounts.find((a) => a.type === 'cash') || null;
        },
        bankAccounts: function () {
            return this.accounts.filter((a) => a.type !== 'cash');
        },
        bankAmountEntered: function () {
            return (parseFloat(this.formData.bank_amount) || 0) > 0;
        },
        customerCurrentBalance: function () {
            if (!this.selectedCustomerData) return 0;
            return parseFloat(this.selectedCustomerData.opening_balance) || 0;
        },
        totalPaid: function () {
            const cash    = parseFloat(this.formData.cash_amount)    || 0;
            const bank    = parseFloat(this.formData.bank_amount)    || 0;
            const balance = parseFloat(this.formData.balance_amount) || 0;
            return cash + bank + balance;
        },
        invoiceDue: function () {
            const due = parseFloat(this.calculateTotalWithTax()) - this.totalPaid;
            return due > 0 ? due : 0;
        },
        balanceAfterInvoice: function () {
            const balanceUsed = parseFloat(this.formData.balance_amount) || 0;
            return this.customerCurrentBalance - balanceUsed - this.invoiceDue;
        },
    },
    methods: {
        searchSelectSku(e) {
            let query = e.target.value;
            if (query.length > 1) {
                // Search product by sku
                axios
                    .get(`/admin/app/api/product-stocks/name-sku/search/${query}/${this.warehouse_id}`)
                    .then((res) => {
                        this.product_list = res.data.data;
                    })
                    .catch((err) => {
                    });
            } else if (query.length == 0) {
                let warehouse_id = this.warehouse_id;
                axios
                    .get(`/admin/app/api/product-stocks/warehouse/search/${warehouse_id}`)
                    .then((res) => {
                        this.product_list = res.data.data;
                    })
                    .catch((err) => {
                    });
                // this.searched_product = [];
            }
            else {
                this.searched_product = [];
            }
        },
        submitBarcode() {
            axios
                .get(`/admin/app/api/product-stocks/barcode/${this.search}`)
                .then((res) => {
                    let product_stock = res.data.data;

                    if (product_stock) {
                        if (!product_stock.backorders_allowed && product_stock.quantity < 1) {
                            this.$swal("Error!!!", `Out of stock`, "warning");
                            // Empty search field
                            this.search = "";
                            return;
                        }

                        let already_added = this.formData.items.find(
                            (i) => i.id == product_stock.id
                        );

                        if (already_added) {
                            already_added.quantity = already_added.quantity + 1;
                        } else {
                            if (product_stock.product.is_variant == 1) {
                                this.formData.items.push({
                                    id: product_stock.id,
                                    variation: {
                                       id: product_stock.variation?.id ?? null,
                                       name: product_stock.variation?.name ?? null,
                                    },
                                    is_variant: product_stock.product.is_variant,
                                    product_id: product_stock.product.id,
                                    split_sale: product_stock.product.split_sale,
                                    sku: product_stock.product.sku,
                                    name: product_stock.product.name,
                                    price: product_stock.price_for_sale,
                                    ...this.weightFields(product_stock),
                                    stock: product_stock.quantity,
                                    batch: product_stock.batch,
                                    quantity: 1,
                                    tax_status: product_stock.product.tax_status,
                                    custom_tax: product_stock.product.custom_tax,
                                    discount: 0,
                                    discount_type: "percent",
                                });
                            } else {
                                this.formData.items.push({
                                    id: product_stock.id,
                                    variation:null,
                                    is_variant: product_stock.product.is_variant,
                                    product_id: product_stock.product.id,
                                    split_sale: product_stock.product.split_sale,
                                    sku: product_stock.product.sku,
                                    name: product_stock.product.name,
                                    price: product_stock.price_for_sale,
                                    ...this.weightFields(product_stock),
                                    stock: product_stock.quantity,
                                    batch: product_stock.batch,
                                    quantity: 1,
                                    tax_status: product_stock.product.tax_status,
                                    custom_tax: product_stock.product.custom_tax,
                                    discount: 0,
                                    discount_type: "percent",
                                });
                            }
                        }
                    }
                    let warehouse_id = this.warehouse_id;
                    axios
                        .get(`/admin/app/api/product-stocks/warehouse/search/${warehouse_id}`)
                        .then((res) => {
                            this.product_list = res.data.data;
                        })
                        .catch((err) => {
                        });
                })
                .catch((err) => {
                });
            // Empty search field
            this.search = "";
        },
        selectCategory({ id }) {
            axios
                .get(`/admin/app/api/product-stocks/category/${id}/${this.warehouse_id}`)
                .then((res) => {
                    this.product_list = res.data.data;
                })
                .catch((err) => {
                });
        },
        shippingSameBilling(e) {
            if (this.is_shipping_same_billing) {
                this.formData.shipping.name = this.formData.billing.name;
                this.formData.shipping.email = this.formData.billing.email;
                this.formData.shipping.phone = this.formData.billing.phone;
                this.formData.shipping.address_line_1 =
                    this.formData.billing.address_line_1;
                this.formData.shipping.address_line_2 =
                    this.formData.billing.address_line_2;
                this.formData.shipping.city = this.formData.billing.city;
                this.formData.shipping.state = this.formData.billing.state;
                this.formData.shipping.zip = this.formData.billing.zip;
                this.formData.shipping.country = this.formData.billing.country;
            }
        },
        billinAddressFull() {
            let { address_line_1, address_line_2, city, state, zip, country } =
                this.formData.billing;

            return `${address_line_1 != null ? address_line_1 + ', ' : ''} ${address_line_2 != null ? address_line_2 + ', ' : ''}
                    ${city != null ? city + ', ' : ''} ${state != null ? state + ', ' : ''}
                    ${zip != null ? zip + ', ' : ''} ${country != null ? country + ', ' : ''}`;
        },
        shippingAddressFull() {
            let { address_line_1, address_line_2, city, state, zip, country } =
                this.formData.shipping;

            return `${address_line_1 != null ? address_line_1 + ', ' : ''} ${address_line_2 != null ? address_line_2 + ', ' : ''}
                    ${city != null ? city + ', ' : ''} ${state != null ? state + ', ' : ''}
                    ${zip != null ? zip + ', ' : ''} ${country != null ? country + ', ' : ''}`;

            // return `${address_line_1}, ${address_line_2}, ${city}, ${state}, ${zip}, ${country}`;
        },
        selectedCustomer({ id }) {
            let customer = this.localCustomers.find((item) => item.id == id);
            if (!customer) return;

            this.selectedCustomerData = customer;

            // Set billing address
            this.formData.billing = {
                name: customer.full_name,
                email: customer.email,
                phone: customer.b_phone,
                address_line_1: customer.b_address_line_1,
                address_line_2: customer.b_address_line_2,
                city: customer.b_city_data ? customer.b_city_data.name : "",
                state: customer.b_state_data ? customer.b_state_data.name : "",
                zip: customer.b_zipcode,
                country: customer.b_country_data ? customer.b_country_data.name : "",
            };
        },

        // Barrel ("sold by weight") metadata attached to each cart line so a
        // line can be switched between barrel and kg selling.
        weightFields(product_stock) {
            const p = product_stock.product;
            return {
                is_weight_based: p.is_weight_based == 1,
                kg_per_barrel: Number(p.kg_per_barrel) || 0,
                barrel_label: p.barrel_label || 'Barrel',
                unit: 'barrel',
                price_per_barrel: product_stock.price_for_sale,
                backorders_allowed: product_stock.backorders_allowed,
            };
        },
        roundTo(value, decimals = 2) {
            const f = Math.pow(10, decimals);
            return Math.round((Number(value) + Number.EPSILON) * f) / f;
        },
        // per-kg price = per-barrel price / kg_per_barrel (derived).
        changeUnit(index, unit) {
            const item = this.formData.items[index];
            if (!item.is_weight_based || !(item.kg_per_barrel > 0) || item.unit === unit) {
                return;
            }
            if (unit === 'kg') {
                item.quantity = this.roundTo(Number(item.quantity) * item.kg_per_barrel, 2);
                item.price = this.roundTo(Number(item.price_per_barrel) / item.kg_per_barrel, 2);
            } else {
                item.quantity = this.roundTo(Number(item.quantity) / item.kg_per_barrel, 2);
                item.price = Number(item.price_per_barrel);
            }
            item.unit = unit;
        },
        availableInUnit(item) {
            const barrels = Number(item.stock) || 0;
            return item.is_weight_based && item.unit === 'kg' && item.kg_per_barrel > 0
                ? barrels * item.kg_per_barrel
                : barrels;
        },
        addNewItem(product_stock) {
            let found = this.formData.items.findIndex((p) => p.id == product_stock.id);
            if (found >= 0) {
                let item = this.formData.items[found];
                const maxStock = this.availableInUnit(item);
               if (!product_stock.backorders_allowed && item.quantity >= maxStock) {
                    item.quantity = maxStock;
                    this.$swal("Error!!!", `Out of stock`, "warning");
                    // Empty search field
                    this.search = "";
                    return;
                }
                item.quantity = Number(item.quantity) + 1;
            } else {
                if (!product_stock.backorders_allowed && product_stock.quantity < 1) {
                    this.$swal("Error!!!", `Out of stock`, "warning");
                    // Empty search field
                    this.search = "";
                    return;
                }

                if (product_stock.product.is_variant == 1) {
                    this.formData.items.push({
                        id: product_stock.id,
                        variation: {
                               id: product_stock.variation?.id ?? null,
                              name: product_stock.variation?.name ?? null,
                        },
                        is_variant: product_stock.product.is_variant,
                        product_id: product_stock.product.id,
                        split_sale: product_stock.product.split_sale,
                        sku: product_stock.product.sku,
                        name: product_stock.product.name,
                        model: product_stock.product.model,
                        price: product_stock.price_for_sale,
                        ...this.weightFields(product_stock),
                        stock: product_stock.quantity,
                        batch: product_stock.batch,
                        quantity: 1,
                        tax_status: product_stock.product.tax_status,
                        custom_tax: product_stock.product.custom_tax,
                        discount: 0,
                        discount_type: "percent",
                    });
                } else {
                    this.formData.items.push({
                        id: product_stock.id,
                        variation:null,
                        is_variant: product_stock.product.is_variant,
                        product_id: product_stock.product.id,
                        split_sale: product_stock.product.split_sale,
                        sku: product_stock.product.sku,
                        name: product_stock.product.name,
                        model: product_stock.product.model,
                        price: product_stock.price_for_sale,
                        ...this.weightFields(product_stock),
                        stock: product_stock.quantity,
                        batch: product_stock.batch,
                        quantity: 1,
                        tax_status: product_stock.product.tax_status,
                        custom_tax: product_stock.product.custom_tax,
                        discount: 0,
                        discount_type: "percent",
                    });
                }
            }
        },
        deleteItem: function (index) {
            this.formData.items.splice(index, 1);
        },
        updatePrice(event, index) {
            let item = this.formData.items[index];
            if (item.is_weight_based && item.kg_per_barrel > 0) {
                const val = Number(event.target.value);
                item.price_per_barrel = item.unit === 'kg' ? val * item.kg_per_barrel : val;
            }
        },
        // Exact per-unit price for money math (kg: barrel_price / kg_per_barrel).
        lineUnitPrice(item) {
            if (item.is_weight_based && item.unit === 'kg' && item.kg_per_barrel > 0) {
                const ppb = Number(item.price_per_barrel) || (Number(item.price) * item.kg_per_barrel);
                return ppb / item.kg_per_barrel;
            }
            return Number(item.price);
        },
        updateQuantity(event, index) {
            const value = event.target.valueAsNumber;
            let item = this.formData.items[index];
            const maxStock = this.availableInUnit(item);
          if (!item.backorders_allowed && value > maxStock) {
                item.quantity = maxStock;
                this.$swal("Error!!!", `Out of stock`, "warning");
                return;
            }
        },

        calculateSubtotal(index) {
            let item = this.formData.items[index];
            const total = item.quantity * (this.lineUnitPrice(item) - this.calculateDiscount(item));
            return total.toFixed(2);
        },
        calculateTotalWithOutTax() {
            const total = this.itemsTotal() - this.totalTax();
            return Number(total).toFixed(2);
        },
        calculateTotalWithTax() {
            const total = Number(this.itemsTotal()) - Number(this.calculateGlobalDiscount()) + Number(this.formData.additional_charge_amount);
            return Number(total).toFixed(2);
        },
        itemsTotal() {
            if (this.formData.items.length > 0) {
                let total = 0;
                this.formData.items.map((item) => {
                    total =
                        (Number(this.lineUnitPrice(item) - this.calculateDiscount(item)) +
                            Number(this.calculateTax(item))) *
                        item.quantity +
                        Number(total);
                });

                return total.toFixed(2);
            }
            return 0;
        },

        totalTax() {
            let total = 0;
            this.formData.items.map((item) => {
                total = Number(this.calculateTax(item)) * item.quantity + Number(total);
            });

            return total.toFixed(2);
        },
        calculateTax(item) {
            let tax = 0;
            // Tax include
            if (item.tax_status == "included") {
                if (item.custom_tax) {
                    tax = this.lineUnitPrice(item) * (item.custom_tax / 100);
                } else {
                    tax = this.lineUnitPrice(item) * (Number(this.default_tax) / 100);
                }
            }

            return tax;
        },
        calculateDiscount(item) {
            if (item.discount_type == "percent") {
                const total = this.lineUnitPrice(item) * (item.discount / 100);
                return total.toFixed(2);
            } else {
                return item.discount;
            }
        },
        calculateGlobalDiscount() {
            if (this.formData.discount_type == "percent") {
                const total =
                    this.calculateTotalWithOutTax() * (this.formData.discount / 100);
                return total.toFixed(2);
            } else {
                return this.formData.discount;
            }
        },
        calculateAllDiscount() {
            let total = 0;
            this.formData.items.map((item) => {
                total += item.quantity * this.calculateDiscount(item);
            });
            return total.toFixed(2);
        },
        calculateTotalDiscount() {
            let total = 0;
            this.formData.items.map((item) => {
                total += item.quantity * this.calculateDiscount(item);
            });

            if (Number(this.formData.discount) > 0) {
                total = Number(total) + Number(this.calculateGlobalDiscount());
            }
            return Number(total).toFixed(2);
        },
        calculateDue() {
            let due = this.calculateTotalWithOutTax() - this.formData.total_paid;
            if (due <= 0) return 0;
            return due.toFixed(2);
        },
        calculateExchange() {
            let exchange = this.formData.total_paid - this.calculateTotalWithOutTax();
            if (exchange > 0) {
                this.form.exchange = exchange;
                return exchange.toFixed(2);
            } else {
                this.form.exchange = 0;
                return false;
            }
        },

        submitInvoice() {
            // Validate balance amount
            const maxUsableBalance = Math.max(0, this.customerCurrentBalance);
            if (this.formData.balance_amount > maxUsableBalance) {
                this.$swal("Error!", "Balance amount cannot exceed available balance.", "warning");
                return;
            }

            // Derive payment_type and total_paid from individual amounts
            this.formData.total_paid = this.totalPaid;
            const hasCash    = (parseFloat(this.formData.cash_amount)    || 0) > 0;
            const hasBank    = (parseFloat(this.formData.bank_amount)    || 0) > 0;
            const hasBalance = (parseFloat(this.formData.balance_amount) || 0) > 0;
            const count = [hasCash, hasBank, hasBalance].filter(Boolean).length;
            if (count > 1)       this.formData.payment_type = 'combined';
            else if (hasCash)    this.formData.payment_type = 'cash';
            else if (hasBank)    this.formData.payment_type = 'bank';
            else if (hasBalance) this.formData.payment_type = 'balance';
            else                 this.formData.payment_type = 'cash';

            this.isSubmitting = true;
            axios
                .put(`/admin/invoices/${this.invoice.id}`, this.formData)
                .then((res) => {
                    window.location.href = window.appUrl("/admin/invoices/" + res.data.invoice);
                })
                .catch((err) => {
                    if (err.response && err.response.data && err.response.data.success == false) {
                        this.isExistsValidationErrors = 0;
                        this.$swal("Error!!!", err.response.data.message || "Something went wrong!", "error");
                    } else if (err.response && err.response.data) {
                        this.validationErrors = err.response.data;
                        this.isExistsValidationErrors = Object.keys(this.validationErrors.errors || {}).length;
                        this.$swal("Error!!!", err.response.data.message || "Something went wrong!", "error");
                    } else {
                        this.$swal("Error!!!", "Something went wrong!", "error");
                    }
                }).finally(() => {
                    this.isSubmitting = false;
                });
        },
        print() {
            this.$htmlToPaper("invoice-print");
        },
        btnWalkinCustomer() {
            this.formData.walkin_customer.full_name = '';
            this.formData.walkin_customer.phone = '';
            this.formData.customer_id = '';
            this.formData.billing.name = '';
            this.formData.billing.email = '';
            this.formData.billing.phone = '';
            this.formData.billing.address_line_1 = '';
            this.formData.billing.address_line_2 = '';
            this.formData.billing.city = '';
            this.formData.billing.state = '';
            this.formData.billing.zip = '';
            this.formData.billing.country = '';

            this.formData.shipping.name = '';
            this.formData.shipping.email = '';
            this.formData.shipping.phone = '';
            this.formData.shipping.address_line_1 = '';
            this.formData.shipping.address_line_2 = '';
            this.formData.shipping.city = '';
            this.formData.shipping.state = '';
            this.formData.shipping.zip = '';
            this.formData.shipping.country = '';
        },
        formatNumber(number, decimals = 2) {
            if (number === null || number === undefined) return 0;
            return Number(number).toFixed(decimals);
        },
        addFreeItem() {
            this.formData.free_items.push({
                product_id: null,
                product_stock_id: null,
                product_name: '',
                search: '',
                results: [],
                show_dropdown: false,
                quantity: 1,
            });
        },
        removeFreeItem(idx) {
            this.formData.free_items.splice(idx, 1);
        },
        searchFreeProduct(idx) {
            const fi = this.formData.free_items[idx];
            const q = fi.search.trim();
            if (q.length < 2) { fi.show_dropdown = false; return; }
            axios.get(`/admin/app/api/product-stocks/name-sku/search/${encodeURIComponent(q)}/${this.warehouse_id}`)
                .then(res => {
                    fi.results = res.data.data || [];
                    fi.show_dropdown = fi.results.length > 0;
                });
        },
        selectFreeProduct(idx, result) {
            const fi = this.formData.free_items[idx];
            fi.product_id       = result.product.id;
            fi.product_stock_id = result.id;
            fi.product_name     = result.product.name;
            fi.search           = result.product.name + ' (' + result.product.sku + ')';
            fi.show_dropdown    = false;
            fi.results          = [];
        },
        closeFreeDropdown(idx) {
            setTimeout(() => {
                if (this.formData.free_items[idx]) {
                    this.formData.free_items[idx].show_dropdown = false;
                }
            }, 200);
        },
    },
    filters: {
        custom_date: function (value) {
            if (value) {
                return moment(String(value)).format("YYYY-MM-DD hh:mm a");
            }
        },
    },
    validations: {
        formData: {
            date: {
                required,
            },
            customer_id: {
                required,
            },
            status: {
                required,
            },
        },
    },
};
</script>

<style scoped>
.list-image {
    height: 100px;
    object-fit: cover;
    object-position: center;
}
/* Bank sub-fields */
.ic-bank-fields { margin-top: 14px; border-top: 1px solid #ececec; padding-top: 12px; }

.product-item:hover {
    cursor: pointer;
}

.modal-footer {
    display: -ms-flexbox;
    display: block;
    -ms-flex-align: center;
    align-items: center;
    -ms-flex-pack: end;
    justify-content: flex-end;
    padding: 1rem;
    border-top: 1px solid #dee2e6;
    border-bottom-right-radius: 0.3rem;
    border-bottom-left-radius: 0.3rem;
}
</style>
