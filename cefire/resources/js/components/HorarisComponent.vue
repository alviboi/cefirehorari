<template>
	<div>
        <div class="uk-grid-small uk-child-width-expand uk-margin" uk-grid>
            <div class="uk-width-1-3">
                <h3>Busca l'horari de l'assessor</h3>
            </div>
            <div class="uk-width-1-3">
                <form class="uk-width-expand uk-search uk-search-default" autocomplete="on">
                    <a @click="filterResults()" uk-search-icon></a>
                    <input list="llista" v-model="busca_ass" class="uk-search-input">
                      <datalist class="llista" id="llista">
                            <option v-for="(user,key) in users" :key="key" :value="user.name">{{user.name}}</option>
                      </datalist>
                </form>
            </div>
        </div>
    <div  class="calendari-general">
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
                class="holiday-us-traditional holiday-us-official"
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
 * En aquest component podem buscar l'horari de tot el mes d'un assessor
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
            isOpen: false,
            results: null,
            busca_ass: "",
            users: [],
			dia: new Date(),
			theme: "gcal",
			items: Array()
		}
	},
	computed: {
		themeOptions() {
            let ret = {
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
        // Quan s'establix un mes es demana tota la informació del dia de tots els elements
        setdia(d) {
            this.dia = d;
            this.items= [];
            if(this.busca_ass != ""){
                this.tots_els_elements_get();
            }
		},
        // Filtra els resultats
        filterResults() {
            this.results = this.users.filter((item => item.name.toLowerCase() == this.busca_ass.toLowerCase()));
            if (this.results[0] !== undefined){
                this.tots_els_elements_get();
            }
        },
        // Es demanen tots el elements
        tots_els_elements_get() {
            this.items= [];
            this.get_element('cefire');
            this.get_element('compensa');
            this.get_element('curs');
            this.get_element('guardia');
            this.get_element('permis');
            this.get_element('visita');
        },
        // Petició de les dades de tots els usuaris per al desplegable
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
        // Funció per a demanar el element concret a través de axios
        get_element(element){
            var result = []
            let any=this.dia.getFullYear();
            let mes=this.dia.getMonth();
            let url="/user_"+element+"/"+this.results[0].id+"/"+any+"/"+(mes+1)
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
                default:
                    num=7000000;
                    clase="custom-date-class-red";
                    break;
            }
            for (let index = 0; index < result.length; index++) {
                const ele = result[index];
                let inici=ele.inici.replace(/:/g,'');
                let inici_int=Number(inici);
                let fi=ele.inici.replace(/:/g,'');
                console.log("Fi: "+fi);
                let fi_int=Number(fi);
                console.log("Fi_int: "+fi_int);
                let text = '';
                if (toti == 'inici') {
                    text = ele['inici']+'-'+ele['fi'];
                } else {
                    text = ele[toti];
                }

                let mati = (inici_int>=80000 && inici_int<150000) ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
                let fechas = ele.data.split('-');
                let i = {
                    id: (ele.id+num),
                    title: "<div id="+(ele.id+num)+" data-uk-tooltip='pos: right; animation: true; offset: 12;' title=\""+text+"\">"+mati+" "+element.toUpperCase()+"</div>",
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
                                        return "Error";
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
            return "Cefire";
            }
        },
        envia_missatge(calendarItem, windowEvent) {
            var parser = new DOMParser();
            var doc = parser.parseFromString(calendarItem.title, 'text/html');
            let destinatari = this.results[0].name;
            let data = fecha_format(calendarItem.startDate);
            let id = this.comproba_id_element(Number(doc.all[3].id));
            console.log(id);
            console.log(this.results[0].name);
            let envia =  {
                destinatari: destinatari,
                assumpte: "En relació al element "+id+" del dia "+ data
            }
            this.$eventBus.$emit('missatge-variables',envia);
        }
    },
    mounted() {
        this.agafa_users();
    },
    watch: {
        busca_ass() {
            this.filterResults();
        }
    },
}
</script>

<style lang="sass" scope>
$linea: #dddddd

.altura
    padding-bottom: 15px !important
.general
    font-family: Avenir, Arial, Helvetica, sans-serif
    display: flex
    height: 97vh
    width: 100%

item-general
    border: 1px solid $linea
    border-radius: 8px
    font-size: 1.4em

.cv-item.custom-date-class-red
    @extend item-general
    background-color: red

.cv-item.custom-date-class-gray
    @extend item-general
    background-color: gray

.cv-item.custom-date-class-yellow
    @extend item-general
    background-color: yellow

.cv-item.custom-date-class-blue
    @extend item-general
    background-color: blue

.cv-item.custom-date-class-green
    @extend item-general
    background-color: green

.cv-item.custom-date-class-pink
    @extend item-general
    background-color: pink

.cv-item
    margin-top: 1px

.calendari-general
    height: 90vh
.llista
    color: $linea
    background-color: #ececec

</style>
