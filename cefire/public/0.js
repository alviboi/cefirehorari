(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[0],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/avisos/EscriuavisComponent.vue?vue&type=script&lang=js&":
/*!*************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/avisos/EscriuavisComponent.vue?vue&type=script&lang=js& ***!
  \*************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
/* harmony default export */ __webpack_exports__["default"] = ({
  data: function data() {
    return {
      id: null,
      cap: "",
      avis: "",
      resposta: ""
    };
  },
  props: ['showModal'],
  methods: {
    ix: function ix() {
      this.resposta = "";
      this.cap = "";
      this.avis = "";
      UIkit.modal('#modal_avis').hide();
    },
    envia: function envia() {
      var _this = this;

      console.log(this.cap.length);

      if (this.cap.length === 0 || this.avis.length === 0) {
        this.resposta = "Falta algun paràmetre per emplenar";
      } else {
        var url = "avisos";
        var params = {
          cap: this.cap,
          avis: this.avis
        };
        axios.post(url, params).then(function (res) {
          console.log(res);
          _this.resposta = res.data;

          _this.$eventBus.$emit('avis-enviat');

          _this.cap = "";
          _this.avis = "";
        })["catch"](function (err) {
          console.error(err);
        });
      }
    }
  },
  mounted: function mounted() {
    this.id = this._uid;
  },
  watch: {
    showModal: function showModal() {
      if (this.showModal == true) {
        UIkit.modal('#modal_avis').show();
      }
    }
  }
});

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/avisos/EscriuavisComponent.vue?vue&type=template&id=36036ad4&":
/*!*****************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/avisos/EscriuavisComponent.vue?vue&type=template&id=36036ad4& ***!
  \*****************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { attrs: { id: "modal_avis", "uk-modal": "" } }, [
    _c(
      "div",
      {
        staticClass: "uk-modal-dialog uk-modal-body",
        staticStyle: { "background-color": "#f7f7f7" }
      },
      [
        _c("fieldset", { staticClass: "uk-fieldset" }, [
          _c("legend", { staticClass: "uk-legend" }, [
            _vm._v("Escriu avís de primera pàgina")
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "uk-margin" }, [
            _c("input", {
              directives: [
                {
                  name: "model",
                  rawName: "v-model",
                  value: _vm.cap,
                  expression: "cap"
                }
              ],
              staticClass: "uk-input",
              attrs: { type: "text", placeholder: "Capçalera de l'avís" },
              domProps: { value: _vm.cap },
              on: {
                input: function($event) {
                  if ($event.target.composing) {
                    return
                  }
                  _vm.cap = $event.target.value
                }
              }
            })
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "uk-margin" }, [
            _c("textarea", {
              directives: [
                {
                  name: "model",
                  rawName: "v-model",
                  value: _vm.avis,
                  expression: "avis"
                }
              ],
              staticClass: "uk-textarea",
              attrs: { rows: "5", placeholder: "Missatge que vols escriure" },
              domProps: { value: _vm.avis },
              on: {
                input: function($event) {
                  if ($event.target.composing) {
                    return
                  }
                  _vm.avis = $event.target.value
                }
              }
            })
          ])
        ]),
        _vm._v(" "),
        _c("transition", { attrs: { name: "fade" } }, [
          _vm.resposta
            ? _c("div", [
                _vm._v(
                  "\n                    " +
                    _vm._s(_vm.resposta) +
                    "\n                "
                )
              ])
            : _vm._e()
        ]),
        _vm._v(" "),
        _c("div"),
        _vm._v(" "),
        _c(
          "p",
          { staticClass: "uk-text-right" },
          [
            _c("transition-group", { attrs: { name: "list-complete2" } }, [
              _c(
                "button",
                {
                  key: 1 + _vm.id,
                  staticClass: "uk-button uk-button-default",
                  attrs: { type: "button" },
                  on: {
                    click: function($event) {
                      $event.preventDefault()
                      return _vm.ix($event)
                    }
                  }
                },
                [_vm._v("Cancel·la")]
              ),
              _vm._v(" "),
              _c(
                "button",
                {
                  key: 2 + _vm.id,
                  staticClass: "uk-button uk-button-primary",
                  attrs: { type: "button" },
                  on: {
                    click: function($event) {
                      $event.preventDefault()
                      return _vm.envia($event)
                    }
                  }
                },
                [_vm._v("Envia")]
              )
            ])
          ],
          1
        )
      ],
      1
    )
  ])
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./resources/js/components/avisos/EscriuavisComponent.vue":
/*!****************************************************************!*\
  !*** ./resources/js/components/avisos/EscriuavisComponent.vue ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _EscriuavisComponent_vue_vue_type_template_id_36036ad4___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./EscriuavisComponent.vue?vue&type=template&id=36036ad4& */ "./resources/js/components/avisos/EscriuavisComponent.vue?vue&type=template&id=36036ad4&");
/* harmony import */ var _EscriuavisComponent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./EscriuavisComponent.vue?vue&type=script&lang=js& */ "./resources/js/components/avisos/EscriuavisComponent.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _EscriuavisComponent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _EscriuavisComponent_vue_vue_type_template_id_36036ad4___WEBPACK_IMPORTED_MODULE_0__["render"],
  _EscriuavisComponent_vue_vue_type_template_id_36036ad4___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/avisos/EscriuavisComponent.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/avisos/EscriuavisComponent.vue?vue&type=script&lang=js&":
/*!*****************************************************************************************!*\
  !*** ./resources/js/components/avisos/EscriuavisComponent.vue?vue&type=script&lang=js& ***!
  \*****************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_EscriuavisComponent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib??ref--4-0!../../../../node_modules/vue-loader/lib??vue-loader-options!./EscriuavisComponent.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/avisos/EscriuavisComponent.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_EscriuavisComponent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/avisos/EscriuavisComponent.vue?vue&type=template&id=36036ad4&":
/*!***********************************************************************************************!*\
  !*** ./resources/js/components/avisos/EscriuavisComponent.vue?vue&type=template&id=36036ad4& ***!
  \***********************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_EscriuavisComponent_vue_vue_type_template_id_36036ad4___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../node_modules/vue-loader/lib??vue-loader-options!./EscriuavisComponent.vue?vue&type=template&id=36036ad4& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/avisos/EscriuavisComponent.vue?vue&type=template&id=36036ad4&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_EscriuavisComponent_vue_vue_type_template_id_36036ad4___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_EscriuavisComponent_vue_vue_type_template_id_36036ad4___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);