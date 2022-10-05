<template>
    <div class="general">
        <div v-for="(user, index) in missatges" :key="index" class="missatge" :uk-toggle="'target: #missatge'+user.id">
            <div class="de"><span uk-icon="user"></span> {{user.name}}</div>
            <div><span uk-icon="mail"></span> {{user.cap}}</div>
            <div :id="'missatge'+user.id" uk-modal>
            <div class="uk-modal-dialog uk-modal-body">
                <h4><b>De:</b> {{user.name}}</h4>
                <h5><b>Capçalera:</b> {{user.cap}}</h5>
                <p>{{user.missatge}}</p>
                <p class="uk-text-right">
                    <button @click="borra(user.id)" class="uk-button uk-button-default uk-modal-button" type="button">Borra</button>
                    <button class="uk-button uk-button-primary uk-modal-close" type="button">Tanca</button>
                </p>
            </div>
        </div>
        </div>

    </div>
</template>

<script>
export default {
    // Crea tots els modal per a quan vullgues llegir un missatge
    data() {
        return {
            // users: [],
            user: 0,
            missatges: [],
            results: []
        }
    },
    methods: {
        // Obre el modal del missatge concret
        obri(id){
            UIkit.modal(id).show();
        },
        // Esborra el missatge de la base de dades
        borra(id){
            for (let index = 0; index < this.missatges.length; index++) {
                if(this.missatges[index].id == id){
                    this.missatges.splice(index,1);
                }
            }
            let url = "notificacions/" + id;
            axios.delete(url)
            .then(res => {
                console.log(res)
            })
            .catch(err => {
                console.error(err);
            })
            UIkit.modal('#missatge'+id).hide();
        },
        // Agafa tots els missatges
        agafa_missatges() {
            axios.get("notificacions")
            .then(res => {
                this.missatges = res.data;
                console.log(res);
            })
            .catch(err => {
                console.log(err);
            })
        },
        // Crear canal de websocket per a poder llegir-lo i que et notifique l'arribada d'un missatge
        channel_create_mail(){
            var aux;
            var self=this;
            let chan='MailEntrada';
            channel.bind(chan,
                function(data) {
                    aux = data;
                    if(aux.user == Vue.prototype.$user_id){
                        self.missatges.push(aux.msg);
                        self.$toast.success("Has rebut un correu de "+aux.msg.name);
                    }
                }
            );
        },

    },
    mounted() {
        this.agafa_missatges();
        this.channel_create_mail();
    },
}
</script>

<style lang="sass" scope>

.general
    display: flex
    flex-direction: column
    align-items: stretch
    align-content: flex-start

.missatge
    display: block
    flex-grow: 0
    flex-shrink: 1
    flex-basis: auto
    align-self: auto
    margin-top: 15px
    border: 2px solid grey
    color: black
    background-color: white
    border-radius: 10px
    padding: 5px
    .de
        background: hsl(0, 0%, 85%)
        overflow-y: hidden

</style>
