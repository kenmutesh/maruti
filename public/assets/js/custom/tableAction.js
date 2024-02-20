function prefillPurchaseOrderRow(selectElement) {
    const optionSelected = selectElement.selectedOptions[0];
    const itemRow = selectElement.closest("tr");
    const nameInput = itemRow.querySelector('input[name="item_name[]"]');
    const qtyInput = itemRow.querySelector('input[name="item_qty[]"]');
    const kgInput = itemRow.querySelector('input[name="item_kg[]"]');
    const typeInput = itemRow.querySelector('input[name="item_type[]"]');

    const vatInput = itemRow.querySelector('input[name="item_vat[]"]');
    const costWithoutVATInput = itemRow.querySelector('input[name="unit_cost_without_vat[]"]');
    const costWithVATInput = itemRow.querySelector('input[name="unit_cost_with_vat[]"]');

    const amountInput = itemRow.querySelector('input[name="amount[]"]');

    if (optionSelected.parentElement.label == "Powder") {
        qtyInput.readOnly = true;
        qtyInput.value = 0;
        kgInput.readOnly = false;
        kgInput.value = 1;
        kgInput.min = 0;
    } else {
        qtyInput.readOnly = false;
        qtyInput.value = 1;
        qtyInput.min = 0;
        kgInput.readOnly = true;
        kgInput.value = 0;
    }

    typeInput.value = optionSelected.parentElement.label;

    vatInput.value = optionSelected.getAttribute('attr-data-cost-vat');
    costWithoutVATInput.value = optionSelected.getAttribute('attr-data-cost-without-vat');
    costWithVATInput.value = optionSelected.getAttribute('attr-data-cost');
    amountInput.value = optionSelected.getAttribute('attr-data-cost');
    if (optionSelected.getAttribute('attr-data-name') != "") {
        nameInput.readOnly = true;
    } else {
        nameInput.readOnly = false;
    }
    nameInput.value = optionSelected.getAttribute('attr-data-name');
    calculatePurchaseOrderGrandTotal();
}

function calculatePurchaseOrderItemRowTotal(inputElement) {
    const itemRow = inputElement.closest("tr");
    const vatInput = itemRow.querySelector('input[name="item_vat[]"]');
    const costWithoutVATInput = itemRow.querySelector('input[name="unit_cost_without_vat[]"]');
    const costWithVATInput = itemRow.querySelector('input[name="unit_cost_with_vat[]"]');
    const amountInput = itemRow.querySelector('input[name="amount[]"]');
    const qtyInput = itemRow.querySelector('input[name="item_qty[]"]');
    const kgInput = itemRow.querySelector('input[name="item_kg[]"]');
    let units = 0;
    if (qtyInput.readOnly) {
        units = kgInput;
    } else {
        units = qtyInput;
    }
    if (inputElement.name == "unit_cost_with_vat[]") {
        costWithoutVATInput.value = parseFloat(inputElement.valueAsNumber / parseFloat((100 + vatInput.valueAsNumber) / 100)).toFixed(2);
    } else {
        const vatAddition = parseFloat(costWithoutVATInput.valueAsNumber) * parseFloat(vatInput.valueAsNumber / 100);
        costWithVATInput.value = parseFloat(parseFloat(costWithoutVATInput.valueAsNumber) + vatAddition).toFixed(2);
    }
    amountInput.value = parseFloat(costWithVATInput.valueAsNumber * units.valueAsNumber).toFixed(2);
    calculatePurchaseOrderGrandTotal();
}

function calculatePurchaseOrderGrandTotal() {
    const amountInputs = document.querySelectorAll('input[name="amount[]"');
    const grandTotal = [...amountInputs].reduce(
        (previousValue, currentValue) =>
            parseFloat(
                parseFloat(previousValue) + parseFloat(currentValue.valueAsNumber)
            ),
        0
    );
    document.querySelector('input[name="grand_total"]').value =
        parseFloat(grandTotal).toFixed(2);
}

function resetRow(btnElement) {
    const inputs =
        btnElement.parentElement.parentElement.querySelectorAll("input");
    const selectElements =
        btnElement.parentElement.parentElement.querySelectorAll("select");

    [...inputs].forEach((input) => {
        input.value = "";
    });

    [...selectElements].forEach((selectElement, index) => {
        selectElement.options[0].selected = true;
    });
}

// used in creating purchase orders on item rows section
function prefillItemRow(
    selectElement,
    inventory = false,
    coating = false,
    goodsWeight = false
) {
    const optionSelected = selectElement.selectedOptions[0];
    const itemRow = selectElement.closest("tr");

    const inventoryTypeInput = itemRow.querySelector(
        'input[name="inventory_type[]"]'
    );
    const itemIDInput = itemRow.querySelector('input[name="item_id[]"]');
    const nameInput = itemRow.querySelector('input[name="item_name[]"]');
    const codeInput = itemRow.querySelector('input[name="item_code[]"]');
    const descriptionInput = itemRow.querySelector(
        'input[name="item_description[]"]'
    );
    const qtyInput = itemRow.querySelector('input[name="item_qty[]"]');
    const kgInput = itemRow.querySelector('input[name="item_kg[]"]');
    const taxInput = itemRow.querySelector('input[name="item_tax[]"]');
    const unitInput = itemRow.querySelector('input[name="unit_cost[]"]');
    const amountInput = itemRow.querySelector('input[name="amount[]"]');
    const warehouseID = itemRow.querySelector('input[name="warehouse_id[]"]');
    const binID = itemRow.querySelector('input[name="bin_id[]"]');

    if (inventory) {
        if (document.querySelector(".new-item")) {
            document.querySelector(".new-item").value = null;
        }

        let description = optionSelected.getAttribute("attr-data-description");
        let max = optionSelected.getAttribute("attr-current-quantity");
        let minThreshold = optionSelected.getAttribute("attr-min-threshold");
        let code = optionSelected.getAttribute("attr-data-code");
        let cost = optionSelected.getAttribute("attr-data-cost");
        let warehouse = optionSelected.getAttribute("attr-warehouse-id");
        let bin = optionSelected.getAttribute("attr-bin-id");

        inventoryTypeInput.value = optionSelected.parentElement.label;
        warehouseID.value = warehouse;
        binID.value = bin;
        if (optionSelected.parentElement.label == "POWDER") {
            // deactivate quantity
            qtyInput.readOnly = true;
            qtyInput.value = 0;
            kgInput.readOnly = false;
            kgInput.value = 1;
            kgInput.max = max;
            kgInput.min = 0;
        } else {
            // deactivate kilos
            qtyInput.readOnly = false;
            qtyInput.value = 1;
            qtyInput.max = max;
            qtyInput.min = 0;
            if (coating) {
                qtyInput.setAttribute("attr-min-threshold", minThreshold);
            }
            kgInput.readOnly = true;
            kgInput.value = 0;
        }
        itemIDInput.value = selectElement.value;

        descriptionInput.value = description;
        codeInput.value = code;
        nameInput.value = optionSelected.getAttribute("attr-data-name");

        unitInput.value = cost;
    } else {
        $(function () {
            $(selectElement).next().next().val("");
        });
        inventoryTypeInput.value = optionSelected.value;
        // $("#customers_select").select2("val", "");
        if (optionSelected.value == "POWDER") {
            // deactivate quantity
            qtyInput.readOnly = true;
            qtyInput.value = 0;
            kgInput.readOnly = false;
            kgInput.value = 1;
        } else {
            // deactivate kilos
            qtyInput.readOnly = false;
            qtyInput.value = 1;
            kgInput.readOnly = true;
            kgInput.value = 0;
        }

        warehouseID.value = "";
        binID.value = "";
        itemIDInput.value = "";
        descriptionInput.value = "";
        codeInput.value = "";
        nameInput.value = "";
        unitInput.value = 1;
        taxInput.value = 1;
    }

    if (goodsWeight) {
        const goodWeight =
            parseFloat(optionSelected.getAttribute("attr-weight")) *
            6.4 *
            qtyInput.value;
        kgInput.value = goodWeight;
    }

    calculateItemRowTotal(selectElement);
}

// update itemrow final amount - used in PO
function calculateItemRowTotal(
    inputElement,
    coating = false,
    goodsWeight = false
) {
    const itemRow = inputElement.closest("tr");
    const inventoryTypeInput = itemRow.querySelector(
        'input[name="inventory_type[]"]'
    );

    const qtyInput = itemRow.querySelector('input[name="item_qty[]"]');
    const kgInput = itemRow.querySelector('input[name="item_kg[]"]');
    const taxInput = itemRow.querySelector('input[name="item_tax[]"]');
    const unitInput = itemRow.querySelector('input[name="unit_cost[]"]');
    const amountInput = itemRow.querySelector('input[name="amount[]"]');

    if (coating) {
        if (
            parseInt(qtyInput.max) - parseInt(qtyInput.value) <
            parseInt(qtyInput.getAttribute("attr-min-threshold"))
        ) {
            swal({
                title: "Alert",
                text:
                    "Alert! The quantity will lead to the item being below its minimum threshold of " +
                    qtyInput.getAttribute("attr-min-threshold"),
                type: "error",
                timer: 3000,
                buttons: false,
            });
        }
    }
    let taxAddition;
    if (isNaN(parseFloat(taxInput.value)) || parseFloat(taxInput.value) == 0) {
        taxAddition = 1;
    } else {
        taxAddition = parseFloat(
            (parseFloat(taxInput.value) + parseFloat(100)) / 100
        );
    }

    const taxedTotal = parseFloat(taxAddition * unitInput.value).toFixed(2);

    let finalAmount;
    if (inventoryTypeInput.value == "POWDER") {
        finalAmount = parseFloat(taxedTotal * kgInput.value).toFixed(2);
    } else {
        finalAmount = parseFloat(taxedTotal * qtyInput.value).toFixed(2);
    }

    amountInput.value = finalAmount;

    if (goodsWeight) {
        const goodWeight =
            parseFloat(
                itemRow
                    .querySelector("select")
                    .selectedOptions[0].getAttribute("attr-weight")
            ) *
            6.4 *
            qtyInput.value;
        kgInput.value = goodWeight;
    }

    updateGrandTotal();
}

function calculateDiscountItemRowTotal(
    inputElement,
    coating = false,
    goodsWeight = false
) {
    let discount = 0;
    if (parseFloat(inputElement.value) > 0) {
        discount = parseFloat(inputElement.value);
    }
    const itemRow = inputElement.closest("tr");
    const inventoryTypeInput = itemRow.querySelector(
        'input[name="inventory_type[]"]'
    );
    const qtyInput = itemRow.querySelector('input[name="item_qty[]"]');
    const kgInput = itemRow.querySelector('input[name="item_kg[]"]');
    const taxInput = itemRow.querySelector('input[name="item_tax[]"]');
    const unitInput = itemRow.querySelector('input[name="unit_cost[]"]');
    const amountInput = itemRow.querySelector('input[name="amount[]"]');
    const discountInput = itemRow.querySelector(
        'input[name="discount_total[]"]'
    );

    if (coating) {
        if (
            parseInt(qtyInput.max) - parseInt(qtyInput.value) <
            parseInt(qtyInput.getAttribute("attr-min-threshold"))
        ) {
            swal({
                title: "Alert",
                text:
                    "Alert! The quantity will lead to the item being below its minimum threshold of " +
                    qtyInput.getAttribute("attr-min-threshold"),
                type: "error",
                timer: 3000,
                buttons: false,
            });
        }
    }
    let taxAddition;
    if (isNaN(parseFloat(taxInput.value)) || parseFloat(taxInput.value) == 0) {
        taxAddition = 1;
    } else {
        taxAddition = parseFloat(
            (parseFloat(taxInput.value) + parseFloat(100)) / 100
        );
    }

    const taxedTotal = parseFloat(
        taxAddition * (unitInput.value - discount)
    ).toFixed(2);

    let finalAmount;
    if (inventoryTypeInput.value == "POWDER") {
        finalAmount = parseFloat(taxedTotal * kgInput.value).toFixed(2);
        discountInput.value = parseFloat(discount * kgInput.value).toFixed(2);
    } else {
        finalAmount = parseFloat(taxedTotal * qtyInput.value).toFixed(2);
        discountInput.value = parseFloat(discount * qtyInput.value).toFixed(2);
    }

    amountInput.value = finalAmount;

    if (goodsWeight) {
        const goodWeight =
            parseFloat(
                itemRow
                    .querySelector("select")
                    .selectedOptions[0].getAttribute("attr-weight")
            ) *
            6.4 *
            qtyInput.value;
        kgInput.value = goodWeight;
    }

    updateGrandTotal();
}

// update grand-total used in PO
function updateGrandTotal() {
    const totalAmount = document.querySelectorAll('input[name="amount[]"]');
    let initialValue = 0;
    const grandTotal = [...totalAmount].reduce(
        (previousValue, currentValue) =>
            parseFloat(
                parseFloat(previousValue) + parseFloat(currentValue.value)
            ),
        initialValue
    );

    document.querySelector('input[name="grand_total"]').value =
        parseFloat(grandTotal).toFixed(2);

    const totalDiscount = document.querySelectorAll(
        'input[name="discount_total[]"]'
    );
    initialValue = 0;
    const discountTotal = [...totalDiscount].reduce(
        (previousValue, currentValue) =>
            parseFloat(
                parseFloat(previousValue) + parseFloat(currentValue.value)
            ),
        initialValue
    );

    if (document.querySelector('input[name="grand_total_discount"]')) {
        document.querySelector('input[name="grand_total_discount"]').value =
            parseFloat(discountTotal).toFixed(2);
    }

}

// update grand total in a coating job
function updateGrandTotalCoating() { }

// add one item row in PO
function addItemRow(coating = false) {
    if (typeof $.fn.select2 == "function") {
        $(".search-select").select2("destroy");
        $(".searchable-select").each(function (i, obj) {
            if ($(obj).data("select2")) {
                $(obj).select2("destroy");
            }
        });
    }
    if (typeof $.fn.selectpicker == "function") {
        $("select:not(.ms)").selectpicker("destroy");
    }

    const tableBody = document.querySelector(".item-list");
    const itemListRow = tableBody.querySelector("tr");
    const clonedRow = itemListRow.cloneNode(true);

    const inputs = clonedRow.querySelectorAll("input");
    const selectElements = clonedRow.querySelectorAll("select");

    [...inputs].forEach((input) => {
        if (coating) {
            if (input.name != "item_kg[]") {
                input.value = "";
            }
        } else {
            input.value = "";
        }
    });

    [...selectElements].forEach((selectElement, index) => {
        selectElement.options[0].selected = true;
    });

    tableBody.appendChild(clonedRow);

    setTimeout(function () {
        if (typeof $.fn.selectpicker == "function") {
            $("select:not(.ms)").selectpicker();
        }
    }, 500);

    setTimeout(function () {
        if (typeof $.fn.selectpicker == "function") {
            $("select:not(.ms)").selectpicker();
        }
    }, 500);

    setTimeout(() => {
        $(".searchable-select").select2({
            placeholder: "Select an option",
        });
    }, 900);
}

// remove a row of item in the list
function removeRow(btnElement) {
    const tableBody = document.querySelector(".item-list");
    const itemListRow = tableBody.querySelectorAll("tr");

    if (itemListRow.length > 1) {
        btnElement.parentElement.parentElement.remove();
    } else {
        resetRow();
    }
    updateGrandTotal();
}

// for calculating estimate in aluminium powder coating
function calculateEstimate(inputElement, owner = false) {
    const form = inputElement.form;

    let weight = form.goods_weight.value;
    const profile = form.profile_type.value;
    if (isNaN(weight)) {
        weight = 0;
    }
    let result;
    switch (profile) {
        case "Heavy":
            result = weight / 28;
            break;
        case "Medium":
            result = weight / 24;
            break;
        default:
            result = weight / 20;
    }
    form.powder_estimate.value = Math.ceil(result.toFixed(2));
    if (owner) {
        document.querySelectorAll(".owner-kg").forEach((weightInput) => {
            weightInput.value = parseFloat(
                parseFloat(weight) + parseFloat(result)
            ).toFixed(2);
            const changeEvent = new Event("change");
            weightInput.dispatchEvent(changeEvent);
        });
    }
}

// prefill code in aluminium powder coating
function prefillRALData(selectElement) {
    const color =
        selectElement.selectedOptions[0].getAttribute("attr-data-color");
    const code =
        selectElement.selectedOptions[0].getAttribute("attr-data-code");
    const warehouseID =
        selectElement.selectedOptions[0].getAttribute("attr-warehouse-id");
    const binID = selectElement.selectedOptions[0].getAttribute("attr-bin-id");
    selectElement.form.color.value = color;
    selectElement.form.code.value = code;
    selectElement.form.ral_warehouse_id.value = warehouseID;
    selectElement.form.ral_bin_id.value = binID;
}

// update total amount for owner aluminium coating job
function updateAmountOwnerUnits(inputElement) {
    const tableRow = inputElement.parentElement.parentElement;
    const total = tableRow.querySelector('input[name="amount[]"]');
    const itemKG = tableRow.querySelector('input[name="item_kg[]"]');
    const unitCost = tableRow.querySelector('input[name="unit_cost[]"]');
    total.value = parseFloat(itemKG.value * unitCost.value).toFixed(2);
    updateGrandTotal();
}

// used in steel powder coating
function changeItemAmount(inputElement, scaleUp = false) {
    let parent;
    parent = inputElement.parentElement.parentElement;
    if (scaleUp) {
        parent = inputElement.parentElement.parentElement.parentElement;
    }

    let sqMetres = 1;
    if (
        parseFloat(parent.querySelector('input[name="sq_metre[]"]').value) > 0
    ) {
        sqMetres = parent.querySelector('input[name="sq_metre[]"]').value;
    }
    const unitCost = parent.querySelector('input[name="unit_cost[]"]').value;
    const itemQty = parent.querySelector('input[name="item_qty[]"]').value;
    parent.querySelector('input[name="powder_estimate[]"]').value = parseFloat(
        (sqMetres * itemQty) / 3
    ).toFixed(2);
    parent.querySelector('input[name="amount[]"]').value = parseFloat(
        sqMetres * unitCost * itemQty
    ).toFixed(2);
    updateTotalSteel();
}

// calculation of size in steel powder coating
function prefillArea(inputElement) {
    const parent = inputElement.parentElement;
    const length = parent.querySelector('input[name="item_length[]"]').value;
    const width = parent.querySelector('input[name="item_width[]"]').value;
    parent.querySelector('input[name="sq_metre[]"]').value = parseFloat(
        length * width
    ).toFixed(2);
    const event = new Event("change");
    parent.querySelector('input[name="sq_metre[]"]').dispatchEvent(event);
}

// toggling manual size input
function toggleSizeActive(selectElement) {
    if (selectElement.value == "-") {
        selectElement.nextElementSibling.disabled = false;
    } else {
        selectElement.nextElementSibling.disabled = true;
    }
}

// change the area input
function toggleManualArea(checkbox) {
    const parent = checkbox.parentElement.parentElement;
    if (checkbox.checked) {
        parent.querySelector('input[name="item_length[]"]').value = 0;
        parent.querySelector('input[name="item_length[]"]').readOnly = true;
        parent.querySelector('input[name="item_width[]"]').value = 0;
        parent.querySelector('input[name="item_width[]"]').readOnly = true;
        parent.querySelector('input[name="sq_metre[]"]').value = 0;
        parent.querySelector('input[name="sq_metre[]"]').readOnly = false;
    } else {
        parent.querySelector('input[name="item_length[]"]').value = 0;
        parent.querySelector('input[name="item_length[]"]').readOnly = false;
        parent.querySelector('input[name="item_width[]"]').value = 0;
        parent.querySelector('input[name="item_width[]"]').readOnly = false;
        parent.querySelector('input[name="sq_metre[]"]').value = 0;
        parent.querySelector('input[name="sq_metre[]"]').readOnly = true;
    }
}

function updateTotalSteel() {
    const totalInput = document.querySelectorAll('input[name="amount[]"]');
    const values = [...totalInput].map((item) => {
        return item.value || 0;
    });
    const grandTotal = values.reduce((total, current) => {
        if (isNaN(current)) {
            return parseFloat(parseFloat(total) + 0).toFixed(2);
        }
        return parseFloat(parseFloat(total) + parseFloat(current)).toFixed(2);
    });
    document.querySelector('input[name="grand_total"').value = grandTotal;
}

function toggleCollapse(btnElement) {
    const parent = btnElement.parentElement.parentElement;

    $(parent).find(".collapse").collapse("toggle");
}

function prefillItemRowSteel(selectElement) {
    const optionSelected = selectElement.selectedOptions[0];
    const itemRow = selectElement.parentElement.parentElement;

    const inventoryTypeInput = itemRow.querySelector(
        'input[name="inventory_type[]"]'
    );
    const itemIDInput = itemRow.querySelector('input[name="item_id[]"]');
    const codeInput = itemRow.querySelector('input[name="item_code[]"]');
    const descriptionInput = itemRow.querySelector(
        'input[name="item_description[]"]'
    );
    const warehouseID = itemRow.querySelector('input[name="warehouse_id[]"]');
    const binID = itemRow.querySelector('input[name="bin_id[]"]');

    let description = optionSelected.getAttribute("attr-data-description");
    let max = optionSelected.getAttribute("attr-current-quantity");
    let minThreshold = optionSelected.getAttribute("attr-min-threshold");
    let code = optionSelected.getAttribute("attr-data-code");
    let cost = optionSelected.getAttribute("attr-data-cost");
    let warehouse = optionSelected.getAttribute("attr-warehouse-id");
    let bin = optionSelected.getAttribute("attr-bin-id");

    inventoryTypeInput.value = "POWDER";

    warehouseID.value = warehouse;
    binID.value = bin;
    itemIDInput.value = selectElement.value;

    descriptionInput.value = description;
    codeInput.value = code;
}

function prefillRowStockOut(selectElement) {
    const itemRow = selectElement.closest("tr");

    const inventoryType = itemRow.querySelector(
        'input[name="inventory_type[]"]'
    );
    const itemName = itemRow.querySelector('input[name="item_name[]"]');
    const description = itemRow.querySelector(
        'input[name="item_description[]"]'
    );
    const itemSerial = itemRow.querySelector('input[name="item_serial[]"]');
    const warehouseID = itemRow.querySelector('input[name="warehouse_id[]"]');
    const binID = itemRow.querySelector('input[name="bin_id[]"]');

    const currentQuantity = itemRow.querySelector(
        'input[name="current_quantity[]"]'
    );
    const qty = itemRow.querySelector('input[name="item_qty[]"]');

    const selectedItem = selectElement.selectedOptions[0];
    inventoryType.value = selectedItem.parentElement.label;

    itemName.value = selectedItem.getAttribute("attr-data-name");
    description.value = selectedItem.getAttribute("attr-data-description");
    itemSerial.value = selectedItem.getAttribute("attr-data-serial");
    currentQuantity.value = selectedItem.getAttribute("attr-data-level");

    qty.value = "1";
    qty.max = currentQuantity.value;

    warehouseID.value = selectedItem.getAttribute("attr-warehouse-id");
    binID.value = selectedItem.getAttribute("attr-bin-id");
}

function prefillItemTransfer(
    selectElement,
    inventory = false,
    coating = false
) {
    const optionSelected = selectElement.selectedOptions[0];
    const itemRow = selectElement.closest("tr");

    const itemIDInput = itemRow.querySelector('input[name="item_id[]"]');
    const inventoryTypeInput = itemRow.querySelector(
        'input[name="inventory_type[]"]'
    );
    const warehouseID = itemRow.querySelector(
        'input[name="item_warehouse_id[]"]'
    );
    const binID = itemRow.querySelector('input[name="item_bin_id[]"]');
    const nameInput = itemRow.querySelector('input[name="item_name[]"]');
    const codeInput = itemRow.querySelector('input[name="item_code[]"]');
    const unitInput = itemRow.querySelector('input[name="item_unit[]"]');
    const descriptionInput = itemRow.querySelector(
        'input[name="item_description[]"]'
    );
    const qtyInput = itemRow.querySelector('input[name="item_qty[]"]');
    const kgInput = itemRow.querySelector('input[name="item_kg[]"]');

    let description = optionSelected.getAttribute("attr-data-description");
    let unit = optionSelected.getAttribute("attr-data-unit");
    let max = optionSelected.getAttribute("attr-current-quantity");
    let minThreshold = optionSelected.getAttribute("attr-min-threshold");
    let code = optionSelected.getAttribute("attr-data-code");
    let warehouse = optionSelected.getAttribute("attr-warehouse-id");
    let bin = optionSelected.getAttribute("attr-bin-id");

    inventoryTypeInput.value = optionSelected.parentElement.label;
    warehouseID.value = warehouse;
    binID.value = bin;
    if (optionSelected.parentElement.label == "POWDER") {
        // deactivate quantity
        qtyInput.readOnly = true;
        qtyInput.value = 0;
        kgInput.readOnly = false;
        kgInput.value = 1;
        kgInput.max = max;
    } else {
        // deactivate kilos
        qtyInput.readOnly = false;
        qtyInput.value = 1;
        kgInput.readOnly = true;
        kgInput.value = 0;
        qtyInput.max = max;
    }
    itemIDInput.value = selectElement.value;

    descriptionInput.value = description;
    codeInput.value = code;
    unitInput.value = unit;
    nameInput.value = optionSelected.getAttribute("attr-data-name");
}
