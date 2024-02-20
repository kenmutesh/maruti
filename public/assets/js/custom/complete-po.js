let stockInValid = true;

function filterFloors(selectElement) {
    $(".floor-select").each(function (i, obj) {
        if ($(obj).data("select2")) {
            $(obj).select2("destroy");
        }
    });
    const warehouseID = selectElement.value;
    const floorSelectElements = document.querySelectorAll(".floor-select");

    const floorDropdown = document.querySelector(".floor-select");
    floorDropdown.innerHTML = "<option selected disabled>Select Floor</option>";

    floorOptions.forEach((floorOption) => {
        if (floorOption.warehouse.id == warehouseID) {
            const option = document.createElement("option");
            option.value = floorOption.id;
            option.innerHTML = floorOption.floor_name;
            floorDropdown.append(option);
        }
    });
    $(".floor-select").select2();
}

function filterShelves(selectElement) {
    $(".shelf-select").each(function (i, obj) {
        if ($(obj).data("select2")) {
            $(obj).select2("destroy");
        }
    });
    const floorID = selectElement.value;

    const shelfDropdown = document.querySelector(".shelf-select");
    shelfDropdown.innerHTML = "<option selected disabled>Select Shelf</option>";

    shelfOptions.forEach((shelfOption) => {
        if (shelfOption.floor.id == floorID) {
            const option = document.createElement("option");
            option.value = shelfOption.id;
            option.innerHTML = shelfOption.shelf_name;
            shelfDropdown.append(option);
        }
    });

    $(".shelf-select").select2();
}

function filterBins(selectElement) {
    $(".bin-select").select2("destroy");
    $(".bin-select").each(function (i, obj) {
        if ($(obj).data("select2")) {
            $(obj).select2("destroy");
        }
    });
    const shelfID = selectElement.value;

    const binDropwdown = document.querySelector(".bin-select");
    binDropwdown.innerHTML = "<option selected disabled>Select Bin</option>";

    binOptions.forEach((binOption) => {
        if (binOption.shelf.id == shelfID) {
            const option = document.createElement("option");
            option.value = binOption.id;
            option.innerHTML = binOption.bin_name;
            binDropwdown.append(option);
        }
    });

    $(".bin-select").select2();
}

function checkMaxThreshold(inputElement) {
    const stockedIn = inputElement.getAttribute("attr-stocked-in");
    const maxThreshold = inputElement.value;

    if (parseFloat(maxThreshold) < parseFloat(stockedIn)) {
        swal({
            title: "Failed",
            text: "The quantity is already above the maximum threshold",
            type: "error",
            timer: 3000,
            buttons: false,
        });
        inputElement.value = "";
    }
}

function checkMinThreshold(inputElement) {
    const stockedIn = inputElement.getAttribute("attr-stocked-in");
    const minThreshold = inputElement.value;

    if (parseFloat(minThreshold) > parseFloat(stockedIn)) {
        swal({
            title: "Failed",
            text: "The quantity is already below the minimum threshold",
            type: "error",
            timer: 3000,
            buttons: false,
        });
        inputElement.value = "";
    }
}

function toggleErrorShow(value, element, input) {
    if (value == "") {
        stockInValid = false;
        swal({
            title: "Failed",
            text: `${input} cannot be blank!`,
            type: "error",
            timer: 3000,
            buttons: false,
        });
        element.parentElement.classList.add("has-danger");
        return false;
    } else {
        stockInValid = true;
        element.parentElement.classList.remove("has-danger");
        return true;
    }
}

const newItemInputs = document.querySelectorAll('input[name="new_item[]"]');
[...newItemInputs].forEach((input)=>{
    input.addEventListener('change',(event)=>{
        const purchaseOrderItemDIV = event.target.closest('.purchase-order-item');
        const itemInputs = purchaseOrderItemDIV.querySelectorAll('input');
        if(event.target.checked){
            [...itemInputs].forEach((item)=>{
                if(item.name != 'weight_added[]' && item.name != 'quantity_added[]' && item.name != 'standard_cost' && item.name != 'standard_cost_vat'){
                    item.readOnly = false;
                }
            })
        }else{
            [...itemInputs].forEach((item)=>{
                if(item.name != 'weight_added[]' && item.name != 'quantity_added[]' && item.name != 'standard_cost' && item.name != 'standard_cost_vat'){
                    item.readOnly = true;
                }
            }) 
        }
    })
})

function populateAggregateFields(btnElement){
    const purchaseOrderItems = document.querySelectorAll('.purchase-order-item');
    [...purchaseOrderItems].forEach((item)=>{
        // get the type
        const type = item.querySelector('input[name="item_type[]"]');
        if(type.value == "POWDER"){
            const powderInputs = {
                id: item.querySelector('input[name="item_id[]"]').value,
                new: item.querySelector('input[name="new_item[]"]').checked,
                type: type.value,
                powder_color: item.querySelector('input[name="powder_color"]').value,
                powder_code: item.querySelector('input[name="powder_code"]').value,
                powder_description: item.querySelector('input[name="powder_description"]').value,
                serial_no: item.querySelector('input[name="serial_no"]').value,
                manufacture_date: item.querySelector('input[name="manufacture_date"]').value,
                expiry_date: item.querySelector('input[name="expiry_date"]').value,
                goods_weight: item.querySelector('input[name="goods_weight"]').value,
                batch_no: item.querySelector('input[name="batch_no"]').value,
                standard_cost: item.querySelector('input[name="standard_cost"]').value,
                standard_cost_vat: item.querySelector('input[name="standard_cost_vat"]').value,
                standard_price: item.querySelector('input[name="standard_price"]').value,
                standard_price_vat: item.querySelector('input[name="standard_price_vat"]').value,
                min_threshold: item.querySelector('input[name="min_threshold"]').value,
                max_threshold: item.querySelector('input[name="max_threshold"]').value,
                weight_added: item.querySelector('input[name="weight_added[]"]').value
            };
            item.querySelector('input[name="purchase_order_item[]"]').value = JSON.stringify(powderInputs);
        }else if(type.value == "NON INVENTORY"){
            const nonInventoryInputs = {
                id: item.querySelector('input[name="item_id[]"]').value,
                new: item.querySelector('input[name="new_item[]"]').checked,
                type: type.value,
                item_name: item.querySelector('input[name="item_name"]').value,
                standard_cost: item.querySelector('input[name="standard_cost"]').value,
                standard_cost_vat: item.querySelector('input[name="standard_cost_vat"]').value,
                quantity_added: item.querySelector('input[name="quantity_added"]').value 
            };
            item.querySelector('input[name="purchase_order_item[]"]').value = JSON.stringify(nonInventoryInputs);
        }else{
            const inventoryInputs = {
                id: item.querySelector('input[name="item_id[]"]').value,
                new: item.querySelector('input[name="new_item[]"]').checked,
                type: type.value,
                item_name: item.querySelector('input[name="item_name"]').value,
                item_code: item.querySelector('input[name="item_code"]').value,
                item_description: item.querySelector('input[name="item_description"]').value,
                serial_no: item.querySelector('input[name="serial_no"]').value,
                quantity_tag: item.querySelector('input[name="quantity_tag"]').value,
                inventory_type: item.querySelector('select[name="inventory_type"]').value,
                goods_weight: item.querySelector('input[name="goods_weight"]').value,
                standard_cost: item.querySelector('input[name="standard_cost"]').value,
                standard_cost_vat: item.querySelector('input[name="standard_cost_vat"]').value,
                standard_price: item.querySelector('input[name="standard_price"]').value,
                standard_price_vat: item.querySelector('input[name="standard_price_vat"]').value,
                min_threshold: item.querySelector('input[name="min_threshold"]').value,
                max_threshold: item.querySelector('input[name="max_threshold"]').value,
                quantity_added: item.querySelector('input[name="quantity_added[]"]').value,
            };
            item.querySelector('input[name="purchase_order_item[]"]').value = JSON.stringify(inventoryInputs);
        }
    });
    const form = btnElement.parentElement;
    let formValid = true;
    const inputs = form.querySelectorAll('input');
    for (const input of inputs) {
        if(!input.checkValidity()){
            formValid = false;
            input.parentElement.classList.add('has-danger');
        }else{
            input.parentElement.classList.remove('has-danger');
        }
    }
    if(formValid){
        btnElement.parentElement.submit();
    }else{
        alert('Missing required input');
    }
}