@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Inventory',
'datatable' => true,
'bootstrapselect' => true,
]
)

<body class="theme-blue">
  @include('universal-layout.spinner')

  @include('universal-layout.accounts-sidemenu',
  [
  'slug' => '/accounts'
  ]
  )
  <section class="content home">
    <div class="container-fluid">
      <div class="wrapper">
        <div class="main-panel">
      <div class="content">
  <div class="row">

    <div class="col-sm-12 p-0">
      <div class="col-sm-6 mb-2">
        <span>Show Inventory Type:</span>
        <select class="form-control" onchange="showInventoryType(this)">
          <option value="all">All</option>
          <option value="powder">Powder Inventory</option>
          <option value="hardware">Hardware Inventory</option>
          <option value="aluminium">Aluminium Inventory</option>
        </select>
      </div>
    </div>

    <div class="col-md-12">
      <div class="card powder-card">
        <div class="card-header">
          <h4 class="card-title p-0 m-0">Powder Inventory</h4>
        </div>
        <div class="card-body p-0" style="min-height: auto;">
          <div class="table-responsive powderInventory" id="powderInventory">
            <input class="search form-control rounded-sm border-dark my-2" placeholder="Search powder inventory" />
            <table class="table tablesorter table-bordered">
              <thead class=" text-primary">
                <tr>
                  <th data-sort="date" class="border sort cursor-pointer p-0">
                    Date Created
                  </th>
                  <th data-sort="color" class="border sort cursor-pointer p-0">
                    Powder Color
                  </th>
                  <th data-sort="weight" class="border text-nowrap sort cursor-pointer p-0">
                    Weight
                  </th>
                  <th class="border p-0">
                    Action
                  </th>
                </tr>
              </thead>
              <tbody class="list">
                <?php
                  foreach ($powderInventory as $powderItem) {
                    if ((session()->get('auth_warehouse_uid') == 'N/A') && ($powderItem->total_weight < 1)) {
                      continue;
                    }
                ?>
                  <tr>
                    <td style="max-width:7rem;" class="p-0 text-truncate color" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo $powderItem->powder_color ?>">
                      <?php echo $powderItem->date_created ?>
                    </td>
                    <td style="max-width:7rem;" class="p-0 text-truncate description" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo $powderItem->powder_description ?>">
                      <?php echo $powderItem->powder_color ?>
                    </td>
                    <td class="p-0">
                        <?php echo $powderItem->item_weight ?>
                    </td>
                    <td class="p-0">
                        <?php echo $powderItem->reason ?>
                    </td>
                  </tr>
                <?php
                  }
                ?>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="100%">
                    <ul class="pagination list-group list-group-horizontal p-0">

                    </ul>
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-12">
      <div class="card aluminium-card">
        <div class="card-header">
          <h4 class="card-title m-0 p-0">Aluminium Inventory</h4>
        </div>
        <div class="card-body p-0" style="min-height: auto;">
          <div class="table-responsive aluminiumInventory" id="aluminiumInventory">
            <input class="search form-control rounded-sm border-dark my-2" placeholder="Search aluminium inventory" />
            <table class="table tablesorter table-bordered" id="">
              <thead class="text-primary">
                <tr>
                  <th data-sort="date" class="sort cursor-pointer p-0 border">
                    Date Created
                  </th>
                  <th data-sort="name" class="sort cursor-pointer p-0 border">
                    Item Name
                  </th>
                  <th data-sort="quantity" class="sort cursor-pointer text-nowrap p-0 border">
                    Quantity
                  </th>
                  <th data-sort="quantity" class="sort cursor-pointer text-nowrap p-0 border">
                    Action
                  </th>
                </tr>
              </thead>
              <tbody class="list">
                <?php
                  foreach ($aluminiumInventory as $aluminiumItem) {
                    if ((session()->get('auth_warehouse_uid') == 'N/A') && ($aluminiumItem->total_quantity < 1)) {
                      continue;
                    }
                ?>
                  <tr>
                    <td style="max-width:7rem;" class="text-truncate name p-0" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo $aluminiumItem->item_name ?>">
                      <?php echo $aluminiumItem->date_created ?>
                    </td>
                    <td style="max-width:7rem;" class="text-truncate name p-0" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo $aluminiumItem->item_description ?>">
                      <?php echo $aluminiumItem->item_name ?>
                    </td>
                    <td class="p-0">
                        <?php echo $aluminiumItem->item_qty ?>
                    </td>
                    <td class="p-0">
                        <?php echo $aluminiumItem->reason ?>
                    </td>
                  </tr>
                <?php
                  }
                ?>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="100%">
                    <ul class="pagination list-group list-group-horizontal p-0">

                    </ul>
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-12">
      <div class="card hardware-card">
        <div class="card-header">
          <h4 class="card-title m-0 p-0">Hardware Inventory</h4>
        </div>
        <div class="card-body p-0" style="min-height: auto;">
          <div class="table-responsive hardwareInventory" id="hardwareInventory">
            <input class="search form-control rounded-sm border-dark my-2" placeholder="Search hardware inventory" />
            <table class="table tablesorter table-bordered">
              <thead class="text-primary">
                <tr>
                  <th data-sort="date" class="sort cursor-pointer p-0">
                    Date Created
                  </th>
                  <th data-sort="name" class="sort cursor-pointer p-0">
                    Item Name
                  </th>
                  <th data-sort="quantity" class="sort cursor-pointer text-nowrap p-0">
                    Quantity
                  </th>
                  <th data-sort="quantity" class="sort cursor-pointer text-nowrap p-0">
                    Action
                  </th>
                </tr>
              </thead>
              <tbody class="list">
                <?php
                  foreach ($hardwareInventory as $hardwareItem) {
                    if ((session()->get('auth_warehouse_uid') == 'N/A') && ($hardwareItem->total_quantity < 1)) {
                      continue;
                    }
                ?>
                  <tr>
                    <td style="max-width:7rem;" class="text-truncate name p-0" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo $hardwareItem->item_name ?>">
                      <?php echo $hardwareItem->date_created ?>
                    </td>
                    <td style="max-width:7rem;" class="text-truncate description p-0" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo $hardwareItem->item_description ?>">
                      <?php echo $hardwareItem->item_name ?>
                    </td>
                    <td class="p-0">
                        <?php echo $hardwareItem->item_qty ?>
                    </td>
                    <td class="p-0">
                        <?php echo $hardwareItem->reason ?>
                    </td>
                  </tr>
                <?php
                  }
                ?>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="100%">
                    <ul class="pagination list-group list-group-horizontal p-0">

                    </ul>
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
</div>
</div>
    </div>
  </section>

<!-- Init for powder inventory list -->
<script src="/assets/js/plugins/list.min.js"></script>
<script type="text/javascript">
window.addEventListener('load', () =>{
  const powderInventoryOptions = {
    valueNames: [ 'date', 'color', 'description', 'supplier', 'weight'],
    page: 5,
    // pagination: true
  };

  const powderList = new List('powderInventory', powderInventoryOptions);

  const aluminiumInventoryOptions = {
    valueNames: [ 'date', 'name', 'description', 'supplier', 'quantity'],
    page: 5,
    // pagination: true
  };

  const aluminiumList = new List('aluminiumInventory', aluminiumInventoryOptions);

  const hardwareInventoryOptions = {
    valueNames: [ 'date', 'name', 'description', 'supplier', 'quantity'],
    page: 5,
    // pagination: true
  };

  const hardwareList = new List('hardwareInventory', hardwareInventoryOptions);

  const pagination = document.querySelectorAll('.pagination');

  stylePaginationListItems();

  resetAnchorTagStyling();

  function resetAnchorTagStyling(){
    const paginationAnchorTags = document.querySelectorAll('.pagination > li > a');

    [...paginationAnchorTags].forEach((anchorTag, i) => {
      anchorTag.addEventListener('click',(e)=>{
        e.preventDefault();
        setTimeout(()=>{
          stylePaginationListItems();
        }, 10)
      })
    });
  }

  function stylePaginationListItems() {
    [...pagination].forEach((paginationItem) => {
      const list = paginationItem.querySelectorAll('li');
      [...list].forEach((listItem) => {
        listItem.classList += ' list-group-item';
        listItem.querySelector('a').classList += ' text-dark';
      });
    });
    resetAnchorTagStyling();
  }


  powderList.on('updated', stylePaginationListItems);

  powderList.on('searchComplete', stylePaginationListItems);

  powderList.on('sortComplete', stylePaginationListItems);

  powderList.on('filterComplete', stylePaginationListItems);

  aluminiumList.on('updated', stylePaginationListItems);

  aluminiumList.on('searchComplete', stylePaginationListItems);

  aluminiumList.on('sortComplete', stylePaginationListItems);

  aluminiumList.on('filterComplete', stylePaginationListItems);

  hardwareList.on('updated', stylePaginationListItems);

  hardwareList.on('searchComplete', stylePaginationListItems);

  hardwareList.on('sortComplete', stylePaginationListItems);

  hardwareList.on('filterComplete', stylePaginationListItems);

});
const hardwareCard = document.querySelector('.hardware-card');
const aluminiumCard = document.querySelector('.aluminium-card');
const powderCard = document.querySelector('.powder-card');
function showInventoryType(selectElement) {
  switch (selectElement.value) {
    case 'all':
      hardwareCard.style.display = 'block';
      aluminiumCard.style.display = 'block';
      powderCard.style.display = 'block';
      break;
      case 'powder':
        hardwareCard.style.display = 'none';
        aluminiumCard.style.display = 'none';
        powderCard.style.display = 'block';
        break;
        case 'aluminium':
          hardwareCard.style.display = 'none';
          aluminiumCard.style.display = 'block';
          powderCard.style.display = 'none';
          break;
    default:
      hardwareCard.style.display = 'block';
      aluminiumCard.style.display = 'none';
      powderCard.style.display = 'none';

  }
}
</script>
@include('universal-layout.scripts',
  [
  'libscripts' => true,
  'vendorscripts' => true,
  'mainscripts' => true,
  ]
  )
@include('universal-layout.footer')
