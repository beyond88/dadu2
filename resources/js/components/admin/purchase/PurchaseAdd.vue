<template>
  <div class="row">

    <!-- Product search -->
    <div class="col-sm-12 mb-5">
      <label>{{ __("custom.search_product") }}</label>
      <input
        @keyup="searchSelectSku($event)"
        class="form-control"
        placeholder="Search product by Name or SKU"
        type="text"
        v-model="query"
      />
      <div>
        <ul class="list-group">
          <li
            @click="selectProduct(item)"
            class="list-group-item set_poniter"
            v-for="(item, index) in searched_product"
            :key="index"
          >
            <a v-if="item.product.is_variant == 1" href="javascript:void(0)">
              ({{ item.variation.sku }}) {{ item.product.name }}
              <span v-if="item.variation"> ({{ __("custom.variant") }} : {{ item.variation.name }})</span>
            </a>
            <a v-else href="javascript:void(0)">
              ({{ item.product.sku }}) {{ item.product.name }}
            </a>
          </li>
        </ul>
      </div>
    </div>

    <div class="col-12">
      <label>{{ __("custom.product") }} <span class="error">*</span></label>
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>#</th>
              <th>Code</th>
              <th>Item</th>
              <th>{{ __("custom.quantity") }}</th>
              <th>{{ __("custom.buying_price") }}</th>
              <th>{{ __("custom.note") }}</th>
              <th>{{ __("custom.sub_total") }}</th>
              <th>
                <a @click="deleteAllItem" href="#" class="text-danger"><i class="fa fa-trash"></i></a>
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in formData.items" :key="index">
              <td>{{ index + 1 }}</td>
              <td>
                <span v-if="item.is_blank">
                  <input type="text" class="form-control" v-model="item.sku" />
                </span>
                <span v-else>{{ item.sku }}</span>

                <input type="hidden" name="product_stock_id[]" :value="item.id" />
                <input type="hidden" name="product_id[]" :value="item.product_id" />
                <input type="hidden" name="variation_id[]" :value="item.variation_id" />
              </td>
              <td>
                <span v-if="item.is_blank">
                  <input type="text" class="form-control" v-model="item.name" />
                </span>
                <span v-else>
                  <span v-if="item.is_variant == 1">
                    {{ item.name }} <span v-if="item.is_variant"> ({{ __("custom.variant") }} : {{ item.variation_name }})</span>
                  </span>
                  <span v-else>{{ item.name }}</span>
                </span>
              </td>
              <td>
                <template v-if="item.is_weight_based">
                  <input class="form-control" type="number" v-model="item.kg" min="0" step="any"
                         :class="{ 'is-invalid': isFractionalBarrel(item) }"
                         :placeholder="__('custom.weight_kg')" />
                  <small class="text-muted">= {{ $formatNumber(barrels(item)) }} {{ item.barrel_label || __('custom.barrels') }}</small>
                  <small v-if="isFractionalBarrel(item)" class="d-block error">{{ barrelErrorText(item) }}</small>
                  <input type="hidden" name="quantity[]" :value="barrels(item)" />
                </template>
                <template v-else>
                  <input class="form-control" type="number" v-model="item.quantity" min="1" name="quantity[]" />
                </template>
              </td>
              <td>
                <template v-if="item.is_weight_based">
                  <input class="form-control" type="text" v-model="item.price" min="0" :placeholder="item.buying_price" />
                  <small class="text-muted">{{ __('custom.per_kg') }}</small>
                  <input type="hidden" name="price[]" :value="perBarrelPrice(item)" />
                </template>
                <template v-else>
                  <input class="form-control" type="text" v-model="item.price" min="1" name="price[]" :placeholder="item.buying_price" />
                </template>
              </td>
              <td>
                <input type="text" class="form-control" name="product_note[]" />
              </td>
              <td>
                <input readonly class="form-control" type="number" step="any" :value="calculateSubTotal(index)" name="sub_total[]" />
              </td>
              <td>
                <a @click="deleteItem(index)" href="#" class="text-danger"><i class="fa fa-trash"></i></a>
              </td>
            </tr>

            <tr>
              <td colspan="5"></td>
              <td>{{ __("custom.total") }}</td>
              <td>
                <b>{{ currency_symbol }}{{ $formatNumber(calculateTotal) }}</b>
                <input type="hidden" name="total" :value="calculateTotal" />
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import { trans } from "../../../helpers";

export default {
  name: "PurchaseAdd",
  props: {
    currency_symbol: String,
  },
  data() {
    return {
      trans,
      query: "",
      barcodeScanner: false,
      searched_product: [],
      formData: {
        items: [],
        status: "",
        notes: "",
        tax: 0,
        discount: 0,
        discount_type: "percentage",
        total: "",
      },
    };
  },
  mounted() {
    // The Submit button lives in the wrapping Blade <form>, so intercept its
    // native submit to block quantities that produce a fractional barrel.
    this.$nextTick(() => {
      this.purchaseForm = this.$el.closest("form");
      if (this.purchaseForm) {
        this.purchaseForm.addEventListener("submit", this.validateBarrels);
      }
    });
  },
  beforeDestroy() {
    if (this.purchaseForm) {
      this.purchaseForm.removeEventListener("submit", this.validateBarrels);
    }
  },
  methods: {
    calculateSubTotal(index) {
      let item = this.formData.items[index];
      let total = item.is_weight_based
        ? Number(item.kg) * Number(item.price)        // price is per kg
        : Number(item.quantity) * Number(item.price);
      return Number(total).toFixed(2);
    },

    // Barrels (pieces) for a weight-based item, fractional allowed.
    barrels(item) {
      let kgPerBarrel = Number(item.kg_per_barrel);
      if (!kgPerBarrel) return 0;
      return Number((Number(item.kg) / kgPerBarrel).toFixed(4));
    },

    // True when the entered kg does NOT divide evenly into whole barrels.
    isFractionalBarrel(item) {
      if (!item.is_weight_based) return false;
      let kgPerBarrel = Number(item.kg_per_barrel);
      let kg = Number(item.kg);
      if (!kgPerBarrel || !kg) return false;
      let b = kg / kgPerBarrel;
      return Math.abs(b - Math.round(b)) > 1e-6;
    },

    // Hint message suggesting the nearest valid kg values.
    barrelErrorText(item) {
      let kgPerBarrel = Number(item.kg_per_barrel);
      let kg = Number(item.kg);
      let b = kg / kgPerBarrel;
      let lower = Math.max(1, Math.floor(b)) * kgPerBarrel;
      let upper = Math.ceil(b) * kgPerBarrel;
      return this.__("custom.barrel_whole_qty_error", {
        label: item.barrel_label || this.__("custom.barrels"),
        unit: kgPerBarrel,
        kg: kg,
        barrels: Number(b.toFixed(4)),
        lower: lower,
        upper: upper,
      });
    },

    // Block native form submit when any row has a fractional barrel.
    validateBarrels(e) {
      let invalid = this.formData.items.some((item) => this.isFractionalBarrel(item));
      if (invalid) {
        e.preventDefault();
        e.stopPropagation();
        this.$swal.fire({
          icon: "error",
          text: this.__("custom.barrel_whole_qty_submit_error", {
            label: this.__("custom.barrels"),
          }),
        });
      }
    },

    // The per-kg price entered, converted to the per-barrel price that is stored.
    perBarrelPrice(item) {
      return Number((Number(item.price) * Number(item.kg_per_barrel)).toFixed(4));
    },

    searchSelectSku(e) {
      let query = e.target.value;
      if (query.length > 1) {
        // Scope the suggestions to the warehouse picked on the form — the purchase is
        // received into that warehouse, so stock rows of other warehouses only confuse.
        const warehouse_id = document.getElementById("warehouse")?.value || null;
        axios
          .get(`/admin/api/product-stock/search/name-sku/${encodeURIComponent(query)}`, {
            params: warehouse_id ? { warehouse_id } : {},
          })
          .then((res) => {
            this.searched_product = res.data;
          })
          .catch((err) => {});
      } else {
        this.searched_product = [];
      }
    },

    selectProduct(product_stock) {
      let already_added = this.formData.items.find((i) => i.id == product_stock.id);

      if (already_added) {
        this.$swal.fire({
          icon: "error",
          text: "Product already added.",
        });
      } else {
        if (product_stock.product.is_variant == 1 && product_stock.variation) {
          this.formData.items.push({
            id: product_stock.id,
            product_id: product_stock.product_id,
            variation_id: product_stock.variation_id,
            sku: product_stock.variation.sku,
            name: product_stock.product.name,
            variation_name: product_stock.variation.name,
            price: product_stock.product.buying_price,
            buying_price: product_stock.product.buying_price,
            quantity: 1,
            kg: "",
            is_weight_based: product_stock.product.is_weight_based == 1,
            kg_per_barrel: Number(product_stock.product.kg_per_barrel) || 0,
            barrel_label: product_stock.product.barrel_label || this.__('custom.barrels'),
            is_variant: 1,
            is_blank: false,
          });
        } else {
          this.formData.items.push({
            id: product_stock.id,
            product_id: product_stock.product.id,
            variation_id: null,
            sku: product_stock.product.sku,
            name: product_stock.product.name,
            variation: null,
            price: product_stock.product.buying_price,
            buying_price: product_stock.product.buying_price,
            quantity: 1,
            kg: "",
            is_weight_based: product_stock.product.is_weight_based == 1,
            kg_per_barrel: Number(product_stock.product.kg_per_barrel) || 0,
            barrel_label: product_stock.product.barrel_label || this.__('custom.barrels'),
            is_variant: 0,
            is_blank: false,
          });
        }
      }

      this.searched_product = [];
      this.query = "";
    },

    deleteItem(index) {
      this.formData.items.splice(index, 1);
    },

    deleteAllItem() {
      this.formData.items = [];
    },
  },
  computed: {
    calculateTotal() {
      let total = 0;
      this.formData.items.map((item) => {
        total += item.is_weight_based
          ? Number(item.kg) * Number(item.price)
          : Number(item.price) * Number(item.quantity);
      });
      return Number(total).toFixed(2);
    },
  },
};
</script>

<style scoped>
.set_poniter {
  cursor: pointer;
}
</style>
