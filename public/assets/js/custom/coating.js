let currentlyActiveType = "MARUTI";

function calculatePowderEstimateAluminium(inputElement) {
    const form = inputElement.form;

    let weight = form.goods_weight.valueAsNumber;
    const profile = form.profile_type.value;
    if (isNaN(weight)) {
        weight = 0;
    }
    let result;
    switch (profile) {
        case "1":
            result = weight / 28;
            break;
        case "2":
            result = weight / 24;
            break;
        case "3":
            result = weight / 20;
            break;
        default:
            result = 0;
    }
    form.powder_estimate.value = Math.ceil(result.toFixed(2));
    document.querySelectorAll(".owner-kg").forEach((weightInput) => {
        weightInput.value = parseFloat(
            parseFloat(weight) + Math.ceil(parseFloat(result))
        ).toFixed(2);
        const changeEvent = new Event("change");
        weightInput.dispatchEvent(changeEvent);
    });
}

const marutiCoatingDivs = document.querySelectorAll(".maruti-coating");

const ownerAluminiumDivs = document.querySelectorAll(".owner-aluminium-coating");

const ownerSteelDivs = document.querySelectorAll(".owner-steel-coating");

const directSale = document.querySelectorAll('.direct-sale');

/**
 * On load all for owner should be disabled to prevent confusion on submission
 */
ownerAluminiumDivs.forEach((div) => {
    const inputs = div.querySelectorAll("input");
    inputs.forEach((input) => {
        input.disabled = true;
    });
});

ownerAluminiumDivs.forEach((div) => {
    const inputs = div.querySelectorAll("input");
    inputs.forEach((input) => {
        input.disabled = true;
    });
});

ownerSteelDivs.forEach((div) => {
    const inputs = div.querySelectorAll("input");
    inputs.forEach((input) => {
        input.disabled = true;
    });
});

directSale.forEach((div) => {
    const inputs = div.querySelectorAll("input");
    inputs.forEach((input) => {
        input.disabled = true;
    });
});

window.addEventListener('load', () => {
    toggleActiveItemList(document.querySelector('input[name="belongs_to"]:checked'));
});

function toggleActiveItemList(radioBtn) {
    const radioValue = radioBtn.getAttribute('data-value');
    if (radioValue === 'MARUTI') {
        marutiCoatingDivs.forEach((div) => {
            div.style.display = "block";
            const inputs = div.querySelectorAll("input");
            inputs.forEach((input) => {
                input.disabled = false;
            });
        });

        ownerAluminiumDivs.forEach((div) => {
            div.style.display = 'none';
            const inputs = div.querySelectorAll("input");
            inputs.forEach((input) => {
                input.disabled = true;
            });
        });

        ownerSteelDivs.forEach((div) => {
            div.style.display = 'none';
            const inputs = div.querySelectorAll("input");
            inputs.forEach((input) => {
                input.disabled = true;
            });
        });

        directSale.forEach((div) => {
            div.style.display = 'none';
            const inputs = div.querySelectorAll("input");
            inputs.forEach((input) => {
                input.disabled = true;
            });
        });

    } else if (radioValue === 'OWNER-COMBINED') {
        marutiCoatingDivs.forEach((div) => {
            div.style.display = "none";
            const inputs = div.querySelectorAll("input");
            inputs.forEach((input) => {
                input.disabled = true;
            });
        });

        ownerAluminiumDivs.forEach((div) => {
            div.style.display = 'block';
            const inputs = div.querySelectorAll("input");
            inputs.forEach((input) => {
                input.disabled = false;
            });
        });

        ownerSteelDivs.forEach((div) => {
            div.style.display = 'block';
            const inputs = div.querySelectorAll("input");
            inputs.forEach((input) => {
                input.disabled = false;
            });
        });

        directSale.forEach((div) => {
            div.style.display = 'none';
            const inputs = div.querySelectorAll("input");
            inputs.forEach((input) => {
                input.disabled = true;
            });
        });

        currentlyActiveType = 'OWNER';
    } else if (radioValue === 'OWNER-ALUMINIUM') {
        marutiCoatingDivs.forEach((div) => {
            div.style.display = "none";
            const inputs = div.querySelectorAll("input");
            inputs.forEach((input) => {
                input.disabled = true;
            });
        });

        ownerAluminiumDivs.forEach((div) => {
            div.style.display = 'block';
            const inputs = div.querySelectorAll("input");
            inputs.forEach((input) => {
                input.disabled = false;
            });
        });

        ownerSteelDivs.forEach((div) => {
            div.style.display = 'none';
            const inputs = div.querySelectorAll("input");
            inputs.forEach((input) => {
                input.disabled = true;
            });
        });

        directSale.forEach((div) => {
            div.style.display = 'none';
            const inputs = div.querySelectorAll("input");
            inputs.forEach((input) => {
                input.disabled = true;
            });
        });

        currentlyActiveType = 'OWNER';
    } else if (radioValue === 'OWNER-STEEL') {
        marutiCoatingDivs.forEach((div) => {
            div.style.display = "none";
            const inputs = div.querySelectorAll("input");
            inputs.forEach((input) => {
                input.disabled = true;
            });
        });

        ownerAluminiumDivs.forEach((div) => {
            div.style.display = 'none';
            const inputs = div.querySelectorAll("input");
            inputs.forEach((input) => {
                input.disabled = true;
            });
        });

        ownerSteelDivs.forEach((div) => {
            div.style.display = 'block';
            const inputs = div.querySelectorAll("input");
            inputs.forEach((input) => {
                input.disabled = false;
            });
        });

        directSale.forEach((div) => {
            div.style.display = 'none';
            const inputs = div.querySelectorAll("input");
            inputs.forEach((input) => {
                input.disabled = true;
            });
        });

        currentlyActiveType = 'OWNER';
    } else {

        marutiCoatingDivs.forEach((div) => {
            div.style.display = "none";
            const inputs = div.querySelectorAll("input");
            inputs.forEach((input) => {
                input.disabled = true;
            });
        });

        ownerAluminiumDivs.forEach((div) => {
            div.style.display = 'none';
            const inputs = div.querySelectorAll("input");
            inputs.forEach((input) => {
                input.disabled = true;
            });
        });

        ownerSteelDivs.forEach((div) => {
            div.style.display = 'none';
            const inputs = div.querySelectorAll("input");
            inputs.forEach((input) => {
                input.disabled = true;
            });
        });

        directSale.forEach((div) => {
            div.style.display = 'block';
            const inputs = div.querySelectorAll("input");
            inputs.forEach((input) => {
                input.disabled = false;
            });
        });

        currentlyActiveType = 'MARUTI';
    }

}

function updateAmountOwnerUnits(inputElement) {
    const tableRow = inputElement.closest('tr');;
    const total = tableRow.querySelector('input[name="amount[]"]');
    const itemKG = tableRow.querySelector('input[name="item_kg[]"]');
    const unitCost = tableRow.querySelector('input[name="aluminium_unit_price[]"]');
    total.value = parseFloat(itemKG.value * unitCost.value).toFixed(2);
    updateCoatingJobGrandTotal();
}

function prefillArea(inputElement) {
    const parent = inputElement.closest('tr');
    const length = parent.querySelector('input[name="steel_item_length[]"]').value;
    const width = parent.querySelector('input[name="steel_item_width[]"]').value;
    if (parent.querySelector('select[name="size[]"').value != 'LINEAR METRES') {
        parent.querySelector('.sq-metre-input').value = parseFloat(
            length * width
        ).toFixed(2);
    }
    const event = new Event("change");

    parent.querySelector('.sq-metre-input').dispatchEvent(event);
    parent.querySelector('input[name="sq_metre[]"]').dispatchEvent(event);
}

function changeItemAmountCoating(inputElement, scaleUp = false) {
    let parent;
    parent = inputElement.parentElement.parentElement;

    let sqMetres = 1;
    if (
        parseFloat(parent.querySelector('input[name="sq_metre[]"]').value) > 0
    ) {
        sqMetres = parent.querySelector('input[name="sq_metre[]"]').value;
    }

    const unitCost = parent.querySelector(
        'input[name="steel_unit_price[]"]'
    ).value;
    const itemQty = parent.querySelector(
        'input[name="steel_item_qty[]"]'
    ).value;

    if (parent.querySelector('select[name="size[]"').value == 'LINEAR METRES') {
        parent.querySelector('input[name="steel_item_length[]"]').value = parseFloat(parent.querySelector('input[name="sq_metre[]"]').value / itemQty).toFixed(2);
        let sqMetresPowder = parent.querySelector('input[name="steel_item_length[]"]').value * parent.querySelector('input[name="steel_item_width[]"]').value
        parent.querySelector('input[name="steel_powder_estimate[]"]').value =
            parseFloat((sqMetresPowder * itemQty) / 3).toFixed(2);
        updatePowderEstimateSteel();
        if (parent.querySelector('select[name="size[]"').selectedOptions[0].getAttribute('data-per')) {
            parent.querySelector('input[name="steel_amount[]"]').value = parseFloat(
                itemQty.value * unitCost
            ).toFixed(2);
        } else {
            parent.querySelector('input[name="steel_amount[]"]').value = parseFloat(
                sqMetres * unitCost
            ).toFixed(2);
        }
        debounce(() => saveInput(parent))();
    } else {
        let sqMetresPowder = parent.querySelector('input[name="steel_item_length[]"]').value * parent.querySelector('input[name="steel_item_width[]"]').value
        parent.querySelector('input[name="steel_powder_estimate[]"]').value =
            parseFloat((sqMetresPowder * itemQty) / 3).toFixed(2);
        updatePowderEstimateSteel();
        if (parent.querySelector('select[name="size[]"').selectedOptions[0].getAttribute('data-per')) {
            parent.querySelector('input[name="steel_amount[]"]').value = parseFloat(
                unitCost * itemQty.value
            ).toFixed(2);
        } else {
            parent.querySelector('input[name="steel_amount[]"]').value = parseFloat(
                unitCost * itemQty
            ).toFixed(2);
        }

    }

    updateCoatingJobGrandTotal();
}

function updatePowderEstimateSteel() {
    const estimates = document.querySelectorAll('input[name="steel_powder_estimate[]"]');
    const mainEstimate = document.querySelector('input[name="powder_estimate"');
    let totalEstimate = 0;
    for (const estimate of estimates) {
        totalEstimate += parseFloat(estimate.value) || 0;
    }
    mainEstimate.value = totalEstimate;
}

function debounce(func, timeout = 2500) {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => { func.apply(this, args); }, timeout);
    };
}

function saveInput(parent) {
    if (parent.querySelector('input[name="steel_item_width[]"]').value == '') {
        alert('Requiring item width for powder estimate')
    }
}

function updateCoatingJobGrandTotal() {
    let totalAmount;
    if (currentlyActiveType == "OWNER") {
        totalAmount = document.querySelectorAll(".owner-total");
    } else {
        totalAmount = document.querySelectorAll(".maruti-total");
    }
    let grandTotal = 0;
    for (const input of totalAmount) {
        if (!input.disabled) {
            if (input.value > 0) {
                grandTotal = parseFloat(grandTotal) + parseFloat(input.value);
            }
        }
    }
    document.querySelector('input[name="grand_total"]').value =
        parseFloat(grandTotal).toFixed(2);
}

function prefillItemRowSteel(selectElement) {
    const optionSelected = selectElement.selectedOptions[0];
    const itemRow = selectElement.parentElement.parentElement;

    const inventoryTypeInput = itemRow.querySelector(
        'input[name="steel_inventory_type[]"]'
    );
    const itemIDInput = itemRow.querySelector('input[name="steel_item_id[]"]');
    const codeInput = itemRow.querySelector('input[name="steel_item_code[]"]');
    const descriptionInput = itemRow.querySelector(
        'input[name="steel_item_description[]"]'
    );
    const warehouseID = itemRow.querySelector(
        'input[name="steel_warehouse_id[]"]'
    );
    const binID = itemRow.querySelector('input[name="steel_bin_id[]"]');

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

function prefillItemRowMaruti(selectElement) {
    const optionSelected = selectElement.selectedOptions[0];
    const itemRow = selectElement.closest('tr');

    const qtyInput = itemRow.querySelector('input[name="maruti_item_qty[]"]');
    const unitPriceInput = itemRow.querySelector('input[name="maruti_unit_price[]"]');
    const vatInput = itemRow.querySelector('input[name="maruti_item_vat[]"]');
    const uomInput = itemRow.querySelector('input[name="maruti_item_uom[]"]');

    vatInput.value = optionSelected.getAttribute("attr-data-price-vat");
    unitPriceInput.value = optionSelected.getAttribute("attr-data-price");
    uomInput.value = optionSelected.getAttribute("attr-data-uom");
    qtyInput.max = optionSelected.getAttribute("attr-data-current-quantity");
    qtyInput.value = 1;


    calculateMarutiItemRowTotal(qtyInput);
}

function calculateMarutiItemRowTotal(inputElement) {
    const itemRow = inputElement.closest('tr');

    const qtyInput = itemRow.querySelector('input[name="maruti_item_qty[]"]');
    const unitPriceInput = itemRow.querySelector('input[name="maruti_unit_price[]"]');
    const vatInput = itemRow.querySelector('input[name="maruti_item_vat[]"]');
    const uomInput = itemRow.querySelector('input[name="maruti_item_uom[]"]');
    const amountInput = itemRow.querySelector('input[name="maruti_item_amount[]"]');

    amountInput.value = parseFloat(qtyInput.valueAsNumber * unitPriceInput.valueAsNumber).toFixed(2);
    updateCoatingJobGrandTotal();
}

function prefillItemRowMarutiDirect(selectElement) {
    const optionSelected = selectElement.selectedOptions[0];
    const itemRow = selectElement.closest('tr');

    const nameInput = itemRow.querySelector('input[name="custom_item_name[]"]');
    if (nameInput) {
        if (selectElement.value == "") {
            nameInput.readOnly = false;
        } else {
            nameInput.value = selectElement.selectedOptions[0].innerHTML.trim();
            nameInput.readOnly = true;
        }
    }
    const qtyInput = itemRow.querySelector('input[name="maruti_direct_item_qty[]"]');
    const kgInput = itemRow.querySelector('input[name="maruti_direct_item_kg[]"]');
    const unitPriceInput = itemRow.querySelector('input[name="maruti_direct_unit_price[]"]');
    const vatInput = itemRow.querySelector('input[name="maruti_direct_unit_vat[]"]');
    const uomInput = itemRow.querySelector('input[name="maruti_direct_uom[]"]');
    const typeInput = itemRow.querySelector('input[name="maruti_direct_inventory_type[]"]');

    vatInput.value = optionSelected.getAttribute("attr-data-price-vat") || 0;
    unitPriceInput.value = optionSelected.getAttribute("attr-data-price") || 0;
    uomInput.value = optionSelected.getAttribute("attr-data-uom") || 'UNITS';

    if (optionSelected.parentElement.label == "Powder") {
        qtyInput.readOnly = true;
        qtyInput.value = 0;
        kgInput.readOnly = false;
        kgInput.max = optionSelected.getAttribute("attr-data-current-quantity");
        kgInput.value = 1;
        typeInput.value = optionSelected.parentElement.label;
    } else {
        kgInput.readOnly = true;
        kgInput.value = 0;
        qtyInput.readOnly = false;
        qtyInput.max = optionSelected.getAttribute("attr-data-current-quantity");
        qtyInput.value = 1;
        typeInput.value = optionSelected.parentElement.label || "";
    }

    calculateMarutiDirectItemRowTotal(qtyInput);
}

function calculateMarutiDirectItemRowTotal(inputElement) {
    const itemRow = inputElement.closest('tr');

    const qtyInput = itemRow.querySelector('input[name="maruti_direct_item_qty[]"]');
    const kgInput = itemRow.querySelector('input[name="maruti_direct_item_kg[]"]');
    const unitPriceInput = itemRow.querySelector('input[name="maruti_direct_unit_price[]"]');
    const amountInput = itemRow.querySelector('input[name="maruti_direct_amount[]"]');
    if (qtyInput.readOnly) {
        amountInput.value = parseFloat(kgInput.valueAsNumber * unitPriceInput.valueAsNumber).toFixed(2);
    } else {
        amountInput.value = parseFloat(qtyInput.valueAsNumber * unitPriceInput.valueAsNumber).toFixed(2);
    }
    updateCoatingJobGrandTotal();
}


function prefillItemRow(
    selectElement,
    inventory = false,
    coating = false,
    goodsWeight = false
) {
    const optionSelected = selectElement.selectedOptions[0];
    const itemRow = selectElement.closest('tr');

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
    const itemUnitInput = itemRow.querySelector('input[name="item_unit[]"]');
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
        let unit = optionSelected.getAttribute("attr-data-unit");

        inventoryTypeInput.value = optionSelected.parentElement.label;
        warehouseID.value = warehouse;
        binID.value = bin;
        if (itemUnitInput) {
            itemUnitInput.value = unit;
        }
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
        $('.search-select').select2('destroy');
        setTimeout(function () {
            $('.search-select').select2('destroy');
        }, 500);
        $('select:not(.ms)').selectpicker('refresh');
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

function calculateItemRowTotal(
    inputElement,
    coating = false,
    goodsWeight = false
) {
    const itemRow = inputElement.closest('tr');
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
            // $.notify(
            //     {
            //         icon: "tim-icons ui-1_bell-53",
            //         message:
            //             "Alert! The quantity will lead to the item being below its minimum threshold of " +
            //             qtyInput.getAttribute("attr-min-threshold"),
            //     },
            //     {
            //         type: "danger",
            //     }
            // );
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

    const taxedTotal = parseFloat(1 * unitInput.value).toFixed(2);

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

    updateCoatingJobGrandTotal();
}

function toggleManualArea(checkbox) {
    const parent = checkbox.parentElement.parentElement;
    if (checkbox.checked) {
        parent.querySelector('input[name="steel_item_length[]"]').value = 0;
        parent.querySelector('input[name="steel_item_length[]"]').readOnly = true;
        parent.querySelector('input[name="steel_item_width[]"]').value = 0;
        parent.querySelector('input[name="sq_metre[]"]').value = 0;
        parent.querySelector('input[name="sq_metre[]"]').readOnly = false;
    } else {
        parent.querySelector('input[name="steel_item_length[]"]').value = 0;
        parent.querySelector('input[name="steel_item_length[]"]').readOnly = false;
        parent.querySelector('input[name="steel_item_width[]"]').value = 0;
        parent.querySelector('input[name="sq_metre[]"]').value = 0;
        parent.querySelector('input[name="sq_metre[]"]').readOnly = true;
    }
}

function addItemRow(selector) {
    const tableBody = document.querySelector(selector);
    $(tableBody.querySelectorAll('select:not(.ms)')).selectpicker('destroy');
    $(tableBody.querySelectorAll('.search-select')).select2('destroy');

    const itemListRow = tableBody.querySelector("tr");
    const clonedRow = itemListRow.cloneNode(true);

    const inputs = clonedRow.querySelectorAll("input");
    const selectElements = clonedRow.querySelectorAll("select");

    [...inputs].forEach((input) => {
        if (currentlyActiveType == 'OWNER') {
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

    setTimeout(() => {
        $('.search-select').select2();
        $('select:not(.ms)').selectpicker();
    }, 400);

}

function prefillRALData(selectElement) {
    const color =
        selectElement.selectedOptions[0].getAttribute("attr-data-color");
    const code =
        selectElement.selectedOptions[0].getAttribute("attr-data-code");
    selectElement.form.color.value = color;
    selectElement.form.code.value = code;
}

function removeRow(btnElement, edit = false, inputName = '', totalRow = '') {
    const tableBody = btnElement.closest('tbody');
    const itemListRow = tableBody.querySelectorAll("tr");
    if (!edit) {
        if (itemListRow.length > 1) {
            btnElement.parentElement.parentElement.remove();
        } else {
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
    } else {
        if (itemListRow.length > 1) {
            btnElement.parentElement.parentElement.classList.add('d-none');
            btnElement.parentElement.parentElement.querySelector(`input[name="${inputName}"]`).checked = true;
            btnElement.parentElement.parentElement.querySelector(`.${totalRow}`).classList.remove(totalRow);
        } else {
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
    }

    updateCoatingJobGrandTotal();
}

function toggleSizeActive(selectElement) {
    if (selectElement.value == "-") {
        selectElement.nextElementSibling.disabled = false;
    } else {
        selectElement.nextElementSibling.disabled = true;
    }
    if (selectElement.value == 'LINEAR METRES') {
        document.querySelector('.linear-metre-input').disabled = false;
        document.querySelector('.sq-metre-input').disabled = true;
    } else {
        document.querySelector('.linear-metre-input').disabled = true;
        document.querySelector('.sq-metre-input').disabled = false;
    }
}

function toggleCollapse(btnElement) {
    const parent = btnElement.parentElement.parentElement;

    $(parent).find(".collapse").collapse("toggle");
}
