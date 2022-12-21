<template>
  <div>
    <transition-group name="list3" tag="div">

      <div v-for="item in users" :key="item.id">
          <div class="llistat">
              <div class="id">
                  <span uk-icon="icon: home"></span>
                  {{ item.id }}
              </div>
              <div class="nom">
                  <span data-uk-icon="icon: user"></span>
                  <b>{{ item.name }}</b>
              </div>
              <div class="mail">
                  <span data-uk-icon="icon: mail"></span>
                  {{ item.email }}
              </div>
              <div class="perfil">
                  <span data-uk-icon="icon: world"></span>
                  <span v-if=" item.Perfil == 1">Admin</span>
                  <span v-else-if=" item.Perfil == 0">Assessor</span>
                  <span v-else>Altres</span>
              </div>
              <div class="moscosos">
                  <span title="Moscosos" uk-tooltip><i class="fa-solid fa-champagne-glasses" ></i></span>
                  {{ item.moscosos }}
              </div>
              <div class="vacances">
                  <span title="Vacances" uk-tooltip><i class="fa-solid fa-plane-departure"></i></span>
                  {{ item.vacances }}
              </div>
              <div class="rfid">
                  <span data-uk-icon="icon: credit-card"  title="Núm. Sèrie targeta" uk-tooltip ></span>
                  {{ item.rfid }}
              </div>
              <div class="botons">
                <div @click.prevent="edita(item.id)" class="uk-icon-button" uk-icon="file-edit"></div>
                <div @click.prevent="borra(item.id)" class="uk-icon-button uk-text-danger" uk-icon="trash"></div>
              </div>
          </div>
      </div>
    </transition-group>

    <div :id="'edita'+this.id" uk-modal>
        <div class="uk-modal-dialog">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <div class="uk-modal-header">
                <h2 class="uk-modal-title">Edita Assessor</h2>
            </div>
            <div class="uk-modal-body bac">

                    <div class="uk-margin">
                        <div class="uk-inline" title="Identificador assessor" uk-tooltip >
                            <span class="uk-form-icon" uk-icon="icon: sign-in"></span>
                            <input :value="edita_u.id" class="uk-input" type="text" placeholder="id" disabled>
                        </div>
                    </div>

                    <div class="uk-margin" title="Nom de l'assessor" uk-tooltip >
                        <div class="uk-inline">
                            <span class="uk-form-icon" uk-icon="icon: user"></span>
                            <input v-model="edita_u.nom" class="uk-input" type="text" placeholder="Nom">
                        </div>
                    </div>

                    <div title="Email i nom de l'usuari" uk-tooltip  class="uk-margin">
                        <div class="uk-inline">
                            <span class="uk-form-icon" uk-icon="icon: mail"></span>
                            <input v-model="edita_u.mail" class="uk-input" type="text" placeholder="Mail">
                        </div>
                    </div>
                    <div title="Perfil, 1 Administrador, 2 Assessor" uk-tooltip  class="uk-margin">
                        <div class="uk-inline">
                            <span class="uk-form-icon" uk-icon="icon: world"></span>
                            <input v-model="edita_u.perfil" class="uk-input" type="number" min="0" max="3" step="1" placeholder="Perfil">
                        </div>
                    </div>
                    <div title="Dies moscosos" uk-tooltip  class="uk-margin">
                        <div class="uk-inline">
                            <span class="uk-form-icon"><i class="fa-solid fa-champagne-glasses"></i></span>
                            <input v-model="edita_u.moscosos" class="uk-input" type="number" min="0" step="1" placeholder="moscosos">
                        </div>
                    </div>
                    <div title="Dies de vacances" uk-tooltip  class="uk-margin">
                        <div class="uk-inline">
                            <span class="uk-form-icon"><i class="fa-solid fa-plane-departure"></i></span>
                            <input v-model="edita_u.vacances" class="uk-input" type="number" min="0" step="1" placeholder="vacances">
                        </div>
                    </div>
                    <div title="Número de sèrie de la targeta identificadora" uk-tooltip  class="uk-margin">
                        <div class="uk-inline">
                            <span class="uk-form-icon" uk-icon="icon: credit-card"></span>
                            <input v-model="edita_u.rfid" class="uk-input" type="text" placeholder="rfid">
                        </div>
                    </div>
                    <div title="Reducció horària setmanal" uk-tooltip class="uk-margin">
                        <div class="uk-inline">
                            <input v-model="edita_u.reduccio" type="checkbox" placeholder="rfid">
                            <span>Reducció horària</span>
                        </div>
                    </div>
            </div>
            <div class="uk-modal-footer uk-text-right">
                <button class="uk-button uk-button-default uk-modal-close" type="button">Cancel</button>
                <button class="uk-button uk-button-primary" type="button" @click="envia_edit()">Save</button>
            </div>
        </div>
    </div>


  </div>
</template>

<script>
/**
 * Component que mostra les dades de tots els assessors
 */
export default {
    data() {
        return {
            users: [],
            update: "",
            edita_u:  {
                id: "",
                nom: "",
                mail: "",
                perfil: 0,
                moscosos: 0,
                vacances: 0,
                rfid: "",
                reduccio: false
            }
        }
    },
    methods: {
        // Actualitxa assessor
        envia_edit(){
            axios.put("user/"+this.edita_u.id,this.edita_u)
            .then(res => {
                console.log(res);
                this.agafa_users();
                this.$toast.success("Dades guardades correctament");
            })
            .catch(err => {
                console.error(err);
            })
        },
        // Borra assessor
        borra(id){
            let url = "user/" + id;
            axios.delete(url)
            .then(res => {
                console.log(res);
                this.agafa_users();
            })
            .catch(err => {
                console.error(err);
            })
        },
        // Edita assessor
        edita(id){
            this.edita_u = {};
            let results = this.users.filter((item => item.id == id));
            this.edita_u.reduccio=(results[0].reduccio==1)?true:false;
            this.edita_u.id=results[0].id;
            this.edita_u.nom=results[0].name;
            this.edita_u.mail=results[0].email;
            this.edita_u.perfil=results[0].Perfil;
            this.edita_u.rfid=results[0].rfid;
            this.edita_u.moscosos=results[0].moscosos;
            this.edita_u.vacances=results[0].vacances;
            
            UIkit.modal("#edita"+this.id).show();
        },
        // Agafa usuaris
        agafa_users(){
            this.users = [];
            axios.get("user")
            .then(res => {
                console.log(res);
                this.users=res.data;
            })
            .catch(err => {
                console.error(err);
            })
        },

    },
    mounted() {
        this.id = this._uid;
        this.agafa_users();
    },
}
</script>

<style lang="sass" scope>
$fondo: #f1faee
.bac
    background-color: $fondo
.uk-inline
    width: 100%
.llistat
    display: grid
    grid-template-columns: 0.3fr 1.2fr 1fr 0.5fr 0.3fr 0.3fr 0.7fr 0.3fr
    grid-template-rows: 1fr
    gap: 0px 0px
    grid-template-areas: "id nom mail perfil moscosos vacances rfid botons"
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
    .id
        @extend comu
        grid-area: id
    .nom
        @extend comu
        grid-area: nom
    .mail
        @extend comu
        grid-area: mail
    .perfil
        grid-area: perfil
    .moscosos
        @extend comu
        grid-area: moscosos
    .vacances
        @extend comu
        grid-area: vacances
    .rfid
        @extend comu
        grid-area: rfid
    .botons
        @extend comu
        grid-area: botons
        display: flex
        align-items: right
        text-align: right



</style>
