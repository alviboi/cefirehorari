<template>
      <div id="modal_incidencia" uk-modal>

    <div style="background-color: #f7f7f7" class="uk-modal-dialog uk-modal-body">
                <fieldset class="uk-fieldset">
                    <legend class="uk-legend">Escriu incidència</legend>
                    <div class="uk-margin">
                        <input v-model="incidencia" class="uk-input" type="text" placeholder="Motiu de la incidència">
                    </div>
                        <div class="uk-margin">
                            <Datepicker
                            :language="ca"
                            :monday-first="true"
                            v-model="dia_inc"
                            placeholder="Escollix data de la incidència"
                            input-class="uk-input"
                            >
                            </Datepicker>
                        </div>
                        <div class="uk-margin">                        
                        <label>Hora: </label>
                            <vue-timepicker :minute-interval="1" v-model="inici"></vue-timepicker>
                        <span> a </span>
                            <vue-timepicker :minute-interval="1" v-model="fi"></vue-timepicker> 
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
            dia_inc: new Date(),
            inici: "09:00",
            fi: "14:00",
            incidencia: ""
        }
        // props: ['show-incidencia']
    },
    props: ['show-incidencia'],
    watch: {
        showIncidencia(){
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
            this.$eventBus.$emit('tanca-incidencia');
            this.resposta="";


        },
        // Envia la informació emplenada
        envia() {
            console.log(this.incidencia.length+" "+this.inici.length+" "+this.fi.length);
            if (this.incidencia.length===0 || this.inici.length===0 || this.fi.length===0) {
                this.$toast.error("Falta algun paràmetre");
            } else {
                let url="incidencies";
                var dia_env = data_db(this.dia_inc);
                let params = {
                    incidencia: this.incidencia,
                    data: dia_env,
                    inici: this.inici,
                    fi: this.fi
                }
                axios.post(url,params)
                .then(res => {
                    console.log(res);
                    this.$toast.success("Incidència registrada");
                    this.$eventBus.$emit('incidencia-enviada');
                    this.incidencia="";
                })
                .catch(err => {
                    console.error(err);
                })
            }
        },
        // Mostra el modal en funció del que diga l'event
        mostra_modal() {
            if (this.showIncidencia == true) {
                UIkit.modal('#modal_incidencia',{ bgClose: false, escClose: false, modal: false, keyboard:false}).show();
            } else {
                UIkit.modal('#modal_incidencia').hide();

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
