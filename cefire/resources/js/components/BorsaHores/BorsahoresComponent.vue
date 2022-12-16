<template>
  <div id="modal_borsahores" uk-modal>
    <div
      style="background-color: #f7f7f7"
      class="uk-modal-dialog uk-modal-body"
    >
      <fieldset class="uk-fieldset">
        <legend class="uk-legend">Sol·licitud afegir a borsa hores</legend>
        <div class="uk-margin">
          <div class="uk-text-medium">
            Tens un excés de
            <span>{{ minuts_sobrants_mes_anterior / 60 }}</span> hores el més
            anterior.
          </div>
        </div>
        <div class="uk-margin">
          <div class="uk-inline">
            <span class="uk-text-medium"
              >Nombre de minuts que has fet de més(no has de posar el número de minuts multiplicat))</span
            >
            <input
              class="uk-input uk-inline"
              type="text"
              v-model="minuts_a_afegir"
            />
          </div>
        </div>
        <div class="uk-margin">
          <label for="just">Justifica perquè fas aquesta sol·licitud:</label>
          <textarea
            id="just"
            v-model="justificacio"
            class="uk-textarea"
            rows="5"
            placeholder="Justificació"
          ></textarea>
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
      minuts_sobrants_mes_anterior: 0,
      minuts_a_afegir: 0,
      justificacio: "",
    };
  },
  props: ["show-borsahores"],
  watch: {
    showBorsahores() {
      this.mostra_modal();
    },
  },
  methods: {
    // Surt del modal i reseteja totes les dades
    ix() {
      this.$eventBus.$emit("tanca-borsahores");
      //UIkit.modal('#modal_borsahores').hide();
      this.minuts_sobrants_mes_anterior = 0;
      this.minuts_a_afegir = 0;
    },

    // Envia el misstage
    envia() {
      //console.log(this.cap.length);
      if (this.minuts_a_afegir == 0 || this.justificacio.length === 0) {
        this.$toast.error("Falta algun paràmetre per emplenar");
      } else {
        let url = "borsasolicituds";
        let params = {
          minuts_a_afegir: this.minuts_a_afegir,
          justificacio: this.justificacio,
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
      }
    },
    // Mostra modal en funció del bus
    mostra_modal() {
      if (this.showBorsahores == true) {
        UIkit.modal("#modal_borsahores", {
          bgClose: false,
          escClose: false,
          modal: false,
          keyboard: false,
        }).show();
      } else {
        UIkit.modal("#modal_borsahores").hide();
      }
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
