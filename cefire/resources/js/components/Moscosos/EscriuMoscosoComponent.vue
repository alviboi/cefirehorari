<template>
      <div id="modal_moscoso" uk-modal>

    <div style="background-color: #f7f7f7" class="uk-modal-dialog uk-modal-body">
                <fieldset class="uk-fieldset">
                    <legend class="uk-legend">Escollix moscós</legend>
                    <!-- <div class="uk-margin">
                        <input v-model="moscoso" class="uk-input" type="text" placeholder="Motiu de la incidència">
                    </div> -->
                        <div class="uk-margin">
                            <Datepicker
                            :language="ca"
                            :monday-first="true"
                            v-model="dia_mosc"
                            placeholder="Escollix data on vols el moscós"
                            input-class="uk-input"
                            >
                            </Datepicker>
                        </div>
                        <!-- <div class="uk-margin">                        
                        <label>Hora: </label>
                            <vue-timepicker :minute-interval="15" v-model="inici"></vue-timepicker>
                        <span> a </span>
                            <vue-timepicker :minute-interval="15" v-model="fi"></vue-timepicker> 
                        </div> -->
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

import Datepicker from "vuejs-datepicker";
import { ca } from "vuejs-datepicker/dist/locale";
import VueTimepicker from 'vue2-timepicker'
import 'vue2-timepicker/dist/VueTimepicker.css'
export default {
    data() {
        return {
            id: null,
            resposta: "",
            ca: ca,
            dia_mosc: new Date(),
        }
        // props: ['show-moscoso']
    },
    props: ['show-moscoso'],
    watch: {
        showMoscoso(){
            this.mostra_modal();
        }
    },
    components: {
        VueTimepicker,
        Datepicker,
        
    },
    methods: {
        // Botó ixir del modal. Envia event per a tancar-los
        ix() {
            this.$eventBus.$emit('tanca-moscoso');
            this.resposta="";
            this.cap="";
            this.avis="";

        },
        // Envia la informació emplenada
        envia() {

                let url="moscosos";
                var dia_env = data_db(this.dia_mosc);
                let params = {
                    moscoso: this.moscoso,
                    data: dia_env,
                }
                axios.post(url,params)
                .then(res => {
                    console.log(res);
                    this.$toast.success("Moscós registrat");
                    this.$eventBus.$emit('moscoso-enviat');
                    this.moscoso="";
                    this.inici="";
                    this.fi="";
                })
                .catch(err => {
                    this.$toast.error(err.response.data.message);
                    console.error(err);
                });
        },
        // Mostra el modal en funció del que diga l'event
        mostra_modal() {
            if (this.showMoscoso == true) {
                UIkit.modal('#modal_moscoso',{ bgClose: false, escClose: false, modal: false, keyboard:false}).show();
            } else {
                UIkit.modal('#modal_moscoso').hide();

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
