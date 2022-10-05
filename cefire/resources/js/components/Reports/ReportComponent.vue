<template>
  <div>
    <div
      class="uk-grid uk-grid-divider uk-grid-medium uk-child-width-1-2 uk-child-width-1-4@l uk-child-width-1-5@xl"
      data-uk-grid
    >
      <div>
        <span class="uk-text-small"
          ><span
            data-uk-icon="icon:users"
            class="uk-margin-small-right uk-text-primary"
          ></span
          >Temps total del rang</span
        >
        <h1 class="uk-heading-primary uk-margin-remove uk-text-primary">
          {{ total_calculat }} hores
        </h1>
        <div class="uk-text-small">
            <span :class="(mes_per_mes >= 0)? 'uk-text-success' : 'uk-text-warning'" :data-uk-icon="(mes_per_mes >= 0)? 'icon: triangle-up' : 'icon: triangle-down'">{{ mes_per_mes }}%</span> <span v-if="(mes_per_mes >= 0)"> més que el mes passat</span><span v-else>menys que el mes passat</span>
        </div>
        <div class="uk-text-small">Sense contar eixides.</div>
      </div>
      <div>
        <span class="uk-text-small"
          ><span
            data-uk-icon="icon:clock"
            class="uk-margin-small-right uk-text-primary"
          ></span
          >Total mes passat</span
        >
        <h1 class="uk-heading-primary uk-margin-remove uk-text-primary">
          {{ total_mes_passat }} hores
        </h1>
        <div class="uk-text-small">
            <span :class="(mes_per_mes <= 0)? 'uk-text-success' : 'uk-text-warning'" :data-uk-icon="(mes_per_mes <= 0)? 'icon: triangle-up' : 'icon: triangle-down'">{{ -mes_per_mes }}%</span> <span v-if="(mes_per_mes <= 0)"> més que el càlcul</span><span v-else>menys que el càlcul</span>
        </div>
      </div>
      <div>

        <span class="uk-text-small"><span data-uk-icon="icon:calendar" class="uk-margin-small-right uk-text-primary"></span>Rang de dates:</span>

        <div class="uk-margin-small-top">
            <Datepicker
                :language="ca"
                input-class="uk-input uk-form-small"
                :monday-first="true"
                placeholder="Desde"
                value=""
                v-model="desde"
                @input="canvia_data()"
                >
        </Datepicker>
        <Datepicker
                input-class="uk-input uk-form-small"
                :language="ca"
                :monday-first="true"
                placeholder="Fins"
                value=""
                v-model="fins"
                @input="canvia_data()"
                >
        </Datepicker>

        </div>
    </div>
    </div>

    <hr />
    <div>
      <div
        class="uk-grid uk-grid-divider uk-grid-medium uk-child-width-1-2 uk-child-width-1-2@l uk-child-width-1-2@xl"
        data-uk-grid
      >
        <div>
          <line-component
            :key="refresca"
            :data="datos"
            :labels="labels"
            :nom="nom"
            :nom_datos="nom_datos"
          />
        </div>
        <div>
          <pie-component
            :key="refresca+10000"
            :data="datos2"
            :labels="labels2"
            :nom="nom2"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>

import Datepicker from "vuejs-datepicker";
import { ca } from "vuejs-datepicker/dist/locale";
export default {
  data() {
    return {
      desde: new Date(),
      fins: new Date(),
      ca: ca,
      dia: new Date(),
      total_pass: 0,
      total: 0,
      datos: [],
      labels: [],
      datos2: [],
      labels2: [],
      nom: "Gràfica dels minuts fets al cefire",
      nom2: "Distribució d'hores realitzades",
      nom_datos: "Cefire",
      refresca: 0,
    };
  },
  components: {
        Datepicker,
  },
  computed: {
      total_calculat() {
          return (this.total / 60).toFixed(2);
      },
      total_mes_passat() {
          return (this.total_pass / 60).toFixed(2)
      },
      mes_per_mes() {
         let aux = ((this.total_calculat/this.total_mes_passat)*100-100).toFixed(2);
         return aux;
      }
  },
  methods: {
    canvia_data(){
        console.log(this.fins);
        console.log(this.desde);
        if(this.fins > this.desde) {
            this.get_temps();
        } else {
            this.$toast.error("No puc calcular temps invertit");
        }
    },
    primer_data_mes_passat(data) {
      let dia = data.getDate();
      let mes = data.getMonth();
      let an = data.getFullYear();
      if (mes == 0) {
        let ret = an - 1 + "-12-1";
        return ret;
      } else {
        let ret = an + "-" + mes + "-1";
        return ret;
      }
    },
    ultim_data_mes_passat(data) {
      var d = new Date(this.dia.getFullYear(), this.dia.getMonth() + 1, 0);
      let dia = d.getUTCDate();
      let mes = d.getMonth();
      let an = d.getFullYear();
      if (mes == 0) {
        let ret = an - 1 + "-12-" + dia;
        return ret;
      } else {
        let ret = an + "-" + mes + "-" + dia;
        return ret;
      }
    },
    // data_mes_inici() {
    //   let dia = this.dia.getDate();
    //   let mes = this.dia.getMonth() + 1;
    //   let an = this.dia.getFullYear();
    //   let ret = an + "-" + mes + "-01";
    //   return ret;
    // },
    data_mes_inici_date() {
      let dia = this.dia.getDate();
      let mes = this.dia.getMonth() + 1;
      let an = this.dia.getFullYear();
      let ret = new Date(an + "-" + mes + "-01");
      return ret;
    },

    get_temps() {
      let desde = data_db(this.desde);
      let fins = data_db(this.fins);
      let url = "/" + desde + "/" + fins;
      axios
        .get('contar'+url)
        .then((res) => {
          console.log(res);
          this.datos = res.data.data;
          this.labels = res.data.labels;
          this.total = res.data.total;
          this.refresca++;
        })
        .catch((err) => {
          console.error(err);
        });

      axios.get('contar_tot'+url)
      .then(res => {
          console.log(res)
          this.datos2 = res.data.datos;
          this.labels2 = res.data.labels;
          this.refresca++;
      })
      .catch(err => {
          console.error(err);
      })
    },
    get_temps2() {
      let desde = this.primer_data_mes_passat(this.dia);
      let fins = this.ultim_data_mes_passat(this.dia);
    //   let desde = "2020-12-01";
    //   let fins = "2020-12-31";
      let url = "contar/" + desde + "/" + fins;
      axios
        .get(url)
        .then((res) => {
          console.log(res);
          this.total_pass = res.data.total;
        })
        .catch((err) => {
          console.error(err);
        });
    },
  },
  mounted() {
    this.desde = this.data_mes_inici_date();
    this.get_temps();
    this.get_temps2();

  },
};
</script>

<style>
</style>
