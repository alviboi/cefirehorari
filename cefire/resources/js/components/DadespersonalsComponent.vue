<template>
<div>
            <div class="top">
                <Datepicker
                :language="ca"
                :monday-first="true"
                placeholder="Desde"
                @selected="busca_desde"
                value=""
                :clear-button="true"
                clear-button-icon="fa fa-times"
                @cleared="borra_desde"
                >
                </Datepicker>
                <span > a </span>
                <Datepicker
                :language="ca"
                :monday-first="true"
                placeholder="Fins"
                @selected="busca_fins"
                value=""
                :clear-button="true"
                clear-button-icon="fa fa-times"
                @cleared="borra_fins"
                >
                </Datepicker>
            </div>
            <hr>
        <div>
            <br>
            <div v-if="cefire[0] !== undefined">
            <h3>CEFIRE</h3>
            <hr>
            <div v-for="item in cefire" :key="item.id" class="top">
                <div v-for="(it, name) in item" :key="name">| <b>{{name}}</b>: {{it}} |</div>
            </div>
            </div>
            <br>
            <div v-if="compensa[0] !== undefined">
            <h3>COMPENSA</h3>
            <hr>
            <div v-for="item in compensa" :key="item.id" class="top">
                <div v-for="(it, name) in item" :key="name">| <b>{{name}}</b>: {{it}} |</div>
            </div>
            </div>
            <br>
            <div v-if="curs[0] !== undefined">
            <h3>CURS</h3>
            <hr>
            <div v-for="item in curs" :key="item.id" class="top">
                <div v-for="(it, name) in item" :key="name">| <b>{{name}}</b>: {{it}} |</div>
            </div>
            </div>
            <br>
            <div v-if="guardia[0] !== undefined">
            <h3>GUARDIA</h3>
            <hr>
            <div v-for="item in guardia" :key="item.id" class="top">
                <div v-for="(it, name) in item" :key="name">| <b>{{name}}</b>: {{it}} |</div>
            </div>
            </div>
            <br>
            <div v-if="permis[0] !== undefined">
            <h3>PERMIS</h3>
            <hr>
            <div v-for="item in permis" :key="item.id" class="top">
                <div v-for="(it, name) in item" :key="name">| <b>{{name}}</b>: {{it}} |</div>
            </div>
            </div>

        </div>
</div>
</template>

<script>
/**
 * Bolcat de totes les dades de l'assessor, s'utilitza per a temes de depuració
 */
import Datepicker from "vuejs-datepicker";
import { ca } from "vuejs-datepicker/dist/locale";
export default {
    data() {
        return {
            cefire: {},
            compensa: {},
            curs: {},
            guardia: {},
            permis: {},
            ca: ca,
            desde: "",
            fins: ""
        }
    },
    methods: {
        get_datos(){
            axios.get("user_all/"+this.desde+"/"+this.fins)
            .then(res => {
                console.log(res);
                this.cefire=res.data.cefire;
                this.compensa=res.data.compensa;
                this.curs=res.data.curs;
                this.guardia=res.data.guardia;
                this.permis=res.data.permis;
            })
            .catch(err => {
                console.error(err);
            })
        },
        busca_desde(a){
            this.desde=data_db(a);
            if(this.fins != ""){
                this.get_datos();
            }
        },
        busca_fins(a){
            this.fins=data_db(a);
            if(this.desde != ""){
                this.get_datos();
            }
        },
        borra_desde(){
            this.desde="";
            this.cefire={};
            this.compensa= {};
            this.curs= {};
            this.guardia= {};
            this.permis= {};
        },
        borra_fins(){
            this.fins="";
            this.cefire={};
            this.compensa= {};
            this.curs= {};
            this.guardia= {};
            this.permis= {};
        }
    },
    components: {
        Datepicker,
    },
    mounted() {
        // this.get_usuarios();
    },
}
</script>

<style lang="sass" scope>
.top > *
    display: inline-block
</style>
