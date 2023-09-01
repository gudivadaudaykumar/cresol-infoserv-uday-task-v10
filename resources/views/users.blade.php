<!DOCTYPE html>
<html>

<head>
    <title>Cresol Infoserv Task - Uday(V10)</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .error {
            color: red;
            float: right;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Users List</h1>
        <button type="button" class="btn btn-primary float-right" style="margin: 2rem;" data-toggle="modal" data-target="#createUserModal">Create</button>
        <table class="table table-bordered data-table">
            <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Hobbies</th>
                    <th width="100px">Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>

        <!-- Create User Modal -->
        <div class="modal" id="createUserModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Create User</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="createUser">
                            <div class="form-group">
                                <label for="firstName">First Name:</label>
                                <input class="form-control" placeholder="Enter first name" id="firstName" name="firstName">
                            </div>
                            <div class="form-group">
                                <label for="lastName">Last Name:</label>
                                <input class="form-control" placeholder="Enter last name" id="lastName" name="lastName">
                            </div>
                            <div class="form-group">
                                <label for="lastName">Hobbies:</label>
                                @foreach($hobbies as $hobby)
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" value="{{ $hobby['id'] ?? '' }}" name="hobbies[]">{{ $hobby['hobbie_name'] ?? '' }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <div class="col text-center">
                            <button type="button" class="btn btn-danger align-center" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit User Modal -->
        <div class="modal" id="editUserModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit User</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="editUser">
                            <input type="hidden" id="editUserId" name="editUserId">
                            <div class="form-group">
                                <label for="firstName">First Name:</label>
                                <input class="form-control" placeholder="Enter first name" id="editFirstName" name="editFirstName">
                            </div>
                            <div class="form-group">
                                <label for="lastName">Last Name:</label>
                                <input class="form-control" placeholder="Enter last name" id="editLastName" name="editLastName">
                            </div>
                            <div class="form-group">
                                <label for="lastName">Hobbies:</label>
                                @foreach($hobbies as $hobby)
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" value="{{ $hobby['id'] ?? '' }}" name="edithobbies[]">{{ $hobby['hobbie_name'] ?? '' }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <div class="col text-center">
                            <button type="button" class="btn btn-danger align-center" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

<script type="text/javascript">
    $(document).ready(function() {
        let table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('users.index') }}",
            columns: [{
                    data: 'first_name',
                    name: 'first_name'
                },
                {
                    data: 'last_name',
                    name: 'last_name'
                },
                {
                    data: 'hobbies',
                    name: 'hobbies'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ],
            initComplete: function() {
                this.api().columns().every(function() {
                    var column = this;
                    if (column.index() === 2) {
                        const input = $('<input type="text" placeholder="Search hobbies" class="form-control form-control-sm" />')
                            .appendTo($(column.footer()).empty())
                            .on('keyup change', function() {
                                const searchValue = $(this).val();
                                const encodedSearchValue = encodeURIComponent(searchValue);
                                column.search(encodedSearchValue, true, false, true).draw();
                            });
                    }
                });
            }
        });

        $('#createUser').validate({
            rules: {
                firstName: {
                    minlength: 2,
                    required: true
                },
                lastName: {
                    minlength: 1,
                    required: true
                },
                'hobbies[]': {
                    minlength: 1,
                    required: true,
                }
            },
            submitHandler: function(form) {
                event.preventDefault();
                const formData = $(form).serialize();
                const csrfToken = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: "{{ route('users.store') }}",
                    type: "POST",
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        $("#createUser").trigger('reset');
                        $("#createUserModal").modal('hide');
                        table.clear().draw();
                        Swal.fire(
                            'User created successfully!',
                            '',
                            'success'
                        );
                    },
                    error: function(xhr, status, error) {
                        alert(error);
                    }
                });

            }
        });

        $(document).on('click', '.edit-user', function() {
            $("#editUser").trigger('reset');
            const userId = $(this).data('user-id');
            $.ajax({
                url: "{{ url('user-info') }}/" + userId,
                type: "GET",
                success: function(data) {
                    $("#editUserId").val(data.id);
                    $("#editFirstName").val(data.first_name);
                    $("#editLastName").val(data.last_name);
                    $.each(data.hobbies, function(index, hobby) {
                        $('input[name="edithobbies[]"][value="' + hobby?.id + '"]').prop('checked', true);
                    });
                    $('#editUserModal').modal('show');
                }
            });
        });


        $(document).on('click', '.delete-user', function() {
            const userId = $(this).data('user-id');
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            Swal.fire({
                title: 'Delete User',
                text: 'Are you sure you want to delete this user?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('delete-user') }}/" + userId,
                        type: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        success: function(data) {
                            table.clear().draw();
                            Swal.fire(
                                'User Deleted successfully!',
                                '',
                                'success'
                            );
                        },
                        error: function(xhr, status, error) {
                            alert(error);
                        }
                    });
                }
            });
        });

        $('#editUser').validate({
            rules: {
                editFirstName: {
                    minlength: 2,
                    required: true
                },
                editLastName: {
                    minlength: 1,
                    required: true
                },
                'edithobbies[]': {
                    minlength: 1,
                    required: true,
                }
            },
            submitHandler: function(form) {
                event.preventDefault();
                const formData = $(form).serialize();
                const csrfToken = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: "{{ route('users.update') }}",
                    type: "POST",
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        $("#editUser").trigger('reset');
                        $("#editUserModal").modal('hide');
                        table.clear().draw();
                        Swal.fire(
                            'User details updated successfully!',
                            '',
                            'success'
                        );
                    },
                    error: function(xhr, status, error) {
                        alert(error);
                    }
                });
            }
        });
    });
</script>

</html>