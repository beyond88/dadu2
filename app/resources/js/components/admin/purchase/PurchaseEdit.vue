<template>
  <div class="row">
    <div class="col-sm-12 mb-5">
      <label>{{ __("custom.search_product") }}</label>
      <input
        @keyup="searchSelectSku($event)"
        class="form-control"
        :placeholder="__('custom.search_product_by_name_sku')"
        type="text"
        v-model="query"
      />
      <div>
        <ul class="list-group">
          <li
            @click="selectProduct(item.id)"
            class="list-group-item set_poniter"
            v-for="(item, index) in searched_product"
            :key="index"
          >
            <a v-if="item.product.is_variant == 1" href="javascript:void(0)">
              ({{ item.variation.sku }}) {{ item.product.name }}
              ({{ __("custom.variant") }}: {{ item.variation.name }})
            </a>
            <a v-else href="javascript:void(0)">
              ({{ item.product.sku }}) {{ item.product.name }}
            </a>
          </li>
        </ul>
      </div>
    </div>

    <div class="col-12">
      <label>{{ __("custom.product") }}</label>
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>#</th>
              <th>{{ __("custom.sku") }}</th>
              <th>{{ __("custom.name") }}</th>
              <th>{{ __("custom.quantity") }}</th>
              <th>Buying Price</th>
              <th>{{ __("custom.note") }}</th>
              <th>{{ __("custom.sub_total") }}</th>
              <th>
                <a @click="deleteAllItem" href="#" class="text-danger">
                  <i class="fa fa-trash"></i>
                </a>
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in formData.items" :key="index">
              <td>{{ index + 1 }}</td>
              <td>
                {{ item.sku }}
                <input type="hidden" name="purchase_item_id[]" :value="item.purchase_item_id" />
                <input type="hidden" name="product_id[]" :value="item.product_id" />
                <input type="hidden" name="product_stock_id[]" :value="item.id" />
                <input type="hidden" name="variation_id[]" :value="item.variation_id" />
              </td>
              <td>
                <span v-if="item.is_variant == 1">
                  {{ item.name }} ({{ __("custom.variant") }}: {{ item.variation_name }})
                </span>
                <span v-else>{{ item.name }}</span>
              </td>
              <td>
                <input
                  class="form-control"
                  type="number"
                  v-model="item.quantity"
                  min="1"
                  name="quantity[]"
                />
              </td>
              <td>
                <input class="form-control" type="text" v-model="item.price" name="price[]" />
              </td>
              <td>
                <input type="text" class="form-control" name="product_note[]" :value="item.note" />
              </td>
              <td>
                <input readonly class="form-control" type="number" :value="calculateSubTotal(index)" name="sub_total[]" />
              </td>
              <td>
                <a @click="deleteItem(index, item.purchase_item_id)" href="#" class="text-danger">
                  <i class="fa fa-trash"></i>
                </a>
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
export default {
  props: ["products", "currency_symbol"],
  data() {
    return {
      query: "",
      searched_product: [],
      formData: {
        items: [],
        status: "",
        notes: "",
        discount: 0,
        discount_type: "percentage",
        total: "",
      },
    };
  },
  mounted() {
    this.products.forEach((item) => {
      const ps = item.product_stock; // related product_stock
      this.formData.items.push({
        purchase_item_id: item.id,
        id: item.product_stock_id,
        product_id: item.product.id,
        variation_id: ps?.variation_id || null,
        sku: ps?.variation_id ? ps.variation.sku : ps.product.sku, // SKU from variation if exists
        name: ps.product.name,
        variation_name: ps?.variation_id ? ps.variation.name : null,
        quantity: item.quantity,
        price: ps?.variation_id ? ps.variation.price : ps.price, // price from variation if variant
        is_variant: ps.product.is_variant,
        note: item.note,
        is_blank: false,
      });
    });
  },
  methods: {
    calculateSubTotal(index) {
      let item = this.formData.items[index];
      let total = Number(item.quantity) * Number(item.price);
      return Number(total).toFixed(2);
    },
    searchSelectSku(e) {
      const query = e.target.value;
      if (query.length > 1) {
        axios
          .get(`/admin/api/product-stock/search/name-sku/${query}`)
          .then((res) => {
            this.searched_product = res.data;
          });
      } else {
        this.searched_product = [];
      }
    },
    selectProduct(id) {
      const product_stock = this.searched_product.find((item) => item.id == id);
      const already_added = this.formData.items.find((i) => i.id == product_stock.id);

      if (already_added) {
        this.$swal.fire({ icon: "error", text: "Product already added." });
        return;
      }

      if (product_stock.product.is_variant == 1 && product_stock.variation) {
        this.formData.items.push({
          id: product_stock.id,
          product_id: product_stock.product.id,
          variation_id: product_stock.variation.id,
          sku: product_stock.variation.sku,
          name: product_stock.product.name,
          variation_name: product_stock.variation.name,
          price: product_stock.variation.price,
          quantity: 1,
          is_variant: 1,
          purchase_item_id: null,
          is_blank: false,
        });
      } else {
        this.formData.items.push({
          id: product_stock.id,
          product_id: product_stock.product.id,
          variation_id: null,
          sku: product_stock.product.sku,
          name: product_stock.product.name,
          variation_name: null,
          price: product_stock.product.price,
          quantity: 1,
          is_variant: 0,
          purchase_item_id: null,
          is_blank: false,
        });
      }

      this.searched_product = [];
      this.query = "";
    },
    deleteItem(index, purchase_item_id) {
      if (purchase_item_id) {
        axios
          .get(`/admin/api/purchase_item/delete/${purchase_item_id}`)
          .then(() => this.formData.items.splice(index, 1))
          .catch(() => {});
      } else {
        this.formData.items.splice(index, 1);
      }
    },
    deleteAllItem() {
      this.formData.items = [];
    },
  },
  computed: {
    calculateTotal() {
      let total = 0;
      this.formData.items.forEach((item) => {
        total += Number(item.price) * Number(item.quantity);
      });
      return Number(total).toFixed(2);
    },
  },
};
</script>


<style scoped>
.set_poniter { cursor: pointer; }
</style>
