<template>
    <div id="edita_perfil" uk-modal>
        <div class="uk-modal-dialog">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <div class="uk-modal-header">
                <h2 class="uk-modal-title">Edita les teues dades</h2>
            </div>
            <div class="uk-modal-body bac">
                    Nota: Les dades t'apareixeran reflectides la pròxima vegada que et logues.<br>
                    <div class="uk-margin">
                        <div class="uk-inline">
                            <span class="uk-form-icon" uk-icon="icon: sign-in"></span>
                            <input :value="datos.id" class="uk-input" type="text" placeholder="id" disabled>
                        </div>
                    </div>

                    <div class="uk-margin">
                        <div class="uk-inline">
                            <span class="uk-form-icon" uk-icon="icon: user"></span>
                            <input v-model="datos.name" class="uk-input" type="text" placeholder="Nom">
                        </div>
                    </div>

                    <div class="uk-margin">
                        <div class="uk-inline">
                            <span class="uk-form-icon" uk-icon="icon: mail"></span>
                            <input v-model="datos.email" class="uk-input" type="text" placeholder="Mail">
                        </div>
                    </div>
                    <div class="uk-margin">
                        <div class="uk-inline">
                            <span class="uk-form-icon" uk-icon="icon: world"></span>
                            <input v-model="datos.Perfil" class="uk-input" type="number" min="0" max="3" step="1" placeholder="Perfil">
                        </div>
                    </div>

                    <div class="uk-margin">
                        <div class="uk-inline">
                            <span class="uk-form-icon" uk-icon="icon: credit-card"></span>
                            <input v-model="datos.rfid" class="uk-input" type="text" placeholder="rfid">
                        </div>
                    </div>
                    <div class="uk-margin">
                        <div class="uk-inline">
                            <span class="uk-form-icon" uk-icon="icon: lock"></span>
                            <input v-model="contrasenya" class="uk-input" type="text" placeholder="contrasenya">
                        </div>
                    </div>
                    <div class="uk-margin">
                        <div class="uk-inline">
                            <span class="uk-form-icon" uk-icon="icon: lock"></span>
                            <input v-model="contrasenya2" class="uk-input" type="text" placeholder="confirma contrasenya">
                        </div>
                    </div>

            </div>
            <div class="uk-modal-footer uk-text-right">
                <button class="uk-button uk-button-default" @click="ix()" type="button">Cancel·la</button>
                <button class="uk-button uk-button-primary" type="button" @click="envia()">Guarda</button>
            </div>
        </div>
    </div>

</template>

<script>

/**
En aquest component es crea el modal per a poder editar les dades de l'assessor
 */

export default {
    data() {
        return {
            datos: {},
            resposta: "",
            update: "",
            contrasenya: "",
            contrasenya2: ""
        }
    },
    props: ['show-edita'],
    watch: {
        showEdita(){
            this.mostra_modal();
        }
    },

    methods: {
        // Agafa les dades de l'usuari
        get_datos(){
            axios.get("user_get")
            .then(res => {
                console.log(res);
                this.datos=res.data;
            })
            .catch(err => {
                console.error(err);
            })
        },
        // Surt del modal
        ix() {
            this.$eventBus.$emit('tanca-edita');
            UIkit.modal('#modal_edita').hide();
        },
        // Envia les dades per a actualitzar
        envia() {
            if (this.contrasenya == this.contrasenya2) {

                let params ={
                    'id': this.datos.id,
                    'nom': this.datos.name,
                    'mail': this.datos.email,
                    'perfil': this.datos.Perfil,
                    'rfid': this.datos.rfid,
                    'contrasenya': this.contrasenya
                }
                axios.put("user/"+this.datos.id,params)
                .then(res => {
                    console.log(res);
                    this.$toast.success("Dades guardades correctament");
                })
                .catch(err => {
                    console.error(err);
                })

            } else {
                alert("Les contrasenyes no coincideixen");
            }

        },
        // Funció que mostra el modal
        mostra_modal() {
            if (this.showEdita == true) {
                UIkit.modal('#edita_perfil',{ bgClose: false, escClose: false, modal: false, keyboard:false}).show();
                this.get_datos();

            } else {
                UIkit.modal('#edita_perfil').hide();
            }

        }
    },
    mounted() {
        this.id = this._uid;
    },


}
</script>

<style>

</style>
