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
      <!-- VISITES -->
      <hr />
      <h1 v-if="moscosos" class="uk-heading-line uk-text-center">COMISSIONS DE SERVEIS</h1>
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
            <div class="mati"></div>
            <div class="motiu"></div>
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
      <h1 v-if="moscosos" class="uk-heading-line uk-text-center">
        FITXATGES OBLIDATS
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
  </div>
</template>

<script>
/**
 * En aquest component es mostren tots els justificants de tots els permisos dels assessors, es pot buscar per assessor i per dades concretes
 */
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
      visita: {}
    };
  },
  components: {
    VueTimepicker,
  },
  methods: {
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
    borra_oblidat(id) {
      var here = this;
      UIkit.modal
        .confirm(
          "<h3>Segur que vols esborrar-lo? Aquesta acció no es pot desfer</h3>"
        )
        .then(
          function () {
            let url = "cefire/" + id;
            for (let index = 0; index < here.oblits.length; index++) {
              if (here.oblits[index].id == id) {
                here.oblits.splice(index, 1);
              }
            }
            axios
              .delete(url)
              .then((res) => {
                console.log(res);
                here.$toast.success("Borrat correctament");
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
  },
  mounted() {
    this.agafa_compensacions();
    this.agafa_moscosos();
    this.agafa_vacances();
    this.agafa_oblits();
    this.agafa_visita();
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
