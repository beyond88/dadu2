<template>
    <div class="col-sm-12">
    <div class="table-responsive">
        <table class="table table-bordered mb-4">
            <tr>
                <th colspan="6">
                    <img class="img-100-60" :src="product.thumb_url" :alt="product.name" />
                    <span class="ml-4">
                        <small>{{ __("custom.product_name") }}: </small>
                        {{ product.name }}
                    </span>
                </th>
                <th colspan="2">
                    {{ __("custom.alert_quantity") }}
                    <input type="number" class="form-control" name="alert_quantity" required v-model="stock_alert_quantity" :placeholder='__("custom.alert_quantity")'>
                </th>
            </tr>
            <tr style="white-space: nowrap;">
                <th>{{ __("custom.warehouse_name") }}</th>
                <th>{{ __("custom.current_stock") }} <small v-if="product.weight_unit">({{ product.weight_unit.name }})</small></th>
                <th>
                    {{ __("custom.qty") }}
                    <small v-if="isWeightBased">(KG)</small>
                    <small v-else-if="product.weight_unit">({{ product.weight_unit.name }})</small>
                </th>
                <th style="min-width:120px;">{{ __("custom.buying_price") }} <small v-if="isWeightBased">(per KG)</small></th>
                <th>{{ __("custom.selling_price") }} <small v-if="isWeightBased">(per KG)</small></th>
                <th>{{ __("custom.adjust_type") }} <small>({{ __("custom.stock")}})</small></th>
                <th style="min-width:180px;">Note</th>
                <th></th>
            </tr>
            <tr v-for="(item, index) in items" :key="index">
                <td>
                    <select
                        @change="checkDuplicate(index, $event)"
                        class="form-control"
                        v-model="item.warehouse"
                        :name="'warehouse_stock[' + index + '][warehouse]'"
                    >
                        <option value="" selected>{{ __("custom.select_warehouse") }}</option>
                        <option
                            v-for="(item, index) in warehouses"
                            :key="index"
                            :value="item.id"
                        >
                            {{ item.name }}
                        </option>
                    </select>

                </td>
                <td>
                    <input
                        readonly="1"
                        type="number"
                        class="form-control"
                        :name="'warehouse_stock[' + index + '][stock]'"
                        v-model="item.stock"
                    />
                </td>
                <td>
                    <!-- KG input for weight-based products -->
                    <template v-if="isWeightBased">
                        <input
                            min="0"
                            width="30px"
                            type="number"
                            step="any"
                            class="form-control"
                            :class="{ 'is-invalid': isFractionalBarrelFromKg(item) }"
                            v-model.number="item.kg_quantity"
                            @input="onKgInput(item)"
                            :placeholder="'Enter KG'"
                        />
                        <!-- Hidden field: actual barrel qty sent to backend -->
                        <input
                            type="hidden"
                            :name="'warehouse_stock[' + index + '][quantity]'"
                            :value="barrelQtyFromKg(item)"
                        />
                        <small class="text-muted d-block" v-if="kgPerBarrel">
                            1 {{ barrelLabel }} = {{ kgPerBarrel }} kg
                        </small>
                        <small class="text-info d-block" v-if="item.kg_quantity && !isFractionalBarrelFromKg(item) && kgPerBarrel">
                            = {{ barrelQtyFromKg(item) }} {{ barrelLabel }}
                        </small>
                        <small v-if="isFractionalBarrelFromKg(item)" class="d-block text-danger">
                            {{ kgPerBarrel }} kg এর গুণিতক হতে হবে (পূর্ণ {{ barrelLabel }} হতে হবে)
                        </small>
                    </template>
                    <!-- Normal qty input for non-weight-based products -->
                    <template v-else>
                        <input
                            min="0"
                            width="30px"
                            type="number"
                            step="any"
                            class="form-control"
                            :name="'warehouse_stock[' + index + '][quantity]'"
                            v-model.number="item.quantity"
                            @input="clampQty(item, 'quantity')"
                        />
                    </template>
                </td>
                <td>
                    <template v-if="isWeightBased">
                        <input
                            min="0"
                            width="30px"
                            type="number"
                            step="any"
                            class="form-control"
                            v-model.number="item.kg_price"
                            placeholder="Buying Price per KG"
                        />
                        <!-- Hidden field: converts per-KG price to per-barrel for backend -->
                        <input
                            type="hidden"
                            :name="'warehouse_stock[' + index + '][price]'"
                            :value="barrelPriceFromKg(item.kg_price)"
                        />
                        <small class="text-info d-block" v-if="item.kg_price && kgPerBarrel">
                            = {{ (currency_symbol || '') + barrelPriceFromKg(item.kg_price) }} / {{ barrelLabel }}
                        </small>
                    </template>
                    <template v-else>
                        <input
                            min="0"
                            width="30px"
                            type="number"
                            class="form-control"
                            :name="'warehouse_stock[' + index + '][price]'"
                            v-model="item.price"
                        />
                    </template>
                </td>
                <td>
                    <template v-if="isWeightBased">
                        <input
                            min="0"
                            width="30px"
                            type="number"
                            step="any"
                            class="form-control"
                            v-model.number="item.kg_customer_buying_price"
                            placeholder="Selling price per KG"
                        />
                        <!-- Hidden field: converts per-KG selling price to per-barrel for backend -->
                        <input
                            type="hidden"
                            :name="'warehouse_stock[' + index + '][customer_buying_price]'"
                            :value="barrelPriceFromKg(item.kg_customer_buying_price)"
                        />
                        <small class="text-info d-block" v-if="item.kg_customer_buying_price && kgPerBarrel">
                            = {{ (currency_symbol || '') + barrelPriceFromKg(item.kg_customer_buying_price) }} / {{ barrelLabel }}
                        </small>
                    </template>
                    <template v-else>
                        <input
                            min="0"
                            width="30px"
                            type="number"
                            class="form-control"
                            :name="'warehouse_stock[' + index + '][customer_buying_price]'"
                            v-model="item.customer_buying_price"
                        />
                    </template>
                </td>
                <td v-if="old_stocks.length >0">
                    <select class="form-control" v-model="item.adjust_type" :name="'warehouse_stock[' + index + '][adjust_type]'">
                        <option value="" selected>{{ __("custom.select_adjust_type") }}</option>
                        <option v-for="(item, index) in adjust_type" :key="index" :value="item">{{ item }}</option>
                    </select>
                    <input type="hidden" :name="'warehouse_stock[' + index + '][manage_stock]'" v-model="item.manage_stock" />
                    <input type="hidden" :name="'warehouse_stock[' + index + '][backorders_allowed]'" v-model="item.backorders_allowed" />
                </td>
                <td v-else>
                    <select class="form-control" v-model="item.adjust_type" :name="'warehouse_stock[' + index + '][adjust_type]'">
                        <option value="" selected>{{ __("custom.select_adjust_type") }}</option>
                        <option value="Add" selected>Add</option>
                    </select>
                    <input type="hidden" :name="'warehouse_stock[' + index + '][manage_stock]'" v-model="item.manage_stock" />
                    <input type="hidden" :name="'warehouse_stock[' + index + '][backorders_allowed]'" v-model="item.backorders_allowed" />
                </td>
                <td>
                    <input
                        type="text"
                        class="form-control"
                        :name="'warehouse_stock[' + index + '][note]'"
                        v-model="item.note"
                        maxlength="500"
                        placeholder="Reason..."
                    />
                </td>
                <td>
                    <button v-if="item.id"
                            @click="deleteItem(index)"
                            type="button" disabled
                            class="btn btn-sm btn-outline-danger"
                    >
                        <i class="fa fa-trash"></i>
                    </button>

                    <button v-else
                            @click="deleteItem(index)"
                            type="button"
                            class="btn btn-sm btn-outline-danger"
                    >
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>

            <tfoot>
            <tr>
                <td colspan="8">
                    <button @click="addItem" type="button" class="btn btn-sm btn-info float-right"
                            :title="__('custom.add_warehouse')">
                        <i class="fa fa-plus"></i>
                    </button>
                </td>
            </tr>
            </tfoot>
        </table>
    </div>
    </div>
</template>

<script>
export default {
    props: ["product", "warehouses", "old_stocks", "currency_symbol"],
    data() {
        return {
            items: [
                {
                    id:'',
                    warehouse: "",
                    stock: 0,
                    quantity: 0,
                    kg_quantity: 0,
                    customer_buying_price: 0,
                    price: 0,
                    kg_price: 0,
                    kg_customer_buying_price: 0,
                    adjust_type: '',
                    manage_stock: false,
                    backorders_allowed: false,
                    note: '',
                },
            ],
            adjust_type:['Add', 'Subtract'],
            stock_alert_quantity: 0
        };
    },
    mounted() {
        if (this.old_stocks.length > 0) {
            this.items = [];
            this.old_stocks.map((item) => {
                // For weight-based products, stored prices are per-barrel.
                // Convert to per-KG for display.
                const kgFactor = this.kgPerBarrel || 1;
                this.items.push({
                    id: item.id,
                    warehouse: item.warehouse_id,
                    stock: item.quantity,
                    quantity: 0,
                    kg_quantity: 0,
                    adjust_type: '',
                    customer_buying_price: item.customer_buying_price,
                    price: item.price,
                    kg_price: this.isWeightBased ? parseFloat((Number(item.price) / kgFactor).toFixed(4)) : 0,
                    kg_customer_buying_price: this.isWeightBased ? parseFloat((Number(item.customer_buying_price) / kgFactor).toFixed(4)) : 0,
                    manage_stock: item.manage_stock ?? false,
                    backorders_allowed: item.backorders_allowed ?? false,
                    note: '',
                });
            });
        }

        this.stock_alert_quantity = this.product.stock_alert_quantity

        // The Submit button lives in the wrapping Blade <form>, so intercept its
        // native submit to block fractional barrel quantities for weight-based products.
        this.$nextTick(() => {
            this.stockForm = this.$el.closest("form");
            if (this.stockForm) {
                this.stockForm.addEventListener("submit", this.validateBarrels);
            }
        });
    },
    beforeDestroy() {
        if (this.stockForm) {
            this.stockForm.removeEventListener("submit", this.validateBarrels);
        }
    },
    computed: {
        isWeightBased() {
            return this.product.is_weight_based == 1;
        },
        kgPerBarrel() {
            return Number(this.product.kg_per_barrel) || 0;
        },
        barrelLabel() {
            return this.product.barrel_label || this.__("custom.barrels");
        },
        barrelErrorText() {
            return this.__("custom.barrel_whole_stock_error", { label: this.barrelLabel });
        },
    },
    methods: {
        clampQty(item, key) {
            if (item[key] < 0) item[key] = 0;
        },

        onKgInput(item) {
            if (item.kg_quantity < 0) item.kg_quantity = 0;
        },

        // Convert KG input to barrel count
        barrelQtyFromKg(item) {
            if (!this.kgPerBarrel) return 0;
            const kg = Number(item.kg_quantity) || 0;
            return kg / this.kgPerBarrel;
        },

        // Check if KG value does NOT produce a whole barrel count
        isFractionalBarrelFromKg(item) {
            if (!this.isWeightBased) return false;
            if (!this.kgPerBarrel) return false;
            const kg = Number(item.kg_quantity);
            if (!kg) return false;
            const barrels = kg / this.kgPerBarrel;
            return Math.abs(barrels - Math.round(barrels)) > 1e-6;
        },

        // Read-only per-kg equivalent of a per-barrel price (display hint only).
        perKgText(value) {
            if (!this.isWeightBased || !this.kgPerBarrel) return "";
            const perKg = Number(value) / this.kgPerBarrel;
            return (this.currency_symbol || "") + perKg.toFixed(2) + " /kg";
        },

        // Block native form submit when any row has a fractional barrel quantity.
        validateBarrels(e) {
            if (!this.isWeightBased) return;

            const invalid = this.items.some((item) => this.isFractionalBarrelFromKg(item));
            if (invalid) {
                e.preventDefault();
                e.stopPropagation();
                this.$swal.fire({
                    icon: "error",
                    text: "KG পরিমাণ " + this.kgPerBarrel + " kg এর গুণিতক হতে হবে যাতে পূর্ণ " + this.barrelLabel + " হয়।",
                });
            }
        },
        checkDuplicate(index, e) {
            let id = e.target.value;
            let is_duplicate = this.items.filter((item) => item.warehouse == id);
            if (is_duplicate.length > 1) {
                this.$swal.fire({
                    icon: "error",
                    text: "Duplicate warehouse selected!",
                });
                this.items.splice(index, 1);
            }
        },
        // Convert per-KG price to per-barrel price
        barrelPriceFromKg(kgPrice) {
            if (!this.kgPerBarrel) return 0;
            const val = Number(kgPrice) || 0;
            return parseFloat((val * this.kgPerBarrel).toFixed(2));
        },

        addItem() {
            // For new rows on weight-based products, use product's
            // buying_price and customer_buying_price as per-KG defaults
            this.items.push({
                id:'',
                warehouse: "",
                stock: 0,
                quantity: 0,
                kg_quantity: 0,
                customer_buying_price: this.product.customer_buying_price,
                price: this.product.price,
                kg_price: this.isWeightBased ? Number(this.product.buying_price) || 0 : 0,
                kg_customer_buying_price: this.isWeightBased ? Number(this.product.customer_buying_price) || 0 : 0,
                adjust_type: '',
                manage_stock: false,
                backorders_allowed: false,
                note: '',
            });
        },
        deleteItem(index) {
            this.items.splice(index, 1);
        },
    },
};
</script>
