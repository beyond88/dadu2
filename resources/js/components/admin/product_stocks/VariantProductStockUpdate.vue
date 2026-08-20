<template>
  <div class="col-sm-12">
    <div class="table-responsive">
    <table class="table table-bordered">
      <tr>
        <th colspan="6">
          <img class="img-100-60" :src="product.thumb_url" :alt="product.name"/>
          <span class="ml-4">
            <small>{{ __("custom.product_name") }}: </small>
            {{ product.name }}
          </span>
        </th>
        <th colspan="2">
          {{ __("custom.alert_quantity") }}
          <input
            type="number"
            class="form-control"
            :name="'alert_quantity'"
            v-model="stock_alert_quantity"
            :placeholder='__("custom.alert_quantity")'
          >
        </th>
      </tr>

      <tr style="white-space: nowrap;">
        <th>{{ __("custom.warehouse_name") }}</th>
        <th>{{ __("custom.current_stock") }} <small v-if="product.weight_unit">({{ product.weight_unit.name }})</small></th>
        <th>{{ __("custom.qty") }} <small v-if="product.weight_unit">({{ product.weight_unit.name }})</small></th>
        <th style="min-width:120px;">{{ __("custom.price") }}</th>
        <th>{{ __("custom.customer_buying_price") }}</th>
        <th>{{ __("custom.adjust_type") }} <small>({{ __("custom.stock")}})</small></th>
        <th style="min-width:180px;">Note</th>
        <th></th>
      </tr>

      <tr v-for="(item, index) in items" :key="index">
        <!-- Warehouse Selector -->
      <td>
          <select
            @change="checkDuplicate(index, $event)"
            class="form-control"
            v-model="item.warehouse"
            :name="'warehouse_stock[' + index + '][warehouse]'"
          >
            <option value="" selected>{{ __("custom.select_warehouse") }}</option>
            <option v-for="(w, wIndex) in warehouses" :key="wIndex" :value="w.id">{{ w.name }}</option>
          </select>
        </td>

        <!-- Stock per variation -->
        <td>
          <table class="table table-bordered">
            <tr v-for="variation in product.variations" :key="variation.id">
              <td>
                {{ variation.name }}
                <input
                  type="number"
                  class="form-control"
                  v-model.number="item.stock[variation.id]"
                  :name="`warehouse_stock[${index}][stock][${variation.id}]`"
                  readonly
                />
                 <!-- Hidden Woo fields -->
                <input
                  type="hidden"
                  :name="`warehouse_stock[${index}][manage_stock][${variation.id}]`"
                  :value="item.manage_stock[variation.id]"
                />
                <input
                  type="hidden"
                  :name="`warehouse_stock[${index}][backorders_allowed][${variation.id}]`"
                  :value="item.backorders_allowed[variation.id]"
                />
              </td>
            </tr>
          </table>
        </td>

        <!-- Quantity per variation -->
        <td>
          <table class="table table-bordered">
            <tr v-for="variation in product.variations" :key="variation.id">
              <td>
                {{ variation.name }}
                <input
                  type="number"
                  min="0"
                  class="form-control"
                  v-model.number="item.quantity[variation.id]"
                  :name="`warehouse_stock[${index}][quantity][${variation.id}]`"
                  @input="clampVariantQty(item, variation.id)"
                />
              </td>
            </tr>
          </table>
        </td>

        <!-- Price per variation -->
        <td>
          <table class="table table-bordered">
            <tr v-for="variation in product.variations" :key="variation.id">
              <td>
                {{ variation.name }}
                <input
                  type="number"
                  class="form-control"
                  v-model.number="item.price[variation.id]"
                  :name="`warehouse_stock[${index}][price][${variation.id}]`"
                />
              </td>
            </tr>
          </table>
        </td>

        <!-- Customer Buying Price per variation -->
        <td>
          <table class="table table-bordered">
            <tr v-for="variation in product.variations" :key="variation.id">
              <td>
                {{ variation.name }}
                <input
                  type="number"
                  class="form-control"
                  v-model.number="item.customer_buying_price[variation.id]"
                  :name="`warehouse_stock[${index}][customer_buying_price][${variation.id}]`"
                />
              </td>
            </tr>
          </table>
        </td>


        <!-- Adjust type -->
        <!-- Adjust type -->
            <td>
            <select
                class="form-control"
                v-model="item.adjust_type"
                :name="'warehouse_stock[' + index + '][adjust_type]'"
            >
                <option value="" selected>{{ __("custom.select_adjust_type") }}</option>
                <option
                v-for="(type, tIndex) in (old_stocks.length > 0 ? adjust_type : ['Add'])"
                :key="tIndex"
                :value="type"
                >
                {{ type }}
                </option>
            </select>
            </td>



        <!-- Note -->
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

        <!-- Delete button -->
        <td>
          <button
            v-if="item.id"
            @click="deleteItem(index)"
            type="button"
            disabled
            class="btn btn-sm btn-outline-danger"
          >
            <i class="fa fa-trash"></i>
          </button>
          <button
            v-else
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
            <button
              @click="addItem"
              type="button"
              class="btn btn-sm btn-info mb-4 float-right"
              :title="__('custom.add_warehouse')"
            >
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
  props: ["product", "warehouses", "old_stocks"],
  data() {
    return {
      items: [],
      adjust_type: ['Add', 'Subtract'],
      stock_alert_quantity: 0
    };
  },
  mounted() {
    this.stock_alert_quantity = this.product.stock_alert_quantity;

    if (this.old_stocks.length > 0) {
      const warehouseMap = {};

      this.old_stocks.forEach((stock) => {
        if (!warehouseMap[stock.warehouse_id]) {
          warehouseMap[stock.warehouse_id] = {
            id: stock.id || "",
            warehouse: stock.warehouse_id,
            adjust_type: '',
            stock: {},
            quantity: {},
            price: {},
            customer_buying_price: {},
            manage_stock: {},
            backorders_allowed: {},
            note: '',
          };
        }
        warehouseMap[stock.warehouse_id].stock[stock.variation_id] = stock.quantity;
        warehouseMap[stock.warehouse_id].quantity[stock.variation_id] = 0;
        warehouseMap[stock.warehouse_id].price[stock.variation_id] = stock.variation.price;
        warehouseMap[stock.warehouse_id].customer_buying_price[stock.variation_id] = stock.variation.customer_buying_price;
        warehouseMap[stock.warehouse_id].manage_stock[stock.variation_id] = stock.manage_stock ?? false;
        warehouseMap[stock.warehouse_id].backorders_allowed[stock.variation_id] = stock.backorders_allowed ?? false;
      });

      this.items = Object.values(warehouseMap);
    } else {
      this.addItem();
    }
  },
  methods: {
    clampVariantQty(item, varId) {
      if (item.quantity[varId] < 0) item.quantity[varId] = 0;
    },
    addItem() {
      const stockObj = { stock: {}, quantity: {}, price: {}, customer_buying_price: {},manage_stock:{},backorders_allowed:{} };
      this.product.variations.forEach(v => {
        stockObj.stock[v.id] = 0;
        stockObj.quantity[v.id] = 0;
        stockObj.price[v.id] = v.price;
        stockObj.customer_buying_price[v.id] = v.customer_buying_price;
        stockObj.manage_stock[v.id] = false;
        stockObj.backorders_allowed[v.id] = false;
      });

      this.items.push({
        id: "",
        warehouse: "",
        adjust_type: 'Add',
        note: '',
        ...stockObj
      });
    },
    deleteItem(index) {
      this.items.splice(index, 1);
    },
    checkDuplicate(index, e) {
      const id = e.target.value;
      const duplicates = this.items.filter(item => item.warehouse == id);
      if (duplicates.length > 1) {
        this.$swal.fire({
          icon: "error",
          text: "Duplicate warehouse selected!",
        });
        this.items.splice(index, 1);
      }
    }
  }
};
</script>
