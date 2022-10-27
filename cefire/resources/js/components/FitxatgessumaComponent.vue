<template>
  <div class="general2">
    <div class="cabecal">
        <div class="arrere">
            <button @click="canvia('arr')" class="uk-button uk-button-primary uk-button-large uk-float-left"><span uk-icon="arrow-left"></span></button>
        </div>
        <div class="uk-text-lead mig">
          Relació del temps: {{nom_mes}} de {{any}}
        </div>
          
        <div class="avant">
            <button @click="canvia('av')" class="uk-button uk-button-primary uk-button-large uk-float-right"><span uk-icon="arrow-right"></span></button>
        </div>
    </div>

    <div>
      <table class="uk-table uk-table-striped">
          <thead>
              <tr>

                <th v-for="(item,name,key) in users_statistic[0]" :key="key"><b>{{ name }}</b></th>

              </tr>
          </thead>
          
          <tbody>
              <tr v-for="(item,key) in users_statistic" :key="key">
                  <td v-for="(item2,name,key) in item" :key="key"><span v-if="name == 'diferència'" :class="(item2 >= 0)? 'uk-text-success' : 'uk-text-warning'" :data-uk-icon="(item2 >= 0)? 'icon: triangle-up' : 'icon: triangle-down'">{{item2}}</span><span v-else>{{item2}} </span></td>
              </tr>
          </tbody>
          
      </table>
    
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
      ca: ca,
      dia_aux: null,
      users_statistic: [],
      any: null,
      mes: null,
      nom_mes: "",
    };
  },
  methods: {
    get_mes_any() {
      this.mes = this.dia.getMonth()+1;
      this.any = this.dia.getFullYear();
      this.nom_mes = this.dia.toLocaleString("ca", { month: "long" });
    },

    get_data_statistics() {
      //var here = this;
      axios
        .get("tots_els_dies_mes/" + this.any + "/" + this.mes)
        .then((res) => {
          console.log(res);
          this.users_statistic = res.data;
        })
        .catch((err) => {
          for (let index = 0; index < this.users_statistic.length; index++) {            								
            this.users_statistic[index]['cefire'] = 0;
            this.users_statistic[index]['permis'] = 0;
            this.users_statistic[index]['compensa'] = 0;
            this.users_statistic[index]['curs'] = 0;
            this.users_statistic[index]['visita'] = 0;
            this.users_statistic[index]['moscosos'] = 0;
            this.users_statistic[index]['vacances'] = 0;
            this.users_statistic[index]['total'] = 0;
            this.users_statistic[index]['diferència'] = 0;
            
          }
          this.$toast.error(err.response.data.message);
          console.error(err);
        });
    },

    // Quan canviem la data, al buscar una data
    canvia_data(val) {
      let aux = new Date(val);
      this.dia = aux;
      //this.emplena_lloc();
      this.componentKey++;
    },
    // Quan pujem o baixem una setmana
    canvia(ar) {
      var result = ((ar != "arr") ? -1 : 1);
      this.dia.setMonth(this.dia.getMonth() - result);
      this.get_mes_any();
      this.componentKey++;
      

      this.get_data_statistics();
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
    //this.emplena_lloc();
  },
  mounted() {
    this.dia_aux = this.dia;
    this.get_mes_any();
    this.get_data_statistics();
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
  grid-template-columns: 1fr 1fr 1fr
  grid-template-rows: 1fr
  gap: 0px 20px
  grid-template-areas: "arrere mig avant"
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
