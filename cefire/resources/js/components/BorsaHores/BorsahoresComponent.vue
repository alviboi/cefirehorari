<template>
  <div :id="'modal_borsahores' + id" uk-modal>
    <div
      style="background-color: #f7f7f7"
      class="uk-modal-dialog uk-modal-body"
    >
      <fieldset class="uk-fieldset">
        <legend class="uk-legend">Sol·licitud afegir a borsa hores</legend>
        <div class="uk-margin">
          <div class="uk-text-medium">
            <div v-if="minuts_sobrants_mes_anterior > 0">
              Tens un excés de
              <span class="uk-text-emphasis"
                >{{ (minuts_sobrants_mes_anterior / 60).toFixed(2) }} ({{
                  minuts_sobrants_mes_anterior
                }}
                minuts)</span
              >
              hores el més anterior.
            </div>
            <div v-else-if="minuts_sobrants_mes_anterior <= 0">
              Tens un deute de
              <span>{{ (minuts_sobrants_mes_anterior / 60).toFixed(2) }}</span>
              hores del més anterior. Per tant no pots fer cap sol·licitud. Si
              penses que és un error posat en contacte amb l'administrador.
            </div>
          </div>
        </div>
        <div v-if="minuts_sobrants_mes_anterior > 0">
          <div class="uk-margin">
            <div class="uk-inline">
              <span class="uk-text-medium"
                >Nombre de <b>minuts</b> que vols que t'afegixquen a la borsa
                d'hores (has de posar el número de <b>minuts</b> multiplicat amb
                els càlculs fets). Has de detallar correctament el càlcul sino
                se't denegarà la sol·licitud.</span
              ><br />
              <hr>
              <div>
                <div>
                  <input
                    class="uk-input uk-form-width-medium"
                    type="number"
                    v-model="minuts_a_afegir_25"
                    :max="minuts_sobrants_mes_anterior * 2.5"
                    min="0"
                    step="1"
                  />
                  <span>x2.5 = {{ minuts_a_afegir_25 * 2.5 }} minuts</span>
                </div>
                <div class="uk-margin">
                  <label for="just">Justifica el x2.5:</label>
                  <textarea
                    id="just"
                    v-model="justificacio25"
                    class="uk-textarea"
                    rows="2"
                    placeholder="Justifica aquest modificador"
                  ></textarea>
                </div>
                <hr>
                <div>
                  <input
                    class="uk-input uk-form-width-medium"
                    type="number"
                    v-model="minuts_a_afegir_2"
                    :max="minuts_sobrants_mes_anterior * 2"
                    min="0"
                    step="1"
                  />
                  <span>x2 = {{ minuts_a_afegir_2 * 2 }} minuts</span>
                </div>
                <div class="uk-margin">
                  <label for="just"
                    >Justifica el x2:</label
                  >
                  <textarea
                    id="just"
                    v-model="justificacio2"
                    class="uk-textarea"
                    rows="2"
                    placeholder="Justifica aquest modificador"
                  ></textarea>
                </div>
                <hr>
                <div>
                  <input
                    class="uk-input uk-form-width-medium"
                    type="number"
                    v-model="minuts_a_afegir_1"
                    min="0"
                    step="1"
                    disabled
                  />
                  <span>x1 = {{ minuts_a_afegir_1 }} minuts</span>
                </div>
                <div>
                  <span class="uk-align-right">TOTAL = {{ total }} minuts</span>
                </div>
                <div v-if="minuts_a_afegir_1 < 0">
                  <span class="uk-label uk-label-warning"
                    >NO POTS COMPENSAR TANT</span
                  >
                </div>
              </div>
            </div>
          </div>
          <div class="uk-margin">
            <label for="just">Observacions:</label>
            <textarea
              id="just"
              v-model="justificacio"
              class="uk-textarea"
              rows="2"
              placeholder="Justificació"
            ></textarea>
          </div>
        </div>
      </fieldset>

      <div></div>
      <p class="uk-text-right">
        <transition-group name="list-complete2">
          <button
            :key="1 + id"
            @click.prevent="ix"
            class="uk-button uk-button-default uk-button-close"
            type="button"
          >
            Cancel·la
          </button>
          <button
            :key="2 + id"
            @click.prevent="envia"
            class="uk-button uk-button-primary"
            type="button"
            :disabled="minuts_a_afegir_1 < 0"
          >
            Envia
          </button>
        </transition-group>
      </p>
    </div>
  </div>
</template>

<script>
/**
 * Component amb el modal per a escriure un missatge
 */
export default {
  data() {
    return {
      minuts_sobrants_mes_anterior: 0,
      //minuts_a_afegir_1: 0,
      minuts_a_afegir_2: 0,
      minuts_a_afegir_25: 0,
      justificacio25: "",
      justificacio2: "",
      justificacio: "",
      id: 0,
    };
  },
  props: ["show-borsahores"],
  watch: {
    showBorsahores() {
      this.mostra_modal();
    },
  },
  computed: {
    minuts_a_afegir_1() {
      return (
        this.minuts_sobrants_mes_anterior -
        this.minuts_a_afegir_2 -
        this.minuts_a_afegir_25
      );
    },
    total() {
      var a = Math.random();
      return (
        this.minuts_a_afegir_25 * 2.5 +
        this.minuts_a_afegir_1 +
        this.minuts_a_afegir_2 * 2
      );
    },
  },
  methods: {
    deutes_mes_un_usuari(id, user_id) {
      axios
        .get("/calcula_deutes_mes_un_usuari")
        .then((res) => {
          this.minuts_sobrants_mes_anterior = res.data["diferència"];
        })
        .catch((err) => {
          this.$toast.error(err.response.data.message);
        });
    },
    // Surt del modal i reseteja totes les dades
    ix() {
      this.$eventBus.$emit("tanca-borsahores");
      UIkit.modal("#modal_borsahores" + this.id).hide();
      //this.minuts_sobrants_mes_anterior = 0;
      //this.minuts_a_afegir = 0;
    },

    // Envia el misstage
    envia() {
      //console.log(this.cap.length);
      // if (this.minuts_a_afegir > this.minuts_sobrants_mes_anterior * 2.5) {
      //   this.$toast.error("No pots demanar tants minuts");
      //   return 0;
      // }

      // if (this.minuts_a_afegir <= this.minuts_sobrants_mes_anterior) {
      //   this.$toast.error("No et convé fer esta sol·licitud!");
      //   return 0;
      // }



      if ((this.minuts_a_afegir_25>0 && this.justificacio25.length === 0) || (this.minuts_a_afegir_25===0 && this.justificacio25.length > 0) ) {
        this.$toast.error("Falta algun paràmetre per emplenar a l'apartat de x2.5");
        return;
      } 


      if ((this.minuts_a_afegir_2>0 && this.justificacio2.length === 0) || (this.minuts_a_afegir_2===0 && this.justificacio2.length > 0) )  {
        this.$toast.error("Falta algun paràmetre per emplenar a l'apartat de x2");
        return;
      }


        let url = "borsasolicituds";
        let params = {
          //minuts_a_afegir: this.minuts_a_afegir,
          justificacio: (this.justificacio=="")?"No ha fet cap justificació":this.justificacio,
          minutsx1: this.minuts_a_afegir_1,
          minutsx2: this.minuts_a_afegir_2,
          minutsx25: this.minuts_a_afegir_25,
          justificaciox25: (this.justificacio25=="")?"No ha fet cap justificació":this.justificacio25,
          justificaciox2: (this.justificacio2=="")?"No ha fet cap justificació":this.justificacio2,
        };
        axios
          .post(url, params)
          .then((res) => {
            //console.log(res);
            //this.resposta=res.data;
            this.$toast.success(res.data);
          })
          .catch((err) => {
            this.$toast.error(err.response.data.message);

            //console.error(err);
          });
      
    },
    // Mostra modal en funció del bus
    mostra_modal() {
      if (this.showBorsahores == true) {
        UIkit.modal("#modal_borsahores" + this.id, {
          bgClose: false,
          escClose: false,
          modal: false,
          keyboard: false,
        }).show();
        this.deutes_mes_un_usuari();
      } // else {
      //   UIkit.modal("#modal_borsahores").hide();
      // }
    },
    // Obre el missatge
    obre_missatge(envia) {
      this.cap = envia.assumpte;
      this.destinatari = envia.destinatari;
      UIkit.modal("#modal_borsahores").show();
    },
  },

  mounted() {
    this.id = this._uid;
  },

  created() {
    // Crea event per a obrir el missatge
    this.$eventBus.$on("missatge-variables", this.obre_missatge);
  },
  beforeDestroy() {
    this.$eventBus.$off("missatge-variables");
  },
};
</script>

<style>
</style>
