<template>
  <div>
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
    <table class="table table-bordered mt-3" v-if="variantList.length">
      <thead>
        <tr>
          <th style="width: 10%">Variant</th>
          <th style="width: 10%">SKU <span class="error">*</span></th>
          <th style="width: 30%">Barcode <span class="error">*</span></th>
          <th style="width: 15%">Selling Price <span class="error">*</span></th>
          <th style="width: 8%">Weight</th>
          <th style="width: 12%">Dimensions (L × W × D)</th>
          <th style="width: 5%">Action</th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="(variant, index) in variantList" :key="index">
          <!-- Variant Name -->
          <td>
            {{ variant.name }}
            <input type="hidden" :name="`variants[${index}][name]`" :value="variant.name" />
            <input
              v-for="id in variant.attribute_items"
              type="hidden"
              :key="id"
              :name="`variants[${index}][attribute_items][]`"
              :value="id"
            />
            <!-- include DB id when editing -->
            <input v-if="variant.id" type="hidden" :name="`variants[${index}][id]`" :value="variant.id" />
          </td>

          <!-- SKU -->
          <td>
            <input type="text" v-model="variant.sku" class="form-control" :name="`variants[${index}][sku]`" />
            <small class="text-danger" v-if="variant.errors && variant.errors.sku">{{ variant.errors.sku[0] }}</small>
          </td>

          <!-- Barcode -->
          <td>
            <div class="d-flex align-items-center">
              <input
                type="text"
                v-model="variant.barcode"
                class="form-control me-2"
                style="flex: 2;"
                :name="`variants[${index}][barcode]`"
                @input="generateBarcodeImage(index)"
              />
              <small class="text-danger" v-if="variant.errors && variant.errors.barcode">{{ variant.errors.barcode[0] }}</small>

              <div style="flex: 1; text-align: right;">
                <img class="img-fluid barcode-image barcode-max-height" :id="`barcode-img-${index}`" alt="barcode" />
                <input
                  type="hidden"
                  :id="`barcode-value-${index}`"
                  :name="`variants[${index}][barcode_image]`"
                  v-model="variant.barcode_image"
                />
              </div>
            </div>
          </td>

          <!-- Price -->
          <td>
            <input type="number" v-model="variant.price" class="form-control" :name="`variants[${index}][price]`" />
            <small class="text-danger" v-if="variant.errors && variant.errors.price">{{ variant.errors.price[0] }}</small>
          </td>

          <!-- Weight -->
          <td>
            <input type="number" v-model="variant.weight" class="form-control form-control-sm" :name="`variants[${index}][weight]`" />
          </td>

          <!-- Dimensions -->
          <td>
            <div class="d-flex gap-1">
              <input type="number" v-model="variant.dimension_l" placeholder="L" class="form-control form-control-sm" :name="`variants[${index}][dimension_l]`" />
              <input type="number" v-model="variant.dimension_w" placeholder="W" class="form-control form-control-sm" :name="`variants[${index}][dimension_w]`" />
              <input type="number" v-model="variant.dimension_d" placeholder="D" class="form-control form-control-sm" :name="`variants[${index}][dimension_d]`" />
            </div>
          </td>

          <!-- Delete -->
          <td>
            <button class="btn btn-sm btn-danger" @click.prevent="removeVariant(index)"><i class="fa fa-trash"></i></button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import Multiselect from "vue-multiselect";
import axios from "axios";

export default {
  components: { Multiselect },
  props: {
    attributes: {
      type: Array,
      default: () => []
    },
    product: {
      type: Object,
      default: () => ({ id: null })
    },
    variants: {
      type: Array,
      default: () => []
    },
    validationErrors: {
      type: Object,
      default: () => ({})
    }
  },
  data() {
    return {
      selectedAttributes: [],
      attributeItems: {},
      selectedAttributeValues: {}, // { attributeId: [itemObj, ...] }
      variantList: [] // variants shown in table
    };
  },
  mounted() {
    if (this.variants.length) {
      // map server variants to UI shape
      this.variantList = this.variants.map((v, index) => {
        const errors = {};
        Object.keys(this.validationErrors).forEach(key => {
          const match = key.match(/^variants\.(\d+)\.(.+)$/);
          if (match && Number(match[1]) === index) {
            errors[match[2]] = this.validationErrors[key];
          }
        });

        return {
          id: v.id ?? null,
          name: v.name ?? "",
          sku: v.sku ?? "",
          barcode: v.barcode ?? "",
          barcode_image: v.barcode_image ?? "",
          price: v.price ?? "",
          customer_buying_price: v.customer_buying_price ?? "",
          weight: v.weight ?? "",
          dimension_l: v.dimension_l ?? "",
          dimension_w: v.dimension_w ?? "",
          dimension_d: v.dimension_d ?? "",
          // store IDs for form submission
          attribute_items: (v.attribute_items || []).map(ai => ai.id),
          // keep full objects for selecting in multiselect
          _attribute_items_full: (v.attribute_items || []),
          errors
        };
      });

      // Build selectedAttributeValues from all variants but avoid duplicates
      const attrMap = {}; // { attrId: { itemId: itemObj, ... }, ... }
      this.variants.forEach(v => {
        (v.attribute_items || []).forEach(ai => {
          const attrId = String(ai.attribute_id);
          if (!attrMap[attrId]) attrMap[attrId] = {};
          if (!attrMap[attrId][ai.id]) {
            attrMap[attrId][ai.id] = ai; // store item object
          }
        });
      });

      // Convert map -> selectedAttributeValues arrays
      Object.keys(attrMap).forEach(attrId => {
        const arr = Object.values(attrMap[attrId]);
        // use Vue.set to make it reactive
        this.$set(this.selectedAttributeValues, attrId, arr);
      });

      // Set selectedAttributes (attribute sets) based on keys present in selectedAttributeValues
      this.selectedAttributes = this.attributes.filter(attr =>
        Object.prototype.hasOwnProperty.call(this.selectedAttributeValues, String(attr.id))
      );

      // load the attribute items for selected attributes (so multiselect options exist)
      this.loadAttributeItems();

      // regenerate barcode images for preloaded variants
      this.$nextTick(() => this.variantList.forEach((v, i) => this.generateBarcodeImage(i)));
    }
  },
  methods: {
    async loadAttributeItems() {
      // load attribute items for each selected attribute if not already loaded
      for (let attr of this.selectedAttributes) {
        if (!this.attributeItems[attr.id]) {
          try {
            let res = await axios.get(`/admin/api/attribute-items/${attr.id}`);
            // res.data should be an array of { id, name, attribute_id, ... }
            this.$set(this.attributeItems, attr.id, res.data);

            // If we already have preselected items for this attribute (from variant), we need to
            // replace the objects with the ones from res.data so multiselect can match options correctly.
            const selected = this.selectedAttributeValues[String(attr.id)];
            if (selected && selected.length) {
              // map of loaded options by id for quick lookup
              const optById = {};
              res.data.forEach(o => (optById[o.id] = o));
              // replace each selected entry with the option object from res.data (if exists)
              const normalized = selected
                .map(s => (optById[s.id] ? optById[s.id] : s))
                // dedupe just in case
                .filter((v, i, a) => a.findIndex(x => x.id === v.id) === i);
              this.$set(this.selectedAttributeValues, String(attr.id), normalized);
            }
          } catch (err) {
            console.error(err);
          }
        } else {
          // attributeItems already loaded: ensure selected items reference the same objects (dedupe)
          const selected = this.selectedAttributeValues[String(attr.id)] || [];
          const optById = {};
          this.attributeItems[attr.id].forEach(o => (optById[o.id] = o));
          const normalized = selected.map(s => (optById[s.id] ? optById[s.id] : s))
            .filter((v, i, a) => a.findIndex(x => x.id === v.id) === i);
          this.$set(this.selectedAttributeValues, String(attr.id), normalized);
        }
      }
    },

    generateVariants() {
      // called when user changes attribute values (multiselect input)
      const values = Object.values(this.selectedAttributeValues).filter(v => v && v.length);
      if (!values.length) {
        this.variantList = [];
        return;
      }

      const combine = arr =>
        arr.length === 1
          ? arr[0].map(v => [v])
          : arr.reduce((a, b) => a.flatMap(d => b.map(e => [].concat(d, e))));

      const combos = combine(values);

      // build new variants from combos
      this.variantList = combos.map((combo, idx) => {
        const variantName = combo.map(val => val.name).join("-");
        const attributeItemsIds = combo.map(val => val.id);

        return {
          id: null, // new variant (no db id yet)
          name: variantName,
          sku: `${variantName}-${this.product.id}`,
          barcode: (Math.floor(Date.now() / 1000) + idx + 1)
            .toString()
            .slice(-10),
          price: "",
          customer_buying_price: "",
          weight: "",
          dimension_l: "",
          dimension_w: "",
          dimension_d: "",
          attribute_items: attributeItemsIds,
          errors: {}
        };
      });

      this.$nextTick(() => this.variantList.forEach((v, i) => this.generateBarcodeImage(i)));
    },

    generateBarcodeImage(index) {
      const variant = this.variantList[index];
      if (!variant || !variant.barcode) return;

      const imgId = `#barcode-img-${index}`;
      // JsBarcode can render to <svg> or <img> based on usage in your project — this assumes it
      // accepts a CSS selector id for an <img> or <svg> element on the page.
      // Keep your existing usage of JsBarcode.
      JsBarcode(imgId, variant.barcode, { displayValue: true });

      const imgEl = document.querySelector(imgId);
      if (imgEl) {
        // If JsBarcode renders as <svg>, you may need to convert to dataURL. Many implementations
        // place the svg inline and put a src on an <img> as base64. Keep your project-specific method.
        variant.barcode_image = imgEl.getAttribute("src") || imgEl.outerHTML;
      }
    },

    removeVariant(index) {
      // if you need to mark server-side deletion, push variant.id into a deleted array here
      this.variantList.splice(index, 1);
    }
  }
};
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
