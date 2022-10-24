<template>
    <div id="modal_vacances" uk-modal>

    <div style="background-color: #f7f7f7" class="uk-modal-dialog uk-modal-body">
                <fieldset class="uk-fieldset">
                    <legend class="uk-legend">Escollix els dies de vacances</legend>
                    <!-- <div class="uk-margin">
                        <input v-model="moscoso" class="uk-input" type="text" placeholder="Motiu de la incidència">
                    </div> -->
                    <hr>
                    <div class="uk-child-width-1-2@s" uk-grid>
                        <div>
                        Des de:
                        <Datepicker
                            :language="ca"
                            :monday-first="true"
                            v-model="dia_vac1"
                            placeholder="Des de"
                            input-class="uk-input"
                            ></Datepicker>
                        </div>
                        
                        <div>
                            Fins a:
                            <Datepicker
                            :language="ca"
                            :monday-first="true"
                            v-model="dia_vac2"
                            placeholder="fins a"
                            input-class="uk-input"
                            >
                            </Datepicker>
                        </div>
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
            dia_vac1: new Date(),
            dia_vac2: new Date()
        }
        // props: ['show-moscoso']
    },
    props: ['show-vacances'],
    watch: {
        showVacances(){
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
            this.$eventBus.$emit('tanca-vacances');
            this.resposta="";
            this.cap="";
            this.avis="";

        },
        // Envia la informació emplenada
        envia() {

                let url="vacances";
                var dia_env1 = data_db(this.dia_vac1);
                var dia_env2 = data_db(this.dia_vac2);
                let params = {
                    dia_inici: this.dia_vac1,
                    dia_fi: this.dia_vac2,
                }
                axios.post(url,params)
                .then(res => {
                    console.log(res);
                    this.$toast.success("Vacances registrades");
                    this.$eventBus.$emit('vacances-enviat');
                    //this.vacances="";
                    this.dia_vac1="";
                    this.dia_vac2="";
                })
                .catch(err => {
                    this.$toast.error(err.response.data.message);
                    console.error(err);
                });
        },
        // Mostra el modal en funció del que diga l'event
        mostra_modal() {
            if (this.showVacances == true) {
                UIkit.modal('#modal_vacances',{ bgClose: false, escClose: false, modal: false, keyboard:false}).show();
            } else {
                UIkit.modal('#modal_vacances').hide();

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
