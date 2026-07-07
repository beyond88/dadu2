"use strict";
(self["webpackChunk"] = self["webpackChunk"] || []).push([["resources_js_components_admin_attributes_AttributeEdit_vue"],{

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/admin/attributes/AttributeEdit.vue?vue&type=script&lang=js&":
/*!*************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/admin/attributes/AttributeEdit.vue?vue&type=script&lang=js& ***!
  \*************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  props: ["items"],
  data: function data() {
    return {
      formData: {
        items: []
      }
    };
  },
  computed: {},
  mounted: function mounted() {
    var _this = this;
    this.items.map(function (item) {
      _this.formData.items.push({
        id: item.id,
        // ✅ include ID
        name: item.name,
        color: item.color,
        image: item.image,
        file_url: item.file_url
      });
    });
  },
  methods: {
    addItem: function addItem() {
      this.formData.items.push({
        id: null,
        name: "",
        color: "#000"
      });
    },
    deleteItem: function deleteItem(index) {
      this.formData.items.splice(index, 1);
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/lib/loaders/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/admin/attributes/AttributeEdit.vue?vue&type=template&id=d952be8e&":
/*!************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/lib/loaders/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/admin/attributes/AttributeEdit.vue?vue&type=template&id=d952be8e& ***!
  \************************************************************************************************************************************************************************************************************************************************************************************************************/
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
    staticClass: "form-group col-md-12 col-lg-8"
  }, [_c("label", {
    attrs: {
      "for": ""
    }
  }, [_vm._v(_vm._s(_vm.__("custom.attribute_items")))]), _vm._v(" "), _c("div", {
    staticClass: "table-responsive"
  }, [_c("table", {
    staticClass: "table table-bordered table-sm"
  }, [_c("thead", [_c("tr", [_c("th", [_vm._v(_vm._s(_vm.__("custom.item_name")))]), _vm._v(" "), _c("th", [_vm._v(_vm._s(_vm.__("custom.color")))]), _vm._v(" "), _c("th", [_vm._v(_vm._s(_vm.__("custom.image")))]), _vm._v(" "), _c("th", [_vm._v(_vm._s(_vm.__("custom.action")))])])]), _vm._v(" "), _c("tbody", _vm._l(_vm.formData.items, function (item, index) {
    return _c("tr", {
      key: index
    }, [_c("td", {
      attrs: {
        width: "40%"
      }
    }, [_c("input", {
      directives: [{
        name: "model",
        rawName: "v-model",
        value: item.id,
        expression: "item.id"
      }],
      attrs: {
        type: "hidden",
        name: "item_data[" + index + "][id]"
      },
      domProps: {
        value: item.id
      },
      on: {
        input: function input($event) {
          if ($event.target.composing) return;
          _vm.$set(item, "id", $event.target.value);
        }
      }
    }), _vm._v(" "), _c("input", {
      directives: [{
        name: "model",
        rawName: "v-model",
        value: item.name,
        expression: "item.name"
      }],
      staticClass: "form-control",
      attrs: {
        type: "text",
        name: "item_data[" + index + "][name]",
        maxlength: "100",
        placeholder: "Item name"
      },
      domProps: {
        value: item.name
      },
      on: {
        input: function input($event) {
          if ($event.target.composing) return;
          _vm.$set(item, "name", $event.target.value);
        }
      }
    })]), _vm._v(" "), _c("td", {
      attrs: {
        width: "20%"
      }
    }, [_c("input", {
      directives: [{
        name: "model",
        rawName: "v-model",
        value: item.color,
        expression: "item.color"
      }],
      staticClass: "form-control",
      attrs: {
        type: "color",
        name: "item_data[" + index + "][color]"
      },
      domProps: {
        value: item.color
      },
      on: {
        input: function input($event) {
          if ($event.target.composing) return;
          _vm.$set(item, "color", $event.target.value);
        }
      }
    })]), _vm._v(" "), _c("td", [item.image ? _c("img", {
      staticClass: "img-32 float-left",
      attrs: {
        src: item.file_url,
        alt: ""
      }
    }) : _vm._e(), _vm._v(" "), _c("input", {
      staticClass: "form-control",
      attrs: {
        type: "file",
        name: "item_data[" + index + "][image]"
      }
    }), _vm._v(" "), _c("input", {
      directives: [{
        name: "model",
        rawName: "v-model",
        value: item.image,
        expression: "item.image"
      }],
      staticClass: "form-control",
      attrs: {
        type: "hidden",
        name: "item_data[" + index + "][old_image]"
      },
      domProps: {
        value: item.image
      },
      on: {
        input: function input($event) {
          if ($event.target.composing) return;
          _vm.$set(item, "image", $event.target.value);
        }
      }
    })]), _vm._v(" "), _c("td", [_c("button", {
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
  }), 0)])]), _vm._v(" "), _c("button", {
    staticClass: "btn btn-info btn-sm",
    attrs: {
      type: "button"
    },
    on: {
      click: _vm.addItem
    }
  }, [_c("i", {
    staticClass: "fa fa-plus"
  }), _vm._v(" " + _vm._s(_vm.__("custom.add_item")) + "\n  ")])]);
};
var staticRenderFns = [];
render._withStripped = true;


/***/ }),

/***/ "./resources/js/components/admin/attributes/AttributeEdit.vue":
/*!********************************************************************!*\
  !*** ./resources/js/components/admin/attributes/AttributeEdit.vue ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _AttributeEdit_vue_vue_type_template_id_d952be8e___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./AttributeEdit.vue?vue&type=template&id=d952be8e& */ "./resources/js/components/admin/attributes/AttributeEdit.vue?vue&type=template&id=d952be8e&");
/* harmony import */ var _AttributeEdit_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./AttributeEdit.vue?vue&type=script&lang=js& */ "./resources/js/components/admin/attributes/AttributeEdit.vue?vue&type=script&lang=js&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! !../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */
;
var component = (0,_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _AttributeEdit_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _AttributeEdit_vue_vue_type_template_id_d952be8e___WEBPACK_IMPORTED_MODULE_0__.render,
  _AttributeEdit_vue_vue_type_template_id_d952be8e___WEBPACK_IMPORTED_MODULE_0__.staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/admin/attributes/AttributeEdit.vue"
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (component.exports);

/***/ }),

/***/ "./resources/js/components/admin/attributes/AttributeEdit.vue?vue&type=script&lang=js&":
/*!*********************************************************************************************!*\
  !*** ./resources/js/components/admin/attributes/AttributeEdit.vue?vue&type=script&lang=js& ***!
  \*********************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_lib_index_js_vue_loader_options_AttributeEdit_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./AttributeEdit.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/admin/attributes/AttributeEdit.vue?vue&type=script&lang=js&");
 /* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_lib_index_js_vue_loader_options_AttributeEdit_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/admin/attributes/AttributeEdit.vue?vue&type=template&id=d952be8e&":
/*!***************************************************************************************************!*\
  !*** ./resources/js/components/admin/attributes/AttributeEdit.vue?vue&type=template&id=d952be8e& ***!
  \***************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_lib_index_js_vue_loader_options_AttributeEdit_vue_vue_type_template_id_d952be8e___WEBPACK_IMPORTED_MODULE_0__.render),
/* harmony export */   "staticRenderFns": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_lib_index_js_vue_loader_options_AttributeEdit_vue_vue_type_template_id_d952be8e___WEBPACK_IMPORTED_MODULE_0__.staticRenderFns)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_lib_index_js_vue_loader_options_AttributeEdit_vue_vue_type_template_id_d952be8e___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??ruleSet[1].rules[2]!../../../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./AttributeEdit.vue?vue&type=template&id=d952be8e& */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/lib/loaders/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/admin/attributes/AttributeEdit.vue?vue&type=template&id=d952be8e&");


/***/ })

}]);