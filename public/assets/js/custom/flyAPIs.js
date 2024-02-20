// adding suppliers on the fly
const supplierDropdown = document.querySelector('.supplier-select');

async function addSupplierViaAPI(submitBtnElement) {
    // reference to the previous html content
    const previousHTML = submitBtnElement.innerHTML;
    submitBtnElement.disabled = true;
    // show the spinner
    submitBtnElement.innerHTML = '<span class="spinner-border text-dark"></span>';

    const formElement = submitBtnElement.form;

    let formData = new FormData();
    formData.append("supplier_name", formElement.supplier_name.value);
    formData.append("supplier_email", formElement.supplier_email.value);
    formData.append("supplier_mobile", formElement.supplier_mobile.value);
    formData.append("supplier_description", formElement.supplier_description.value);
    formData.append("company_location", formElement.company_location.value);
    formData.append("company_pin", formElement.company_pin.value);
    formData.append("company_box", formElement.company_box.value);

    let success = true;

    try {
      let response = await fetch('/suppliers/storeAPI', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        body: formData
      });

      let addSupplierResponse = await response.json();
      const option = document.createElement('option');
      option.value = addSupplierResponse.data.id;
      option.innerHTML = addSupplierResponse.data.supplier_name;
      option.selected = true;
      supplierDropdown.append(option);

    } catch (e) {

      success = false;

    }

    // a delay to show responses
    setTimeout(()=>{
      if (success) {
        swal({
          title: "Success",
          text: 'Supplier has been added successfully',
          type: "success",
          timer: 3000,
          buttons: false,
        });


        formElement.reset();
        $(submitBtnElement.parentElement.parentElement.parentElement.parentElement).modal('toggle');
      }else {
        swal({
          title: "Error",
          text: 'Failed to add supplier please retry',
          type: "error",
          timer: 3000,
          buttons: false,
        });
      }
    }, 800)


    setTimeout(()=>{
      submitBtnElement.innerHTML = previousHTML;
      submitBtnElement.disabled = false;
    }, 2000)
}

// adding warehouses on the fly
const warehouseDropdowns = document.querySelectorAll('.warehouse-select');

async function addWarehouseViaAPI(submitBtnElement) {
    // reference to the previous html content
    const previousHTML = submitBtnElement.innerHTML;
    submitBtnElement.disabled = true;
    // show the spinner
    submitBtnElement.innerHTML = '<span class="spinner-border text-dark"></span>';

    const formElement = submitBtnElement.form;

    let formData = new FormData();
    formData.append("location_id", formElement.location_id.value);
    formData.append("warehouse_name", formElement.warehouse_name.value);
    formData.append("warehouse_description", formElement.warehouse_description.value);
    formData.append("warehouse_status", formElement.warehouse_status.value);

    let success = true;

    try {
      let response = await fetch('/warehouses/storeAPI', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        body: formData
      });

      let addWarehouseResponse = await response.json();
      if (!addWarehouseResponse.status) {
        success = false;
      }
      const option = document.createElement('option');
      option.value = addWarehouseResponse.data.id;
      option.innerHTML = addWarehouseResponse.data.warehouse_name + '(' + formElement.location_id.selectedOptions[0].innerHTML + ')';
      option.selected = true;

      [...warehouseDropdowns].forEach((warehouseDropdown) => {
        warehouseDropdown.append(option);
      });

    } catch (e) {

      success = false;

    }

    // a delay to show responses
    setTimeout(()=>{
      if (success) {
        swal({
          title: "Success",
          text: 'Warehouse has been added successfully',
          type: "success",
          timer: 3000,
          buttons: false,
        });

        formElement.reset();
        $(submitBtnElement.parentElement.parentElement.parentElement.parentElement).modal('toggle');
      }else {
        swal({
          title: "Success",
          text: 'Failed to add warehouse please retry',
          type: "success",
          timer: 3000,
          buttons: false,
        });
      }
    }, 800)


    setTimeout(()=>{
      submitBtnElement.innerHTML = previousHTML;
      submitBtnElement.disabled = false;
    }, 2000)
}

// adding floors on the fly
const floorDropdowns = document.querySelectorAll('.floor-select');

async function addFloorViaAPI(submitBtnElement) {
    // reference to the previous html content
    const previousHTML = submitBtnElement.innerHTML;
    submitBtnElement.disabled = true;
    // show the spinner
    submitBtnElement.innerHTML = '<span class="spinner-border text-dark"></span>';

    const formElement = submitBtnElement.form;

    let formData = new FormData();
    formData.append("floor_name", formElement.floor_name.value);
    formData.append("warehouse_id", formElement.warehouse_id.value);

    let success = true;

    try {
      let response = await fetch('/floors/storeAPI', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        body: formData
      });

      let addFloorResponse = await response.json();
      if (!addFloorResponse.status) {
        success = false;
      }
      floorOptions.push(addFloorResponse.data);
      const option = document.createElement('option');
      option.value = addFloorResponse.data.id;
      option.innerHTML = addFloorResponse.data.floor_name;
      option.selected = true;

      [...floorDropdowns].forEach((floorDropdown) => {
        floorDropdown.append(option);
      });

      filterFloors(document.querySelector('.warehouse-select'));

    } catch (e) {
      console.log(e);
      success = false;

    }

    // a delay to show responses
    setTimeout(()=>{
      if (success) {
        swal({
          title: "Success",
          text: 'Floor has been added successfully',
          type: "success",
          timer: 3000,
          buttons: false,
        });

        formElement.reset();
        $(submitBtnElement.parentElement.parentElement.parentElement.parentElement).modal('toggle');
      }else {
        swal({
          title: "Error",
          text: 'Failed to add floor please retry',
          type: "error",
          timer: 3000,
          buttons: false,
        });
      }
    }, 800)


    setTimeout(()=>{
      submitBtnElement.innerHTML = previousHTML;
      submitBtnElement.disabled = false;
    }, 2000)
}

// adding shelf on the fly
const shelfDropdowns = document.querySelectorAll('.shelf-select');

async function addShelfViaAPI(submitBtnElement) {
    // reference to the previous html content
    const previousHTML = submitBtnElement.innerHTML;
    submitBtnElement.disabled = true;
    // show the spinner
    submitBtnElement.innerHTML = '<span class="spinner-border text-dark"></span>';

    const formElement = submitBtnElement.form;

    let formData = new FormData();
    formData.append("shelf_name", formElement.shelf_name.value);
    formData.append("floor_id", formElement.floor_id.value);

    let success = true;

    try {
      let response = await fetch('/shelves/storeAPI', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        body: formData
      });

      let addShelfResponse = await response.json();
      if (!addShelfResponse.status) {
        success = false;
      }
      shelfOptions.push(addShelfResponse.data);
      const option = document.createElement('option');
      option.value = addShelfResponse.data.id;
      option.innerHTML = addShelfResponse.data.shelf_name;
      option.selected = true;

      [...shelfDropdowns].forEach((shelfDropdown) => {
        shelfDropdown.append(option);
      });

      filterShelves(document.querySelector('.floor-select'));

    } catch (e) {
      console.log(e);
      success = false;

    }

    // a delay to show responses
    setTimeout(()=>{
      if (success) {
        swal({
          title: "Success",
          text: 'Shelf has been added successfully',
          type: "success",
          timer: 3000,
          buttons: false,
        });

        formElement.reset();
        $(submitBtnElement.parentElement.parentElement.parentElement.parentElement).modal('toggle');
      }else {
        swal({
          title: "Error",
          text: 'Failed to add shelf please retry',
          type: "error",
          timer: 3000,
          buttons: false,
        });
      }
    }, 800)


    setTimeout(()=>{
      submitBtnElement.innerHTML = previousHTML;
      submitBtnElement.disabled = false;
    }, 2000)
}

const binDropdowns = document.querySelectorAll('.bin-select');

async function addBinViaAPI(submitBtnElement) {
    // reference to the previous html content
    const previousHTML = submitBtnElement.innerHTML;
    submitBtnElement.disabled = true;
    // show the spinner
    submitBtnElement.innerHTML = '<span class="spinner-border text-dark"></span>';

    const formElement = submitBtnElement.form;

    let formData = new FormData();
    formData.append("shelf_id", formElement.shelf_id.value);
    formData.append("bin_name", formElement.bin_name.value);
    formData.append("bin_description", formElement.bin_description.value);
    formData.append("bin_status", formElement.bin_status.value);

    let success = true;

    try {
      let response = await fetch('/bins/storeAPI', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        body: formData
      });

      let addBinResponse = await response.json();
      if (!addBinResponse.status) {
        success = false;
      }
      binOptions.push(addBinResponse.data);

      filterBins(document.querySelector('.shelf-select'));

    } catch (e) {

      console.log(e);

      success = false;

    }

    // a delay to show responses
    setTimeout(()=>{
      if (success) {
        swal({
          title: "Success",
          text: 'Bin has been added successfully',
          type: "success",
          timer: 3000,
          buttons: false,
        });

        formElement.reset();
        $(submitBtnElement.parentElement.parentElement.parentElement.parentElement).modal('toggle');
      }else {
        swal({
          title: "Error",
          text: 'Failed to add bin please retry',
          type: "error",
          timer: 3000,
          buttons: false,
        });
      }
    }, 800)


    setTimeout(()=>{
      submitBtnElement.innerHTML = previousHTML;
      submitBtnElement.disabled = false;
    }, 2000)
}

const customerDropdown = document.querySelectorAll('.customer-select');
let success = true;
async function addCustomerViaAPI(submitBtnElement) {
    // reference to the previous html content
    const previousHTML = submitBtnElement.innerHTML;
    submitBtnElement.disabled = true;
    // show the spinner
    submitBtnElement.innerHTML = '<span class="spinner-border text-dark"></span>';

    const formElement = submitBtnElement.form;

    let formData = new FormData();
    formData.append("customer_name", formElement.customer_name.value);
    formData.append("credit_limit", formElement.credit_limit.value);
    formData.append("contact_number", formElement.contact_number.value);
    formData.append("location", formElement.location.value);
    formData.append("company", formElement.company.value);
    formData.append("contact_person_name", formElement.contact_person_name.value);
    formData.append("contact_person_email", formElement.contact_person_email.value);
    formData.append("kra_pin", formElement.kra_pin.value);

    let success = true;

    try {
      let response = await fetch('/customers/storeAPI', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        body: formData
      });

      let addCustomerResponse = await response.json();
      const option = document.createElement('option');
      option.value = addCustomerResponse.data.id;
      option.innerHTML = addCustomerResponse.data.company;
      option.selected = true;
      [...customerDropdown].forEach((customerSelect) => {
        customerSelect.append(option);
      })

      $(submitBtnElement.parentElement.parentElement.parentElement.parentElement).modal('toggle');

    } catch (e) {

      console.log(e);

      success = false;

    }

    // a delay to show responses
    setTimeout(()=>{
      if (success) {
        swal({
          title: "Success",
          text: 'Customer has been added successfully',
          type: "success",
          timer: 3000,
          buttons: false,
        });

        formElement.reset();
        $(submitBtnElement.parentElement.parentElement.parentElement.parentElement).modal('toggle');
      }else {
        swal({
          title: "Error",
          text: 'Failed to add customer please retry',
          type: "error",
          timer: 3000,
          buttons: false,
        });
      }
    }, 800)


    setTimeout(()=>{
      submitBtnElement.innerHTML = previousHTML;
      submitBtnElement.disabled = false;
    }, 2000)


  }
