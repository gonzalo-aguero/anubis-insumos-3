async function async_deleteContacts(selection) {
    var data = new FormData();
    data.append("gma-action","deleteContacts");
    data.append("selection",selection);
    const r = await fetch(`${THEME_PATH}functions.php`,{ 
        method: 'POST',
        body: data
    });
    const formattedResponse = await r.text();
    if(r.status !== 200)
        throw Error("Error en async_deleteContacts()");

    if(formattedResponse == 1)
        alert(selection.length + " contacto(s) eliminado(s) correctamente.");
    else if(formattedResponse == 0)
        alert("Error al eliminar contactos.");
    else
        alert(`Ha ocurrido un error desconocido.\n${formattedResponse}`);
    window.location.reload();
}
async function async_deleteDayOrders(selection) {
    var data = new FormData();
    data.append("gma-action","deleteDayOrders");
    data.append("selection",selection);
    const r = await fetch(`${THEME_PATH}functions.php`,{ 
        method: 'POST',
        body: data
    });
    const formattedResponse = await r.text();
    if(r.status !== 200)
        throw Error("Error en async_deleteDayOrders()");

    if(formattedResponse == 1){
        alert(selection.length + " ítems eliminados correctamente.");
        console.log("Ítems eliminados:",selection);
    }else if(formattedResponse == 0){
        alert("Error al eliminar ítems.");
    }else{
        alert(`Ha ocurrido un error desconocido.\n${formattedResponse}`);
    }
    window.location.reload();
}
async function async_saveDiscounts() {
    var minAmount1 = document.getElementById("minAmount_discount1").value;
    var percent1 = document.getElementById("percent_discount1").value;
    var actived1 = document.getElementById("actived_discount1").checked;
    
    var minAmount2 = document.getElementById("minAmount_discount2").value;
    var percent2 = document.getElementById("percent_discount2").value;
    var actived2 = document.getElementById("actived_discount2").checked;
    
    var discounts = [{"name":"discount1","actived":actived1,"percent":percent1,"minAmount":minAmount1},{"name":"discount2","actived":actived2,"percent":percent2,"minAmount":minAmount2}];
    
    var data = new FormData();
    data.append("gma-action","saveDiscounts");
    data.append("data",JSON.stringify(discounts));
    const r = await fetch(`${THEME_PATH}functions.php`,{ 
        method: 'POST',
        body: data
    });
    const formattedResponse = await r.text();
    if(r.status !== 200)
        throw Error("Error en async_saveDiscounts()");

    if(formattedResponse == 1)
        alert("Descuentos guardados correctamente");
    else if(formattedResponse == 0)
        alert("Error al guardar descuentos");
    else
        alert(`Ha ocurrido un error desconocido.\n${formattedResponse}`);
    window.location.reload();
}
async function async_saveFreeShippings() {
    // FS1 = freeShippingProducts
    const FS1_ids = document.getElementById("freeShippingProducts").value;
    const FS1_actived = document.getElementById("freeShippingProductsStatus").checked;
    // FS2 = freeShippingMinAmountStatus
    const FS2_minAmount = document.getElementById("freeShippingMinAmount").value;
    const FS2_actived = document.getElementById("freeShippingMinAmountStatus").checked;
    
    var FS1_data = {
        "ids": FS1_ids.split(','),
        "actived": FS1_actived
    };
    var FS2_data = {
        "minAmount": parseFloat(FS2_minAmount),
        "actived": FS2_actived
    };
    
    var data = new FormData();
    data.append("gma-action","saveFreeShippings");
    data.append("FS1_data",JSON.stringify(FS1_data));
    data.append("FS2_data",JSON.stringify(FS2_data));
    const r = await fetch(`${THEME_PATH}functions.php`,{ 
        method: 'POST',
        body: data
    });
    const formattedResponse = await r.text();
    if(r.status !== 200)
        throw Error("Error en async_saveFreeShippings()");

    if(formattedResponse == 1)
        alert("Envíos gratis guardados correctamente");
    else if(formattedResponse == 0)
        alert("Error al guardar envíos gratis");
    else
        alert(`Ha ocurrido un error desconocido.\n${formattedResponse}`);
    window.location.reload();
}
async function async_saveEditableZones() {
    var editableZones = [];
    document.querySelectorAll(".editableZoneInput").forEach( input => {
        const index = input.id.replace("editableZone_","");
        let value = input.value.replaceAll("\n", "<br>");
        value = value.replaceAll('"', '\\"');
        editableZones[index] = value;
    });
    
    var data = new FormData();
    data.append("gma-action","saveEditableZones");
    data.append("data",JSON.stringify(editableZones));
    const r = await fetch(`${THEME_PATH}functions.php`,{ 
        method: 'POST',
        body: data
    });
    const formattedResponse = await r.text();
    if(r.status !== 200)
        throw Error("Error en async_saveEditableZones()");

    if(formattedResponse == 1)
        alert("¡Zonas editables guardadas correctamente!");
    else if(formattedResponse == 0)
        alert("Error al guardar zonas editables");
    else
        alert(`Ha ocurrido un error desconocido.\n${formattedResponse}`);
    window.location.reload();
}