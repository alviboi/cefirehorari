<template>
  <div>
    <div class="uk-child-width-1-2" uk-grid>
      <div>
        <h2>Afegix dies amb horari especial</h2>
      </div>
    </div>
    <div>
      <div class="llista_horari">
        <div class="data1">
          <!-- <Datepicker :bootstrap-styling="true"
            :append-to-body=true
            :language="ca"
            :monday-first="true"
            v-model="dia"
            input-class="uk-input"
          >
          </Datepicker> -->
          <input v-model="dia" class="uk-input" type="date" />
        </div>
        <div class="text" style="text-align: right; padding-right: 20px">
          Horari:
        </div>
        <div class="horade">
          <vue-timepicker
            append-to-body
            :minute-interval="5"
            v-model="horade"
          ></vue-timepicker>
        </div>
        <div class="horafins">
          <vue-timepicker
            append-to-body
            :minute-interval="5"
            v-model="horafins"
          ></vue-timepicker>
        </div>
        <div class="botons">
          <a
            @click="afegix()"
            class="uk-icon-button"
            uk-icon="icon: plus"
            style="background: green"
          >
          </a>
        </div>
      </div>
    </div>
    <transition-group name="llista_horari" tag="div">

    <div
      v-for="(item) in calendariespecial"
      :key="item.id"
      class="llista_horari llista_horari-item"
    >
      <div class="data1">{{ item.data }}</div>
      <div class="text" style="text-align: right; padding-right: 20px">
        Horari:
      </div>
      <div class="horade">{{ item.inici }}</div>
      <div class="horafins">{{ item.fi }}</div>
      <div class="botons">
        <a
          @click.prevent="lleva(item.id)"
          class="uk-icon-button"
          uk-icon="icon: minus"
          style="background: red"
        ></a>
      </div>
    </div>
    </transition-group>
  </div>
</template>

<script>
/**
 * Component d'un únic dia que controla totes les accions que es realitzen sobre el mateix dia
 */

import Datepicker from "vuejs-datepicker";
import { ca } from "vuejs-datepicker/dist/locale";
import VueTimepicker from "vue2-timepicker";
import "vue2-timepicker/dist/VueTimepicker.css";
export default {
  data() {
    return {
      ca: ca,
      calendariespecial: [],
      dia: new Date(),
      horade: "",
      horafins: "",
      id: 0,
    };
  },
  watch: {},
  components: {
    VueTimepicker,
    Datepicker,
  },
  methods: {
    afegix() {
      var dia_a = new Date(this.dia);

      var afg = {
        dia: dia_a.toISOString().split("T")[0],
        inici: this.horade,
        fi: this.horafins,
      };
      axios
        .post("horariespecial", afg)
        .then((res) => {
          this.calendariespecial.push(res.data);
          console.log(res);
        })
        .catch((err) => {
          console.log(err);
        });
      // var dia_a = new Date(this.dia);
      // var afg = {
      //   id: 100000 + this.id,
      //   dia: dia_a.toISOString().split("T")[0],
      //   horade: this.horade,
      //   horafins: this.horafins,
      // };
      // this.id++;
      // this.calendariespecial.push(afg);
    },
    lleva(id) {
      var url = "horariespecial/" + id;
      axios
        .delete(url)
        .then((res) => {
          console.log(res);
          for (let index = 0; index < this.calendariespecial.length; index++) {
            if (this.calendariespecial[index].id == id) {
              this.calendariespecial.splice(index, 1);
            }
          }
        })
        .catch((err) => {
          console.log(err);
        });
    },
    agafadies() {
      axios
        .get("horariespecial")
        .then((res) => {
          console.log(res);
          this.calendariespecial = res.data;
        })
        .catch((err) => {
          console.log(err);
        });
    },
  },

  mounted() {
    this.agafadies();
  },
};
</script>

<style lang="sass" scope>
.llista_horari-item
    transition: all 1s

.llista_horari-enter, .llista_horari-leave-to
    opacity: 0

.llista_horari-leave-active
    position: absolute

.llista_horari
  display: grid
  grid-template-columns: 0.7fr 0.7fr 0.7fr 0.7fr 0.3fr
  grid-template-rows: 1fr
  gap: 0px 0px
  grid-template-areas: "data1 text horade horafins botons"
  border: 2px solid black
  border-radius: 10px
  margin: 10px
  padding: 10px
  box-shadow: 3px 6px 121px -42px rgba(0,0,0,0.75)
  align-content: center
  align-items: center
  background-color: white
  overflow: hidden
  comu
  .data1
    @extend comu
    grid-area: data1
    .text
      @extend comu
      grid-area: data2
      color: black
      font-color: black
      padding-right: 20px
      .horade
        @extend comu
        grid-area: horade
        .horafins
          grid-area: horafins
        .botons
          @extend comu
          grid-area: botons
          display: flex
          align-items: right
          text-align: right
</style>