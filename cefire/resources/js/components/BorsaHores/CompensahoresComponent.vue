<template>
  <div :id="'modal_borsahores_compensa' + id" uk-modal>
    <div
      style="background-color: #f7f7f7"
      class="uk-modal-dialog uk-modal-body"
    >
      <fieldset class="uk-fieldset">
        <legend class="uk-legend">
          Sol·licitud per a compensar deute de mes per temps de borsa d'hores
        </legend>
        <div class="uk-margin">
          <div class="uk-text-medium">
            <div v-if="minuts_en_borsa > 0">
              Tens
              <span class="uk-text-emphasis"
                >{{ (minuts_en_borsa / 60).toFixed(2) }} hores ({{
                  minuts_en_borsa
                }}
                minuts)</span
              >
              hores en la borsa y un
              <span v-if="deute_mes >= 0">superàvit</span>
              <span v-else>deute</span>
              de
              <span class="uk-text-emphasis uk-text-warning"
                >{{ (deute_mes / 60).toFixed(2) }} hores ({{
                  deute_mes
                }}
                minuts)</span
              >
            </div>
            <div v-else-if="minuts_en_borsa <= 0">
              No tens temps en la borsa per a poder fer cap compensació.
            </div>
          </div>
        </div>
        <div v-if="minuts_en_borsa > 0">
          <div class="uk-margin">
            <div class="uk-inline">
              <span class="uk-text-medium"
                >Quants minuts vols trapasat de la borsa d'hores al deute de
                mes?</span
              >
              <input
                class="uk-input uk-inline"
                type="number"
                v-model="minuts_a_compensar"
                :max="minuts_en_borsa"
                min="0"
                step="1"
              />
            </div>
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
      minuts_en_borsa: 0,
      deute_mes: 0,
      minuts_a_compensar: 0,
      id: 0,
    };
  },
  props: ["show-compensahores"],
  watch: {
    showCompensahores() {
      this.mostra_modal();
    },
  },
  methods: {
    minuts_en_borsa_f() {
      axios
        .get("/borsahores/1")
        .then((res) => {
          //console.log(res.data.minuts);
          this.minuts_en_borsa = res.data.minuts;
          //alert(this.minuts_en_borsa);
        })
        .catch((err) => {
          this.$toast.error(err.response.data.message);
        });
    },
    // Surt del modal i reseteja totes les dades
    ix() {
      this.$eventBus.$emit("tanca-compensahores");
      UIkit.modal("#modal_borsahores_compensa" + this.id).hide();
    },

    deute_mes_f() {
      axios
        .get("/deutemes/1")
        .then((res) => {
          //console.log(res.data.minuts);
          this.deute_mes = res.data.minuts;
          //alert(this.minuts_en_borsa);
        })
        .catch((err) => {
          this.$toast.error(err.response.data.message);
        });
    },
    // Surt del modal i reseteja totes les dades
    ix() {
      this.$eventBus.$emit("tanca-compensahores");
      UIkit.modal("#modal_borsahores_compensa" + this.id).hide();
    },

    // Envia el misstage
    envia() {
      //console.log(this.cap.length);
      if (this.minuts_a_compensar > this.minuts_en_borsa) {
        this.$toast.error("No pots demanar tants minuts");
        return 0;
      }

      if (this.minuts_a_compensar == 0) {
        this.$toast.error("Has de demanar un temps");
      } else {
        let url = "minuts_a_compensar_solicitud";
        let params = {
          minuts: this.minuts_a_compensar,
        };
        axios
          .post(url, params)
          .then((res) => {
            //console.log(res);
            //this.resposta=res.data;
            this.minuts_en_borsa = res.data.nou_borsa_hores;
            this.deute_mes = res.data.nou_deute_mes;
            this.$toast.success("Pareix que tot ha anat bé");
          })
          .catch((err) => {
            this.$toast.error(err.response.data.message);

            //console.error(err);
          });
      }
    },
    // Mostra modal en funció del bus
    mostra_modal() {
      if (this.showCompensahores == true) {
        UIkit.modal("#modal_borsahores_compensa" + this.id, {
          bgClose: false,
          escClose: false,
          modal: false,
          keyboard: false,
        }).show();
        this.minuts_en_borsa_f();
        this.deute_mes_f();
      } //else {
      //   UIkit.modal("#modal_borsahores_compensa").hide();
      // }
    },
    // Obre el missatge
    obre_missatge(envia) {
      this.cap = envia.assumpte;
      this.destinatari = envia.destinatari;
      UIkit.modal("#modal_borsahores_compensa").show();
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
