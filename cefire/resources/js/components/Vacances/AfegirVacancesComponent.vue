<template>
  <div>
<div class="center-screen uk-margin-auto" :style="(loading)?'display:block':'display:none'">
    <div v-if="loading" class="loader"></div>
    <span v-if="loading" class="uk-text-large uk-margin carregant">Carregant dades</span>
</div>

    
    <div class="uk-text-large uk-margin">Escollix les vacances oficials</div>
    <div class="uk-grid">
      <div class="uk-width-3-4 uk-margin uk-padding">
        <v-calendar
          timezone="UTC"
          :min-date="from"
          :max-date="to"
          :attributes="attributes"
          @dayclick="onDayClick"
          :columns="$screens({ este1: 4, este2: 3 })"
          :rows="$screens({ este1: 3, este2: 4 })"
          :is-expanded="$screens({ default: true, este1: false, este2: false })"
          locale="ca"
          :from-date="from"
          :to-date="to"
        />
      </div>
      <div class="uk-width-1-4 uk-margin">
        <div class="uk-text-large">Dies escollits</div>
        <div class="uk-button-group uk-margin">
          <button class="uk-button uk-button-primary" @click="envia_dates">
            Enviar
          </button>
          <button class="uk-button uk-button-danger" @click="borrar_tot">
            Borrar tot
          </button>
        </div>
        <transition-group name="list">
          <div class="data_es uk-text-small" v-for="item in days" :key="item.id">
            {{
              item.date.getDate() +
              "-" +
              (item.date.getMonth() + 1) +
              "-" +
              item.date.getFullYear()
            }}
          </div>
        </transition-group>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      loading: true,
      days: [],
      from: new Date(),
      to: new Date(),
    };
  },
  computed: {
    dates() {
      return this.days.map((day) => day.date);
    },
    attributes() {
      return this.dates.map((date) => ({
        highlight: true,
        dates: date,
      }));
    },
  },

  methods: {
    onDayClick(day) {
      const idx = this.days.findIndex((d) => d.id === day.id);
      //this.dia_separat = day.date.split('-');
      if (idx >= 0) {
        this.days.splice(idx, 1);
      } else {
        this.days.push({
          id: day.id,
          //date: this.dia_separat[2]+"-"+this.dia_separat[1]+"-"+this.dia_separat[0],
          date: day.date,
        });
      }
    },
    borrar_tot() {
      this.days.splice(0, this.days.length);
    },
    agafa_curs() {
      var dia_avui = new Date();
      console.log("El mes es" + (dia_avui.getMonth()));
      if (dia_avui.getMonth() < 7) {
        this.from = new Date((dia_avui.getFullYear() - 1) + "-09-01");
        this.to = new Date(dia_avui.getFullYear() + "-08-31");
      } else {
        this.from = new Date(dia_avui.getFullYear() + "-09-01");
        this.to = new Date(dia_avui.getFullYear() + 1 + "-08-31");
      }
    },
    agafa_dates() {
      // var params = {
      //   from: this.from,
      //   to: this.to,
      // }
      var url = "/vacancesoficials";
      axios
        .get(url)
        .then((res) => {
          console.log(res.data);
          for (let index = 0; index < res.data.length; index++) {
            const element = res.data[index];
            this.days.push({
              id: element.data,
              //date: this.dia_separat[2]+"-"+this.dia_separat[1]+"-"+this.dia_separat[0],
              date: new Date(element.data),
            });
          }
          this.loading = false;
        })
        .catch((err) => {
          console.error(err);
        });
    },

    envia_dates() {
      axios
        .post("/vacancesoficials", this.days)
        .then((res) => {
          console.log(res);
          this.$toast.success("Dades enviades");
        })
        .catch((err) => {
          console.log(err);
        });
    },
  },
  mounted() {
    this.agafa_curs();
    this.agafa_dates();
  },
};
</script>
<style lang="sass" scoped>
.data_es
  border-style: solid
  padding: 5px
  border-radius: 10px
  background-color: greenyellow
  margin-bottom: 3px

.list-item
  display: inline-block
  margin-right: 10px

.list-enter-active, .list-leave-active
  transition: all 1s

.list-enter, .list-leave-to
  opacity: 0
  transform: translateY(30px)

.loader
  border: 16px solid #f3f3f3
  border-top: 16px solid #3498db
  border-radius: 50%
  width: 36px
  height: 36px
  margin: auto
  animation: spin 2s linear infinite


@keyframes spin 
  0% 
    transform: rotate(0deg)
  
  100% 
    transform: rotate(360deg)

.carregant
  animation: txtcarr 2s linear infinite

@keyframes txtcarr 
  0% 
    opacity: 0
  
  50% 
    opacity: 1

  100% 
    opacity: 0
  
.center-screen
  position: absolute
  margin: auto
  display: block
  top: 10%
  z-index: 1
  left: 50%
  

</style>