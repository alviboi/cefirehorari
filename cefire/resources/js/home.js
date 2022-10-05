const { delay } = require("lodash");


/**
 *
 * Aquesta funció canvia una data al format de la base de dades
 *
 * @param {*} data
 */

window.data_db = function (data) {
    var mes_s = '';
    var dia_s = '';
    let dia = data.getDate();
    let mes = data.getMonth() + 1;
    let an = data.getFullYear();
    if (mes < 10){
        mes_s = '0'+ mes;
    } else {
        mes_s = mes;
    }
    if (dia < 10){
        dia_s = '0'+dia;
    } else {
        dia_s = dia;
    }
    let ret = an + "-" + mes_s + "-" + dia_s;
    return ret;
}



window.getDiumenge= function (d) {
    d = new Date(d);
    var day = d.getDay(),
        diff = d.getDate() - day; // ajustar
    return new Date(d.setDate(diff));
  }

window.avis = function() {
    UIkit.modal('modal_avis').show();
}

/**
 *
 * Mostrem la data amb format natural
 *
 * @param {*} d
 */

window.fecha_format = function (d = new Date) {
    let mes = String(d.getMonth() + 1);
    let dia = String(d.getDate());
    const any = String(d.getFullYear());

    if (mes.length < 2) mes = '0' + mes;
    if (dia.length < 2) dia = '0' + dia;

    return `${dia}/${mes}/${any}`;
  }

/**
 *
 * Per obrir un element en una nova pestanya.
 * No s'utiltiza, però es deixa a caràcter optatiu per als fulls dels permisos
 *
 * @param {*} url
 */

window.newTab = function(url){
    var win = window.open(url);
    win.focus();
}



// window.agafa_users = function(){
//     var ret = [];
//     axios.get("user")
//     .then(res => {
//         ret = res.data;
//     })
//     .catch(err => {
//         console.error(err);
//         return err;
//     })
//     return ret;
// }
