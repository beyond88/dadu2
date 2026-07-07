"use strict";
(self["webpackChunk"] = self["webpackChunk"] || []).push([["resources_js_components_admin_product_stocks_NormalProductStockUpdate_vue"],{

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/admin/product_stocks/NormalProductStockUpdate.vue?vue&type=script&lang=js&":
/*!****************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/admin/product_stocks/NormalProductStockUpdate.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  props: ["product", "warehouses", "old_stocks", "currency_symbol"],
  data: function data() {
    return {
      items: [{
        id: '',
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
        note: ''
      }],
      adjust_type: ['Add', 'Subtract'],
      stock_alert_quantity: 0
    };
  },
  mounted: function mounted() {
    var _this = this;
    if (this.old_stocks.length > 0) {
      this.items = [];
      this.old_stocks.map(function (item) {
        var _item$manage_stock, _item$backorders_allo;
        // For weight-based products, stored prices are per-barrel.
        // Convert to per-KG for display.
        var kgFactor = _this.kgPerBarrel || 1;
        _this.items.push({
          id: item.id,
          warehouse: item.warehouse_id,
          stock: item.quantity,
          quantity: 0,
          kg_quantity: 0,
          adjust_type: '',
          customer_buying_price: item.customer_buying_price,
          price: item.price,
          kg_price: _this.isWeightBased ? parseFloat((Number(item.price) / kgFactor).toFixed(4)) : 0,
          kg_customer_buying_price: _this.isWeightBased ? parseFloat((Number(item.customer_buying_price) / kgFactor).toFixed(4)) : 0,
          manage_stock: (_item$manage_stock = item.manage_stock) !== null && _item$manage_stock !== void 0 ? _item$manage_stock : false,
          backorders_allowed: (_item$backorders_allo = item.backorders_allowed) !== null && _item$backorders_allo !== void 0 ? _item$backorders_allo : false,
          note: ''
        });
      });
    }
    this.stock_alert_quantity = this.product.stock_alert_quantity;

    // The Submit button lives in the wrapping Blade <form>, so intercept its
    // native submit to block fractional barrel quantities for weight-based products.
    this.$nextTick(function () {
      _this.stockForm = _this.$el.closest("form");
      if (_this.stockForm) {
        _this.stockForm.addEventListener("submit", _this.validateBarrels);
      }
    });
  },
  beforeDestroy: function beforeDestroy() {
    if (this.stockForm) {
      this.stockForm.removeEventListener("submit", this.validateBarrels);
    }
  },
  computed: {
    isWeightBased: function isWeightBased() {
      return this.product.is_weight_based == 1;
    },
    kgPerBarrel: function kgPerBarrel() {
      return Number(this.product.kg_per_barrel) || 0;
    },
    barrelLabel: function barrelLabel() {
      return this.product.barrel_label || this.__("custom.barrels");
    },
    barrelErrorText: function barrelErrorText() {
      return this.__("custom.barrel_whole_stock_error", {
        label: this.barrelLabel
      });
    }
  },
  methods: {
    clampQty: function clampQty(item, key) {
      if (item[key] < 0) item[key] = 0;
    },
    onKgInput: function onKgInput(item) {
      if (item.kg_quantity < 0) item.kg_quantity = 0;
    },
    // Convert KG input to barrel count
    barrelQtyFromKg: function barrelQtyFromKg(item) {
      if (!this.kgPerBarrel) return 0;
      var kg = Number(item.kg_quantity) || 0;
      return kg / this.kgPerBarrel;
    },
    // Check if KG value does NOT produce a whole barrel count
    isFractionalBarrelFromKg: function isFractionalBarrelFromKg(item) {
      if (!this.isWeightBased) return false;
      if (!this.kgPerBarrel) return false;
      var kg = Number(item.kg_quantity);
      if (!kg) return false;
      var barrels = kg / this.kgPerBarrel;
      return Math.abs(barrels - Math.round(barrels)) > 1e-6;
    },
    // Read-only per-kg equivalent of a per-barrel price (display hint only).
    perKgText: function perKgText(value) {
      if (!this.isWeightBased || !this.kgPerBarrel) return "";
      var perKg = Number(value) / this.kgPerBarrel;
      return (this.currency_symbol || "") + perKg.toFixed(2) + " /kg";
    },
    // Block native form submit when any row has a fractional barrel quantity.
    validateBarrels: function validateBarrels(e) {
      var _this2 = this;
      if (!this.isWeightBased) return;
      var invalid = this.items.some(function (item) {
        return _this2.isFractionalBarrelFromKg(item);
      });
      if (invalid) {
        e.preventDefault();
        e.stopPropagation();
        this.$swal.fire({
          icon: "error",
          text: "KG পরিমাণ " + this.kgPerBarrel + " kg এর গুণিতক হতে হবে যাতে পূর্ণ " + this.barrelLabel + " হয়।"
        });
      }
    },
    checkDuplicate: function checkDuplicate(index, e) {
      var id = e.target.value;
      var is_duplicate = this.items.filter(function (item) {
        return item.warehouse == id;
      });
      if (is_duplicate.length > 1) {
        this.$swal.fire({
          icon: "error",
          text: "Duplicate warehouse selected!"
        });
        this.items.splice(index, 1);
      }
    },
    // Convert per-KG price to per-barrel price
    barrelPriceFromKg: function barrelPriceFromKg(kgPrice) {
      if (!this.kgPerBarrel) return 0;
      var val = Number(kgPrice) || 0;
      return parseFloat((val * this.kgPerBarrel).toFixed(2));
    },
    addItem: function addItem() {
      // For new rows on weight-based products, use product's
      // buying_price and customer_buying_price as per-KG defaults
      this.items.push({
        id: '',
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
        note: ''
      });
    },
    deleteItem: function deleteItem(index) {
      this.items.splice(index, 1);
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/lib/loaders/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/admin/product_stocks/NormalProductStockUpdate.vue?vue&type=template&id=06a54144&":
/*!***************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/lib/loaders/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/admin/product_stocks/NormalProductStockUpdate.vue?vue&type=template&id=06a54144& ***!
  \***************************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render),
/* harmony export */   "staticRenderFns": () => (/* binding */ staticRenderFns)
/* harmony export */ });
var render = function render() {
  var _vm = this,
    _c = _vm._self._c;
  return _c("div", {
    staticClass: "col-sm-12"
  }, [_c("div", {
    staticClass: "table-responsive"
  }, [_c("table", {
    staticClass: "table table-bordered mb-4"
  }, [_c("tr", [_c("th", {
    attrs: {
      colspan: "6"
    }
  }, [_c("img", {
    staticClass: "img-100-60",
    attrs: {
      src: _vm.product.thumb_url,
      alt: _vm.product.name
    }
  }), _vm._v(" "), _c("span", {
    staticClass: "ml-4"
  }, [_c("small", [_vm._v(_vm._s(_vm.__("custom.product_name")) + ": ")]), _vm._v("\n                    " + _vm._s(_vm.product.name) + "\n                ")])]), _vm._v(" "), _c("th", {
    attrs: {
      colspan: "2"
    }
  }, [_vm._v("\n                " + _vm._s(_vm.__("custom.alert_quantity")) + "\n                "), _c("input", {
    directives: [{
      name: "model",
      rawName: "v-model",
      value: _vm.stock_alert_quantity,
      expression: "stock_alert_quantity"
    }],
    staticClass: "form-control",
    attrs: {
      type: "number",
      name: "alert_quantity",
      required: "",
      placeholder: _vm.__("custom.alert_quantity")
    },
    domProps: {
      value: _vm.stock_alert_quantity
    },
    on: {
      input: function input($event) {
        if ($event.target.composing) return;
        _vm.stock_alert_quantity = $event.target.value;
      }
    }
  })])]), _vm._v(" "), _c("tr", {
    staticStyle: {
      "white-space": "nowrap"
    }
  }, [_c("th", [_vm._v(_vm._s(_vm.__("custom.warehouse_name")))]), _vm._v(" "), _c("th", [_vm._v(_vm._s(_vm.__("custom.current_stock")) + " "), _vm.product.weight_unit ? _c("small", [_vm._v("(" + _vm._s(_vm.product.weight_unit.name) + ")")]) : _vm._e()]), _vm._v(" "), _c("th", [_vm._v("\n                " + _vm._s(_vm.__("custom.qty")) + "\n                "), _vm.isWeightBased ? _c("small", [_vm._v("(KG)")]) : _vm.product.weight_unit ? _c("small", [_vm._v("(" + _vm._s(_vm.product.weight_unit.name) + ")")]) : _vm._e()]), _vm._v(" "), _c("th", {
    staticStyle: {
      "min-width": "120px"
    }
  }, [_vm._v(_vm._s(_vm.__("custom.buying_price")) + " "), _vm.isWeightBased ? _c("small", [_vm._v("(per KG)")]) : _vm._e()]), _vm._v(" "), _c("th", [_vm._v(_vm._s(_vm.__("custom.selling_price")) + " "), _vm.isWeightBased ? _c("small", [_vm._v("(per KG)")]) : _vm._e()]), _vm._v(" "), _c("th", [_vm._v(_vm._s(_vm.__("custom.adjust_type")) + " "), _c("small", [_vm._v("(" + _vm._s(_vm.__("custom.stock")) + ")")])]), _vm._v(" "), _c("th", {
    staticStyle: {
      "min-width": "180px"
    }
  }, [_vm._v("Note")]), _vm._v(" "), _c("th")]), _vm._v(" "), _vm._l(_vm.items, function (item, index) {
    return _c("tr", {
      key: index
    }, [_c("td", [_c("select", {
      directives: [{
        name: "model",
        rawName: "v-model",
        value: item.warehouse,
        expression: "item.warehouse"
      }],
      staticClass: "form-control",
      attrs: {
        name: "warehouse_stock[" + index + "][warehouse]"
      },
      on: {
        change: [function ($event) {
          var $$selectedVal = Array.prototype.filter.call($event.target.options, function (o) {
            return o.selected;
          }).map(function (o) {
            var val = "_value" in o ? o._value : o.value;
            return val;
          });
          _vm.$set(item, "warehouse", $event.target.multiple ? $$selectedVal : $$selectedVal[0]);
        }, function ($event) {
          return _vm.checkDuplicate(index, $event);
        }]
      }
    }, [_c("option", {
      attrs: {
        value: "",
        selected: ""
      }
    }, [_vm._v(_vm._s(_vm.__("custom.select_warehouse")))]), _vm._v(" "), _vm._l(_vm.warehouses, function (item, index) {
      return _c("option", {
        key: index,
        domProps: {
          value: item.id
        }
      }, [_vm._v("\n                        " + _vm._s(item.name) + "\n                    ")]);
    })], 2)]), _vm._v(" "), _c("td", [_c("input", {
      directives: [{
        name: "model",
        rawName: "v-model",
        value: item.stock,
        expression: "item.stock"
      }],
      staticClass: "form-control",
      attrs: {
        readonly: "1",
        type: "number",
        name: "warehouse_stock[" + index + "][stock]"
      },
      domProps: {
        value: item.stock
      },
      on: {
        input: function input($event) {
          if ($event.target.composing) return;
          _vm.$set(item, "stock", $event.target.value);
        }
      }
    })]), _vm._v(" "), _c("td", [_vm.isWeightBased ? [_c("input", {
      directives: [{
        name: "model",
        rawName: "v-model.number",
        value: item.kg_quantity,
        expression: "item.kg_quantity",
        modifiers: {
          number: true
        }
      }],
      staticClass: "form-control",
      "class": {
        "is-invalid": _vm.isFractionalBarrelFromKg(item)
      },
      attrs: {
        min: "0",
        width: "30px",
        type: "number",
        step: "any",
        placeholder: "Enter KG"
      },
      domProps: {
        value: item.kg_quantity
      },
      on: {
        input: [function ($event) {
          if ($event.target.composing) return;
          _vm.$set(item, "kg_quantity", _vm._n($event.target.value));
        }, function ($event) {
          return _vm.onKgInput(item);
        }],
        blur: function blur($event) {
          return _vm.$forceUpdate();
        }
      }
    }), _vm._v(" "), _c("input", {
      attrs: {
        type: "hidden",
        name: "warehouse_stock[" + index + "][quantity]"
      },
      domProps: {
        value: _vm.barrelQtyFromKg(item)
      }
    }), _vm._v(" "), _vm.kgPerBarrel ? _c("small", {
      staticClass: "text-muted d-block"
    }, [_vm._v("\n                        1 " + _vm._s(_vm.barrelLabel) + " = " + _vm._s(_vm.kgPerBarrel) + " kg\n                    ")]) : _vm._e(), _vm._v(" "), item.kg_quantity && !_vm.isFractionalBarrelFromKg(item) && _vm.kgPerBarrel ? _c("small", {
      staticClass: "text-info d-block"
    }, [_vm._v("\n                        = " + _vm._s(_vm.barrelQtyFromKg(item)) + " " + _vm._s(_vm.barrelLabel) + "\n                    ")]) : _vm._e(), _vm._v(" "), _vm.isFractionalBarrelFromKg(item) ? _c("small", {
      staticClass: "d-block text-danger"
    }, [_vm._v("\n                        " + _vm._s(_vm.kgPerBarrel) + " kg এর গুণিতক হতে হবে (পূর্ণ " + _vm._s(_vm.barrelLabel) + " হতে হবে)\n                    ")]) : _vm._e()] : [_c("input", {
      directives: [{
        name: "model",
        rawName: "v-model.number",
        value: item.quantity,
        expression: "item.quantity",
        modifiers: {
          number: true
        }
      }],
      staticClass: "form-control",
      attrs: {
        min: "0",
        width: "30px",
        type: "number",
        step: "any",
        name: "warehouse_stock[" + index + "][quantity]"
      },
      domProps: {
        value: item.quantity
      },
      on: {
        input: [function ($event) {
          if ($event.target.composing) return;
          _vm.$set(item, "quantity", _vm._n($event.target.value));
        }, function ($event) {
          return _vm.clampQty(item, "quantity");
        }],
        blur: function blur($event) {
          return _vm.$forceUpdate();
        }
      }
    })]], 2), _vm._v(" "), _c("td", [_vm.isWeightBased ? [_c("input", {
      directives: [{
        name: "model",
        rawName: "v-model.number",
        value: item.kg_price,
        expression: "item.kg_price",
        modifiers: {
          number: true
        }
      }],
      staticClass: "form-control",
      attrs: {
        min: "0",
        width: "30px",
        type: "number",
        step: "any",
        placeholder: "Buying Price per KG"
      },
      domProps: {
        value: item.kg_price
      },
      on: {
        input: function input($event) {
          if ($event.target.composing) return;
          _vm.$set(item, "kg_price", _vm._n($event.target.value));
        },
        blur: function blur($event) {
          return _vm.$forceUpdate();
        }
      }
    }), _vm._v(" "), _c("input", {
      attrs: {
        type: "hidden",
        name: "warehouse_stock[" + index + "][price]"
      },
      domProps: {
        value: _vm.barrelPriceFromKg(item.kg_price)
      }
    }), _vm._v(" "), item.kg_price && _vm.kgPerBarrel ? _c("small", {
      staticClass: "text-info d-block"
    }, [_vm._v("\n                        = " + _vm._s((_vm.currency_symbol || "") + _vm.barrelPriceFromKg(item.kg_price)) + " / " + _vm._s(_vm.barrelLabel) + "\n                    ")]) : _vm._e()] : [_c("input", {
      directives: [{
        name: "model",
        rawName: "v-model",
        value: item.price,
        expression: "item.price"
      }],
      staticClass: "form-control",
      attrs: {
        min: "0",
        width: "30px",
        type: "number",
        name: "warehouse_stock[" + index + "][price]"
      },
      domProps: {
        value: item.price
      },
      on: {
        input: function input($event) {
          if ($event.target.composing) return;
          _vm.$set(item, "price", $event.target.value);
        }
      }
    })]], 2), _vm._v(" "), _c("td", [_vm.isWeightBased ? [_c("input", {
      directives: [{
        name: "model",
        rawName: "v-model.number",
        value: item.kg_customer_buying_price,
        expression: "item.kg_customer_buying_price",
        modifiers: {
          number: true
        }
      }],
      staticClass: "form-control",
      attrs: {
        min: "0",
        width: "30px",
        type: "number",
        step: "any",
        placeholder: "Selling price per KG"
      },
      domProps: {
        value: item.kg_customer_buying_price
      },
      on: {
        input: function input($event) {
          if ($event.target.composing) return;
          _vm.$set(item, "kg_customer_buying_price", _vm._n($event.target.value));
        },
        blur: function blur($event) {
          return _vm.$forceUpdate();
        }
      }
    }), _vm._v(" "), _c("input", {
      attrs: {
        type: "hidden",
        name: "warehouse_stock[" + index + "][customer_buying_price]"
      },
      domProps: {
        value: _vm.barrelPriceFromKg(item.kg_customer_buying_price)
      }
    }), _vm._v(" "), item.kg_customer_buying_price && _vm.kgPerBarrel ? _c("small", {
      staticClass: "text-info d-block"
    }, [_vm._v("\n                        = " + _vm._s((_vm.currency_symbol || "") + _vm.barrelPriceFromKg(item.kg_customer_buying_price)) + " / " + _vm._s(_vm.barrelLabel) + "\n                    ")]) : _vm._e()] : [_c("input", {
      directives: [{
        name: "model",
        rawName: "v-model",
        value: item.customer_buying_price,
        expression: "item.customer_buying_price"
      }],
      staticClass: "form-control",
      attrs: {
        min: "0",
        width: "30px",
        type: "number",
        name: "warehouse_stock[" + index + "][customer_buying_price]"
      },
      domProps: {
        value: item.customer_buying_price
      },
      on: {
        input: function input($event) {
          if ($event.target.composing) return;
          _vm.$set(item, "customer_buying_price", $event.target.value);
        }
      }
    })]], 2), _vm._v(" "), _vm.old_stocks.length > 0 ? _c("td", [_c("select", {
      directives: [{
        name: "model",
        rawName: "v-model",
        value: item.adjust_type,
        expression: "item.adjust_type"
      }],
      staticClass: "form-control",
      attrs: {
        name: "warehouse_stock[" + index + "][adjust_type]"
      },
      on: {
        change: function change($event) {
          var $$selectedVal = Array.prototype.filter.call($event.target.options, function (o) {
            return o.selected;
          }).map(function (o) {
            var val = "_value" in o ? o._value : o.value;
            return val;
          });
          _vm.$set(item, "adjust_type", $event.target.multiple ? $$selectedVal : $$selectedVal[0]);
        }
      }
    }, [_c("option", {
      attrs: {
        value: "",
        selected: ""
      }
    }, [_vm._v(_vm._s(_vm.__("custom.select_adjust_type")))]), _vm._v(" "), _vm._l(_vm.adjust_type, function (item, index) {
      return _c("option", {
        key: index,
        domProps: {
          value: item
        }
      }, [_vm._v(_vm._s(item))]);
    })], 2), _vm._v(" "), _c("input", {
      directives: [{
        name: "model",
        rawName: "v-model",
        value: item.manage_stock,
        expression: "item.manage_stock"
      }],
      attrs: {
        type: "hidden",
        name: "warehouse_stock[" + index + "][manage_stock]"
      },
      domProps: {
        value: item.manage_stock
      },
      on: {
        input: function input($event) {
          if ($event.target.composing) return;
          _vm.$set(item, "manage_stock", $event.target.value);
        }
      }
    }), _vm._v(" "), _c("input", {
      directives: [{
        name: "model",
        rawName: "v-model",
        value: item.backorders_allowed,
        expression: "item.backorders_allowed"
      }],
      attrs: {
        type: "hidden",
        name: "warehouse_stock[" + index + "][backorders_allowed]"
      },
      domProps: {
        value: item.backorders_allowed
      },
      on: {
        input: function input($event) {
          if ($event.target.composing) return;
          _vm.$set(item, "backorders_allowed", $event.target.value);
        }
      }
    })]) : _c("td", [_c("select", {
      directives: [{
        name: "model",
        rawName: "v-model",
        value: item.adjust_type,
        expression: "item.adjust_type"
      }],
      staticClass: "form-control",
      attrs: {
        name: "warehouse_stock[" + index + "][adjust_type]"
      },
      on: {
        change: function change($event) {
          var $$selectedVal = Array.prototype.filter.call($event.target.options, function (o) {
            return o.selected;
          }).map(function (o) {
            var val = "_value" in o ? o._value : o.value;
            return val;
          });
          _vm.$set(item, "adjust_type", $event.target.multiple ? $$selectedVal : $$selectedVal[0]);
        }
      }
    }, [_c("option", {
      attrs: {
        value: "",
        selected: ""
      }
    }, [_vm._v(_vm._s(_vm.__("custom.select_adjust_type")))]), _vm._v(" "), _c("option", {
      attrs: {
        value: "Add",
        selected: ""
      }
    }, [_vm._v("Add")])]), _vm._v(" "), _c("input", {
      directives: [{
        name: "model",
        rawName: "v-model",
        value: item.manage_stock,
        expression: "item.manage_stock"
      }],
      attrs: {
        type: "hidden",
        name: "warehouse_stock[" + index + "][manage_stock]"
      },
      domProps: {
        value: item.manage_stock
      },
      on: {
        input: function input($event) {
          if ($event.target.composing) return;
          _vm.$set(item, "manage_stock", $event.target.value);
        }
      }
    }), _vm._v(" "), _c("input", {
      directives: [{
        name: "model",
        rawName: "v-model",
        value: item.backorders_allowed,
        expression: "item.backorders_allowed"
      }],
      attrs: {
        type: "hidden",
        name: "warehouse_stock[" + index + "][backorders_allowed]"
      },
      domProps: {
        value: item.backorders_allowed
      },
      on: {
        input: function input($event) {
          if ($event.target.composing) return;
          _vm.$set(item, "backorders_allowed", $event.target.value);
        }
      }
    })]), _vm._v(" "), _c("td", [_c("input", {
      directives: [{
        name: "model",
        rawName: "v-model",
        value: item.note,
        expression: "item.note"
      }],
      staticClass: "form-control",
      attrs: {
        type: "text",
        name: "warehouse_stock[" + index + "][note]",
        maxlength: "500",
        placeholder: "Reason..."
      },
      domProps: {
        value: item.note
      },
      on: {
        input: function input($event) {
          if ($event.target.composing) return;
          _vm.$set(item, "note", $event.target.value);
        }
      }
    })]), _vm._v(" "), _c("td", [item.id ? _c("button", {
      staticClass: "btn btn-sm btn-outline-danger",
      attrs: {
        type: "button",
        disabled: ""
      },
      on: {
        click: function click($event) {
          return _vm.deleteItem(index);
        }
      }
    }, [_c("i", {
      staticClass: "fa fa-trash"
    })]) : _c("button", {
      staticClass: "btn btn-sm btn-outline-danger",
      attrs: {
        type: "button"
      },
      on: {
        click: function click($event) {
          return _vm.deleteItem(index);
        }
      }
    }, [_c("i", {
      staticClass: "fa fa-trash"
    })])])]);
  }), _vm._v(" "), _c("tfoot", [_c("tr", [_c("td", {
    attrs: {
      colspan: "8"
    }
  }, [_c("button", {
    staticClass: "btn btn-sm btn-info float-right",
    attrs: {
      type: "button",
      title: _vm.__("custom.add_warehouse")
    },
    on: {
      click: _vm.addItem
    }
  }, [_c("i", {
    staticClass: "fa fa-plus"
  })])])])])], 2)])]);
};
var staticRenderFns = [];
render._withStripped = true;


/***/ }),

/***/ "./resources/js/components/admin/product_stocks/NormalProductStockUpdate.vue":
/*!***********************************************************************************!*\
  !*** ./resources/js/components/admin/product_stocks/NormalProductStockUpdate.vue ***!
  \***********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _NormalProductStockUpdate_vue_vue_type_template_id_06a54144___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./NormalProductStockUpdate.vue?vue&type=template&id=06a54144& */ "./resources/js/components/admin/product_stocks/NormalProductStockUpdate.vue?vue&type=template&id=06a54144&");
/* harmony import */ var _NormalProductStockUpdate_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./NormalProductStockUpdate.vue?vue&type=script&lang=js& */ "./resources/js/components/admin/product_stocks/NormalProductStockUpdate.vue?vue&type=script&lang=js&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! !../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */
;
var component = (0,_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _NormalProductStockUpdate_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _NormalProductStockUpdate_vue_vue_type_template_id_06a54144___WEBPACK_IMPORTED_MODULE_0__.render,
  _NormalProductStockUpdate_vue_vue_type_template_id_06a54144___WEBPACK_IMPORTED_MODULE_0__.staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/admin/product_stocks/NormalProductStockUpdate.vue"
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (component.exports);

/***/ }),

/***/ "./resources/js/components/admin/product_stocks/NormalProductStockUpdate.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************!*\
  !*** ./resources/js/components/admin/product_stocks/NormalProductStockUpdate.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_lib_index_js_vue_loader_options_NormalProductStockUpdate_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./NormalProductStockUpdate.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/admin/product_stocks/NormalProductStockUpdate.vue?vue&type=script&lang=js&");
 /* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_lib_index_js_vue_loader_options_NormalProductStockUpdate_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/admin/product_stocks/NormalProductStockUpdate.vue?vue&type=template&id=06a54144&":
/*!******************************************************************************************************************!*\
  !*** ./resources/js/components/admin/product_stocks/NormalProductStockUpdate.vue?vue&type=template&id=06a54144& ***!
  \******************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_lib_index_js_vue_loader_options_NormalProductStockUpdate_vue_vue_type_template_id_06a54144___WEBPACK_IMPORTED_MODULE_0__.render),
/* harmony export */   "staticRenderFns": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_lib_index_js_vue_loader_options_NormalProductStockUpdate_vue_vue_type_template_id_06a54144___WEBPACK_IMPORTED_MODULE_0__.staticRenderFns)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_lib_index_js_vue_loader_options_NormalProductStockUpdate_vue_vue_type_template_id_06a54144___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??ruleSet[1].rules[2]!../../../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./NormalProductStockUpdate.vue?vue&type=template&id=06a54144& */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/lib/loaders/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/admin/product_stocks/NormalProductStockUpdate.vue?vue&type=template&id=06a54144&");


/***/ })

}]);