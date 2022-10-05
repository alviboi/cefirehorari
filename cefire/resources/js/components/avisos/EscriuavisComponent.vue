<template>
      <div id="modal_avis" uk-modal>

    <div style="background-color: #f7f7f7" class="uk-modal-dialog uk-modal-body">
                <fieldset class="uk-fieldset">
                    <legend class="uk-legend">Escriu avís de primera pàgina</legend>
                    <div class="uk-margin">
                        <input v-model="cap" class="uk-input" type="text" placeholder="Capçalera de l'avís">
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
                    <button :key="1+id" @click.prevent="ix" class="uk-button uk-button-default" type="button">Cancel·la</button>
                    <button :key="2+id" @click.prevent="envia" class="uk-button uk-button-primary" type="button">Envia</button>
                    </transition-group>
                </p>
    </div>
     </div>
</template>

<script>
/**
Aques component crea un modal per a escriure l'avís

 */
export default {
    data() {
        return {
            id: null,
            cap: "",
            avis: "",
            resposta: ""
        }
        // props: ['show-modal']
    },
    props: ['show-modal'],
    watch: {
        showModal(){
            this.mostra_modal();
        }
    },

    methods: {
        // Botó ixir del modal. Envia event per a tancar-los
        ix() {
            this.$eventBus.$emit('tanca-avis');
            this.resposta="";
            this.cap="";
            this.avis="";

        },
        // Envia la informació emplenada
        envia() {
            console.log(this.cap.length);
            if (this.cap.length===0 || this.avis.length===0) {
                this.resposta="Falta algun paràmetre per emplenar";
            } else {
                let url="avisos";
                let params = {
                    cap: this.cap,
                    avis: this.avis
                }
                axios.post(url,params)
                .then(res => {
                    console.log(res);
                    this.resposta=res.data;
                    this.$eventBus.$emit('avis-enviat');
                    this.cap="";
                    this.avis="";
                })
                .catch(err => {
                    console.error(err);
                })
            }
        },
        // Mostra el modal en funció del que diga l'event
        mostra_modal() {
            if (this.showModal == true) {
                UIkit.modal('#modal_avis',{ bgClose: false, escClose: false, modal: false, keyboard:false}).show();
            } else {
                UIkit.modal('#modal_avis').hide();

            }
        }
    },
    mounted() {
        this.id = this._uid;
    },


}
</script>

<style>

</style>
