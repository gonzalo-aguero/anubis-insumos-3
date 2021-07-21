"use strict";
var selection = [];
var freeShipping;
wc_add_to_cart_params.cart_url = GMA_CART_URL;//Change the cart url in the Woocommerce data.
changeCartUrl();
eventWhenChangingHtml(document.getElementById("cartPreview"), changeCartUrl, 200);
global();
switch(CURRENT_PATH){
    case '/':
        homeActions();
        break;
    case '/carrito/':
        console.log("/carrito/");
        const updateCartBtn = document.querySelector(".woocommerce-cart-form button[name='update_cart'].button");
        updateCartBtn.addEventListener("click",GMA_reloadPage);
        const productRemoveBtns = document.querySelectorAll(".product-remove");
        productRemoveBtns.forEach( button => {
            button.addEventListener("click", GMA_reloadPage);
        });
        break;
    case '/gracias-por-tu-compra/':
        break;
    case '/preguntas-frecuentes/':
        break;
    case '/gma-admin/':
        GMA_adminEvents();
        break;
    default:
        break;
}



function GMA_reloadPage(){
    setTimeout(()=>{
        console.log("Reloading page...");
        location.reload();
    },2000);
}

/**
 * Execute functions regardless of current page.
 **/
function global(){
    document.getElementById("primary").style = `
        margin: 0 !important;
    `;
    mainMenuEvents();
    freeShipping = new GMA_freeShipping();
    
    
    // ====== SHOW AND HIDE PRODUCT DETAIL ====== //
    try{
        const showProductDetailBtns = document.querySelectorAll(".showProductDetail");
        showProductDetailBtns.forEach( button => {
            const productId = button.getAttribute("data-productid");
            const productDetail = document.getElementById(`productDetail_${productId}`);
            //Event to show
            button.addEventListener("click", ()=>{
                productDetail.style.display = "flex";
                document.body.style.overflow = "hidden";
            });
            //Event to hide
            productDetail.querySelectorAll(".closeSpan").forEach( closeSpan => {
                closeSpan.addEventListener("click", ()=>{
                    document.body.style.overflow = "auto";
                    productDetail.style.display = "none";
                }); 
            });
        });
    }catch(error){
        console.warn("Event could not be assigned.",error);
    }
    // ====== PRODUCT GALLERY SLIDER ====== //
    const productGallerySliders = document.querySelectorAll(".GMA.productGallerySlider");
    productGallerySliders.forEach( productGallerySlider => {
        LRControlEvents({
            element: productGallerySlider.querySelector("figure"),
            controls: {
                L: productGallerySlider.querySelector(".GMA.scrollXControl.Left"),
                R: productGallerySlider.querySelector(".GMA.scrollXControl.Right")
            },
            advance: 1000,
        });  
    });
    // ====== DELETE LINK IN PRODUCT IMAGES ====== //
    setInterval(removeImageLinkInProductDetail, 100);
}
/**
 * Assign the events for the home page.
 **/
function homeActions(){
    document.getElementById("primary").style = `
        padding-left: 0 !important;
        padding-right: 0 !important;
        margin: 0 !important;
    `;
    GMA_productSliders();
}
/**
 * Open or close the main menu.
 **/
function openOrCloseMenu(){
    const nav = document.querySelector("#mainNavContainer > nav");
    const closeBtn = document.querySelector("#mainNavContainer > .closeMenuBtn");
    const currentDisplay = nav.style.display;
    if(currentDisplay !== "none"){
        // HIDE MAIN MENU.
        nav.classList.remove("animate__fadeInLeftBig");
        nav.classList.add("animate__fadeOutLeftBig");
        closeBtn.style.display = "none";
        document.body.style.overflowY = "auto";
        setTimeout( ()=>{
            nav.style.display = "none" 
            document.body.style.overflow = "auto";
        },250);
    }else{
        // SHOW MAIN MENU.
        nav.classList.remove("animate__fadeOutLeftBig");
        nav.classList.add("animate__fadeInLeftBig");
        closeBtn.style.display = "flex";
        nav.style.display = "flex";
        document.body.style.overflowY = "none";
        document.body.style.overflow = "hidden";
    }
}
function mainMenuEvents(){
    // ===== OPEN AND CLOSE THE MAIN MENU ===== //
    const openAndCloseMenuBtns = document.querySelectorAll(".openMenuBtn, .closeMenuBtn");
    openAndCloseMenuBtns.forEach( button => {
        button.addEventListener("click",openOrCloseMenu);
    });
    // ===== CATEGORY NAVIGATION ===== //
    const categoryNavigation = document.querySelector("#categoryNavigation > ul");
    LRControlEvents({
        element: categoryNavigation,
        controls: {
            L: categoryNavigation.querySelector(".GMA.scrollXControl.Left"),
            R: categoryNavigation.querySelector(".GMA.scrollXControl.Right")
        },
        advance: 150
    });    
    // ===== SEARCH BUTTON ===== //
    document.getElementById("searchBtn").addEventListener("click",()=>{
        const navDisplay = document.querySelector("#mainNavContainer > nav").style.display;
        if(navDisplay !== "none" && navDisplay !== ""){
            openOrCloseMenu();
        }
        const container = document.getElementById("transparentDarkBackground");
        //print the search form.
        container.innerHTML = `
            <form role="search" method="get" class="GMA searchForm" action="${GMA_DOMAIN}">
                <input type="search" placeholder="Buscar productos" name="s">
                <input type="hidden" name="post_type" value="product">
                <input type="submit" value="Buscar">
            </form>
            <span class="closeSpan"></span>
        `;
        container.style.display = "flex";//by default it's "none" (hidden).
        document.querySelector(".GMA.searchForm input[type='search']").focus();
        //close the search form just by tapping outside of it.
        container.querySelector(".closeSpan").addEventListener("click", function(){
            container.style.display = "none";
        });
    });
    // ===== MINI CART ===== //
    const cartPreview = document.getElementById("cartPreview");
    document.querySelector("a.icon-cart").addEventListener("click",()=>{
        if(CURRENT_PATH === '/gracias-por-tu-compra/'){
            location.href= GMA_DOMAIN;
            return;
        }
        //Show mini cart.
        // if(screen.width <= 700)
        //     openOrCloseMenu();
        // if(CURRENT_PATH !== '/carrito/'){
            cartPreview.style.display = "flex";
        // }
    });
    cartPreview.querySelector(".closeSpan").addEventListener("click",()=>{
        //Hide mini cart.
        cartPreview.style.display = "none";
    });
}
function GMA_productSliders(){
    const productSliders = document.querySelectorAll(".GMA.productSlider");
    productSliders.forEach( productSlider => {
        LRControlEvents({
            element: productSlider,
            controls: {
                L: productSlider.querySelector(".GMA.scrollXControl.Left"),
                R: productSlider.querySelector(".GMA.scrollXControl.Right")
            },
            advance: 18,
            unit: 'em'
        });  
    });
}
function getEmValueIn(parentElement){
    var div = document.createElement('div');
    div.style = `
        width: 1000em;
        position: absolute;
    `;
    parentElement.appendChild(div);
    var pixels = div.offsetWidth / 1000;
    parentElement.removeChild(div);
    return pixels;
}
function LRControlEvents({element ,controls = {L, R}, advance = 100, unit = 'px', behavior = "smooth"}){
    var advancePixels = advance;
    switch(unit){
        case 'em':
            const em = getEmValueIn(element);
            advancePixels = Math.round(advancePixels * em);
            break;
        case 'px':
            break;
        default:
            console.error("Invalid unit of measurement.");
            break;
    }
    console.log("Unidad --->",unit,"\nMedida --->",advance,"\nMedida en px --->",advancePixels);
    
    controls.L.addEventListener("click",()=>{
        const currentScroll = element.scrollLeft;
        element.scroll({
            left: currentScroll - advancePixels,
            top: 0,
            behavior: behavior
        });
    });
    controls.R.addEventListener("click",()=>{
        const currentScroll = element.scrollLeft;
        element.scroll({
            left: currentScroll + advancePixels,
            top: 0,
            behavior: behavior
        });
    });
}
/**
 * Remove the "Finalize the purchase"/"Finalizar compra"  button.
 **/
function changeCartUrl(){
    //Removes the cart buttons and creates a new button with the new cart URL.
    var miniCartBtns = document.querySelector('p.woocommerce-mini-cart__buttons.buttons');
    if(miniCartBtns !== null && miniCartBtns !== undefined)
        miniCartBtns.innerHTML = `<a href="${GMA_CART_URL}" class="button checkout wc-forward" id="GMA_cartButton">Carrito</a>`;
    
    //Removes the links to the product page in the cart items.
    const productPageLinks = document.querySelectorAll(".woocommerce-mini-cart-item > a:not(.remove_from_cart_button)")
    productPageLinks.forEach( link => link.removeAttribute("href"));
}
function removeImageLinkInProductDetail(){
    document.querySelectorAll(".woocommerce-product-gallery__image > a").forEach( a => {
        a.removeAttribute("href");
    });
}
/**
 * Event when changing HTML content.
 * element - Element HTML to assign the event.
 * f - Callback function.
 * interval - Observation interval in ms. By default it's 1000.
 **/
function eventWhenChangingHtml(element, f, interval = 1000){
    var prevContent = element.innerHTML;
    var currentContent = "";
    setInterval(()=>{
        currentContent = element.innerHTML;
        if(currentContent !== prevContent){
            f();
        }
        prevContent = currentContent;
    },interval);
}
/**
 * Add all events for GMA admin page operation.
 */
function GMA_adminEvents() {
    // ====== DELETE CONTACTS ====== //
    try{
        document.getElementById('deleteBtn').addEventListener("click", async ()=> {
            const selection = getSelection();
            if(selection.length > 0){
                const msg = `
                    Se eliminarán los contactos seleccionados.
                    ¿Estás seguro?
                `;
                if(confirm(msg))
                    await async_deleteContacts(selection);
            }else{
                alert("No hay contactos seleccionados.");
            }
        });
    }catch(error){
        console.warn("Evento para eliminar contactos no asignado.");
    }
    // ====== DELETE DAY ORDERS ====== //
    try{
        document.getElementById('deleteBtn2').addEventListener("click", async ()=> {
            const selection = getSelection();
            if(selection.length > 0){
                const msg = `
                    Se eliminarán los ítems seleccionados.
                    ¿Estás seguro?
                `;
                if(confirm(msg))
                    await async_deleteDayOrders(selection);
            }else{
                alert("No hay ítems seleccionados.");
            }
        });
    }catch(error){
        console.warn("Evento para eliminar órdenes del día no asignado.");
    }
    // ====== Select or deselect all items ====== //
    try{
        const selectOrDeselectAllBtn = document.getElementById("selectOrDeselectAll");
        selectOrDeselectAllBtn.addEventListener("click",()=>{
            document.querySelectorAll("table tr input[type='checkbox']:not(#selectOrDeselectAll)").forEach( checkbox => {
                selectOrDeselectAllBtn.checked === true ? checkbox.checked = true : checkbox.checked = false;
            });
        });
    }catch(error){
        console.warn("Evento para selecionar y deseleccionar todos los ítems no asignado.");
    }
    // ====== SAVE DISCOUNT CHANGES ====== //
    try{
        document.getElementById("saveDiscounts").addEventListener("click", ()=>{
            async_saveDiscounts();
        });
    }catch(error){
        console.warn("Evento para guardar descuentos no asignado.");
    }
    // ====== SAVE FREE SHIPPINGS ====== //
    try{
        document.getElementById("saveFreeShippings").addEventListener("click",()=>{
            async_saveFreeShippings();
        });
    }catch(error){
        console.warn("Evento para guardar envíos gratis no asignado.");
    }
    // ====== SAVE EDITABLE ZONES ====== //
    try{
        document.getElementById("saveEditableZones").addEventListener("click", ()=>{
            async_saveEditableZones(); 
        });
    }catch(error){
        console.warn("Evento para guardar zonas editables no asignado.");
    }
}
/**
 * Add or remove an item from the selection to delete.
 * @param {int} id id of selected element.
 */
function addOrRemoveFromSelection(id) {
    var i = selection.indexOf(id);
    if(i === -1){
        selection.push(id);//Add
    }else{
        selection.splice(i,1);//Remove
    }
    console.log("Selection updated:",selection);
}
function getSelection(){
    var selection = [];
    document.querySelectorAll("table tr input[type='checkbox']:not(#selectOrDeselectAll)").forEach( checkbox => {
        if(checkbox.checked !== true){
            return;
        }
        let id;
        if(checkbox.id.includes("gma-contact_"))
            id = checkbox.id.replace("gma-contact_","");
        else if(checkbox.id.includes("gma-dayOrder_"))
            id = checkbox.id.replace("gma-dayOrder_","");
        selection.push(id);
    });
    return selection;
}
function openProductDetail({productId}){
    document.getElementById(`productDetail_${productId}`).style.display = "flex";
    document.body.style.overflow = "hidden";
}
function openFreeShippings(){
    freeShipping.openWindow();
}