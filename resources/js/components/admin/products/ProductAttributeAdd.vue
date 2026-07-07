<template>
  <div>
    <!-- General Validation Errors -->


    <!-- Attribute Sets -->
    <div class="mb-3">
      <label><strong>Attribute Sets</strong></label>
      <multiselect
        v-model="selectedAttributes"
        :options="attributes"
        :multiple="true"
        label="name"
        track-by="id"
        placeholder="Select attributes"
        @input="loadAttributeItems"
      />
      <small class="text-info">
        N.B: Select Attribute sets of this product to add attribute values
      </small>
    </div>

    <!-- Attribute Values -->
    <div v-for="attr in selectedAttributes" :key="attr.id" class="mb-3">
      <label><strong>{{ attr.name }}</strong></label>
      <multiselect
        v-model="selectedAttributeValues[attr.id]"
        :options="attributeItems[attr.id] || []"
        :multiple="true"
        label="name"
        track-by="id"
        placeholder="Select values"
        @input="generateVariants"
      />
    </div>

    <!-- Variants Table -->
    <div class="table-responsive">
      <table class="table table-bordered mt-3" v-if="variants.length">
      <thead>
      <tr>
          <th style="width: 10%">Variant</th>
          <th style="width: 10%">SKU <span class="error">*</span></th>
          <th style="width: 15%">Price <span class="error">*</span></th>
          <th style="width: 10%">Customer Buying Price</th>
          <th style="width: 8%">Weight</th>
          <th style="width: 12%">Dimensions (L × W × D)</th>
          <th style="width: 5%">Action</th>
      </tr>
      </thead>


        <tbody>
          <tr v-for="(variant, index) in variants" :key="index">
            <!-- Variant Name -->
            <td>
              {{ variant.name }}
              <input
                type="hidden"
                :name="`variants[${index}][name]`"
                :value="variant.name"
              />
                <input
                  v-for="id in variant.attribute_items"
                  type="hidden"
                  :key="id"
                  :name="`variants[${index}][attribute_items][]`"
                  :value="id"
              />
            </td>

            <!-- SKU -->
            <td>
              <input
                type="text"
                v-model="variant.sku"
                class="form-control"
                :name="`variants[${index}][sku]`"
              />
              <small class="text-danger" v-if="variant.errors.sku">
                {{ variant.errors.sku[0] }}
              </small>
            </td>

            <!-- Price -->
            <td>
              <input
                type="number"
                v-model="variant.price"
                class="form-control"
                :name="`variants[${index}][price]`"
              />
              <small class="text-danger" v-if="variant.errors.price">
                {{ variant.errors.price[0] }}
              </small>
            </td>

            <!-- Customer Buying Price -->
            <td>
              <input
                  type="number"
                  v-model="variant.customer_buying_price"
                  class="form-control form-control-sm"
                  :name="`variants[${index}][customer_buying_price]`"
              />
           </td>


            <!-- Weight -->
                  <td>
              <input
                  type="number"
                  v-model="variant.weight"
                  class="form-control form-control-sm"
                  :name="`variants[${index}][weight]`"
              />
              </td>


            <!-- Dimensions -->
            <td>
              <div class="d-flex gap-1">
                  <input
                  type="number"
                  v-model="variant.dimension_l"
                  placeholder="L"
                  class="form-control form-control-sm"
                  :name="`variants[${index}][dimension_l]`"
                  />
                  <input
                  type="number"
                  v-model="variant.dimension_w"
                  placeholder="W"
                  class="form-control form-control-sm"
                  :name="`variants[${index}][dimension_w]`"
                  />
                  <input
                  type="number"
                  v-model="variant.dimension_d"
                  placeholder="D"
                  class="form-control form-control-sm"
                  :name="`variants[${index}][dimension_d]`"
                  />
              </div>
              </td>


            <!-- Delete -->
            <td>
              <button
                class="btn btn-sm btn-danger"
                @click.prevent="removeVariant(index)"
              >
                <i class="fa fa-trash"></i>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import Multiselect from "vue-multiselect";
import axios from "axios";

export default {
  components: { Multiselect },
 props: {
  attributes: Array,
  productId: Number,
  oldVariants: {
    type: Array,
    default: () => []
  },
  validationErrors: {
    type: Object,  // <-- change this from Array to Object
    default: () => ({})
  }
},

  data() {
    return {
      selectedAttributes: [],
      attributeItems: {},
      selectedAttributeValues: {},
      variants: [],

    };
  },
mounted() {
  console.log('i am attribute add');
  // Separate general errors from variant errors


// Map old variants with errors
if (this.oldVariants.length) {
  this.variants = this.oldVariants.map((v, index) => {
    const errors = {};
    Object.keys(this.validationErrors).forEach(key => {
      const match = key.match(/^variants\.(\d+)\.(.+)$/);
      if (match && Number(match[1]) === index) {
        errors[match[2]] = this.validationErrors[key];
      }
    });
    return { ...v, errors };
  });


  // Populate selectedAttributeValues for multiselect
  this.oldVariants.forEach(v => {
    (v.attributes || []).forEach(attr => {
      if (!this.selectedAttributeValues[attr.id]) {
        this.$set(this.selectedAttributeValues, attr.id, []);
      }
      this.selectedAttributeValues[attr.id].push(attr);
    });
  });

  // Set selectedAttributes
  this.selectedAttributes = this.attributes.filter(attr =>
    Object.keys(this.selectedAttributeValues).includes(attr.id.toString())
  );

  this.loadAttributeItems();
}

},
  methods: {
    async loadAttributeItems() {
      for (let attr of this.selectedAttributes) {
        if (!this.attributeItems[attr.id]) {
          try {
            let res = await axios.get(`/admin/api/attribute-items/${attr.id}`);
            this.$set(this.attributeItems, attr.id, res.data);
          } catch (err) {
            console.error(err);
          }
        }
      }
    },
    generateVariants() {
        const values = Object.values(this.selectedAttributeValues).filter(
            v => v && v.length
        );

        if (!values.length) {
            this.variants = [];
            return;
        }

        const combine = arr =>
            arr.length === 1
            ? arr[0].map(v => [v])
            : arr.reduce((a, b) =>
                a.flatMap(d => b.map(e => [].concat(d, e)))
                );

        const combos = combine(values);

        this.variants = combos.map((combo, idx) => {
            const variantName = combo.map(val => val.name).join("-");

            // Collect attribute item IDs for backend
            const attributeItemsIds = combo.map(val => val.id);

            return {
            name: variantName,
            sku: `${variantName}-${this.productId}`,
            price: "",
            customer_buying_price: "",
            weight: "",
            dimension_l: "",
            dimension_w: "",
            dimension_d: "",
            attribute_items: attributeItemsIds, // ✅ send IDs
            errors: {}
            };
        });
        },

    removeVariant(index) {
      this.variants.splice(index, 1);
    }
  }
};
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
