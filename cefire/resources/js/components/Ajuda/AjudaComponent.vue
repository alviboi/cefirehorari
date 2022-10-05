<template>
<div id="tabs" class="container">

    <div class="tabs">
        <a v-on:click="activetab='horari'" v-bind:class="[ activetab === 'horari' ? 'active' : '' ]">Horaris</a>
        <a v-on:click="activetab='horaritots'" v-bind:class="[ activetab === 'horaritots' ? 'active' : '' ]">Horaris Assessors</a>
        <a v-on:click="activetab='buscaenhoraris'" v-bind:class="[ activetab === 'buscaenhoraris' ? 'active' : '' ]">Busca en horaris</a>
        <a v-on:click="activetab='centres'" v-bind:class="[ activetab === 'centres' ? 'active' : '' ]">Busca en centres</a>
        <a v-on:click="activetab='calendar'" v-bind:class="[ activetab === 'calendar' ? 'active' : '' ]">Afegix guardies</a>
        <a v-on:click="activetab='configuracio'" v-bind:class="[ activetab === 'configuracio' ? 'active' : '' ]">Configuració</a>

    </div>

    <div class="content">
        <div class="tabcontent">
        <transition name="component-fade" mode="out-in">
            <component  v-bind:is="activetab"></component>
        </transition>
        </div>
    </div>

</div>
</template>

<script>

Vue.component('horariComponent', require('./horariComponent.vue').default);
Vue.component('horariasseComponent', require('./horariasseComponent.vue').default);
Vue.component('buscahorariComponent', require('./buscahorariComponent.vue').default);
Vue.component('afegixguardiaComponent', require('./afegixguardiaComponent.vue').default);
Vue.component('centresComponent', require('./centresComponent.vue').default);
Vue.component('configComponent', require('./configComponent.vue').default);


export default {
    data() {
        return {
            ajuda2: 'aa',
            activetab: 'horari',
            comp: ['horari','horaritots','buscaenhoraris','centres','calendar','configuracio']
        }
    },
    components: {
        'horari' : {
        template: '<div ><horariComponent /></div>'
        },
        'horaritots' : {
        template: '<div ><horariasseComponent /></div>'
        },
        'buscaenhoraris' : {
        template: '<div ><buscahorariComponent /></div>'
        },
        'centres' : {
        template: '<div ><centresComponent /></div>'
        },
        'calendar' : {
        template: '<div ><afegixguardiaComponent /></div>'
        },
        'configuracio' : {
        template: '<div ><configComponent /></div>'
        }
    },
    methods: {
        activat() {
            if(!this.comp.includes(this.$root.$data.ajuda)){
                this.activetab = 'horari';
            } else {
                this.activetab = this.$root.$data.ajuda;
            }
        }
    },
    mounted() {
        this.activat();
    },

}
</script>

<style lang="sass" scope>
/* Import Google Font */
@import url(https://fonts.googleapis.com/css?family=Nunito+Sans)

/* RESET */
*
  box-sizing: border-box
  margin: 0
  padding: 0


/* STYLING */
.container
    max-width: 100%
    min-width: 100%
    margin: 20px auto
    font-family: "Nunito Sans", Arial, Helvetica, sans-serif
    color: #888


/* Style the tabs */
.tabs
    overflow: hidden
    margin-left: 20px
    margin-bottom: -2px // hide bottom border


.tabs ul
    list-style-type: none
    margin-left: 20px


.tabs a
    float: left
    cursor: pointer
    padding: 12px 24px
    transition: background-color 0.2s
    border: 1px solid #ccc
    border-right: none
    background-color: #f1f1f1
    border-radius: 10px 10px 0 0
    font-weight: bold

.tabs a:last-child
    border-right: 1px solid #ccc


/* Change background color of tabs on hover */
.tabs a:hover
    background-color: #aaa
    color: #fff


/* Styling for active tab */
.tabs a.active
    background-color: #fff
    color: #484848
    border-bottom: 2px solid #fff
    cursor: default


/* Style the tab content */
.tabcontent
    padding: 30px
    border: 1px solid #ccc
    border-radius: 10px
    box-shadow: 3px 3px 6px #e1e1e1

</style>
