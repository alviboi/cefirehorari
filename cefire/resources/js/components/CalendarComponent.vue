<template>
	<div>
        
        <div class="uk-child-width-1-4@s" uk-grid>
            <div>
                <h2>Afegix guàrdies</h2>
            </div>
            <div>
                <button class="uk-button uk-button-default" @click="change_height">Nombre de guàrdies</button>
            </div>            
        </div>
        <div>
            <collapse-transition dimension="height">
                <div v-show="isOpen">           
                        <table class="uk-table uk-table-divider uk-table-small uk-text-meta">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                    <tr v-for="item in users_total" :key="item.user_id">
                                        <td>{{ item[1] }}</td>
                                        <td>{{ item[2] }}</td>
                                    </tr>
                            </tbody>

                        </table>
                </div>
            </collapse-transition>
        </div>
        
        <div  class="grid-calendar">
            <div  class="calendari">
                <calendar-view
                    :doEmitItemMouseEvents='false'
                    :show-date="dia"
                    :items="items"
                    :enable-date-selection="false"
                    :enableDragDrop="true"
                    :selection-start="selectionStart"
                    :selection-end="selectionEnd"
                    :display-week-numbers="false"
                    :item-top="themeOptions.top"
                    :item-content-height="themeOptions.height"
                    :item-border-height="themeOptions.border"
                    :class="`theme-` + theme"
                    :current-period-label="themeOptions.currentPeriodLabel"
                    :startingDayOfWeek=1
                    class="holiday-cef-traditional holiday-cef-official"

                    @click-item="borrar"
                    @drag-start="drag_comencant"
                    @drop-on-date="asolta_en"
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
            <div  class="costat">
                <div class="mati">
                <input type="radio" id="mati" value="m" v-model="mati_radio">
                <label for="mati">Matí</label>
                <br>
                <input type="radio" id="vesprada" value="v" v-model="mati_radio">
                <label for="vesprada">Vesprada</label>
                </div>
                <div v-for="(user, key) in users" class="assessor_nom" :key="key" :id="user.id" data-uk-tooltip="pos: left; animation: true; offset: 12;" :title="user.name" draggable="true" @drag="comenca_drag">{{user.name}}</div>
            </div>
        </div>
    </div>

</template>

<script>
/**
 * Aquest component ens permet afegir elements al calendari utilitzant drag and drop.
 */
import { CollapseTransition } from "@ivanv/vue-collapse-transition"
import { CalendarView, CalendarViewHeader } from "vue-simple-calendar"
require("vue-simple-calendar/static/css/default.css")
require("vue-simple-calendar/static/css/holidays-us.css")
require("vue-simple-calendar/static/css/gcal.css")
export default {
	components: {
        CollapseTransition,
		CalendarView,
		CalendarViewHeader,
	},
	data: function () {
		return {
            isOpen: false,
            afegit: [],
            users: [],
            users_total: [],
            mati_radio: 'm',
            id_drag: null,
			dia: new Date(),
			selectionStart: null,
			selectionEnd: null,
			theme: "gcal",
			items: Array()
		}
	},
	computed: {
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
        change_height(){
            //alert("est");
            //document.getElementById("baix_este").style.height = "150px";
            this.get_total_guardies_per_user();
            this.isOpen = !this.isOpen;

        },
        async get_total_guardies_per_user(){
            axios.get("guardia/totes_les_guardies")
            .then(res => {
                console.log(res);
                this.users_total=res.data;
            })
            .catch(err => {
                console.error(err);
            })
        },

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
        get_totes_guardies(){
            var result = []
            let any=this.dia.getFullYear();
            let mes=this.dia.getMonth();
            axios.get("guardia/totes/"+(mes+1)+"/"+any)
            .then(res => {
                console.log(res);
                this.emplena_calendari_guardies(res.data)
            })
            .catch(err => {
                console.error(err);
            });

        },
        get_totes_guardies_numero(){
            var result = []
            let any=this.dia.getFullYear();
            let mes=this.dia.getMonth();
            axios.get("guardia/totes_les_guardies")
            .then(res => {
                console.log(res);
                //this.emplena_calendari_guardies(res.data)
            })
            .catch(err => {
                console.error(err);
            });

        },
        // Emplena totes les dades del calendari agafant les dades de les guàrdies.
        emplena_calendari_guardies(result) {
            for (let index = 0; index < result.length; index++) {
                const element = result[index];
                let mati = (element[2]=="09:00:00") ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
                let fechas = element[1].split('-');
                let i = {
                    id: element[3],
                    title: "<div id="+element[3]+" data-uk-tooltip='pos: right; animation: true; offset: 12;' title=\""+element[0]+"\">"+mati+" "+element[0]+"</div>",
                    startDate: Date.UTC(fechas[0], fechas[1]-1, fechas[2]),
                    // endDate: Date.UTC(2020, 11, 10),
                    classes: "custom-date-class-red uk-animation-scale-up",
                };
                this.items.push(i);
            }
            this.index=result.length;
        },
        // Agafem un element de la barra lateral
        comenca_drag(e){
            console.log(e.target);
            this.id_drag=e.target;

        },
        // DEixa l'element sobre una data concreta
        asolta_en(calendarItem, date, windowEvent) {
            console.log(calendarItem);
            console.log(date);
            var id_res=null;
            let fetxa = Date.UTC(date.getUTCFullYear(), date.getUTCMonth(), date.getUTCDate()+1);
            let url="guardia/insert";
            let fetxa2 = data_db(date);
            let params ={
                id: this.id_drag.id,
                data: fetxa2,
                mati: this.mati_radio
            };
            axios.post(url,params)
            .then(res => {
                console.log(res);
                id_res=res.data.id;
                let mati = (this.mati_radio=="m") ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
                let drag_item = {
                    id: id_res,
                    title: "<div id="+id_res+" data-uk-tooltip='pos: right; animation: true; offset: 12;' title=\""+this.id_drag.textContent+"\">"+mati+" "+this.id_drag.textContent+"</div>",
                    startDate: fetxa,
                    classes: "custom-date-class-red uk-animation-scale-up",
                };
                this.items.push(drag_item);
                this.afegit.push(id_res);
                this.get_total_guardies_per_user();
            })
            .catch(err => {
                console.error(err);
                this.$toast.error(err.response.data.message);
            })
            

        },
        // Començant a fet el drag dins del calendari. No s'utilitza, però ho deixem per a futures ampliacions com permetres fer el drag and drop en elements
        // dins del calendari.
        drag_comencant(item,e) {
            console.log(item);
            console.log(e);
        },
        // Borra element del calendari
        borrar(item,e) {
            for (let index = 0; index < this.items.length; index++) {
                if(this.items[index].id == item.id)
                this.items.splice(index,1);
            }
            let url = "guardia/" + item.id;
            axios.delete(url)
            .then(res => {
                console.log(res);
                this.get_total_guardies_per_user();
            })
            .catch(err => {
                console.error(err);
            })
            
        },
        // al canviar el mes agafem totes les guardies
        setdia(d) {
            this.dia = d;
            this.items= [];
            this.get_totes_guardies();

        },
        // Creem canal per a actualitzar les guardies en el cas de que algun assessor estiga treballant al mateix temps
        channel_create(){
            var aux;
            var self=this;
            let chan='GuardiaAfegidaGeneral';
            channel.bind(chan,
                function(data) {
                    aux=data.guardia;
                    //Per a que no s'afegixca dues vegades
                    if (self.afegit.includes(aux.id)){
                        self.afegit.splice(self.afegit.indexOf(aux.id), 1);
                    } else {
                        const element = data.guardia;
                        let mati = (element.inici=="9:00:00") ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
                        let fechas = element.data.split('-');
                        let i = {
                            id: element.id,
                            title: "<div id="+element.user_id+" data-uk-tooltip='pos: right; animation: true; offset: 12;' title=\""+element.nom+"\">"+mati+" "+element.nom+"</div>",
                            startDate: Date.UTC(fechas[0], fechas[1]-1, fechas[2]),
                            // endDate: Date.UTC(2020, 11, 10),
                            classes: "custom-date-class-red uk-animation-scale-up",
                        };
                        self.items.push(i);
                    }
                }
            );
        },
        // Canal per a rebre la informació de element borrat
        channel_borra(){
            var aux;
            var self=this;
            let chan='GuardiaBorradaGeneral';
            channel.bind(chan,
                function(data) {
                    aux=data.guardia;
                    console.log(aux);
                    for (let index = 0; index < self.items.length; index++) {
                        if(self.items[index].id == aux.id)
                        self.items.splice(index,1);
                    }
                }
            );
        }
    },
    mounted() {
        this.agafa_users();
        this.get_totes_guardies();
        this.channel_create();
        this.channel_borra();
        this.get_total_guardies_per_user();
    },
}
</script>

<style lang="sass" scope>
$color_guardia: red
$linea: #dddddd

.altura
    padding-bottom: 30px !important
.general
    font-family: Avenir, Arial, Helvetica, sans-serif
    display: flex
    height: 87vh
    width: 100%


.cv-item.custom-date-class-red
    background-color: $color_guardia
    border: 1px solid $linea
    border-radius: 8px
    font-size: 1.2em


.cv-item
    margin-top: 15px

.cabecal
    display: grid
    grid-template-columns: 1fr 1fr 1fr 1fr
    grid-template-rows: 1fr
    gap: 0px 20px
    grid-template-areas: "arrere mig mig avant"
    .arrere
        grid-area: arrere
    .mig
        grid-area: mig
        text-align: center
        margin-bottom: 0px
        margin-top: 5px
    .avant
        grid-area: avant
.grid-calendar
    display: grid
    grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr
    grid-template-rows: 1fr
    gap: 0px 0px
    grid-template-areas: "calendari calendari calendari calendari calendari calendari calendari calendari calendari costat"
.calendari
    grid-area: calendari
.costat
    padding: 3%
    grid-area: costat
    margin-bottom: 80px
    & .mati
        border: 1px solid black
        border-radius: 5px
        margin-bottom: 3px
        padding: 5px
    & .assessor_nom
        max-width: 10em
        vertical-align: top
        // height: 1.3em
        line-height: 1.3em
        font-size: 0.9em
        white-space: nowrap
        display: block
        cursor: pointer
        border: 1px solid $linea
        border-radius: 8px
        background-color: white
        margin-top: 2px
        background-color: $color_guardia
        color: #fffafd
        padding: 1px
        padding-left: 3px
        
.expand
    background: #dddddd

</style>
