<template>
	<div>

        <div class="uk-grid-small uk-child-width-expand uk-margin" uk-grid>
            <div class="uk-width-1-4">
                <h3>Elements a buscar</h3>
            </div>
            <div class="">
                <label><input v-model="cefire" class="uk-checkbox" type="checkbox"> Fitxatge</label>
                <label><input v-model="compensa" class="uk-checkbox" type="checkbox"> Compensa</label>
                <label><input v-model="curs" class="uk-checkbox" type="checkbox"> Curs</label>
                <label><input v-model="guardia" class="uk-checkbox" type="checkbox"> Guardia</label>
                <label><input v-model="permis" class="uk-checkbox" type="checkbox"> Permis</label>
                <label><input v-model="visita" class="uk-checkbox" type="checkbox"> Com. Serv.</label>
                <label><input v-model="vacances" class="uk-checkbox" type="checkbox"> Vacances</label>
                <label><input v-model="moscosos" class="uk-checkbox" type="checkbox"> Moscosos</label>
            </div>
        </div>




        <div  class="calendari">
            <calendar-view
                :doEmitItemMouseEvents='false'
                :show-date="dia"
                :items="items"
                :enable-date-selection="false"
                :enableDragDrop="false"
                :display-week-numbers="false"
                :item-top="themeOptions.top"
                :item-content-height="themeOptions.height"
                :item-border-height="themeOptions.border"
                :class="`theme-` + theme"
                :current-period-label="themeOptions.currentPeriodLabel"
                :startingDayOfWeek=1
                @click-item="envia_missatge"
                class="holiday-cef-traditional holiday-cef-official"
            >
                <calendar-view-header
                    slot="header"
                    slot-scope="{ headerProps }"
                    :header-props="headerProps"
                    :previous-period-label="themeOptions.previousPeriodLabel"
                    :next-period-label="themeOptions.nextPeriodLabel"
                    @input="setdia"
                />
            </calendar-view>
        </div>
	</div>
</template>

<script>
/**
 *
 * Aquest component busca a tots els assessor que tenen un element determinat en un mes determinat donat una sèrie d'element que podem
 * fitrar segons el checkbox. s'utilitza el component vue-simple-calendar
 *
 */
import { CalendarView, CalendarViewHeader } from "vue-simple-calendar"
require("vue-simple-calendar/static/css/default.css")
require("vue-simple-calendar/static/css/holidays-us.css")
require("vue-simple-calendar/static/css/gcal.css")
export default {
	components: {
		CalendarView,
		CalendarViewHeader,
	},
	data: function () {
		return {
            cefire: false,
            curs: false,
            compensa: false,
            guardia: false,
            permis: false,
            visita: false,
            vacances: false,
            moscosos: false,
			dia: new Date(),
			theme: "gcal",
			items: Array()
		}
	},
	computed: {
        // Passa les dades de formatació del component
		themeOptions() {
            let ret = {
                result: null,
                index: 0,
                users: [],
                top: "1.6em",
                height: "2em",
                border: "2px",
                previousYearLabel: "<<",
                previousPeriodLabel: "<",
                nextPeriodLabel: ">",
                nextYearLabel: ">>",
                currentPeriodLabel: "",

				};
            return ret;
		},
	},
	methods: {
        // Definix el dia en el que estem a partir de la variable i reseteja tots els checkbox. Ho utilitzem per quan canvien de mes
        setdia(d) {
            this.dia = d;
            this.items= [];
            this.cefire= false;
            this.curs= false;
            this.compensa= false;
            this.guardia= false;
            this.permis= false;
            this.visita= false;
            this.vacances =false;
            this.moscosos=false;

            //this.tots_els_elements_get();
        },
        // Agafar totes les dades per a poder emplenar el calendari segons les dades sol·licitades
        get_element(element){
            var result = []
            let any=this.dia.getFullYear();
            let mes=this.dia.getMonth();
            let url="complet/"+element+"/"+any+"/"+(mes+1)
            axios.get(url)
            .then(res => {
                console.log(res);
                this.emplena_calendari(element,res.data)
            })
            .catch(err => {
                console.error(err);
            });

        },
        /**
         * Emplenem el calendari, per a evitar errors es dona a cada element un rang que determina el seu id, per exemple cefire va desde 1000000 a 2000000,
         * és molt difícil que hi haja 1 milió de fitxatges en un mes, ni controlant tots els assessors del cefire de tota la comunitat...
         * Es determina la clase en la que es mostrarà cada element en funció de què és, i el paràmetre toti indica el element que anem a mostrar en l'ajuda
         * contectual.
         */
        emplena_calendari(element,result) {
            var num=0;
            var clase="custom-date-class-red";
            var toti="id";
            switch (element) {
                case 'cefire':
                    num=1000000;
                    clase="custom-date-class-blue";
                    toti="inici";
                    break;
                case 'compensa':
                    num=2000000;
                    clase="custom-date-class-gray";
                    toti="motiu";
                    break;
                case 'curs':
                    num=3000000;
                    clase="custom-date-class-yellow";
                    toti="curs";
                    break;
                case 'guardia':
                    num=4000000;
                    clase="custom-date-class-red";
                    toti="inici";
                    break;
                case 'permis':
                    num=5000000;
                    clase="custom-date-class-green";
                    toti="motiu";
                    break;
                case 'visita':
                    num=6000000;
                    clase="custom-date-class-pink";
                    toti="centre";
                    break;
                case 'vacances':
                    num=7000000;
                    clase="custom-date-class-black";
                    toti="concepte";
                    break;
                case 'moscosos':
                    num=8000000;
                    clase="custom-date-class-violet";
                    toti="concepte";
                    break;
                default:
                    num=9000000;
                    clase="custom-date-class-red";
                    break;
            }
            /**
             * Es crea un for que anirà omlint l'array de dades que es mostren al calendar
             */
            for (let index = 0; index < result.length; index++) {
                var text = '';
                const ele = result[index];
                // Les hores es convertixen un número per a poder comparar. La data donava errades
                let inici=ele.inici.replace(/:/g,'');
                let inici_int=Number(inici);
                let fi=ele.inici.replace(/:/g,'');
                console.log("Fi: "+fi);
                let fi_int=Number(fi);
                console.log("Fi_int: "+fi_int);
                // Es compara la data per a saber si es matí o vesprada, en funció de si és entre les 5 o les 15
                let mati = (inici_int>=50000 && inici_int<150000) ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
                let fechas = ele.data.split('-');
                // En cas de que l'element a mostrar siga unes hores es mostra el inici i el final
                if (toti == 'inici') {
                    text = ele['inici']+'-'+ele['fi'];
                } else {
                    text = ele['inici']+'-'+ele['fi']+'-'+ele[toti];
                }
                // Es crea l'element ja formatat que anirà al calendari i s'afegix al array
                let i = {
                    id: (ele.id+num),
                    title: "<div id="+(ele.id+num)+" data-uk-tooltip='pos: right; animation: true; offset: 12;' title=\""+text+"\">"+mati+" "+ele.name+"</div>",
                    startDate: Date.UTC(fechas[0], fechas[1]-1, fechas[2]),
                    classes: clase+" uk-animation-scale-up",
                };
                this.items.push(i);
            }
            this.index=result.length;
        },
        // Funció que ens servix per a comprobar a què correspon cada element
        comproba_id_element (id){
            if (id>=1000000) {
                if (id>=2000000) {
                    if (id>=3000000) {
                        if (id>=4000000) {
                            if (id>=5000000) {
                                if (id>=6000000) {
                                    if (id>=7000000) {
                                        if (id>=8000000) {
                                            if (id>=9000000) {
                                                return "Error";
                                            }
                                        return "Moscoso";
                                        }
                                    return "Vacances";
                                    }
                                return "Visita";
                                }
                            return "Permís";
                            }
                        return "Guardia";
                        }
                    return "Curs";
                    }
                return "Compensa";
                }
            return "Fitxatge";
            }
        },
        // Envia un missatge obrint el component modal corresponent enviant la petició al bus d'events
        envia_missatge(calendarItem, windowEvent) {
            var parser = new DOMParser();
            var doc = parser.parseFromString(calendarItem.title, 'text/html');
            let destinatari = doc.body.innerText.trim();
            let data = fecha_format(calendarItem.startDate);
            let id = this.comproba_id_element(Number(doc.all[3].id));
            console.log(id);
            let envia =  {
                destinatari: destinatari,
                assumpte: "En relació al element "+id+" del dia "+ data
            }
            this.$eventBus.$emit('missatge-variables',envia);
        },
        // Creem canal per a actualitzar les guardies en el cas de que algun assessor estiga treballant al mateix temps
        channel_create_cefire(){
            var aux;
            var self=this;
            let chan='CefireAfegidaGeneral';
            channel.bind(chan,
                function(data) {
                    if (self.cefire) {
                        var ele = data.cefire;
                        var num=1000000;
                        var compara = num + ele.id;
                        for (let index = 0; index < self.items.length; index++) {
                            if(self.items[index].id == compara)
                            self.items.splice(index,1);
                        }

                        var clase="custom-date-class-blue";
                        var text = '';
                        // Les hores es convertixen un número per a poder comparar. La data donava errades
                        let inici=ele.inici.replace(/:/g,'');
                        let inici_int=Number(inici);
                        let fi=ele.inici.replace(/:/g,'');
                        console.log("Fi: "+fi);
                        let fi_int=Number(fi);
                        console.log("Fi_int: "+fi_int);
                        // Es compara la data per a saber si es matí o vesprada, en funció de si és entre les 5 o les 15
                        let mati = (inici_int>=50000 && inici_int<150000) ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
                        let fechas = ele.data.split('-');
                        // En cas de que l'element a mostrar siga unes hores es mostra el inici i el final

                        text = ele['inici']+'-'+ele['fi'];

                        // Es crea l'element ja formatat que anirà al calendari i s'afegix al array
                        let i = {
                            id: (ele.id+num),
                            title: "<div id="+(ele.id+num)+" data-uk-tooltip='pos: right; animation: true; offset: 12;' title=\""+text+"\">"+mati+" "+ele.nom+"</div>",
                            startDate: Date.UTC(fechas[0], fechas[1]-1, fechas[2]),
                            classes: clase+" uk-animation-scale-up",
                        };
                        self.items.push(i);
                    }

                }
            );
        },
        // Canal per a rebre la informació de element borrat
        channel_borra_cefire(){
            var aux;
            var self=this;
            let chan='CefireBorratGeneral';
            channel.bind(chan,
                function(data) {
                    aux=data.cefire;
                    console.log(aux);
                    var compara = 1000000 + aux.id;
                    for (let index = 0; index < self.items.length; index++) {
                        if(self.items[index].id == compara)
                        self.items.splice(index,1);
                    }
                }
            );
        }
    },
    mounted() {
        this.channel_create_cefire();
        this.channel_borra_cefire();

    },
    watch: {
        // Les següent funcions borren l'element buscant en totes les dades pel seu id.
        cefire(newValue, oldValue) {
            if (newValue == true){
                this.get_element('cefire');
            } else {
                for (let index = this.items.length-1; index >= 0; index--) {
                    console.log((this.items[index].id>=1000000) && (this.items[index].id<2000000));
                    if((this.items[index].id>=1000000) && (this.items[index].id<2000000)) {
                        console.log(index);
                        this.items.splice(index,1);
                    }
                }
            }
        },
        compensa(newValue, oldValue) {
            if (newValue == true){
                this.get_element('compensa');
            } else {
                for (let index = this.items.length-1; index >= 0; index--) {
                    if((this.items[index].id>=2000000) && (this.items[index].id<3000000)) {
                        console.log(index);
                        this.items.splice(index,1);
                    }
                }
            }
        },
        curs(newValue, oldValue) {
            if (newValue == true){
                this.get_element('curs');
            } else {
                for (let index = this.items.length-1; index >= 0; index--) {
                    if((this.items[index].id>=3000000) && (this.items[index].id<4000000)) {
                        console.log(index);
                        this.items.splice(index,1);
                    }
                }
            }
        },
        guardia(newValue, oldValue) {
            if (newValue == true){
                this.get_element('guardia');
            } else {
                for (let index = this.items.length-1; index >= 0; index--) {
                    if((this.items[index].id>=4000000) && (this.items[index].id<5000000)) {
                        console.log(index);
                        this.items.splice(index,1);
                    }
                }
            }
        },
        permis(newValue, oldValue) {
            if (newValue == true){
                this.get_element('permis');
            } else {
                for (let index = this.items.length-1; index >= 0; index--) {
                    if((this.items[index].id>=5000000) && (this.items[index].id<6000000)) {
                        console.log(index);
                        this.items.splice(index,1);
                    }
                }
            }
        },
        visita(newValue, oldValue) {
            if (newValue == true){
                this.get_element('visita');
            } else {
                for (let index = this.items.length-1; index >= 0; index--) {
                    if((this.items[index].id>=6000000) && (this.items[index].id<7000000)) {
                        console.log(index);
                        this.items.splice(index,1);
                    }
                }
            }
        },
        vacances(newValue, oldValue) {
            if (newValue == true){
                this.get_element('vacances');
            } else {
                for (let index = this.items.length-1; index >= 0; index--) {
                    if((this.items[index].id>=7000000) && (this.items[index].id<8000000)) {
                        console.log(index);
                        this.items.splice(index,1);
                    }
                }
            }
        },
        moscosos(newValue, oldValue) {
            if (newValue == true){
                this.get_element('moscosos');
            } else {
                for (let index = this.items.length-1; index >= 0; index--) {
                    if((this.items[index].id>=8000000) && (this.items[index].id<9000000)) {
                        console.log(index);
                        this.items.splice(index,1);
                    }
                }
            }
        },
    },


}
</script>

<style lang="sass" scope>

@import "../../sass/_variables.scss"


$color_guardia: red
$linea: #dddddd

.altura
    padding-bottom: 30px !important
.calendari
    font-family: Avenir, Arial, Helvetica, sans-serif
    // display: flex
    // height: 100vh
    width: 100%
    display: flex
    flex-direction: column
    flex-grow: 1



item-general
    border: 1px solid $linea
    border-radius: 8px
    font-size: 1.1em

.cv-item.custom-date-class-red
    @extend item-general
    background-color: red

.cv-item.custom-date-class-gray
    @extend item-general
    background-color: gray

.cv-item.custom-date-class-yellow
    @extend item-general
    background-color: $yellow
    color: #000

.cv-item.custom-date-class-blue
    @extend item-general
    background-color: $blue

.cv-item.custom-date-class-green
    @extend item-general
    background-color: green

.cv-item.custom-date-class-pink
    @extend item-general
    background-color: #cc00cc

.cv-item.custom-date-class-violet
    @extend item-general
    background-color: violet

.cv-item.custom-date-class-black
    @extend item-general
    background-color: black


.calendari
    height: 87vh

</style>
