<template>
  <div class="general2">
    <div class="cabecal">
        <div class="arrere">
            <button @click="canvia('arr')" class="uk-button uk-button-primary uk-button-large uk-float-left"><span uk-icon="arrow-left"></span></button>
        </div>

          <div class="mig uk-align-right">
                  <Datepicker
                  :language="ca"
                  :monday-first="true"
                  v-model="dia_aux"
                  @selected="canvia_data()"
                  placeholder="Escollix data a buscar"
                  input-class="uk-input"
                  >
                  </Datepicker>
          </div>
          <div class="mig2 uk-align-left">
              <button class="uk-button uk-button-default" @click="change_height">Estadístiques Mes Actual</button>
          </div> 

        
        <div class="avant">
            <button @click="canvia('av')" class="uk-button uk-button-primary uk-button-large uk-float-right"><span uk-icon="arrow-right"></span></button>
        </div>
    </div>
    <div>
            <collapse-transition dimension="height">
                <div v-show="isOpen">      
                        <table class="uk-table uk-table-divider uk-table-small uk-text-meta">
                            <thead>

                                    <tr >
                                        <th v-for="(item, name,key) in user_statistic" :key="key"><b>{{ name }}</b></th>
                                    </tr>

                            </thead>
                            <tbody>
                                    <tr >
                                        <td v-for="(item, name,key) in user_statistic" :key="key">{{ item }}</td>
                                    </tr>
                            </tbody>

                        </table>
                        <span class="uk-text-meta">TOTAL TEMPS és la suma de Fitxatges, Com. Serv. i Cursos</span>
                        <hr>
                        <div class="uk-text-lead">Últims dies que has fitxat</div>
                        <table class="uk-table uk-table-divider uk-table-small uk-text-meta">
                            <thead>

                                    <tr >
                                        <th v-for="(item,key) in user_statistic_dies" :key="key"><b>{{ item.data }}</b></th>
                                    </tr>

                            </thead>
                            <tbody>
                                    <tr >
                                        <td v-for="(item,key) in user_statistic_dies" :key="key"><div v-if="item.diferencia>0">{{ item.diferencia }} minuts
                                              <span :class="(item.diferencia >= item.compara)? 'uk-text-success' : 'uk-text-warning'" :data-uk-icon="(item.diferencia >= item.compara)? 'icon: triangle-up' : 'icon: triangle-down'">{{item.diferencia-item.compara}}</span>
                                        </div>
                                        <div v-else>No s'ha fitxat l'eixida</div>
                                        </td>
                                    </tr>
                            </tbody>

                        </table>
                        <span class="uk-text-meta">Si has faltat un dia, ací no apareix</span>
                </div>
            </collapse-transition>
        </div>
	<hr>

    <div class="grid-container">
      <div v-for="(key, index) in 7" :key="key" :class="'d' + (index + 1) + ' z'+index">
        <dia-component
          :data="lloc[index + 1]"
          :key="index+componentKey+1000"
        />
        <!-- <dia-component
          :data="lloc[index + 1]"
          mati="v"
          :key="index+componentKey"
        /> -->
      </div>
    </div>
  </div>
</template>

<script>

/**
Aquest component mostra tots el dies de la setmana present
 */

import Datepicker from "vuejs-datepicker";
import { ca } from "vuejs-datepicker/dist/locale";
import { CollapseTransition } from "@ivanv/vue-collapse-transition";
export default {
  data() {
    return {
      dia: new Date(),
      componentKey: 0,
      lloc: [],
      fecha_escollida: null,
      ca: ca,
      dia_aux: null,
      isOpen: false,
      user_statistic: [],
      user_statistic_dies: []
    };
  },
  methods: {
    change_height(){
            //alert("est");
            //document.getElementById("baix_este").style.height = "150px";
            //this.get_total_guardies_per_user();
            this.get_data_statistics();
            this.get_data_statistics_dies();
            this.isOpen = !this.isOpen;

        },
    get_data_statistics(){
      axios.get("user_statistics")
            .then(res => {
                console.log(res);
                this.user_statistic=res.data;
            })
            .catch(err => {
                console.error(err);
            })
    },
    get_data_statistics_dies(){
      axios.get("ultims_dies_estadistica")
            .then(res => {
                console.log(res);
                this.user_statistic_dies=res.data;
            })
            .catch(err => {
                console.error(err);
            })
    },
    // Quan canviem la data, al buscar una data
    canvia_data (val) {
        let aux = new Date(val);
        this.dia=aux;
        this.emplena_lloc();
        this.componentKey++;

    },
    // Quan pujem o baixem una setmana
    canvia(ar) {
      let result = ar == "arr" ? -7 : 7;
      this.dia = new Date(
        this.dia.setTime(this.dia.getTime() + result * 86400000)
      );
      this.emplena_lloc();
      this.componentKey++;
      this.dia_aux = this.dia;
    },
    async emplena_lloc() {
      let aux_dia = getDiumenge(this.dia);
      this.lloc[0] = aux_dia;
      // S'ha detectat un bug, la setmana de canvi d'hora, si et connectes entre les 23:00 i 00:00 el diumenge d'eixa setmana no apareix.
      for (let index = 1; index < 8; index++) {
        this.lloc[index] = new Date(
          aux_dia.setTime(aux_dia.getTime() + 1 * 86400000)
        );
      }
    },
  },
  created() {
    this.emplena_lloc();
  },
  mounted() {
      this.dia_aux = this.dia;
      this.get_data_statistics();
      this.get_data_statistics_dies();
  },
  components: {
    CollapseTransition,
    Datepicker,
  },
  watch: {
    dia_aux(newValue, oldValue) {
      this.canvia_data(newValue);
    },
  },
};
</script>

<style lang="sass" scope>
.general2
    padding: 1%
    width: 100%
.grid-container
    display: grid
    margin-left: 2%
    grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr 1fr
    gap: 10px
    grid-template-areas: "d1 d2 d3 d4 d5 d6 d7"
    @for $var from 0 through 6
        .d#{$var}
            grid-area: d#{$var}

.cabecal
    display: grid
    grid-template-columns: 1fr 1fr 1fr 1fr
    grid-template-rows: 1fr
    gap: 0px 20px
    grid-template-areas: "arrere mig mig2 avant"
    .arrere
        grid-area: arrere
    .mig
        grid-area: mig
        text-align: center
        margin-bottom: 0px
        margin-top: 5px
    .mig2
        grid-area: mig2
        text-align: center
        margin-bottom: 0px
        margin-top: 5px
    .avant
        grid-area: avant
// Si la pantalla ajunta molt els elements, el desplegable del dia després es pot superposat al dia abans
@for $i from 1 through 7
    .z#{$i}
        z-index: #{$i}

</style>
