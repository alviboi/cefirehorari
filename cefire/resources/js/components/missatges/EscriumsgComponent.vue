<template>
<div id="modal_missatge" uk-modal>

    <div style="background-color: #f7f7f7" class="uk-modal-dialog uk-modal-body">
                <fieldset class="uk-fieldset">
                    <legend class="uk-legend">Escriu missatge</legend>
                    <div class="uk-margin">
                        <input v-model="cap" class="uk-input" type="text" placeholder="Assumpte">
                    </div>
                    <div class="uk-margin">
                        <label class="uk-form-label" for="form-stacked-select">Destinatari/a</label>
                        <select v-model="destinatari" class="uk-select" placeholder="Destinatari/a" :value="destinatari">
                            <option v-for="(user, key) in users" :key="key" :value="user.name">{{user.name}}</option>
                        </select>
                    </div>
                    <div class="uk-margin">
                        <textarea v-model="avis" class="uk-textarea" rows="5" placeholder="Missatge que vols escriure"></textarea>
                    </div>
                </fieldset>
                <transition name="fade">
                <div v-if="resposta">
                    {{resposta}}
                </div>
                </transition>

                <div>

                </div>
                <p class="uk-text-right">
                    <transition-group name="list-complete2">
                    <button :key="1+id" @click.prevent="ix" class="uk-button uk-button-default uk-button-close" type="button">Cancel·la</button>
                    <button :key="2+id" @click.prevent="envia" class="uk-button uk-button-primary" type="button">Envia</button>
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
            results: [],
            user_id: null,
            destinatari: "",
            users: [],
            id: null,
            cap: "",
            avis: "",
            resposta: ""
        }
    },
    props: ['show-missatge'],
    watch: {
        showMissatge(){
            this.mostra_modal();
        }
    },
    methods: {
        // Agafa tots els usuaris
        agafa_users(){
            axios.get("user")
            .then(res => {
                console.log(res);
                this.users=res.data;
            })
            .catch(err => {
                console.error(err);
            })
        },
        // Surt del modal i reseteja totes les dades
        ix() {
            this.$eventBus.$emit('tanca-missatge');
            UIkit.modal('#modal_missatge').hide();
            this.resposta="";
            this.cap="";
            this.avis="";
            this.destinatari="";
        },
        // filtra els elements
        filterResults() {
            this.results = this.users.filter((item => item.name.toLowerCase() == this.destinatari.toLowerCase()));
            return this.results[0].id;
        },
        // Envia el misstage
        envia() {
            console.log(this.cap.length);
            if (this.cap.length===0 || this.avis.length===0) {
                this.resposta="Falta algun paràmetre per emplenar";
            } else {
                this.user_id = this.filterResults();
                let url="notificacions";
                let params = {
                    cap: this.cap,
                    user_id: this.user_id,
                    avis: this.avis
                }
                axios.post(url,params)
                .then(res => {
                    console.log(res);
                    this.resposta=res.data;
                    this.destinatari="";
                    this.cap="";
                    this.avis="";
                })
                .catch(err => {
                    console.error(err);
                })
            }
        },
        // Mostra modal en funció del bus
        mostra_modal() {
            if (this.showMissatge == true) {
                UIkit.modal('#modal_missatge',{ bgClose: false, escClose: false, modal: false, keyboard:false}).show();
            } else {
                UIkit.modal('#modal_missatge').hide();

            }
        },
        // Obre el missatge
        obre_missatge(envia) {
            this.cap=envia.assumpte;
            this.destinatari=envia.destinatari;
            UIkit.modal('#modal_missatge').show();
        },
    },
    mounted() {
        this.agafa_users();
        this.id = this._uid;
    },

    created() {
        // Crea event per a obrir el missatge
        this.$eventBus.$on('missatge-variables', this.obre_missatge);
    },
    beforeDestroy() {
        this.$eventBus.$off('missatge-variables');
    }
}
</script>

<style>

</style>
