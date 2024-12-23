class Footer extends HTMLElement{
    constructor(){
        super();
    }
    connectedCallback(){
        this.innerHTML=`
        <footer>
            <div class="contenedor_footer">
                <div class="organizacion_footer">
                    <a href="#">UNSA</a>
                </div>

                <div class="derechos_footer">
                    <p>@Todos los redechos reservados</p>
                </div>
            </div>
        </footer>
        `
    }
}
window.customElements.define('pag-footer',Footer);