<template>
  <div>
    <div class="uk-align-center">
      <h2 class="uk-align-center">Llistat Compensacions per validar</h2>
    </div>

    <div>
      <transition-group name="list3" tag="div">
        <div v-for="item in compensacions" :key="item.id">
          <div class="llistatcomp">
            <div class="data">
              <span data-uk-icon="icon: calendar"></span>
              {{ item.data }}
            </div>
            <div class="nom">
              <span data-uk-icon="icon: user"></span>
              <b>{{ item.name }}</b>
            </div>
            <div class="mati">
              <span data-uk-icon="icon: clock"></span>
              <span v-if="item.inici == '09:00:00'"
                ><i class="fas fa-sun"></i>{{ item.inici }} -
                {{ item.fi }}</span
              >
              <span v-else
                ><i class="fas fa-moon"></i>{{ item.inici }} -
                {{ item.fi }}</span
              >
            </div>
            <div class="motiu">
              <span data-uk-icon="icon: comments"></span>
              {{ item.motiu }}
            </div>
            <div class="botons">
              <div
                @click.prevent="valida(item.id)"
                class="uk-icon-button uk-text-success"
                uk-icon="check"
              ></div>
              <div
                @click.prevent="borra(item.id)"
                class="uk-icon-button uk-text-danger"
                uk-icon="close"
              ></div>
            </div>
          </div>
        </div>
      </transition-group>
    </div>
  </div>
</template>

<script>
/**
 * En aquest component es mostren tots els justificants de tots els permisos dels assessors, es pot buscar per assessor i per dades concretes
 */

export default {
  data() {
    return {
      hui: new Date(),
      compensacions: {},
    };
  },
  methods: {
    agafa_compensacions() {
      let url = "compensacions_no_validades";
      axios
        .post(url)
        .then((res) => {
          this.compensacions = res.data;
          console.log(res);
        })
        .catch((err) => {
          this.$toast.error(err.response.data.message);
        });
    },
    borra(id) {
      let url = "compensa/" + id;
      for (let index = 0; index < this.compensacions.length; index++) {
        if (this.compensacions[index].id == id) {
          this.compensacions.splice(index, 1);
        }
      }
      axios
        .delete(url)
        .then((res) => {
          console.log(res);
          this.$toast.success("Borrat correctament");
        })
        .catch((err) => {
          console.error(err);
          this.$toast.error(err.response.data.message);
        });
    },
    // Edita assessor
    valida(id) {
      let url = "validacompensacio";
      for (let index = 0; index < this.compensacions.length; index++) {
        if (this.compensacions[index].id == id) {
          this.compensacions.splice(index, 1);
        }
      }
      let params = {
        id: id,
      };
      axios
        .post(url, params)
        .then((res) => {
          console.log(res);
          this.$toast.success("Has validat la compensació");
        })
        .catch((err) => {
          this.$toast.error(err.response.data.message);
        });
    },
  },
  mounted() {
    this.agafa_compensacions();
  },
  watch: {},
};
</script>

<style lang="sass" scope>
$fondo: #EEC49A
.llistatcomp
    display: grid
    grid-template-columns: 0.7fr 1.1fr 0.8fr 2fr 0.3fr
    grid-template-rows: 1fr
    gap: 0px 0px
    grid-template-areas: "data nom mati motiu botons"
    border: 2px solid black
    border-radius: 10px
    margin: 10px
    padding: 10px
    box-shadow: 3px 6px 121px -42px rgba(0,0,0,0.75)
    align-content: center
    align-items: center
    background-color: $fondo
    comu
    overflow: hidden
    .data
        @extend comu
        grid-area: data
    .nom
        @extend comu
        grid-area: nom
    .mati
        @extend comu
        grid-area: mati
    .motiu
        grid-area: motiu
    .botons
        @extend comu
        grid-area: botons
        display: flex
        align-items: right
        text-align: right
</style>
