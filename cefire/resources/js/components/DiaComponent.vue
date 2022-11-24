<template>
  <div>

    <div class="titulet" data-uk-tooltip="animation: true; offset: 2;" :title="data">{{ nom_dia }} {{ dia_mes }}  </div> <!--<span v-html="mati_icon"></span>-->
    <div class="dia">
      <!-- COLUMNA LATERAL DESPLEGABLE -->
      <div class="lateral_esquerre flex-container">
        <button
          data-toggle="tooltip"
          data-placement="bottom"
          title="FITXATGE"
          @click="afegix_cefire()"
          class="btn btn-primary btn-sm"
        >
          <i class="fa-solid fa-user-check"></i>
        </button>
        <button
          data-toggle="tooltip"
          data-placement="bottom"
          title="CURS"
          :uk-toggle="'target: #curs-modal'+this._uid"
          class="btn btn-primary btn-sm"
        >
          <i class="fas fa-chalkboard-teacher"></i>
        </button>
        <button
          data-toggle="tooltip"
          data-placement="bottom"
          title="GUARDIA"
          @click="afegix('guardia')"
          class="btn btn-primary btn-sm"
        >
          <i class="fas fa-dog"></i>
        </button>
        <button
          data-toggle-second="tooltip"
          data-placement="bottom"
          title="COMPENSA"
          class="btn btn-primary btn-sm"
          :uk-toggle="'target: #compensa-modal'+this._uid"
        >
          <i class="fas fa-umbrella-beach"></i>
        </button>
        <button
          data-toggle-second="tooltip"
          data-placement="bottom"
          title="COM. SERV."
          class="btn btn-primary btn-sm"
          :uk-toggle="'target: #visita-modal'+this._uid"
        >
          <i class="fas fa-school"></i>
        </button>
        <button
          data-toggle-second="tooltip"
          data-placement="bottom"
          title="MOSCOSO"
          class="btn btn-primary btn-sm"
          @click="emitix('obre-moscoso')"
        >
          <i class="fa-solid fa-champagne-glasses"></i>
        </button>
        <button
          data-toggle-second="tooltip"
          data-placement="bottom"
          title="VACANCES"
          class="btn btn-primary btn-sm"
          @click="emitix('obre-vacances')"
        >
          <i class="fa-solid fa-plane-departure"></i>
        </button>
        <button
          data-toggle-second="tooltip"
          data-placement="bottom"
          title="PERMIS"
          class="btn btn-primary btn-sm"
          @click="obre_permis()"
        >
          <i class="fa-solid fa-bed-pulse"></i>
        </button>
      </div>
      <!-- COLUMNA LATERAL DESPLEGABLE -->
      <!-- ESPAI ON ES POSEN LES ETIQUETES -->
      <div id="principal" class="principal">
        <transition-group name="list-complete" tag="div">
          <div
            v-for="cef in cefire"
            class="s-cefire list-complete-item"
            :key="'c'+ act + cef.id"
            data-uk-tooltip="pos: right; animation: true; offset: 12;"
            :title="cef.inici+'-'+cef.fi"
          >
            <span @click="borra_par('cefire', cef.id)" class="cerrar" />
            <span :class="(cef.fi=='00:00:00') ? 'falta_fitxar' : ''"></span>
          </div>

          <div
            v-for="com in compensa"
            class="s-compensa list-complete-item"
            :key="'com' + com.id"
            data-uk-tooltip="pos: right; animation: true; offset: 12;"
            :title="com.motiu"
          >
            <span @click="borra_par('compensa', com.id)" class="cerrar" />
            <span :class="(com.confirmat!=1) ? 'falta_validar' : ''"></span>

          </div>

          <div
            v-for="cur in curs"
            class="s-curs list-complete-item"
            :key="'cur' + cur.id"
            data-uk-tooltip="pos: right; animation: true; offset: 12;"
            :title="cur.curs"
          >
            <span @click="borra_par('curs', cur.id)" class="cerrar" />
          </div>
          <div
            v-for="vis in visita"
            class="s-visita list-complete-item"
            :key="'vis' + vis.id"
            data-uk-tooltip="pos: right; animation: true; offset: 12;"
            :title="vis.centre"
          >
            <span @click="borra_par('visita', vis.id)" class="cerrar" />
          </div>
          <div
            v-for="gua in guardia"
            class="s-guardia list-complete-item"
            :key="'gua' + gua.id"
          >
            <span @click="borra_par('guardia', gua.id)" class="cerrar" />
          </div>
          <div
            v-for="inc in incidencies"
            class="s-incidencia list-complete-item"
            :key="'inc' + inc.id"
            data-uk-tooltip="pos: right; animation: true; offset: 12;"
            :title="inc.incidencia"
          >
            <span @click="borra_par('incidencies', inc.id)" class="cerrar" />
          </div>
          <div
            v-for="mosc in moscosos"
            class="s-moscosos list-complete-item"
            :key="'mosc' + mosc.id"
          >
            <span @click="borra_par('moscosos', mosc.id)" class="cerrar" />
            <span :class="(mosc.aprobada!=1) ? 'falta_validar' : ''"></span>
          </div>
          <div
            v-for="vac in vacances"
            class="s-vacances list-complete-item"
            :key="'vac' + vac.id"
          >
            <span @click="borra_par('vacances', vac.id)" class="cerrar" />
            <span :class="(vac.aprobada!=1) ? 'falta_validar' : ''"></span>
          </div>
          <div
            v-for="perm in permis"
            class="s-permis list-complete-item"
            :key="'perm' + perm.id"
            data-uk-tooltip="pos: right; animation: true; offset: 12;"
            :title="perm.motiu"
          >
            <span @click="borra_par('permis', perm.id)" class="cerrar" />
            <span @click="mira_arxiu('/'+perm.arxiu)" class="arxiu" />
          </div>
        </transition-group>
      </div>
      <!-- ESPAI ON ES POSEN LES ETIQUETES -->
    </div>

    <!-- MODALS DE LES FINESTRES -->
    <!-- Curs modal -->
    <div :id="'curs-modal'+this._uid" uk-modal>
      <div class="uk-modal-dialog uk-modal-body">
        <fieldset class="uk-fieldset">
          <div class="uk-margin">
            <input
              v-model="curs_i"
              class="uk-form-large data-uk-input uk-width-1-1"
              type="text"
              placeholder="Curs a realitzar"
              required
            />
          </div>
          <div class="uk-margin">                        
              <label>Hora: </label>
                  <vue-timepicker :minute-interval="5" v-model="inici"></vue-timepicker>
              <span> a </span>
                  <vue-timepicker :minute-interval="5" v-model="fi"></vue-timepicker> 
              </div>
          </fieldset>
        <p class="uk-text-right">
          <button
            class="uk-button uk-button-default uk-modal-close"
            type="button"
          >
            Cancel·la
          </button>
          <button
            @click="salva('curs')"
            class="uk-button uk-button-primary uk-modal-close"
            type="button"
          >
            Salva
          </button>
        </p>
      </div>
    </div>

    <!-- Compensa modal -->
    <div :id="'compensa-modal'+this._uid" uk-modal>
      <div class="uk-modal-dialog uk-modal-body">
        <fieldset class="uk-fieldset">
          <div class="uk-margin">
            <input
              v-model="compensa_i"
              class="uk-form-large data-uk-input uk-width-1-1"
              type="text"
              placeholder="Motiu pel que compenses"
            />
          </div>
          <div class="uk-margin">                        
              <label>Hora: </label>
                  <vue-timepicker :minute-interval="5" v-model="inici"></vue-timepicker>
              <span> a </span>
                  <vue-timepicker :minute-interval="5" v-model="fi"></vue-timepicker> 
          </div>
        </fieldset>
        <p class="uk-text-right">
          <button
            class="uk-button uk-button-default uk-modal-close"
            type="button"
          >
            Cancel·la
          </button>
          <button
            @click="salva('compensa')"
            class="uk-button uk-button-primary uk-modal-close"
            type="button"
          >
            Salva
          </button>
        </p>
      </div>
    </div>

    <!-- Visita modal -->
    <div :id="'visita-modal'+this._uid" uk-modal>
      <div class="uk-modal-dialog uk-modal-body">
        <fieldset class="uk-fieldset">
          <div class="uk-margin">
            <input
              v-model="visita_i"
              class="uk-form-large data-uk-input uk-width-1-1"
              type="text"
              placeholder="Visita a realitzar"
            />
          </div>
          <div class="uk-margin">                        
              <label>Hora: </label>
                  <vue-timepicker :minute-interval="5" v-model="inici"></vue-timepicker>
              <span> a </span>
                  <vue-timepicker :minute-interval="5" v-model="fi"></vue-timepicker> 
          </div>
          </fieldset>
        <p class="uk-text-right">
          <button
            class="uk-button uk-button-default uk-modal-close"
            type="button"
          >
            Cancel·la
          </button>
          <button
            @click="salva('visita')"
            class="uk-button uk-button-primary uk-modal-close"
            type="button"
          >
            Salva
          </button>
        </p>
      </div>
    </div>
    <!-- Permis modal -->
    <div :id="'permis-modal'+this._uid" uk-modal>
      <div class="uk-modal-dialog uk-modal-body">
        <fieldset class="uk-fieldset">
          <div class="uk-margin">
            <input
              v-model="permis_i"
              class="uk-form-large data-uk-input uk-width-1-1"
              type="text"
              placeholder="Motiu del permís"
            />
            <div class="uk-margin">                        
              <label>Hora: </label>
                  <vue-timepicker :minute-interval="5" v-model="inici"></vue-timepicker>
              <span> a </span>
                  <vue-timepicker :minute-interval="5" v-model="fi"></vue-timepicker> 
              </div>
            <div :id="'upload'+this._uid" class="js-upload uk-placeholder uk-text-center">
                <div>
                        {{avis_pujada}}
                </div>
                <span uk-icon="icon: cloud-upload"></span>
                <span class="uk-text-middle">Puja o arrastra l'arxiu justificant en <b>PDF</b></span>
                <div uk-form-custom>
                    <input type="file">
                    <span class="uk-link">Selecciona un arxiu</span>
                </div>

            </div>
            <progress id="js-progressbar" class="uk-progress" value="0" max="100" hidden></progress>
          </div>
        </fieldset>
        <p class="uk-text-right">
          <button
            class="uk-button uk-button-default uk-modal-close"
            type="button"
          >
            Cancel·la
          </button>
          <button
            @click="salva('permis')"
            class="uk-button uk-button-primary uk-modal-close"
            type="button"
          >
            Salva
          </button>
        </p>
      </div>
    </div>
    <!-- MODALS DE LES FINESTRES -->
  </div>
</template>

<script>
/**
 * Component d'un únic dia que controla totes les accions que es realitzen sobre el mateix dia
 */
import VueTimepicker from 'vue2-timepicker'
import 'vue2-timepicker/dist/VueTimepicker.css'
export default {
  data() {
    return {
      id_user: null,
      id: null,
      compensa: {},
      cefire: {},
      curs: {},
      visita: {},
      guardia: {},
      incidencies: {},
      moscosos: {},
      vacances: {},
      permis: {},
      curs_i: "",
      compensa_i: "",
      visita_i: "",
      permis_i: "",
      inici: "",
      fi: "",
      dies: [
            'Diumenge',
            'Dilluns',
            'Dimarts',
            'Dimecres',
            'Dijous',
            'Divendres',
            'Dissabte',
            'Diumenge',
    ],
    nom_dia: "",
    dia_mes: 0,
    arxiu_pujat: '',
    avis_pujada: "",
    act: 1
    };
  },
  props: ['data'],
  components: {
        VueTimepicker        
  },
  methods: {
    emitix(emitix_bus) {
      this.$eventBus.$emit(emitix_bus);
    },
    // Mètode per a descarregar el arxiu, es reb com un objecte binari de grans dimensions (blob) per a reconstrueix i permet descarregar-se. D'aquesta
    // manera l'arxiu no té cap enllaç per a poder-se descarregar.
    mira_arxiu(d){
        let url="download_permis";
        let params={
            "arxiu": d
        }
        axios(
            {
                url: url,
                method: 'POST',
                responseType: 'blob', // important
                params: params
            }
            )
        .then(response => {
            console.log(response);
            var blob = new Blob([response.data], { type: 'application/pdf' });
            var url = window.URL.createObjectURL(blob);
            var link = document.createElement('a');
            link.href = url;
            link.download = 'permis.pdf'
            link.click();
        })
        .catch(err => {
            this.$toast.error("Error: Pareix que no s'ha pujat el permís");
            console.error(err);
        })
    },
    /**
     * Pujada de l'arxiu del permís
     */
    obre_permis(){
            self=this;
            var arxiu_p;
            this.id = this._uid;
            var bar = document.getElementById('js-progressbar');
            UIkit.upload('#upload'+this.id, {
                    url: 'upload_permis',
                    multiple: false,
                    name: 'arxiu',
                    beforeSend: function (e) {
                        console.log('beforeSend', arguments);
                        e.headers = { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
                    },
                    loadStart: function (e) {
                        bar.removeAttribute('hidden');
                        bar.max = e.total;
                        bar.value = e.loaded;
                    },

                    progress: function (e) {
                        bar.max = e.total;
                        bar.value = e.loaded;
                    },

                    loadEnd: function (e) {
                        bar.max = e.total;
                        bar.value = e.loaded;
                    },
                    completeAll: function () {
                        arxiu_p=arguments[0].response;
                        console.log('completeAll', arguments[0].response);
                        console.log(self.id);
                        self.guarda_arxiu_pujat(arxiu_p);
                        setTimeout(function () {
                            bar.setAttribute('hidden', 'hidden');
                        }, 1000);
                        self.avis_pujada="Pujada realitzada correctament";
                    }

                });
        UIkit.modal('#permis-modal'+this._uid).show();
    },
    guarda_arxiu_pujat(a){
        this.arxiu_pujat=a;
    },
    get_nom_dia() {
        this.nom_dia = this.dies[this.data.getDay()]
    },
    get_dia_mes() {
        this.dia_mes = this.data.getDate()
    },
    // Esborra element
    borra_par(bd, id) {
      let url = bd + "/" + id;
      axios
        .delete(url)
        .then((res) => {
          console.log(res);

            for (let index = 0; index < this[bd].length; index++) {
              if (this[bd][index].id == id) {
                this[bd].splice(index, 1);
              }
            }
                 
        })
        .catch((err) => {
          this.$toast.error(err.response.data.message);
          console.error(err);
        });
    },
    // Agafa elements de la base de dades
    get_de_bd(bd) {
      axios
        .get("dia_"+bd+"/"+data_db(this.data)) ///dia_cefire/{dia}/{mati}
        .then((res) => {
          console.log(res.data);
          this[bd] = res.data;
        })
        .catch((err) => {
          console.error(err);
        });
    },
    get_de_bd_tot() {
      axios
        .get("dia_tot/"+data_db(this.data)) ///dia_cefire/{dia}/{mati}
        .then((res) => {
          console.log(res.data);
          this["cefire"] = res.data["cefire"];
          this["compensa"] = res.data["compensa"];
          this["curs"] = res.data["curs"];
          this["visita"] = res.data["visita"];
          this["guardia"] = res.data["guardia"];
          this["incidencies"] = res.data["incidencies"];
          this["permis"] = res.data["permis"];
          this["moscosos"] = res.data["moscosos"];
          this["vacances"] = res.data["vacances"];
           // this.get_de_bd("cefire");
    // this.get_de_bd();
    // this.get_de_bd();
    // this.get_de_bd();
    // this.get_de_bd();
    // this.get_de_bd();
    // this.get_de_bd();
          
        })
        .catch((err) => {
          console.error(err);
        });
    },

    // Afegix element cefire al dia
    afegix_cefire(){
        //alert(this.data);
      let inici,fi;
      var desti='cefire';

      if (this.mati=='m'){
        inici="9:00:00";
        fi="00:00:00";
      } else {
        inici="09:00:00";
        fi="00:00:00";
      }
      var id=0;
      if (this.cefire.length === undefined || this.cefire.length == 0){
          id=0;
      } else if (this.cefire[this.cefire.length-1]['fi'] != "00:00:00") {
          id=0;
      } else {
          id = this.cefire[this.cefire.length-1]['id'];
        //   alert(id);
      }
      var params = {
        id: id,
        data: data_db(this.data),
        inici: inici,
        fi: fi,
      };
      axios
        .post("cefire", params)
        .then((res) => {
             var aux = res.data;
            if (Object.keys(this.cefire).length == 0 || aux['fi']=="00:00:00") {
                this.cefire.push(aux);
            } else if (aux['fi'] != "00:00:00") {
                for (let index = 0; index < this.cefire.length; index++) {
                    if (this.cefire[index]['id']==aux['id']){
                        this.cefire[index]['fi']=aux['fi'];
                        this.act++;
                    }
                }
            }
        })
        .catch((err) => {
          this.$toast.error(err.response.data.message);
        });
    },
    // Afegix qualsevol altre element que necessite hora a la base de dades
    afegix(desti) {
      //alert(this.data);
      let inici,fi;

      if (this.mati=='m'){
        inici="9:00:00";
        fi="14:00:00";
      } else {
        inici="16:00:00";
        fi="20:00:00";
      }
      var params = {
        data: data_db(this.data),
        inici: inici,
        fi: fi,
      };
      axios
        .post(desti, params)
        .then((res) => {
          console.log(res);
          if (Object.keys(this[desti]).length !== 0) {
                this[desti].push(res.data);
          } else {
            this[desti] = [res.data];
          }
        })
        .catch((err) => {
          console.error(err);
          this.$toast.error("err.response.data.message");
        });
    },
    // Afegix element que requerixca el nom d'un motiu
    salva(desti) {
      let varNom = desti + "_i";
      let inici,fi;
      if (desti=="permis"){
        var params = {
            data: data_db(this.data),
            inici: this.inici,
            fi: this.fi,
            motiu: this[varNom],
            arxiu: this.arxiu_pujat
        };
      } else {
        var params = {
            data: data_db(this.data),
            inici: this.inici,
            fi: this.fi,
            motiu: this[varNom]
        };
      }
      axios
        .post(desti, params)
        .then((res) => {
          console.log(res);
          if (Object.keys(this[desti]).length !== 0) {
            this[desti].push(res.data);
          } else {
            this[desti] = [res.data];
          }
        })
        .catch((err) => {
          console.error(err);
          this.$toast.error(err.response.data.message);
        });
      this[varNom] = "";
      this.arxiu_pujat="";
      this.avis_pujada="";
      UIkit.modal("#"+desti+"-modal"+this._uid).hide();
    },
    get_incidencia(){
        this.get_de_bd("incidencies")
    },
    get_moscosos(){
        this.get_de_bd("moscosos")
    },
    get_vacances(){
        this.get_de_bd("vacances")
    },
    // Afegix guardia al array de guàrdies
    afg_guardia(dat){
        this.guardia.push(dat);
    },
    // Canal per a veure les actualitzacions de les guàrdies
    channel_create(){
        var aux;
        var self=this;
         let chan='GuardiaAfegida'+data_db(this.data)+this.mati;
         channel.bind(chan,
            function(data) {
                aux=data.guardia;
                // self.afg_guardia(data.guardia);
                console.log(aux);
                if(aux.user_id == Vue.prototype.$user_id){
                    self.guardia.push(aux);
                }
            }
        );
    },
    // Esborra canal
    channel_borra(){
        var aux;
        var self=this;
         let chan='GuardiaBorrada'+data_db(this.data)+this.mati;
         channel.bind(chan,
            function(data) {
                aux=data.guardia;
                console.log(aux);
                if(aux.user_id == Vue.prototype.$user_id){
                    for (let index = 0; index < self.guardia.length; index++) {
                        if(aux.id == self.guardia[index].id){
                            self.guardia.splice(index,1);
                        }
                    }
                }
            }
        );
    }
  },
  mounted() {

    // this.get_de_bd("cefire");
    // this.get_de_bd("compensa");
    // this.get_de_bd("curs");
    // this.get_de_bd("visita");
    // this.get_de_bd("guardia");
    // this.get_de_bd("incidencies");
    // this.get_de_bd("permis");
    this.get_de_bd_tot();
    this.get_nom_dia();
    this.get_dia_mes();
    this.channel_create();
    this.channel_borra();
    this.$eventBus.$on('incidencia-enviada', this.get_incidencia);
    this.$eventBus.$on('moscoso-enviat', this.get_moscosos);
    this.$eventBus.$on('vacances-enviat', this.get_vacances);
  },
  beforeDestroy() {
      this.$eventBus.$off('incidencia-enviada');
  },
  computed: {
      // Mostra dia o nit en funció de l'hora
      mati_icon() {
          let m = ((this.mati=='m') ? "<i class='fas fa-sun'></i>" : "<i class='fas fa-moon'></i>");
          return m;
      }
  }
};
</script>

<style lang="sass" scope>
$fondo:  #f1faee

@import "../../sass/_variables.scss"
// Element damunt del dia
.titulet
    margin-left: 10px
    font-color: gray
    overflow: hidden
    transition: all 1s
    @media (min-width: 1480px)
        font-size: 1.2em
    @media (max-width: 1480px)
        font-size: 0.7em
// Especificacions de dins del recuadre
.dia
    max-width: 150px
    display: grid
    grid-template-columns: min-content repeat(3, 1em)
    grid-template-rows: repeat(5, 1fr)
    grid-column-gap: 2px
    grid-row-gap: 2px
    border: 1px solid gray
    border-radius: 7px
    background-color: white
    box-shadow: 0px 0px 34px 5px rgba(187,187,187,0.58)
    -webkit-box-shadow: 0px 0px 34px 5px rgba(187,187,187,0.58)
    -moz-box-shadow: 0px 0px 34px 5px rgba(187,187,187,0.58)
    &:hover > .lateral_esquerre
        visibility: visible
        opacity: 1
        transform: translate(-105%)
        overflow-x: hidden
        white-space: nowrap
        -webkit-overflow-scrolling: touch
    // Botonera que es deplaça des del costat
    .lateral_esquerre
        grid-area: 1 / 1 / 6 / 2
        overflow-x: hidden
        white-space: nowrap
        visibility: hidden
        opacity: 0
        transform: translate(0px)
        transition: transform 0.3s, visibility 1s, opacity 0.5s linear
        -webkit-overflow-scrolling: touch
        z-index: 0
    //Llistat d'elelments
    .principal
        grid-area: 1 / 1 / 6 / 6
        display: inline-flex
        // flex-wrap: wrap
        flex-direction: column
        justify-content: flex-start
        align-items: auto
        align-content: flex-start
        padding: 5px
        z-index: 1
        background-color: $fondo
        border-radius: 10px
        min-height: 160px
        // Cadascun dels elements s'adapta al tamany de la pantalla per a que no es desajuste
        .s-
            flex: 0 1 auto
            margin: 1px
            border: 1px solid
            border-radius: 5px
            padding: 3px
            font-weight: bold
            color: #373444
            width: 99%
            max-width: 140px
            &cefire
                @extend .s-
                background-color: $blue
                &:before
                    @media (min-width: 1280px)
                        content: "FITXATGE"
                    @media (max-width: 1280px)
                        content: "FITX..."
            &compensa
                @extend .s-
                background-color: gray
                &:before
                    @media (min-width: 1380px)
                        content: "COMPENSA"
                    @media (max-width: 1380px)
                        content: "COM..."

            &guardia
                @extend .s-
                background-color: red
                &:before
                    @media (min-width: 1220px)
                        content: "GUARDIA"
                    @media (max-width: 1220px)
                        content: "GUA..."


            &visita
                @extend .s-
                background-color: pink
                &:before
                    @media (min-width: 1024px)
                        content: "VISITA"
                    @media (max-width: 1024px)
                        content: "VIS..."

            &curs
                @extend .s-
                background-color: yellow
                &:before
                    content: "CURS"
            &permis
                @extend .s-
                background-color: green
                &:before
                    @media (min-width: 1280px)
                        content: "PERMÍS"
                    @media (max-width: 1280px)
                        content: "PER..."
            &moscosos
                @extend .s-
                background-color: violet
                &:before
                    @media (min-width: 1280px)
                        content: "MOSCOSO"
                    @media (max-width: 1280px)
                        content: "MOS..."
            &vacances
                @extend .s-
                background-color: white
                &:before
                    @media (min-width: 1280px)
                        content: "VACANCES"
                    @media (max-width: 1280px)
                        content: "VAC..."
            &incidencia
                @extend .s-
                background-color: MediumVioletRed
                &:before
                    @media (min-width: 1280px)
                        content: "INCIDÈNCIA"
                    @media (max-width: 1280px)
                        content: "INC..."


    // Icones dels diferents elements a utilitzar
    .cerrar
        font-family: "Font Awesome 6 Free"
        text-align: right
        float: right
        margin-right: 3px
        color: #373444
        font-weight: bold
        cursor: pointer
        &:before
            content: "\f2ed"

    .falta_fitxar
        font-family: "Font Awesome 6 Free"
        text-align: right
        float: right
        margin-right: 3px
        color: red
        font-weight: bold
        &:before
            content: "\f4fd"

    .falta_validar
        font-family: "Font Awesome 6 Free"
        text-align: right
        float: right
        margin-right: 1px
        color: red
        font-weight: bold
        &:before
            content: "\f556"

    .arxiu
        font-family: "Font Awesome 6 Free"
        text-align: right
        float: right
        margin-right: 3px
        color: #373444
        font-weight: bold
        cursor: pointer
        pointers: all
        &:before
            content: "\f15b"

    .flex-container
        display: flex
        flex-wrap: nowrap
        flex-direction: column
        justify-content: flex-start
        align-items: auto
        align-content: flex-start
        button
            flex: 1 0 auto
            margin: 1px
            padding-left: 8px
            padding-right: 8px

</style>
