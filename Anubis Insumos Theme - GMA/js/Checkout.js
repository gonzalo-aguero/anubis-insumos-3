"use strict";
var checkoutStep = 1;// 1 of 4
var stepIcons = [];
var cartForm = null;
var customerDetails = null;
var orderReview = null;
var paymentForm = null;
var backBtn = null;
var nextBtn = null;
var firstInit = true;
const disabledButtonStyle = `
    cursor: default;
    opacity: 0;
`;
if(CURRENT_PATH === "/carrito/")
    setTimeout(GMA_checkoutInit, 3000);
else if(CURRENT_PATH.includes("/carrito/order-received/") === true)
    GMA_thankYouInit();

/**
 * If the shipment is in Rosario hide "#amountToPayWithCOD", otherwise hide "#amountToPayWithFreeShipping".
 **/
function GMA_shippingInRosario(){
    const amountToPayWithCOD = document.getElementById("amountToPayWithCOD");
    const amountToPayWithFreeShipping = document.getElementById("amountToPayWithFreeShipping");
    var shippingInRosario = false;
    try{
        shippingInRosario = document.getElementById("shipping_method_0_flat_rate6").checked;
    }catch(error){
        console.warn("Error in GMA_shippingInRosario() in Checkout.js",error);
    }
    const display = "table-row";
    if(shippingInRosario === true){
        try{
            amountToPayWithCOD.style.display = "none";
        }catch(error){
            console.warn("Error in GMA_shippingInRosario() in Checkout.js",error);
        }
        try{
            amountToPayWithFreeShipping.style.display = display;
        }catch(error){
            console.warn("Error in GMA_shippingInRosario() in Checkout.js",error);
        }
    }else{
        try{
            amountToPayWithFreeShipping.style.display = "none";
        }catch(error){
            console.warn("Error in GMA_shippingInRosario() in Checkout.js",error);
        }
        try{
            amountToPayWithCOD.style.display = display;
        }catch(error){
            console.warn("Error in GMA_shippingInRosario() in Checkout.js",error);
        }
    }
}
function GMA_displayStep(){
    const display = "block";
    const title = document.querySelector("h1.entry-title");
    //Show and hide blocks.
    switch(checkoutStep){
        case 1:
            cartForm.style.display = display;
            customerDetails.style.display = "none";
            orderReview.style.display = "none";
            paymentForm.style.display = "none";
            checkoutStep = 1;
            title.innerHTML = "Carrito";
            break;
        case 2:
            cartForm.style.display = "none";
            customerDetails.style.display = display;
            orderReview.style.display = "none";
            paymentForm.style.display = "none";
            checkoutStep = 2;
            title.innerHTML = "Información del cliente";
            break;
        case 3:
            cartForm.style.display = "none";
            customerDetails.style.display = "none";
            orderReview.style.display = display;
            paymentForm.style.display = "none";
            checkoutStep = 3;
            title.innerHTML = "Método de entrega";
            break;
        case 4:
            cartForm.style.display = "none";
            customerDetails.style.display = "none";
            orderReview.style.display = "none";
            paymentForm.style.display = display;
            checkoutStep = 4;
            title.innerHTML = "Método de pago";
            break;
    }
    //Show or hide back and next buttons.
    if(checkoutStep === 1){
        backBtn.style = disabledButtonStyle;
        nextBtn.style = "";
    }else if(checkoutStep === 4){
        backBtn.style = "";
        nextBtn.style = disabledButtonStyle;
    }else{
        backBtn.style = "";
        nextBtn.style = "";
    }
    //Display as "checked" the step icons.
    stepIcons.forEach( stepIcon => {
        const stepIconId = stepIcon.id.replace('stepIcon_', '');
        if(stepIconId <= checkoutStep)
            stepIcon.classList.add("checked");
        else if(stepIconId > checkoutStep)
            stepIcon.classList.remove("checked");
    });
    // console.log("Current step:",checkoutStep);
}
function GMA_backOrNextStep(action = '++'){
    if(action === '++'){
        if(checkoutStep < 4){
            checkoutStep++;//go to next step
        }            
    }else if(action === '--'){
        if(checkoutStep > 1){
            checkoutStep--;//go to previous step
        }    
    }
    GMA_displayStep();
}
function GMA_jumpSteps(stepIcon){
    const toStepNumber = stepIcon.id.replace('stepIcon_', '');
    let stepJump = 0;
    if(toStepNumber > checkoutStep){
        // Example: from step 1 to step 3
        stepJump = toStepNumber - checkoutStep;
        for(let i = 1; i <= stepJump; i++){
            GMA_backOrNextStep('++');
        }
    }else if(toStepNumber < checkoutStep){
        // Example: from step 3 to step 1
        stepJump = checkoutStep - toStepNumber;
        for(let i = 1; i <= stepJump; i++){
            GMA_backOrNextStep('--');
        }
    }
}
function GMA_checkoutEvents(){
    // ------ BACK AND NEXT BUTTONS ------ //
    backBtn.addEventListener("click",()=>{ GMA_backOrNextStep('--') });
    nextBtn.addEventListener("click",()=>{ GMA_backOrNextStep('++') });
    // ------ STEP BUTTONS ------ //
    stepIcons.forEach( stepIcon => {
        stepIcon.addEventListener("click",()=> GMA_jumpSteps(stepIcon) );
    });
    firstInit = false;
    // console.log("GMA_checkoutEvents")
}
function GMA_checkoutInit(){
    backBtn = document.getElementById("backBtn");
    nextBtn = document.getElementById("nextBtn");
    cartForm = document.querySelector("form.woocommerce-cart-form");
    customerDetails = document.querySelector("#customer_details");
    orderReview = document.querySelector("table.GMA.orderReview");
    paymentForm = document.querySelector("div.woocommerce-checkout-payment");
    stepIcons = document.querySelectorAll("#finalizePurchase > header > ul > li");
    GMA_displayStep();
    GMA_shippingInRosario();
    
    if(firstInit === true){
        GMA_checkoutEvents();
    }
    console.log("GMA_checkoutInit() restarted.");
}
function GMA_thankYouInit(){
    // ====== EVENT TO SHOW THE ORDER DETAIL ====== //
    const seeOrderDetailBtn = document.getElementById("seeOrderDetail");
    seeOrderDetailBtn.addEventListener("click",()=>{
        const orderDetail = document.querySelector(".GMA.orderDetail");
        const currentDisplay = orderDetail.style.display;
        if(currentDisplay !== 'none'){
            orderDetail.style.display = "none";
            seeOrderDetailBtn.innerHTML = "Ver detalle del pedido";
        }else{
            orderDetail.style.display = "block";
            seeOrderDetailBtn.innerHTML = "Ocultar detalle del pedido";
        }
    });
}