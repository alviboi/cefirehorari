<template>
<div class="general_centres">
    <form id="search">
        Busca <input v-model="busqueda"> Centres per pàgina <input type="number" v-model="centres_per_pagina">
    </form>
<hr>
 <transition-group name="flip-list" tag="div">
     <div key="move_1">

    <table>
    <thead>
      <tr>
        <th v-for="key in columnes"
          @click="sortBy(key)"
          :class="{ active: ordenaKey == key }"
          :key=key>
          {{ key | capitalize }}
          <span class="arrow" :class="ordena[key] > 0 ? 'asc' : 'dsc'">
          </span>
        </th>
        <th v-if="editable">
            Edita
        </th>
      </tr>
    </thead>


    <tbody name="ordenaCeldes" is="transition-group">
      <tr v-for="(entry,index) in mostra" :key=index>
        <td v-for="(key, index2) in columnes" :key=index2>
          {{entry[key]}}
        </td>
        <td v-if="editable">
            <button @click="edita_centre(entry)" class="boto_edita"><i class="fas fa-edit"></i></button>
        </td>
      </tr>
    </tbody>



  </table>
<hr>
</div>
<div key="move_2">
    <nav aria-label="Page navigation example">
        <ul class="paginacio">
            <li class="pagina-item">
                <button type="button" class="pagina-link" v-if="pagina != 1" @click="pagina--"> Anterior </button>
            </li>
            <li class="pagina-item">
                    <button type="button" class="pagina-link" v-for="(pageNumber,index) in pagines.slice(pagina-1, pagina+5)" :key="index" @click="pagina = pageNumber"> {{pageNumber}} </button>				</li>
            <li class="pagina-item">
                <button type="button" class="pagina-link" v-if="pagina < pagines.length" @click="pagina++"> Posterior </button>
            </li>
            <li>
                <span style="margin-left: 10px;"><button type="button" class="uk-button uk-button-primary" @click="copia">Copia Mails</button></span>
            </li>
            <li>
                <span style="margin-left: 10px;"><button type="button" class="uk-button uk-button-primary" @click="fullcalcul">Descarrega csv</button></span>
            </li>
        </ul>
        
    </nav>

</div>

 </transition-group>

<div id="edita-centre" uk-modal>
    <div class="uk-modal-dialog uk-modal-body">
        <h2 class="uk-modal-title">Edita centre</h2>
        <div class="uk-form-controls">
            <label for="recipient-name" class="uk-form-label">Centre:</label>
            <input type="text" class="uk-input" id="recipient-name" v-model="editant['nom']">
          </div>
          <div class="uk-form-controls">
            <label for="recipient-codi" class="uk-form-label">Codi:</label>
            <input type="number" class="uk-input" id="recipient-codi" v-model="editant['codi']">
          </div>
          <div class="uk-form-controls">
            <label for="recipient-situacio" class="uk-form-label">Situació:</label>
            <select type="text" class="uk-select" id="recipient-xituacio" v-model="editant['situacio']">
                <option>PUB.</option>
                <option>CON.</option>
            </select>
          </div>
          <div class="uk-form-controls">
            <label for="recipient-cp" class="uk-form-label">CP:</label>
            <input type="number" class="uk-input" id="recipient-cp" v-model="editant['CP']">
          </div>
          <div class="uk-form-controls">
            <label for="recipient-ciutat" class="uk-form-label">Ciutat:</label>
            <input type="text" class="uk-input" id="recipient-ciutat" v-model="editant['ciutat']">
          </div>
          <div class="uk-form-controls">
            <label for="recipient-assessor" class="uk-form-label">Assessoria:</label>
                <select class="uk-select" id="recipient-assessor" v-model="editant['assessor']">
                    <option v-for="(user,key) in users" :key="key" :value="user.name">{{user.name}}</option>
                </select>
          </div>
          <div class="uk-form-controls">
            <label for="recipient-contacte" class="uk-form-label">Contacte:</label>
            <input type="text" class="uk-input" id="recipient-contacte" v-model="editant['contacte']">
          </div>
          <div class="uk-form-controls">
            <label for="recipient-mail_contacte" class="uk-form-label">Mail contacte:</label>
            <input type="text" class="uk-input" id="recipient-mail_contacte" v-model="editant['mail_contacte']">
          </div>
          <div class="uk-form-controls">
            <label for="recipient-tlf" class="uk-form-label">Tlf contacte:</label>
            <input type="number" class="uk-input" id="recipient-tlf" v-model="editant['tlf_contacte']">
          </div>
          <div class="uk-form-controls">
            <label for="recipient-ob" class="uk-form-label">Observacions:</label>
            <textarea type="text" class="uk-input" rows="2" id="recipient-ob" v-model="editant['Observacions']">
            </textarea>
          </div>
          <div class="uk-text-center uk-margin">
              <a :href="url" class="uk-margin uk-h4">Informació del centre</a>
          </div>

        <p class="uk-text-right">
            <button class="uk-button uk-button-default" @click="tanca()" type="button">Tanca</button>
            <button class="uk-button uk-button-danger" @click="borra()" type="button">Borra centre</button>
            <button class="uk-button uk-button-primary" type="button" @click="envia_dades()">Salva</button>
        </p>
    </div>
</div>



</div>
</template>

<script>
/**
 * Modal que mostra tots els centres de la base de dades
 */

import UIkit from 'uikit';
import Icons from 'uikit/dist/js/uikit-icons';
export default {
    data() {
        return {
            users: {},
            editant: {},
            centres_per_pagina: 15,
            pagina: 1,
            pagines: [],
            ordenaKey: [],
            ordena: {"id":1,"assessor":1,"nom":1,"codi":1,"situacio":1,"CP":1,"ciutat":1,"contacte":1,"mail_contacte":1,"tlf_contacte":1,"Observacions":1},
            busqueda: '',
            columnes: ["id","assessor","nom","codi","situacio","CP","ciutat","contacte","mail_contacte","tlf_contacte","Observacions"],
            datos: [],
            editable_c: true
        }
    },
    // busqueda2 es quan es busquen nomes els centres de l'assessor logat
    // editable si es pot editar el centre o no
    props: ['busqueda2','editable'],
    computed: {
        // Torna la url per a enllaçar en la guia de centres
        url () {
            return "http://www.ceice.gva.es/abc/i_guiadecentros/es/centro.asp?codi="+this.editant['codi'];
        },
        // Mostra les dades filtrades quan es filtres
        dadesFiltrades() {
            var ordenaKey = this.ordenaKey
            var filtraKey = this.busqueda && this.busqueda.toLowerCase()
            var order = this.ordena[ordenaKey] || 1
            var data = this.datos
            if (filtraKey) {
                data = data.filter(function (row) {
                return Object.keys(row).some(function (key) {
                    return String(row[key]).toLowerCase().indexOf(filtraKey) > -1
                })
                })
            }
            if (ordenaKey) {
                data = data.slice().sort(function (a, b) {
                a = a[ordenaKey]
                b = b[ordenaKey]
                return (a === b ? 0 : a > b ? 1 : -1) * order
                })
            }
            return data

        },
        // Les dades mostrades es pasen a la funció per a calcula el número de pàgines
        mostra () {
            return this.pagina_f(this.dadesFiltrades);
        }
    },
    filters: {
        capitalize: function (str) {
            return str.charAt(0).toUpperCase() + str.slice(1)
        }
    },
    methods: {
        // Agafa usuaris de la base de dades
        agafa_users(){
            axios.get("user")
            .then(res => {
                console.log(res);
                this.users=res.data;
            })
            .catch(err => {
                console.error(err);
            })
        },
        fullcalcul () {
            let csv;
            let dat;

            // Loop the array of objects
            for(let row = 0; row < this.dadesFiltrades.length; row++){
                let keysAmount = Object.keys(this.dadesFiltrades[row]).length
                let keysCounter = 0

                // If this is the first row, generate the headings
                if(row === 0){
                    // Loop each property of the object
                    for(let key in this.dadesFiltrades[row]){
                                        // This is to not add a comma at the last cell
                                        // The '\r\n' adds a new line
                        csv += key + (keysCounter+1 < keysAmount ? ',' : '\r\n' )
                        keysCounter++
                    }
                } else {
                for(let key in this.dadesFiltrades[row-1]){
                    if (key == "Observacions"){
                       if (this.dadesFiltrades[row-1][key] !== null){
                           dat = this.dadesFiltrades[row-1][key].replace(/(\r\n|\n|\r)/gm,"   ");
                       } else {
                           dat = ""; 
                       }
                       
                       csv += dat + (keysCounter+1 < keysAmount ? ',' : '\r\n' );
                    } else {
                        csv += this.dadesFiltrades[row-1][key] + (keysCounter+1 < keysAmount ? ',' : '\r\n' )
                    }
                    keysCounter++
                }
                }

                keysCounter = 0
            }
            /**********************/
                let keysCounter = 0
                let keysAmount = Object.keys(this.dadesFiltrades[this.dadesFiltrades.length-1]).length
                for(let key in this.dadesFiltrades[this.dadesFiltrades.length-1]){
                    if (key == "Observacions"){
                       if (this.dadesFiltrades[this.dadesFiltrades.length-1][key] !== null){
                           dat = this.dadesFiltrades[this.dadesFiltrades.length-1][key].replace(/(\r\n|\n|\r)/gm,"   ");
                       } else {
                           dat = "";
                       }

                       csv += dat + (keysCounter+1 < keysAmount ? ',' : '\r\n' );
                    } else {
                        csv += this.dadesFiltrades[this.dadesFiltrades.length-1][key] + (keysCounter+1 < keysAmount ? ',' : '\r\n' )
                    }
                    keysCounter++
                }

            /*********************/

            // Once we are done looping, download the .csv by creating a link
            let link = document.createElement('a')
            link.id = 'download-csv'
            link.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(csv));
            link.setAttribute('download', 'centresfiltrats.csv');
            document.body.appendChild(link);
            document.querySelector('#download-csv').click();
        },
        // Obre el modal per a editar un centre
        edita_centre(edit) {
            UIkit.modal("#edita-centre").show();
            this.editant = edit;
        },
        // Ordena les dades
        sortBy(key) {
            this.ordenaKey = key;
            this.ordena[key] = this.ordena[key] * -1;
        },
        // Agafa totes les dades d'un centre
        get_centres() {
            axios.get("centres")
            .then(res => {
                console.log(res);
                this.datos=res.data;
            })
            .catch(err => {
                console.error(err);
            })
            this.setPagines();
        },
        // Calcula el nombre de pagines que te la paginacio
        setPagines () {
            this.pagines = [];
			let NumeroPagines = Math.ceil(this.dadesFiltrades.length / this.centres_per_pagina);
			for (let index = 1; index <= NumeroPagines; index++) {
				this.pagines.push(index);
            }
        },
        // Pagina les dades en funció del numero de pàgines
        pagina_f (datos) {
			let pagina = this.pagina;
			let perPagina = this.centres_per_pagina;
			let from = (pagina * perPagina) - perPagina;
			let to = (pagina * perPagina);
			return  datos.slice(from, to);
        },
        // Envia les dades d'actualització d'un centre
        envia_dades() {
            let user_id=0;
            for (let index = 0; index < this.users.length; index++) {
                if (this.users[index]['name'] == this.editant['assessor']){
                    user_id=this.users[index]['id'];
                }
            }
            this.editant.user_id=user_id;


            let params = this.editant;
            let url = "centres/"+this.editant['id'];
            axios.put(url,params)
            .then(res => {
                console.log(res);
                this.$toast.success("Dades guardades");
            })
            .catch(err => {
                this.$toast.error(err.message);
                console.error(err);
            })
        },
        // Envia petició d'esborrar un centre
        borra() {
            axios.delete("centres/"+this.editant['id'])
            .then(res => {
                console.log(res);
                this.$toast.success("Has esborrat el centre");
                for (let index = 0; index < this.datos.length; index++) {
                    if (this.datos[index]['id'] == this.editant['id']){
                        this.datos.splice(index,1);
                    }
                }
                this.editant = {};
            })
            .catch(err => {
                console.error(err);
                this.$toast.error("Alguna cosa ha anat malament");
            })
        },
        tanca() {
            this.editant={};
            UIkit.modal("#edita-centre").hide();
        },
        copia() {
            var element = '';
            for (let index = 0; index < this.dadesFiltrades.length; index++) {
                if (this.dadesFiltrades[index]['mail_contacte'].includes('@')){
                    element+=this.dadesFiltrades[index]['mail_contacte']+', ';
                }
            }
            this.copyText(element);
            this.$toast.success("Mails copiats");
        },
        copyText (textToCopy) {
            this.copied = false

            // Create textarea element
            const textarea = document.createElement('textarea')

            // Set the value of the text
            textarea.value = textToCopy

            // Make sure we cant change the text of the textarea
            textarea.setAttribute('readonly', '');

            // Hide the textarea off the screnn
            textarea.style.position = 'absolute';
            textarea.style.left = '-9999px';

            // Add the textarea to the page
            document.body.appendChild(textarea);

            // Copy the textarea
            textarea.select()

            try {
                var successful = document.execCommand('copy');
                this.copied = true
            } catch(err) {
                this.copied = false
            }

            textarea.remove()

        }

    },
    watch: {
		dadesFiltrades () {
            this.setPagines();
        },
        busqueda2 () {
            this.busqueda=this.busqueda2;
        },
        editable () {
            this.editable_c=this.editable;
        },
        centres_per_pagina(newVal,oldVal){
            this.pagines = [];
			let NumeroPagines = Math.ceil(this.dadesFiltrades.length / newVal);
			for (let index = 1; index <= NumeroPagines; index++) {
				this.pagines.push(index);
            }
        }

	},
    mounted() {
        this.get_centres();
        this.agafa_users();
    }
}
</script>

<style lang="sass" scope>
    .general_centres
        table
            /* border: 2px solid #42b983; */
            border-radius: 3px
            background-color: #fff

        th
            background-color: #22111a
            color: rgba(255,255,255,0.66)
            cursor: pointer
            .active
                color: #fff
                .arrow
                    opacity: 1

        td
            background-color: #f9f9f9

        th, td
            min-width: 3px
            padding: 2px 10px
            max-width: 200px
            overflow: hidden


    .arrow
        display: inline-block
        vertical-align: middle
        width: 0
        height: 0
        margin-left: 5px
        opacity: 0.66

    .arrow.asc
        border-left: 4px solid transparent
        border-right: 4px solid transparent
        border-bottom: 4px solid #fff

    .arrow.dsc
        border-left: 4px solid transparent
        border-right: 4px solid transparent
        border-top: 4px solid #fff

    button.pagina-link
        display: inline-block
        font-size: 20px
        color: #22111a
        font-weight: 500
        background-color: white
        border: #22111a 1px solid
        padding: 3px
        cursor: pointer

    .offset
        width: 500px !important
        margin: 20px auto

    .paginacio
        display: flex
        padding-left: 0
        list-style: none
        border-radius: .25rem

    // Format de les transicions del llistat

    .ordenaCeldes-enter-active,.ordenaCeldes-leave-active
        transition: all 1s

    .ordenaCeldes-enter,.ordenaCeldes-leave-to
            opacity: 0
            transform: translateX(30px)

    .flip-list-move
        transition: transform 1s


    .boto_edita
        background-color: #22111a
        color: white
        border-color: #444
        cursor: pointer
        padding: 3px


</style>
