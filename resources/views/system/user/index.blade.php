@include('universal-layout.header',
[
'pageTitle' => 'Aprotec | Users',
]
)
<style>
  .modal-backdrop.show {
    z-index: 4;
  }
</style>

<body class="theme-green">
  @include('universal-layout.spinner')

  @include('universal-layout.system-sidemenu',
  [
  'slug' => '/settings'
  ]
  )
  <section class="content home">
    <div class="container-fluid">
      <div class="wrapper">
        <div class="main-panel">

      <div class="content">

        <div class="row">
          <button type="button" name="button" class="btn btn-default d-flex align-items-center container justify-content-center mb-3 ml-3 w-25" data-toggle="modal" data-target="#createLocationForm">
            <i class="tim-icons icon-simple-add"></i> CREATE A USER
          </button>

          <!-- Modal -->
          <div class="modal fade" id="createLocationForm" tabindex="-1" role="dialog" aria-labelledby="createCompanyForm" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Create A User in the System</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="tim-icons icon-simple-remove"></i>
                  </button>
                </div>
                <div class="modal-body">

                  <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('users.store') }}">
                    @csrf
                    <div class="d-flex flex-column">

                      <div class="col">
                        <div class="form-group">
                          <label for="locationName">Username</label>
                          <input type="text" required name="username" class="form-control" id="userName" aria-describedby="userName" class="dark-text" placeholder="Enter username">
                        </div>
                      </div>

                      <div class="col">
                        <div class="form-group">
                          <label for="userEmail">Email</label>
                          <input type="email" required name="email" class="form-control" id="userEmail" aria-describedby="userEmail" class="dark-text" placeholder="Enter email">
                        </div>
                      </div>

                      <div class="col">
                        <div class="form-group">
                          <label>Select Role</label>
                          <select class="form-control ms" name="role_id" >
                            @foreach($roles as $singleRole)
                              <option value="{{ $singleRole->id }}">
                                {{ $singleRole->name }}
                              </option>
                            @endforeach
                          </select>
                        </div>
                      </div>

                    </div>


                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                  <button type="submit" name="submit_btn" value="Create User" class="btn btn-primary">
                    CREATE USER
                  </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>


        <div class="col">
          <div class="card card-plain">
            <div class="card-header">
              <h4 class="card-title p-0 m-0">Available Users</h4>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive p-0">
                <table class="table table-bordered sorter fixed-table col-12 table-fixed-2 p-0 data-table" id="">
                  <thead class="text-primary">
                    <tr>
                      <th class="py-0 px-1 border">
                        Date Created
                      </th>
                      <th class="py-0 px-1 border">
                        Username
                      </th>
                      <th class="py-0 px-1 border">
                        Email
                      </th>
                      <th class="py-0 px-1 border">
                        Verified
                      </th>
                      <th class="py-0 px-1 border">
                        Role
                      </th>
                      <th class="py-0 px-1 border">
                        Actions
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                      @foreach($users as $singleUser)
                        <tr>
                          <td class="p-0">
                            {{ $singleUser->created_at }}
                          </td>

                          <td class="p-0">
                            {{ $singleUser->username }}
                          </td>

                          <td class="text-center p-0">
                            {{ $singleUser->email }}
                          </td>

                          <td class="p-0">
                              {{ $singleUser->email_verified_at ?? 'N/A' }}
                          </td>

                          <td class="p-0">
                            {{ $singleUser->role->name }}
                          </td>

                          <td class="p-0">
                            <button type="button" name="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteLocationForm{{ $singleUser->system_user_id }}">
                              DELETE USER
                            </button>
                            <!-- Modal -->
                            <div class="modal fade" id="deleteLocationForm{{ $singleUser->system_user_id }}" tabindex="-1" role="dialog" aria-labelledby="editLocationForm{{ $singleUser->system_user_id }}" aria-hidden="true">
                              <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Delete User: {{ $singleUser->username }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                      <i class="tim-icons icon-simple-remove"></i>
                                    </button>
                                  </div>
                                  <div class="modal-body">

                                    <form onsubmit="showSpinner(event)" method="POST" autocomplete="off" action="{{ route('users.destroy', $singleUser->id) }}">
                                      @csrf
                                      <input type="hidden" name="user_id" value="{{ $singleUser->system_user_id }}">

                                      <p class="text-center">
                                        Are you sure you want to delete this user?
                                      </p>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                                    <button type="submit" name="submit_btn" value="Delete Location" class="btn btn-danger">YES</button>
                                    </form>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <!-- end modal -->
                          </td>
                        </tr>
                      @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      </div>
      @include('universal-layout.scripts',
  [
  'libscripts' => true,
  'vendorscripts' => true,
  'mainscripts' => true,
  'datatable' => true,
   
  ]
  )
  @include('universal-layout.alert')
      @include('universal-layout.footer')
