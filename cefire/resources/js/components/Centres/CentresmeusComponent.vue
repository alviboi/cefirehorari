<template>
    <centres-component :busqueda2="bus" :editable='true'/>
</template>

<script>
/**
 * Aquest component agafa només els centres que tinga l'assessor
 */
export default {
    data() {
        return {
            bus: ""
        }
    },
    methods: {
        // Comproba el nom de l'assessor que està logat
        logat() {
            axios.get("logat")
            .then(res => {
                console.log(res);
                this.bus=res.data;
            })
            .catch(err => {
                console.error(err);
            })
        }
    },
    mounted() {
        this.logat();
    },
}
</script>

<style>

</style>


