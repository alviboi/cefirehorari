{{-- <div id="modal_avis" uk-modal>
    <escriuavis-component />
</div>

<div id="modal_missatge" uk-modal>
    <escriumsg-component />
</div>
 --}}
<escriuincidencia-component :show-incidencia="this.showModalInc"></escriuincidencia-component>

<escriumoscoso-component :show-moscoso="this.showModalMosc"></escriumoscoso-component>

<escriuvacances-component :show-vacances="this.showModalVac"></escriuvacances-component>

<escriuavis-component :show-modal="this.showModal"></escriuavis-component>

<editaperfil-component :show-edita="this.showEdita"></editaperfil-component>

<escriumsg-component :show-missatge="this.showMissatge" />
</escriumsg-component>

<borsahores-component :show-borsahores="this.showModalBorsa" />
</borsahores-component>

<compensahores-component :show-Compensahores="this.showModalCompensaBorsa" />
</compensahores-component>
