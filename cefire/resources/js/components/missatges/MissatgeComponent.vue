<template>
  <div>
      <div v-for="(avis, key) in avisos" :key="key">
          <div class="uk-card uk-card-default uk-width-1-1 margens" uk-margin>
            <div class="uk-card-header">
                <div class="uk-grid-small uk-flex-middle" uk-grid>
                    <div class="uk-width-auto">
                        <span uk-icon="icon: warning; ratio: 2.5"></span>
                    </div>
                    <div class="uk-width-expand">
                        <h3 class="uk-card-title uk-margin-remove-bottom">{{avis.cap}}</h3>
                        <p class="uk-text-meta uk-margin-remove-top"><time datetime="2016-04-01T19:00">{{avis.data}}</time></p>
                    </div>
                </div>
            </div>
            <div class="uk-card-body">
                <p>{{avis.avis}}</p>
            </div>
        </div>
      </div>
  </div>
</template>

<script>
/**
 * Llistar tots els missatge al off-canvas
 */
export default {
    data() {
        return {
            avisos: []
        }
    },
    methods: {
        // Agafa tots el missatges
        get_avisos() {
                let url="notificacions";
                axios.get(url)
                .then(res => {
                    console.log(res);
                    this.avisos=res.data;
                })
                .catch(err => {
                    console.error(err);
                })
            }
        },
        mounted() {
            this.get_avisos();
        },
        created() {
            // Comproba el missatges si reps un event
            this.$eventBus.$on('msg-enviat', this.get_avisos);
        },
        beforeDestroy() {
            this.$eventBus.$off('msg-enviat');
        }
}
</script>

<style lang="sass" scope>
.margens
    margin-top: 10px


</style>
