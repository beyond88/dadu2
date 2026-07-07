"use strict";
(self["webpackChunk"] = self["webpackChunk"] || []).push([["resources_js_components_admin_product_stocks_VariantProductStockUpdate_vue"],{

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/admin/product_stocks/VariantProductStockUpdate.vue?vue&type=script&lang=js&":
/*!*****************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/admin/product_stocks/VariantProductStockUpdate.vue?vue&type=script&lang=js& ***!
  \*****************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { _defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  props: ["product", "warehouses", "old_stocks"],
  data: function data() {
    return {
      items: [],
      adjust_type: ['Add', 'Subtract'],
      stock_alert_quantity: 0
    };
  },
  mounted: function mounted() {
    this.stock_alert_quantity = this.product.stock_alert_quantity;
    if (this.old_stocks.length > 0) {
      var warehouseMap = {};
      this.old_stocks.forEach(function (stock) {
        var _stock$manage_stock, _stock$backorders_all;
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
            note: ''
          };
        }
        warehouseMap[stock.warehouse_id].stock[stock.variation_id] = stock.quantity;
        warehouseMap[stock.warehouse_id].quantity[stock.variation_id] = 0;
        warehouseMap[stock.warehouse_id].price[stock.variation_id] = stock.variation.price;
        warehouseMap[stock.warehouse_id].customer_buying_price[stock.variation_id] = stock.variation.customer_buying_price;
        warehouseMap[stock.warehouse_id].manage_stock[stock.variation_id] = (_stock$manage_stock = stock.manage_stock) !== null && _stock$manage_stock !== void 0 ? _stock$manage_stock : false;
        warehouseMap[stock.warehouse_id].backorders_allowed[stock.variation_id] = (_stock$backorders_all = stock.backorders_allowed) !== null && _stock$backorders_all !== void 0 ? _stock$backorders_all : false;
      });
      this.items = Object.values(warehouseMap);
    } else {
      this.addItem();
    }
  },
  methods: {
    clampVariantQty: function clampVariantQty(item, varId) {
      if (item.quantity[varId] < 0) item.quantity[varId] = 0;
    },
    addItem: function addItem() {
      var stockObj = {
        stock: {},
        quantity: {},
        price: {},
        customer_buying_price: {},
        manage_stock: {},
        backorders_allowed: {}
      };
      this.product.variations.forEach(function (v) {
        stockObj.stock[v.id] = 0;
        stockObj.quantity[v.id] = 0;
        stockObj.price[v.id] = v.price;
        stockObj.customer_buying_price[v.id] = v.customer_buying_price;
        stockObj.manage_stock[v.id] = false;
        stockObj.backorders_allowed[v.id] = false;
      });
      this.items.push(_objectSpread({
        id: "",
        warehouse: "",
        adjust_type: 'Add',
        note: ''
      }, stockObj));
    },
    deleteItem: function deleteItem(index) {
      this.items.splice(index, 1);
    },
    checkDuplicate: function checkDuplicate(index, e) {
      var id = e.target.value;
      var duplicates = this.items.filter(function (item) {
        return item.warehouse == id;
      });
      if (duplicates.length > 1) {
        this.$swal.fire({
          icon: "error",
          text: "Duplicate warehouse selected!"
        });
        this.items.splice(index, 1);
      }
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/lib/loaders/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/admin/product_stocks/VariantProductStockUpdate.vue?vue&type=template&id=5ce7667e&":
/*!****************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/lib/loaders/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/admin/product_stocks/VariantProductStockUpdate.vue?vue&type=template&id=5ce7667e& ***!
  \****************************************************************************************************************************************************************************************************************************************************************************************************************************/
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
    staticClass: "table table-bordered"
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
  }, [_c("small", [_vm._v(_vm._s(_vm.__("custom.product_name")) + ": ")]), _vm._v("\n          " + _vm._s(_vm.product.name) + "\n        ")])]), _vm._v(" "), _c("th", {
    attrs: {
      colspan: "2"
    }
  }, [_vm._v("\n        " + _vm._s(_vm.__("custom.alert_quantity")) + "\n        "), _c("input", {
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
  }, [_c("th", [_vm._v(_vm._s(_vm.__("custom.warehouse_name")))]), _vm._v(" "), _c("th", [_vm._v(_vm._s(_vm.__("custom.current_stock")) + " "), _vm.product.weight_unit ? _c("small", [_vm._v("(" + _vm._s(_vm.product.weight_unit.name) + ")")]) : _vm._e()]), _vm._v(" "), _c("th", [_vm._v(_vm._s(_vm.__("custom.qty")) + " "), _vm.product.weight_unit ? _c("small", [_vm._v("(" + _vm._s(_vm.product.weight_unit.name) + ")")]) : _vm._e()]), _vm._v(" "), _c("th", {
    staticStyle: {
      "min-width": "120px"
    }
  }, [_vm._v(_vm._s(_vm.__("custom.price")))]), _vm._v(" "), _c("th", [_vm._v(_vm._s(_vm.__("custom.customer_buying_price")))]), _vm._v(" "), _c("th", [_vm._v(_vm._s(_vm.__("custom.adjust_type")) + " "), _c("small", [_vm._v("(" + _vm._s(_vm.__("custom.stock")) + ")")])]), _vm._v(" "), _c("th", {
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
    }, [_vm._v(_vm._s(_vm.__("custom.select_warehouse")))]), _vm._v(" "), _vm._l(_vm.warehouses, function (w, wIndex) {
      return _c("option", {
        key: wIndex,
        domProps: {
          value: w.id
        }
      }, [_vm._v(_vm._s(w.name))]);
    })], 2)]), _vm._v(" "), _c("td", [_c("table", {
      staticClass: "table table-bordered"
    }, _vm._l(_vm.product.variations, function (variation) {
      return _c("tr", {
        key: variation.id
      }, [_c("td", [_vm._v("\n              " + _vm._s(variation.name) + "\n              "), _c("input", {
        directives: [{
          name: "model",
          rawName: "v-model.number",
          value: item.stock[variation.id],
          expression: "item.stock[variation.id]",
          modifiers: {
            number: true
          }
        }],
        staticClass: "form-control",
        attrs: {
          type: "number",
          name: "warehouse_stock[".concat(index, "][stock][").concat(variation.id, "]"),
          readonly: ""
        },
        domProps: {
          value: item.stock[variation.id]
        },
        on: {
          input: function input($event) {
            if ($event.target.composing) return;
            _vm.$set(item.stock, variation.id, _vm._n($event.target.value));
          },
          blur: function blur($event) {
            return _vm.$forceUpdate();
          }
        }
      }), _vm._v(" "), _c("input", {
        attrs: {
          type: "hidden",
          name: "warehouse_stock[".concat(index, "][manage_stock][").concat(variation.id, "]")
        },
        domProps: {
          value: item.manage_stock[variation.id]
        }
      }), _vm._v(" "), _c("input", {
        attrs: {
          type: "hidden",
          name: "warehouse_stock[".concat(index, "][backorders_allowed][").concat(variation.id, "]")
        },
        domProps: {
          value: item.backorders_allowed[variation.id]
        }
      })])]);
    }), 0)]), _vm._v(" "), _c("td", [_c("table", {
      staticClass: "table table-bordered"
    }, _vm._l(_vm.product.variations, function (variation) {
      return _c("tr", {
        key: variation.id
      }, [_c("td", [_vm._v("\n              " + _vm._s(variation.name) + "\n              "), _c("input", {
        directives: [{
          name: "model",
          rawName: "v-model.number",
          value: item.quantity[variation.id],
          expression: "item.quantity[variation.id]",
          modifiers: {
            number: true
          }
        }],
        staticClass: "form-control",
        attrs: {
          type: "number",
          min: "0",
          name: "warehouse_stock[".concat(index, "][quantity][").concat(variation.id, "]")
        },
        domProps: {
          value: item.quantity[variation.id]
        },
        on: {
          input: [function ($event) {
            if ($event.target.composing) return;
            _vm.$set(item.quantity, variation.id, _vm._n($event.target.value));
          }, function ($event) {
            return _vm.clampVariantQty(item, variation.id);
          }],
          blur: function blur($event) {
            return _vm.$forceUpdate();
          }
        }
      })])]);
    }), 0)]), _vm._v(" "), _c("td", [_c("table", {
      staticClass: "table table-bordered"
    }, _vm._l(_vm.product.variations, function (variation) {
      return _c("tr", {
        key: variation.id
      }, [_c("td", [_vm._v("\n              " + _vm._s(variation.name) + "\n              "), _c("input", {
        directives: [{
          name: "model",
          rawName: "v-model.number",
          value: item.price[variation.id],
          expression: "item.price[variation.id]",
          modifiers: {
            number: true
          }
        }],
        staticClass: "form-control",
        attrs: {
          type: "number",
          name: "warehouse_stock[".concat(index, "][price][").concat(variation.id, "]")
        },
        domProps: {
          value: item.price[variation.id]
        },
        on: {
          input: function input($event) {
            if ($event.target.composing) return;
            _vm.$set(item.price, variation.id, _vm._n($event.target.value));
          },
          blur: function blur($event) {
            return _vm.$forceUpdate();
          }
        }
      })])]);
    }), 0)]), _vm._v(" "), _c("td", [_c("table", {
      staticClass: "table table-bordered"
    }, _vm._l(_vm.product.variations, function (variation) {
      return _c("tr", {
        key: variation.id
      }, [_c("td", [_vm._v("\n              " + _vm._s(variation.name) + "\n              "), _c("input", {
        directives: [{
          name: "model",
          rawName: "v-model.number",
          value: item.customer_buying_price[variation.id],
          expression: "item.customer_buying_price[variation.id]",
          modifiers: {
            number: true
          }
        }],
        staticClass: "form-control",
        attrs: {
          type: "number",
          name: "warehouse_stock[".concat(index, "][customer_buying_price][").concat(variation.id, "]")
        },
        domProps: {
          value: item.customer_buying_price[variation.id]
        },
        on: {
          input: function input($event) {
            if ($event.target.composing) return;
            _vm.$set(item.customer_buying_price, variation.id, _vm._n($event.target.value));
          },
          blur: function blur($event) {
            return _vm.$forceUpdate();
          }
        }
      })])]);
    }), 0)]), _vm._v(" "), _c("td", [_c("select", {
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
    }, [_vm._v(_vm._s(_vm.__("custom.select_adjust_type")))]), _vm._v(" "), _vm._l(_vm.old_stocks.length > 0 ? _vm.adjust_type : ["Add"], function (type, tIndex) {
      return _c("option", {
        key: tIndex,
        domProps: {
          value: type
        }
      }, [_vm._v("\n              " + _vm._s(type) + "\n              ")]);
    })], 2)]), _vm._v(" "), _c("td", [_c("input", {
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
    staticClass: "btn btn-sm btn-info mb-4 float-right",
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

/***/ "./resources/js/components/admin/product_stocks/VariantProductStockUpdate.vue":
/*!************************************************************************************!*\
  !*** ./resources/js/components/admin/product_stocks/VariantProductStockUpdate.vue ***!
  \************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _VariantProductStockUpdate_vue_vue_type_template_id_5ce7667e___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./VariantProductStockUpdate.vue?vue&type=template&id=5ce7667e& */ "./resources/js/components/admin/product_stocks/VariantProductStockUpdate.vue?vue&type=template&id=5ce7667e&");
/* harmony import */ var _VariantProductStockUpdate_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./VariantProductStockUpdate.vue?vue&type=script&lang=js& */ "./resources/js/components/admin/product_stocks/VariantProductStockUpdate.vue?vue&type=script&lang=js&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! !../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */
;
var component = (0,_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _VariantProductStockUpdate_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _VariantProductStockUpdate_vue_vue_type_template_id_5ce7667e___WEBPACK_IMPORTED_MODULE_0__.render,
  _VariantProductStockUpdate_vue_vue_type_template_id_5ce7667e___WEBPACK_IMPORTED_MODULE_0__.staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/admin/product_stocks/VariantProductStockUpdate.vue"
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (component.exports);

/***/ }),

/***/ "./resources/js/components/admin/product_stocks/VariantProductStockUpdate.vue?vue&type=script&lang=js&":
/*!*************************************************************************************************************!*\
  !*** ./resources/js/components/admin/product_stocks/VariantProductStockUpdate.vue?vue&type=script&lang=js& ***!
  \*************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_lib_index_js_vue_loader_options_VariantProductStockUpdate_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./VariantProductStockUpdate.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/admin/product_stocks/VariantProductStockUpdate.vue?vue&type=script&lang=js&");
 /* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_lib_index_js_vue_loader_options_VariantProductStockUpdate_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/admin/product_stocks/VariantProductStockUpdate.vue?vue&type=template&id=5ce7667e&":
/*!*******************************************************************************************************************!*\
  !*** ./resources/js/components/admin/product_stocks/VariantProductStockUpdate.vue?vue&type=template&id=5ce7667e& ***!
  \*******************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_lib_index_js_vue_loader_options_VariantProductStockUpdate_vue_vue_type_template_id_5ce7667e___WEBPACK_IMPORTED_MODULE_0__.render),
/* harmony export */   "staticRenderFns": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_lib_index_js_vue_loader_options_VariantProductStockUpdate_vue_vue_type_template_id_5ce7667e___WEBPACK_IMPORTED_MODULE_0__.staticRenderFns)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_lib_index_js_vue_loader_options_VariantProductStockUpdate_vue_vue_type_template_id_5ce7667e___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??ruleSet[1].rules[2]!../../../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./VariantProductStockUpdate.vue?vue&type=template&id=5ce7667e& */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/lib/loaders/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/admin/product_stocks/VariantProductStockUpdate.vue?vue&type=template&id=5ce7667e&");


/***/ })

}]);