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
                                <div class="col-12">
                                    <form @submit.prevent="submitBarcode()">
                                        <div class="form-group">
                                            <input v-focus v-model="search" type="text" class="form-control"
                                                placeholder="Search Name, SKU, Model, Parts No or Scan Barcode"
                                                @keyup="searchSelectSku($event)" />
                                        </div>
                                    </form>
                                </div>
                                <div class="col-sm-6 col-lg-6 col-md-6" style="display: none;">
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
                                        <div class="ic-images-out-of-stock" style="display: none;">
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
                                            <p class="card-text p-0 m-0">
                                                {{ __("custom.price") }}: {{ currency_symbol }} {{
                                                    formatNumber(product_stock.price_for_sale, 2) }}<template v-if="product_stock.product.is_weight_based == 1 && product_stock.product.kg_per_barrel > 0">/{{ product_stock.product.barrel_label || 'Barrel' }}</template>
                                            </p>
                                            <p class="card-text p-0 m-0"
                                                v-if="product_stock.product.is_weight_based == 1 && product_stock.product.kg_per_barrel > 0">
                                                {{ __("custom.selling_price") }}: {{ currency_symbol }} {{
                                                    formatNumber(product_stock.price_for_sale / product_stock.product.kg_per_barrel, 2) }}/kg
                                            </p>
                                            <p class="card-text p-0 m-0">
                                                {{ __("custom.buying_price") }}: {{ currency_symbol }} {{
                                                    product_stock.product.buying_price ? formatNumber(product_stock.product.buying_price, 2) : '0.00' }}<template v-if="product_stock.product.is_weight_based == 1 && product_stock.product.kg_per_barrel > 0">/kg</template>
                                            </p>
                                            <p class="card-text p-0 m-0">
                                                {{ __("custom.stock") }}: {{ formatNumber(product_stock.quantity, 2) }}<template v-if="product_stock.product.is_weight_based == 1 && product_stock.product.kg_per_barrel > 0"> {{ product_stock.product.barrel_label || 'Barrel' }}/{{ formatNumber(product_stock.quantity * product_stock.product.kg_per_barrel, 2) }}kg</template>
                                            </p>

                                            <p class="card-text p-0 m-0" v-if="product_stock.product.model">
                                                {{ __("custom.model") }}: {{ product_stock.product.model }}
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
                                    :options="localCustomers"
                                    :settings="{ placeholder: 'Select Customer' }"
                                    @select="selectedCustomer($event)">
                                </Select2>
                                <!-- Customer current balance badge -->
                                <div v-if="selectedCustomerData && !formData.is_walkin_customer" class="mt-2">
                                    <span class="badge"
                                        :class="customerCurrentBalance >= 0 ? 'badge-success' : 'badge-danger'"
                                        style="font-size:0.85rem; padding:5px 10px;">
                                        Current Balance: {{ currency_symbol }}{{ $formatNumber(customerCurrentBalance) }}
                                    </span>
                                </div>
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
                                        <a class="float-right" href="ic-javascriptVoid" data-toggle="modal"
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

                            <div class="row">
                                <div class="from-group mt-3 mb-3 col-md-6">
                                    <label for="">{{ __("custom.date") }}</label>
                                    <datepicker input-class="form-control" v-model="formData.date" format="yyyy-MM-dd"
                                        placeholder="Select date" v-model.trim="$v.formData.date.$model"></datepicker>
                                    <small class="error" v-if="!$v.formData.date.required">
                                        {{ __("custom.required") }}
                                    </small>
                                </div>

                                <div class="from-group mt-3 mb-3 col-md-6">
                                    <label for="">{{ __("custom.due_date") }}</label>
                                    <datepicker input-class="form-control" v-model="formData.due_date" format="yyyy-MM-dd"
                                        placeholder="Select due date"></datepicker>
                                </div>
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
                                                <p class="p-0 m-0" v-if="item.is_variant && item.variation">{{ item.name }}
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
                                                <input type="number" v-if="item.split_sale || item.is_weight_based" min="0" step="any"
                                                    v-model="item.price" @input="updatePrice($event, index)"
                                                    class="form-control text-center border" />
                                                <input type="number" v-else min="1"
                                                    onkeydown="if(event.key==='.'){event.preventDefault();}"
                                                    oninput="event.target.value = event.target.value.replace(/[^0-9]*/g,'');"
                                                    v-model="item.price" @input="updatePrice($event, index)"
                                                    class="form-control text-center border" />
                                                <!--                                            {{ currency_symbol }} {{ item.price }}-->
                                            </td>
                                            <td>
                                                <input type="number" v-if="item.split_sale || item.is_weight_based" min="0" step="any"
                                                    v-model="item.quantity" @input="updateQuantity($event, index)"
                                                    class="form-control text-center border" />
                                                <input type="number" v-else min="1"
                                                    onkeydown="if(event.key==='.'){event.preventDefault();}"
                                                    oninput="event.target.value = event.target.value.replace(/[^0-9]*/g,'');"
                                                    v-model="item.quantity" @input="updateQuantity($event, index)"
                                                    class="form-control text-center border" />
                                            </td>

                                            <td>
                                                <input min="0" type="number" v-model="item.discount"
                                                    class="form-control text-center border" />
                                            </td>
                                            <td>
                                                <select v-model="item.discount_type" class="form-control border">
                                                    <option value="percent">%</option>
                                                    <option value="fixed">{{ __("custom.fixed") }}</option>
                                                </select>
                                            </td>
                                            <td>{{ currency_symbol }}{{ $formatNumber(calculateSubtotal(index)) }}</td>
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
                                                <b>{{ currency_symbol }} {{ $formatNumber(calculateTotalWithOutTax()) }}</b>
                                            </td>

                                            <td colspan="2"></td>
                                        </tr>

                                        <tr v-if="cartNotEmpty">
                                            <td colspan="2">
                                                <b>{{ __("custom.discount") }}</b>
                                            </td>
                                            <td>
                                                <input type="number" v-model="formData.discount"
                                                    class="form-control text-center border" />
                                            </td>
                                            <td colspan="2">
                                                <select v-model="formData.discount_type"
                                                        class="form-control text-center border">
                                                    <option value="percent">%</option>
                                                    <option value="fixed">{{ __("custom.fixed") }}</option>
                                                </select>
                                            </td>
                                            <td>
                                                <b>{{ currency_symbol }} {{ $formatNumber(calculateGlobalDiscount()) }}</b>
                                            </td>
                                            <td colspan="2"></td>
                                        </tr>

                                        <tr v-if="cartNotEmpty">
                                            <td colspan="6">
                                                <b>{{ __("custom.total_discount") }}</b>
                                            </td>
                                            <td>
                                                <b>{{ currency_symbol }} {{ $formatNumber(calculateTotalDiscount()) }}</b>
                                            </td>
                                            <td></td>
                                        </tr>


                                        <tr v-if="cartNotEmpty">
                                            <td colspan="2">
                                                <b>{{ __("custom.additional_charge") }}</b>
                                            </td>
                                            <td colspan="3">
                                                <input type="text" v-model="formData.additional_charge_name"
                                                    class="form-control border" placeholder="Charge Name" />
                                            </td>
                                            <td>
                                                <input type="number" step="any" v-model="formData.additional_charge_amount"
                                                    class="form-control text-center border" placeholder="Amount" />
                                            </td>
                                            <td colspan="2"></td>
                                        </tr>

                                        <tr>
                                            <td colspan="6">
                                                <b>{{ __("custom.total") }}</b>
                                            </td>
                                            <td>
                                                <b>{{ currency_symbol }} {{ $formatNumber(calculateTotalWithTax()) }}</b>
                                            </td>
                                            <td></td>
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

                            <div class="form-grou">
                                <div class="row">
                                    <div class="col-8">
                                        <label for="">{{ __("custom.coupon") }}</label>
                                        <input class="form-control input-sm" name="coupon"
                                            v-model="formData.coupon.code" />
                                    </div>
                                    <div class="col-4">
                                        <label for="">&nbsp;</label>
                                        <button type="button" class="btn btn-primary btn-block btn-sm"
                                            @click="applyCoupon">
                                            {{ __("custom.apply") }}
                                        </button>
                                    </div>
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
                                <div style="border:1px solid #dee2e6;border-radius:10px;padding:10px 12px;background:#fff;margin-bottom:8px;min-height:0;">
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
                                <textarea v-model="formData.notes" cols="30" rows="2" class="form-control"></textarea>
                            </div>

                            <div class="col-sm-12 p-0 mt-3" v-if="cartNotEmpty">
                                <div class="custom-control custom-checkbox">
                                    <input name="is_delivered" v-model="formData.is_delivered"
                                        class="form-check-input custom-control-input" type="checkbox" checked="checked"
                                        id="is_delivered" />
                                    <label class="form-check-label custom-control-label checkbox-label"
                                        for="is_delivered">
                                        {{ __("custom.is_delivered") }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-12 p-0 mt-3">
                                <div class="text-center">
                                    <button v-if="cartNotEmpty" type="button" class="btn btn-dark btn-block"
                                        @click.prevent="submitInvoice" :disabled="isSubmitting || isDraftSubmitting">
                                        <span v-if="isSubmitting">
                                            <i class="fa fa-spinner fa-spin"></i>
                                        </span>
                                        <span v-else>
                                            {{ __("custom.confirm") }}
                                        </span>
                                    </button>
                                    <button v-if="cartNotEmpty" type="button" class="btn btn-warning btn-block"
                                        @click.prevent="submitDraft" :disabled="isSubmitting || isDraftSubmitting">
                                        <span v-if="isDraftSubmitting">
                                            <i class="fa fa-spinner fa-spin"></i>
                                        </span>
                                        <span v-else>
                                            <i class="fas fa-file-invoice"></i> {{ __("custom.save_as_draft") }}
                                        </span>
                                    </button>
                                    <button class="btn btn-link float-right" v-if="cartNotEmpty"
                                        @click="resetAllValues"><i class="fas fa-redo"></i> {{ __("custom.reset") }}
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
                                        <div class="form-group">
                                            <label for="">{{ __("custom.name") }}</label>
                                            <input type="text" class="form-control" v-model="formData.billing.name" />
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="">{{ __("custom.email") }}</label>
                                            <input type="email" class="form-control" v-model="formData.billing.email" />
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="">{{ __("custom.phone") }}</label>
                                            <input type="text" class="form-control" v-model="formData.billing.phone" />
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="">{{ __("custom.address_line_1") }}</label>
                                            <input type="text" class="form-control"
                                                v-model="formData.billing.address_line_1" />
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="">{{ __("custom.address_line_2") }}</label>
                                            <input type="text" class="form-control"
                                                v-model="formData.billing.address_line_2" />
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="">{{ __("custom.city") }}</label>
                                            <input type="text" class="form-control" v-model="formData.billing.city" />
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="">{{ __("custom.state") }}</label>
                                            <input type="text" class="form-control" v-model="formData.billing.state" />
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="">{{ __("custom.zipcode") }}</label>
                                            <input type="text" class="form-control" v-model="formData.billing.zip" />
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="">{{ __("custom.country") }}</label>
                                            <input type="text" class="form-control"
                                                v-model="formData.billing.country" />
                                        </div>
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
                                        <label for="">{{ __("custom.post_code_or_zip_code") }}</label>
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
        "warehouse_id",
        "currency_symbol",
        "accounts",
    ],
    components: {
        Datepicker,
    },
    data() {
        return {
            isSubmitting: false,
            isDraftSubmitting: false,
            validationErrors: [],
            isExistsValidationErrors: 0,
            currencySymbol: "",
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
                is_delivered: true,
                walkin_customer: {
                    full_name: "",
                    phone: "",
                },
                cash_amount: 0,
                bank_amount: 0,
                balance_amount: 0,
                total_paid: 0,
                bank_info: {
                    bank_name: "",
                    ac_no: "",
                    t_no: "",
                    date: "",
                },
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
                discount_type: "",
                coupon: {
                    code: "",
                    discount: 0,
                    discount_type: "percent",
                },
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
    },
    mounted() {
        this.formData.date = new Date().toISOString();
        this.formData.due_date = new Date().toISOString();
        this.product_list = this.product_stocks;

        // Default the bank dropdown to the first available bank account (not the cash account),
        // so a bank payment is recorded against a real bank account. Account Number stays manual.
        if (this.bankAccounts.length > 0) {
            this.formData.bank_info.bank_name = this.bankAccounts[0].id;
        }

        // Force Select2 to reinitialize after DOM is ready
        this.$nextTick(() => {
            setTimeout(() => {
                this.select2Key++;
            }, 50);
        });

        console.log(this.customers, this.categories);
        console.log('invoice add components');

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
        },
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
            return this.accounts.find((account) => account.type === 'cash') || null;
        },
        bankAccounts: function () {
            return this.accounts.filter((account) => account.type !== 'cash');
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
        resetAllValues() {
            window.location.replace(location.href)
        },
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
                        if (!product_stock.backorders_allowed  && product_stock.quantity < 1) {
                            this.$swal("Error!!!", `Out of stock`, "warning");
                            this.search = "";
                            return;
                        }

                        if (product_stock.product.is_variant == 0) {
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
                                        is_variant: Number(product_stock.product.is_variant),
                                        product_id: product_stock.product.id,
                                        split_sale: product_stock.product.split_sale,
                                        sku: product_stock.product.sku,
                                        name: product_stock.product.name,
                                        price: product_stock.price_for_sale,
                                        ...this.weightFields(product_stock),
                                        stock: product_stock.quantity,
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
                                        is_variant: Number(product_stock.product.is_variant),
                                        product_id: product_stock.product.id,
                                        split_sale: product_stock.product.split_sale,
                                        sku: product_stock.product.sku,
                                        name: product_stock.product.name,
                                        price: product_stock.price_for_sale,
                                        ...this.weightFields(product_stock),
                                        stock: product_stock.quantity,
                                        quantity: 1,
                                        tax_status: product_stock.product.tax_status,
                                        custom_tax: product_stock.product.custom_tax,
                                        discount: 0,
                                        discount_type: "percent",
                                    });
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
                            this.search = "";
                        }
                    } else {
                        // this.$swal("info!!!", `Please select variant`, "info");
                        this.product_list = res.data.data;
                    }

                })
                .catch((err) => {
                });
            // Empty search field

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

            return `${address_line_1}, ${address_line_2}, ${city}, ${state}, ${zip}, ${country}`;
        },
        shippingAddressFull() {
            let { address_line_1, address_line_2, city, state, zip, country } =
                this.formData.shipping;

            return `${address_line_1}, ${address_line_2}, ${city}, ${state}, ${zip}, ${country}`;
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
        // Round to a fixed number of decimals without floating-point noise.
        roundTo(value, decimals = 2) {
            const f = Math.pow(10, decimals);
            return Math.round((Number(value) + Number.EPSILON) * f) / f;
        },
        // Switch a barrel line between barrel and kg, converting both the
        // quantity and the unit price so the line value is preserved.
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
        // Available stock for a line expressed in the line's current unit.
        availableInUnit(item) {
            const barrels = Number(item.stock) || 0;
            return item.is_weight_based && item.unit === 'kg' && item.kg_per_barrel > 0
                ? barrels * item.kg_per_barrel
                : barrels;
        },
        // Exact (un-rounded) per-unit price used for money math, so kg totals are
        // penny-exact for any factor. For kg: barrel_price / kg_per_barrel.
        lineUnitPrice(item) {
            if (item.is_weight_based && item.unit === 'kg' && item.kg_per_barrel > 0) {
                const ppb = Number(item.price_per_barrel) || (Number(item.price) * item.kg_per_barrel);
                return ppb / item.kg_per_barrel;
            }
            return Number(item.price);
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
                    // console.log(this.formData.items)
                }
            }
        },
        deleteItem: function (index) {
            this.formData.items.splice(index, 1);
        },

        updatePrice(event, index) {
            let item = this.formData.items[index];
            // Keep the authoritative per-barrel price in sync with manual edits
            // so the exact unit price (used for totals) reflects the user's input.
            if (item.is_weight_based && item.kg_per_barrel > 0) {
                const val = Number(event.target.value);
                item.price_per_barrel = item.unit === 'kg' ? val * item.kg_per_barrel : val;
            }
        },
        updateQuantity(event, index) {
            const value = event.target.valueAsNumber;
            let item = this.formData.items[index];
            // Cap at available stock expressed in the line's current unit
            // (kg sales are capped at barrels * kg_per_barrel).
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
            // console.log(this.itemsTotal());
            // console.log(this.totalTax());
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
                    // console.log(this.calculateTax(item));
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
        formatNumber(number, decimals = 2) {
            if (number === null || number === undefined) return 0;
            return Number(number).toFixed(decimals);
        },

        submitInvoice() {

            // Validate balance amount does not exceed available balance
            const maxUsableBalance = Math.max(0, this.customerCurrentBalance);
            if (this.formData.balance_amount > maxUsableBalance) {
                this.$swal("Error!", "Balance amount cannot exceed available balance.", "warning");
                return;
            }

            // Derive total_paid and payment_type before submitting
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
            axios.post("/admin/invoices", this.formData)
                .then((res) => {
                    window.location.href = window.appUrl("/admin/invoices/" + res.data.invoice);
                })
                .catch((err) => {
                    if (err.response && err.response.data) {
                        if (err.response.data.success == false) {
                            this.isExistsValidationErrors = 0;
                            this.$swal("Error!!!", err.response.data.message || "Something went wrong!", "error");
                        } else {
                            this.validationErrors = err.response.data;
                            this.isExistsValidationErrors = Object.keys(this.validationErrors.errors || {}).length;
                            this.$swal("Error!!!", err.response.data.message || "Something went wrong!", "error");
                        }
                    } else {
                        this.$swal("Error!!!", "Something went wrong!", "error");
                    }
                })
                .finally(() => {
                    this.isSubmitting = false;
                });
        },
        submitDraft() {
            this.isDraftSubmitting = true;
            axios
                .post("/admin/draft-invoices", this.formData)
                .then((res) => {
                    window.location.href = window.appUrl("/admin/draft-invoices/" + res.data.invoice);
                })
                .catch(err => {
                    if (err.response && err.response.data) {
                        if (err.response.data.success == false) {
                            this.isExistsValidationErrors = 0;
                            this.$swal("Error!!!", err.response.data.message || "Something went wrong!", "error");
                        } else {
                            this.validationErrors = err.response.data;
                            this.isExistsValidationErrors = Object.keys(this.validationErrors.errors || {}).length;
                            this.$swal("Error!!!", err.response.data.message || "Something went wrong!", "error");
                        }
                    } else {
                        this.$swal("Error!!!", "Something went wrong!", "error");
                    }
                }).finally(() => {
                    this.isDraftSubmitting = false;
                });
        },
        print() {
            this.$htmlToPaper("invoice-print");
        },
        applyCoupon() {
            if (this.formData.coupon.code) {
                axios
                    .get("/admin/app/api/active-coupon/" + this.formData.coupon.code)
                    .then((res) => {
                        let available_product = false;
                        if (res.data.status == true) {
                            let coupon_product_ids = res.data.coupon_product_ids;
                            for (let i = 0; i < this.formData.items.length; i++) {
                                if (coupon_product_ids.find(id => id == this.formData.items[i].product_id)) {
                                    if (this.formData.items[i].quantity >= res.data.coupon.minimum_shopping) {
                                        this.formData.items[i].discount = res.data.coupon.discount;
                                        this.formData.items[i].discount_type = res.data.coupon.discount_type;
                                        available_product = true;
                                    }
                                }
                            }
                            this.formData.coupon.discount = res.data.coupon.discount;
                            this.formData.coupon.discount_type = res.data.coupon.discount_type;

                            this.$swal("Success!", "Coupon applied successfully!", "success");

                            if (available_product == false) {
                                this.$swal("Error!!!", "This coupon is not applicable for this product!", "error");
                            } else {
                                this.$swal("Success!!!", "Coupon applied successfully!", "success");
                            }
                        } else {
                            this.$swal("Error!!!", "Some thing went wrong! Maybe coupon expire or invalid", "error");
                        }
                    })
                    .catch((err) => {
                        this.$swal("Error!!!", "Some thing went wrong!", "error");
                    });
            }
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
        onBankAccountSelect() {
            const selected = this.bankAccounts.find(a => a.id == this.formData.bank_info.bank_name);
            if (selected) {
                this.formData.bank_info.ac_no = selected.account_number || '';
            }
        },
        btnWalkinCustomer() {
            this.formData.walkin_customer.full_name = '';
            this.formData.walkin_customer.phone = '';
            this.formData.customer_id = '';
            this.selectedCustomerData = null;
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
.product-item:hover { cursor: pointer; }
.modal-footer {
    display: block;
    padding: 1rem;
    border-top: 1px solid #dee2e6;
    border-bottom-right-radius: .3rem;
    border-bottom-left-radius: .3rem;
}

/* ── Payment cards — match items-table visual language ─── */
.ic-payment-card {
    border: 1px solid #e2e2e2;
    border-radius: 14px;
    padding: 14px 16px 16px;
    background: #fff;
}
.ic-payment-card--balance {
    border-color: #8ecba8;
    background: #f5fdf8;
}
.ic-payment-card__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
}
/* Left side: icon image */
.ic-pay-icon { height: 26px; }
/* Right side: plain label — same weight/size as table header */
.ic-pay-label-right {
    font-size: .875rem;
    font-weight: 600;
    color: #343a40;
}
/* Current Balance left label */
.ic-pay-label {
    font-size: .875rem;
    font-weight: 700;
    color: #1a6e3d;
    display: flex;
    align-items: center;
    gap: 7px;
}
.ic-pay-icon--wallet { color: #2da96f; font-size: 1rem; }
/* Available text on right */
.ic-balance-available {
    font-size: .8rem;
    color: #7a7a7a;
    font-weight: 400;
}
/* Input — matches items-table inputs */
.ic-pay-input {
    font-size: .9375rem;
    border-radius: 8px;
    border: 1px solid #d4d4d4;
    padding: 9px 13px;
    height: 46px;
    color: #333;
    background: #fff;
    width: 100%;
    display: block;
    transition: border-color .15s;
}
.ic-pay-input:focus {
    outline: none;
    border-color: #28aaa9;
    box-shadow: 0 0 0 2px rgba(40,170,169,.12);
}
/* Bank sub-fields */
.ic-bank-fields { margin-top: 14px; border-top: 1px solid #ececec; padding-top: 12px; }
.ic-field-label { font-size: .8rem; font-weight: 600; color: #555; margin-bottom: 4px; display: block; }

/* ── Breakdown — matches items-table row style ─────────── */
.ic-breakdown {
    border: 1px solid #e2e2e2;
    border-radius: 14px;
    overflow: hidden;
    font-size: .875rem;          /* same as table */
}
.ic-breakdown__row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 11px 18px;          /* same as table td padding */
    background: #fff;
    border-bottom: 1px solid #f0f0f0;
}
.ic-breakdown__row:last-child { border-bottom: none; }

/* Invoice Total — slightly tinted like table header */
.ic-breakdown__row--total-header {
    background: #fafafa;
    border-bottom: 1px solid #e8e8e8;
}
/* Total Paid — blue tint, separator above & below */
.ic-breakdown__row--paid {
    background: #edf1ff;
    border-top: 1.5px solid #d5daf0;
    border-bottom: 1.5px solid #d5daf0;
}
/* Due — rose tint */
.ic-breakdown__row--due { background: #fff4f4; }
/* Remaining Balance — clean white, top separator */
.ic-breakdown__row--remaining {
    background: #fff;
    border-top: 1.5px solid #e2e2e2;
}

/* Text helpers */
.ic-breakdown__label       { color: #888; font-weight: 400; }
.ic-breakdown__label--bold { font-weight: 700; color: #222; }
.ic-breakdown__value--dark  { font-weight: 700; color: #212529; }
.ic-breakdown__value--green { font-weight: 600; color: #1a9e5c; }
.ic-breakdown__value--red   { font-weight: 600; color: #d93025; }
</style>
