<template>
  <div class="uk-margin-xlarge-bottom">
    <div class="uk-align-center">
      <h2 class="uk-align-center">Llistat de validacions</h2>
    </div>

    <div>
      <h1 v-if="compensacions" class="uk-heading-line uk-text-center">
        COMPENSACIONS
      </h1>
      <transition-group name="list3" tag="div">
        <div v-for="item in compensacions" :key="item.id">
          <div class="llistatcomp">
            <div class="data">
              <span data-uk-icon="icon: calendar"></span>
              {{ item.data }}
            </div>
            <div class="nom">
              <span data-uk-icon="icon: user"></span>
              <b>{{ item.name }}</b>
            </div>
            <div class="mati">
              <span data-uk-icon="icon: clock"></span>
              <span>{{ item.inici }} - {{ item.fi }}</span>
            </div>
            <div class="motiu">
              <span data-uk-icon="icon: comments"></span>
              {{ item.motiu }}
            </div>
            <div class="botons">
              <div
                @click.prevent="valida(item.id)"
                class="uk-icon-button uk-text-success"
                uk-icon="check"
              ></div>
              <div
                @click.prevent="borra(item.id)"
                class="uk-icon-button uk-text-danger"
                uk-icon="close"
              ></div>
            </div>
          </div>
        </div>
      </transition-group>
      <!-- VACANCES -->
      <hr />
      <h1 v-if="vacances" class="uk-heading-line uk-text-center">VACANCES</h1>
      <transition-group name="list3" tag="div">
        <div v-for="item in vacances" :key="item.id">
          <div class="llistatcomp">
            <div class="data">
              <span data-uk-icon="icon: calendar"></span>
              {{ item.data }}
            </div>
            <div class="nom">
              <span data-uk-icon="icon: user"></span>
              <b>{{ item.name }}</b>
            </div>
            <div class="botons">
              <div
                @click.prevent="valida_tot(item.name)"
                class="uk-icon-button uk-text-success"
                :uk-tooltip="'Valida totes les vacances de ' + item.name"
              >
                <i class="fa-solid fa-check-double"></i>
              </div>
              <div
                @click.prevent="valida_vacances(item.id)"
                class="uk-icon-button uk-text-success"
                uk-icon="check"
              ></div>
              <div
                @click.prevent="borra_vacances(item.id)"
                class="uk-icon-button uk-text-danger"
                uk-icon="close"
              ></div>
            </div>
          </div>
        </div>
      </transition-group>
      <!-- MOSCOSOS -->
      <hr />
      <h1 v-if="moscosos" class="uk-heading-line uk-text-center">MOSCOSOS</h1>
      <transition-group name="list3" tag="div">
        <div v-for="item in moscosos" :key="item.id">
          <div class="llistatcomp">
            <div class="data">
              <span data-uk-icon="icon: calendar"></span>
              {{ item.data }}
            </div>
            <div class="nom">
              <span data-uk-icon="icon: user"></span>
              <b>{{ item.name }}</b>
            </div>
            <div class="mati"></div>
            <div class="motiu"></div>
            <div class="botons">
              <div
                @click.prevent="valida_moscosos(item.id)"
                class="uk-icon-button uk-text-success"
                uk-icon="check"
              ></div>
              <div
                @click.prevent="borra_moscosos(item.id)"
                class="uk-icon-button uk-text-danger"
                uk-icon="close"
              ></div>
            </div>
          </div>
        </div>
      </transition-group>
      <!-- BORSA D'HORES -->
      <hr />
      <h1 class="uk-heading-line uk-text-center">BORSA D'HORES</h1>
      <transition-group name="list3" tag="div">
        <div v-for="item in borsahores" :key="item.id">
          <div class="llistatcomp">
            <div class="data">
              <span data-uk-icon="icon: calendar"></span>
              {{ item.mes }} - {{ item.any }}
            </div>
            <div class="nom">
              <span data-uk-icon="icon: user"></span>
              <b>{{ item.nom }}</b>
            </div>
            <div class="mati">
              <span data-uk-icon="icon: clock"></span>
              <span
                ><b>{{ (item.minuts / 60).toFixed(2) }} hores</b></span
              >
            </div>
            <!-- <div class="mati"></div> -->
            <div class="motiu" @click="mostra(item.justificacio)">
              <span data-uk-icon="icon: comments"></span>
              {{
                item.justificacio.length > 20
                  ? item.justificacio.slice(0, 20) + "..."
                  : item.justificacio
              }}
            </div>

            <div class="botons">
              <div
                @click.prevent="
                  valida_borsa_hores(item.id, item.user_id, item.minuts)
                "
                class="uk-icon-button uk-text-success"
                uk-icon="check"
              ></div>
              <div
                @click.prevent="borra_borsahores(item.id)"
                class="uk-icon-button uk-text-danger"
                uk-icon="close"
              ></div>
            </div>
          </div>
        </div>
      </transition-group>
      <!-- VISITES -->
      <hr />
      <h1 class="uk-heading-line uk-text-center">COMISSIONS DE SERVEIS</h1>
      <transition-group name="list3" tag="div">
        <div v-for="item in visita" :key="item.id">
          <div class="llistatcomp">
            <div class="data">
              <span data-uk-icon="icon: calendar"></span>
              {{ item.data }}
            </div>
            <div class="nom">
              <span data-uk-icon="icon: user"></span>
              <b>{{ item.name }}</b>
            </div>
            <div class="mati">
              <span data-uk-icon="icon: clock"></span>
              <span>{{ item.inici }} - {{ item.fi }}</span>
            </div>
            <div class="motiu">
              <span data-uk-icon="icon: comments"></span>
              <b>{{ item.motiu }}</b>
            </div>
            <div class="botons">
              <div
                @click.prevent="valida_visita(item.id)"
                class="uk-icon-button uk-text-success"
                uk-icon="check"
              ></div>
              <div
                @click.prevent="borra_visita(item.id)"
                class="uk-icon-button uk-text-danger"
                uk-icon="close"
              ></div>
            </div>
          </div>
        </div>
      </transition-group>
      <!-- FITXATGES OBLIDATS -->
      <hr />
      <h1 class="uk-heading-line uk-text-center">
        <div class="uk-inline">
          FITXATGES OBLIDATS
          <button
            :uk-toggle="'target: #cefire_afg-modal'"
            class="uk-icon-button uk-button-primary"
            uk-icon="icon: plus"
          ></button>
        </div>
      </h1>
      <transition-group name="list3" tag="div">
        <div
          v-for="item in oblits"
          :key="item.id"
          tabindex="-2"
          style="z-index: -2"
        >
          <div class="llistatcomp">
            <div class="data">
              <span data-uk-icon="icon: calendar"></span>
              {{ item.data }}
            </div>
            <div class="nom">
              <span data-uk-icon="icon: user"></span>
              <b>{{ item.name }}</b>
            </div>
            <div class="mati">
              <span data-uk-icon="icon: calendar"></span>
              <b>{{ item.data }}</b>
            </div>

            <div class="motiu">
              <span data-uk-icon="icon: time"></span>
              <b>{{ item.inici }}</b>
              <span>a</span>
              <vue-timepicker
                append-to-body
                :minute-interval="1"
                v-model="item.fi"
              ></vue-timepicker>
            </div>

            <div class="botons">
              <div
                @click.prevent="valida_oblidat(item.id, item.fi)"
                class="uk-icon-button uk-text-success"
                uk-icon="check"
              ></div>
              <div
                @click.prevent="borra_oblidat(item.id)"
                class="uk-icon-button uk-text-danger"
                uk-icon="close"
              ></div>
            </div>
          </div>
        </div>
      </transition-group>
    </div>
    <!-- Visita modal -->
    <div :id="'cefire_afg-modal'" uk-modal>
      <div class="uk-modal-dialog uk-modal-body">
        <fieldset class="uk-fieldset">
          <div class="uk-margin">
            <div class="uk-text-medium">
              Aquest fitxatge és sols en cas que se li haja oblidat tant
              l'entrada com la sortida
            </div>
            <div class="uk-margin">
              <form
                class="uk-width-expand uk-search uk-search-default"
                autocomplete="on"
              >
                <div class="uk-margin">
                  <label for="seleccio">Selecciona assessor</label>
                  <select
                    id="seleccio"
                    v-model="busca_ass"
                    class="uk-select"
                    aria-label="Select"
                  >
                    <option
                      v-for="(user, key) in users"
                      :key="key"
                      :value="user.id"
                    >
                      {{ user.name }}
                    </option>
                  </select>
                </div>
              </form>
            </div>
            <div class="uk-child-width-expand@s" uk-grid>
              <div>
                <label>Dia fitxatge: </label>
                <Datepicker
                  :language="ca"
                  :monday-first="true"
                  v-model="data"
                  placeholder="Selecciona dia:"
                  input-class="uk-input uk-inline"
                >
                </Datepicker>
              </div>
              <div>
                <label>Hora entrada: </label>
                <vue-timepicker
                  :hour-range="[[7, 19]]"
                  :minute-interval="5"
                  v-model="inici"
                ></vue-timepicker>
              </div>
              <div>
                <span> Hora sortida: </span>
                <vue-timepicker
                  :hour-range="[[7, 19]]"
                  :minute-interval="5"
                  v-model="fi"
                ></vue-timepicker>
              </div>
            </div>
          </div>
        </fieldset>
        <p class="uk-text-right">
          <button
            class="uk-button uk-button-default uk-modal-close"
            type="button"
          >
            Cancel·la
          </button>
          <button
            @click.prevent="afegix_cefire"
            class="uk-button uk-button-primary"
            type="button"
          >
            Fitxa
          </button>
        </p>
      </div>
    </div>
  </div>
</template>

<script>
/**
 * En aquest component es mostren tots els justificants de tots els permisos dels assessors, es pot buscar per assessor i per dades concretes
 */
import Datepicker from "vuejs-datepicker";
import { ca } from "vuejs-datepicker/dist/locale";
import VueTimepicker from "vue2-timepicker";
import "vue2-timepicker/dist/VueTimepicker.css";
export default {
  data() {
    return {
      hui: new Date(),
      compensacions: {},
      moscosos: {},
      vacances: {},
      oblits: {},
      visita: {},
      borsahores: {},
      inici: "",
      fi: "",
      users: [],
      busca_ass: "",
      data: null,
      ca: ca,
    };
  },
  components: {
    VueTimepicker,
    Datepicker,
  },
  methods: {
    mostra(aux) {
      UIkit.modal.dialog('<p class="uk-padding uk-text-large">' + aux + "</p>");
    },
    // Petició de les dades de tots els usuaris per al desplegable
    agafa_users() {
      axios
        .get("user")
        .then((res) => {
          console.log(res);
          this.users = res.data;
        })
        .catch((err) => {
          console.error(err);
        });
    },
    agafa_compensacions() {
      let url = "compensacions_no_validades";
      axios
        .post(url)
        .then((res) => {
          this.compensacions = res.data;
          console.log(res);
        })
        .catch((err) => {
          this.$toast.error(err.response.data.message);
        });
    },
    agafa_moscosos() {
      let url = "moscosos_no_validades";
      axios
        .post(url)
        .then((res) => {
          this.moscosos = res.data;
          console.log(res);
        })
        .catch((err) => {
          this.$toast.error(err.response.data.message);
        });
    },
    agafa_vacances() {
      let url = "vacances_no_validades";
      axios
        .post(url)
        .then((res) => {
          this.vacances = res.data;
          console.log(res);
        })
        .catch((err) => {
          this.$toast.error(err.response.data.message);
        });
    },
    agafa_oblits() {
      let url = "usuaris_oblit_fitxatge";
      axios
        .get(url)
        .then((res) => {
          this.oblits = res.data;
          console.log(res);
        })
        .catch((err) => {
          this.$toast.error(err.response.data.message);
        });
    },
    agafa_visita() {
      let url = "visita_no_validades";
      axios
        .post(url)
        .then((res) => {
          this.visita = res.data;
          console.log(res);
        })
        .catch((err) => {
          this.$toast.error(err.response.data.message);
        });
    },
    agafa_borsa_hores() {
      let url = "borsasolicituds";
      axios
        .get(url)
        .then((res) => {
          this.borsahores = res.data;
          console.log(res);
        })
        .catch((err) => {
          this.$toast.error(err.response.data.message);
        });
    },
    borra(id) {
      let url = "compensa/" + id;
      for (let index = 0; index < this.compensacions.length; index++) {
        if (this.compensacions[index].id == id) {
          this.compensacions.splice(index, 1);
        }
      }
      axios
        .delete(url)
        .then((res) => {
          console.log(res);
          this.$toast.success("Borrat correctament");
        })
        .catch((err) => {
          console.error(err);
          this.$toast.error(err.response.data.message);
        });
    },
    borra_moscosos(id) {
      let url = "moscosos/" + id;
      for (let index = 0; index < this.moscosos.length; index++) {
        if (this.moscosos[index].id == id) {
          this.moscosos.splice(index, 1);
        }
      }
      axios
        .delete(url)
        .then((res) => {
          console.log(res);
          this.$toast.success("Borrat correctament");
        })
        .catch((err) => {
          console.error(err);
          this.$toast.error(err.response.data.message);
        });
    },
    borra_vacances(id) {
      let url = "vacances/" + id;
      for (let index = 0; index < this.vacances.length; index++) {
        if (this.vacances[index].id == id) {
          this.vacances.splice(index, 1);
        }
      }
      axios
        .delete(url)
        .then((res) => {
          console.log(res);
          this.$toast.success("Borrat correctament");
        })
        .catch((err) => {
          console.error(err);
          this.$toast.error(err.response.data.message);
        });
    },
    borra_visita(id) {
      let url = "visita/" + id;
      for (let index = 0; index < this.visita.length; index++) {
        if (this.visita[index].id == id) {
          this.visita.splice(index, 1);
        }
      }
      axios
        .delete(url)
        .then((res) => {
          console.log(res);
          this.$toast.success("Borrat correctament");
        })
        .catch((err) => {
          console.error(err);
          this.$toast.error(err.response.data.message);
        });
    },
    borra_visita(id) {
      let url = "visita/" + id;
      for (let index = 0; index < this.visita.length; index++) {
        if (this.visita[index].id == id) {
          this.visita.splice(index, 1);
        }
      }
      axios
        .delete(url)
        .then((res) => {
          console.log(res);
          this.$toast.success("Borrat correctament");
        })
        .catch((err) => {
          console.error(err);
          this.$toast.error(err.response.data.message);
        });
    },
    borra_borsahores(id) {
      var here = this;
      UIkit.modal
        .confirm(
          "<h3>Segur que vols esborrar-lo? Aquesta acció no es pot desfer</h3>"
        )
        .then(
          function () {
            let url = "borsasolicituds/" + id;
            for (let index = 0; index < here.borsahores.length; index++) {
              if (here.borsahores[index].id == id) {
                here.borsahores.splice(index, 1);
              }
            }
            axios
              .delete(url)
              .then((res) => {
                console.log(res);
                here.$toast.success(res.data);
              })
              .catch((err) => {
                console.error(err);
                here.$toast.error(err.response.data.message);
              });
          },
          function () {
            here.$toast.info("Cap acció realitzada");
          }
        );
    },
    // Edita assessor
    valida(id) {
      let url = "validacompensacio";
      for (let index = 0; index < this.compensacions.length; index++) {
        if (this.compensacions[index].id == id) {
          this.compensacions.splice(index, 1);
        }
      }
      let params = {
        id: id,
      };
      axios
        .post(url, params)
        .then((res) => {
          console.log(res);
          this.$toast.success("Has validat la compensació");
        })
        .catch((err) => {
          this.$toast.error(err.response.data.message);
        });
    },
    valida_moscosos(id) {
      let url = "validamoscosos";
      for (let index = 0; index < this.moscosos.length; index++) {
        if (this.moscosos[index].id == id) {
          this.moscosos.splice(index, 1);
        }
      }
      let params = {
        id: id,
      };
      axios
        .post(url, params)
        .then((res) => {
          console.log(res);
          this.$toast.success("Has validat la compensació");
        })
        .catch((err) => {
          this.$toast.error(err.response.data.message);
        });
    },
    valida_vacances(id) {
      let url = "validavacances";
      for (let index = 0; index < this.vacances.length; index++) {
        if (this.vacances[index].id == id) {
          this.vacances.splice(index, 1);
        }
      }
      let params = {
        id: id,
      };
      axios
        .post(url, params)
        .then((res) => {
          console.log(res);
          this.$toast.success("Has validat el dia de vacances");
        })
        .catch((err) => {
          this.$toast.error(err.response.data.message);
        });
    },
    valida_visita(id) {
      let url = "validavisita";
      for (let index = 0; index < this.visita.length; index++) {
        if (this.visita[index].id == id) {
          this.visita.splice(index, 1);
        }
      }
      let params = {
        id: id,
      };
      axios
        .post(url, params)
        .then((res) => {
          console.log(res);
          this.$toast.success("Has validat la comissió de serveis");
        })
        .catch((err) => {
          this.$toast.error(err.response.data.message);
        });
    },
    valida_tot(nom) {
      let filtrat = this.vacances.filter((vacances) => vacances.name == nom);

      console.log(filtrat);

      filtrat.forEach((element) => {
        this.valida_vacances(element.id);
      });
    },
    valida_oblidat(id, fi) {
      let url = "validaoblidat";
      let params = {
        id: id,
        fi: fi,
      };
      axios
        .post(url, params)
        .then((res) => {
          console.log(res);
          this.$toast.success("Has realitzat el fixatge");
          this.agafa_oblits();
        })
        .catch((err) => {
          this.$toast.error(err.response.data.message);
        });
    },
    valida_borsa_hores(id, user_id, minuts) {
      let url = "borsasolicitudsvalida";

      let params = {
        id: id,
        user_id: user_id,
        minuts: minuts,
      };
      axios
        .post(url, params)
        .then((res) => {
          console.log(res);
          this.$toast.success(res.data);
          for (let index = 0; index < this.borsahores.length; index++) {
            if (this.borsahores[index].id == id) {
              this.borsahores.splice(index, 1);
            }
          }
        })
        .catch((err) => {
          this.$toast.error(err.response.data.message);
        });
    },

    afegix_cefire() {
      //alert(this.data);
      if (
        this.data === null ||
        this.inici == "" ||
        this.fi == "" ||
        this.busca_ass == ""
      ) {
        this.$toast.error("Cal que emplenes totes les dades");
        return 0;
      }
      var params = {
        id: this.busca_ass,
        data: data_db(this.data),
        inici: this.inici,
        fi: this.fi,
      };
      axios
        .post("cefire_fitxa_oblit", params)
        .then((res) => {
          this.$toast.success(res.data);
        })
        .catch((err) => {
          this.$toast.error(err.response.data.message);
        });
    },
    afegix_fitxatge() {
      //Obri finestra
    },
  },
  mounted() {
    this.agafa_compensacions();
    this.agafa_moscosos();
    this.agafa_vacances();
    this.agafa_oblits();
    this.agafa_visita();
    this.agafa_users();
    this.agafa_borsa_hores();
  },
  watch: {},
};
</script>

<style lang="sass" scope>
$fondo: #EEC49A

.vue__time-picker-dropdown
  z-index: 5000
  top: auto
  overflow: auto
.llistatcomp
  display: grid
  grid-template-columns: 0.7fr 1.1fr 0.8fr 2fr 0.3fr
  grid-template-rows: 1fr
  gap: 0px 0px
  grid-template-areas: "data nom mati motiu botons"
  border: 2px solid black
  border-radius: 10px
  margin: 10px
  padding: 10px
  box-shadow: 3px 6px 121px -42px rgba(0,0,0,0.75)
  align-content: center
  align-items: center
  background-color: $fondo
  comu
  overflow: hidden
  .data
    @extend comu
    grid-area: data
    .nom
      @extend comu
      grid-area: nom
      .mati
        @extend comu
        grid-area: mati
      .motiu
        grid-area: motiu
      .botons
        @extend comu
        grid-area: botons
        display: flex
        align-items: right
        text-align: right
</style>
