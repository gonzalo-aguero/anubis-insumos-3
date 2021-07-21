"use strict";
class GMA_freeShipping{
    constructor(){
        this.window;
        this.defaultWindowDisplay = "block";
        this.openButton;
        this.closeButtons;
        this.productSlider;
        this.applied = false;//If the free shipping is applied it's true.
        try{
            this.freeShippingInit();
        }catch(error){
            console.warn("Error in class GMA_freeShipping:\n",error);
        }
    }
    freeShippingInit(){
        this.getElements();
        try{
            this.assignEvents();
        }catch(error){
            console.warn("Events could not be assigned.",error);
        }
        this.window.style = `
            display: none;
            opacity: 100%;
        `;
    }
    getElements(){
        this.window = document.getElementById("freeShippings");
        this.openButton = document.getElementById("openFreeShippings");
        this.closeButtons = document.querySelectorAll("#closeFreeShippings, #freeShippings > .closeSpan");
        this.productSlider = document.querySelector("#freeShippings .GMA.productSlider");
    }
    assignEvents(){
        this.openButton.addEventListener("click",()=>{
            this.openWindow();
        });
        
        this.closeButtons.forEach( closeButton => {
            closeButton.addEventListener("click", ()=>{
                this.closeWindow();
            });
        });
        
        //Event to move scroll of the product slider.
        LRControlEvents({
            element: this.productSlider,
            controls: {
                L: this.productSlider.querySelector(".GMA.scrollXControl.Left"),
                R: this.productSlider.querySelector(".GMA.scrollXControl.Right")
            },
            advance: 18,
            unit: 'em'
        });  
    }
    openWindow(){
        this.window.style.display = this.defaultWindowDisplay;
        this.closeButtons[0].style.display = this.defaultWindowDisplay;
        document.body.style.overflow = "hidden";
        this.window.scroll(0,0);
    }
    closeWindow(){
        document.body.style.overflow = "auto";
        this.window.style.display = "none";
        this.closeButtons[0].style.display = "none";
    }
}