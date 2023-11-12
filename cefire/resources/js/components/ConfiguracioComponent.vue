<template>
  <div>
    <div class="uk-child-width-1-2@s uk-grid-match" uk-grid>
      <div>
        <div class="uk-card uk-card-secondary uk-card-hover uk-card-body">
          <h3 class="uk-card-title">LDAP</h3>
          <label class="uk-form-label" for="form-stacked-text"
            >Introdueix la clau de netadmin del servidor LliureX</label
          >
          <div class="uk-margin">
            <input
              v-model="netadmin"
              class="uk-input"
              type="password"
              placeholder="Contrasenya netadmin"
            />
          </div>
          <label class="uk-form-label" for="form-stacked-text"
            >Introdueix la contrasenya</label
          >
          <div class="uk-margin">
            <input
              v-model="servidor"
              class="uk-input"
              type="text"
              placeholder="IP del servidor"
            />
          </div>
          <div class="uk-margin">
            <button
              @click="get_usuaris_ldap"
              class="uk-button uk-button-primary"
            >
              Agafa usuaris
            </button>
          </div>
        </div>
      </div>

      <div>
        <div class="uk-card uk-card-default uk-card-hover uk-card-body">
          <h3 class="uk-card-title">Comportament fitxatge</h3>
          Habilitar des de l'aparell significa que al fitxar tant des de
          l'ordinador com de l'aparell quedarà enregistrat amb l'hora que s'ha
          fet. S'ha d'informar a tots els assessors que s'ha habilitat aquesta
          opció. Ja que és possible que fitxen amb un dia tota la setmana i no
          quede enregistrada l'hora correcta d'entrada.
          <div class="uk-margin">
            <div class="uk-form-controls uk-form-controls-text">
              <label
                ><input
                  v-model="fitxar_aparell"
                  @click="canvia_dades('aparell')"
                  class="uk-checkbox"
                  type="checkbox"
                />
                Habilita fitxar desde aparell</label
              >
            </div>
          </div>
        </div>
      </div>

      <div>
        <div class="uk-card uk-card-default uk-card-hover uk-card-body">
          <h3 class="uk-card-title">Habilitar registre</h3>
          Aquesta funció habilita el registre d'Assessors. Normalment ho has
          d'habilitar al començament del curs per a que els nous assessors es
          donen ells d'alta.
          <div class="uk-margin">
            <div class="uk-form-controls uk-form-controls-text">
              <label
                ><input
                  v-model="habilita_registre"
                  @click="canvia_dades('registra')"
                  class="uk-checkbox"
                  type="checkbox"
                />
                Habilita registre</label
              >
            </div>
          </div>
        </div>
      </div>
      <div>
        <div class="uk-card uk-card-secondary uk-card-hover uk-card-body">
          <h3 class="uk-card-title">Configuració compensacions</h3>
          Limita el número de compensacions per dia que es poden agafar
          <div class="uk-margin">
            <div class="uk-form-controls uk-form-controls-text">
              <!-- <label><input v-model="habilita_registre" @click="canvia_dades('registra')" class="uk-checkbox" type="checkbox" > Habilita registre</label> -->
              <div class="uk-margin">
                <input
                  v-model="max_compensacions"
                  class="uk-input"
                  type="number"
                  step="1"
                  placeholder="Número de compensacions"
                  min="0"
                  max="9"
                />
              </div>
              <div class="uk-margin">
                <button
                  @click="canvia_dades('max_compensacions')"
                  class="uk-button uk-button-primary"
                >
                  Actualitza
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    
    <!-- AFEGIR MINUTS A LA BORSA DE COMPENSACIONS -->
    <div>
      <div class="uk-card uk-card-secondary uk-card-hover uk-card-body">
        <h3 class="uk-card-title">Afegix minuts a borsa d'hores</h3>
        Afegix minuts a una borsa per decisió de la direcció.
        <div class="uk-margin">
          <div class="uk-margin">
            <label for="seleccio">Selecciona assessor</label>
            <select
              id="seleccio"
              v-model="busca_ass"
              class="uk-select"
              aria-label="Select"
            >
              <option v-for="(user, key) in users" :key="key" :value="user.id">
                {{ user.name }}
              </option>
            </select>
          </div>
          <input
            v-model="minuts_ass"
            class="uk-input"
            type="number"
            name=""
            id=""
          />
          <p class="uk-text-right">
            <button
              @click.prevent="afegix_minuts_admin"
              class="uk-button uk-button-primary"
              type="button"
            >
              Afegix
            </button>
          </p>
        </div>
      </div>
    </div>
    </div>

    <div id="usuaris_ldap" uk-modal>
      <div class="uk-modal-dialog uk-modal-body">
        <h2 class="uk-modal-title">Usuaris importables</h2>
        <div class="uk-margin uk-overflow-auto">
          <p>
            Els següents usuaris que es poden importar a l'aplicació de
            fitxatges són (aquells usuaris que el nom d'usuari no siga un correu
            electrònic no es poden importar):
          </p>
        </div>
        <div class="uk-margin">
          <template v-for="(item, index) in usuaris">
            <div v-if="item.email.includes('@')" :key="item.id">
              <div class="item">
                <div class="nom">
                  <span uk-icon="user"></span>{{ item.name }}
                </div>
                <div class="mail">
                  <span uk-icon="mail"></span>{{ item.email }}
                </div>
                <div class="che">
                  <input
                    class="uk-checkbox"
                    value="false"
                    v-model="importa[index]"
                    type="checkbox"
                  />
                </div>
              </div>
            </div>
          </template>
        </div>
        <p class="uk-text-right">
          <button
            class="uk-button uk-button-default uk-modal-close"
            type="button"
          >
            Tanca
          </button>
          <button
            class="uk-button uk-button-primary"
            @click="importa_f"
            type="button"
          >
            Importa
          </button>
        </p>
      </div>
    </div>
  </div>
</template>

<script>
/**
 * Component per a mostrar el paràmetres de configuració de l'aplicació.
 */
export default {
  data() {
    return {
      usuaris: {},
      habilita_registre: null,
      netadmin: "",
      servidor: "",
      fitxar_aparell: null,
      max_compensacions: 0,
      importa: [],
      users: [],
      minuts_ass: 0,
      busca_ass: "",

    };
  },
  methods: {
    // Importa usuaris del LDAP
    importa_f() {
      let dades_enviar = [];
      for (let index = 0; index < this.importa.length; index++) {
        if (this.importa[index] !== undefined && this.importa[index] == true) {
          dades_enviar.push(this.usuaris[index]);
        }
      }
      axios
        .post("user", dades_enviar)
        .then((res) => {
          console.log(res);
          this.$toast.success(res.data);
        })
        .catch((err) => {
          console.error(err);
          this.$toast.error(err.message);
        });
    },
    // Agafa usuaris del LDAP d'un servidor lliurex
    get_usuaris_ldap() {
      let params = {
        contrasenya: this.netadmin,
        ip: this.servidor,
      };
      axios
        .post("get_usuaris_ldap", params)
        .then((res) => {
          console.log(res);
          this.usuaris = res.data;
          UIkit.modal("#usuaris_ldap").show();
        })
        .catch((err) => {
          console.error(err);
          this.$toast.error(err.message);
        });
    },
    // Agafa les dades de configuració
    get_configuracio() {
      axios
        .get("control")
        .then((res) => {
          this.fitxar_aparell = res.data.aparell;
          this.habilita_registre = res.data.registra;
          this.max_compensacions = res.data.max_compensacions;
          console.log(res);
        })
        .catch((err) => {
          this.$toast.error(err.message);
          console.error(err);
        });
    },
    // Actualitza les dades de configiració
    canvia_dades(nom) {
      var envia = false;
      if (nom == "aparell") {
        envia = this.fitxar_aparell ? 0 : 1;
      } else if (nom == "max_compensacions") {
        envia = this.max_compensacions;
      } else {
        envia = this.habilita_registre ? 0 : 1;
      }
      let params = {};
      params[nom] = envia;
      axios
        .put("control/1", params)
        .then((res) => {
          console.log(res);
          this.$toast.success(res.data);
        })
        .catch((err) => {
          console.error(err);
          this.$toast.error(err.response.data);
        });
    },
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
    afegix_minuts_admin() {
      axios
        .post("afegix_minuts_admin", {
          id: this.busca_ass,
          minuts: this.minuts_ass,
        })
        .then((res) => {
          this.$toast.success(res.data);
          this.busca_ass = "";
          this.minuts_ass = 0;
          console.log(res);
        })
        .catch((err) => {
          console.log(err);
          this.$toast.error(err.response.data);
        });
    },
  },
  mounted() {
    this.get_configuracio();
    this.agafa_users();
  },
};
</script>

<style lang="sass" scope>
.item
    border: 1px solid grey
    margin: 5px
    padding: 5px
    border-radius: 5px
    display: grid
    grid-template-columns: 1.4fr 1.4fr 0.2fr
    grid-template-rows: 1fr
    gap: 5px 5px
    grid-template-areas: "nom mail che"
    &.nom
        grid-area: a
    &.mail
        grid-area: b
    &.che
        grid-area: c
        float: right
        align-content: center
        text-align: center
        padding: 2px
</style>
