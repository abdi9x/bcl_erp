@extends('layouts.app')

@section('content')
<!-- Page-Title -->
<?php
$roles = $role;
$permissions = $permission;
?>
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <div class="row">
                <div class="col">
                    <h4 class="page-title">Permission Manager</h4>
                    <span>{{config('app.name')}}</span>
                </div><!--end col-->
                <div class="col-auto align-self-center">
                    <!-- <button class="btn btn-sm btn-danger waves-effect waves-light" data-toggle="modal" data-target="#md_tambah">
                        <i class="mdi mdi-plus"></i> Tambah Permission
                    </button> -->
                </div><!--end col-->
            </div><!--end row-->
        </div><!--end page-title-box-->
    </div><!--end col-->
</div><!--end row-->

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-dark">
                <div class="row align-self-center">
                    <div class="col align-self-center">
                        <h4 class="card-title text-white">Role</h4>
                    </div>
                    <div class="col-auto align-self-center">
                        <a href="#" class="btn btn-sm btn-light waves-effect waves-light dropdown-toggle" data-toggle="dropdown">
                            <i class="far fa-file-alt"></i> Export <i class="las la-angle-down "></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-bottom side-color side-color-dark">
                            <a class="dropdown-item btn_exls" href="#">Excel</a>
                            <a class="dropdown-item btn_epdf" href="#">PDF</a>
                            <a class="dropdown-item btn_eprint" href="#">Print</a>
                        </div>
                        <button class="btn btn-sm btn-danger waves-effect waves-light" data-toggle="modal" data-target="#md_tambah">
                            <i class="mdi mdi-plus"></i> Tambah Role
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive-sm">
                    <div id="tb_penjualan_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-sm table-hover mb-0 dataTable no-footer" id="tb_kamar">
                                    <thead class="thead-info bg-info">
                                        <tr class="text-white">
                                            <th class="text-center text-white">No</th>
                                            <th class="text-white">Role Name</th>
                                            <th class="text-right"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        ?>
                                        @foreach($roles as $data)
                                        <tr>
                                            <td>{{$no}}</td>
                                            <td>{{$data->name}}</td>
                                            <td class="text-right">
                                                <a href="javascript:void(0)" data-id="{{$data->id}}" class="btn btn-xs btn-warning waves-effect waves-light edit">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <a href="{{route('roles.delete', $data->id)}}" onclick="deletes(event)" class="btn btn-xs btn-danger waves-effect waves-light">
                                                    <i class="mdi mdi-trash-can-outline"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php
                                        $no++;
                                        ?>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-5"></div>
                            <div class="col-sm-12 col-md-7"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-dark">
                <div class="row align-self-center">
                    <div class="col align-self-center">
                        <h4 class="card-title text-white">Permission</h4>
                    </div>
                    <div class="col-auto align-self-center">
                        <a href="#" class="btn btn-sm btn-light waves-effect waves-light dropdown-toggle" data-toggle="dropdown">
                            <i class="far fa-file-alt"></i> Export <i class="las la-angle-down "></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-bottom side-color side-color-dark">
                            <a class="dropdown-item btn_exls" href="#">Excel</a>
                            <a class="dropdown-item btn_epdf" href="#">PDF</a>
                            <a class="dropdown-item btn_eprint" href="#">Print</a>
                        </div>
                        <button class="btn btn-sm btn-danger waves-effect waves-light" data-toggle="modal" data-target="#md_tambah_permission">
                            <i class="mdi mdi-plus"></i> Tambah Permission
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive-sm">
                    <div id="tb_penjualan_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-sm table-hover mb-0 dataTable no-footer" id="tb_permission">
                                    <thead class="thead-info bg-info">
                                        <tr class="text-white">
                                            <th class="text-center text-white">No</th>
                                            <th class="text-white">Permission Name</th>
                                            <th class="text-right"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        ?>
                                        @foreach($permissions as $data)
                                        <tr>
                                            <td>{{$no}}</td>
                                            <td>{{$data->name}}</td>
                                            <td class="text-right">
                                                <a href="javascript:void(0)" data-id="{{$data->id}}" class="btn btn-xs btn-warning waves-effect waves-light edit">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <a href="{{route('permission.delete', $data->id)}}" onclick="deletes(event)" class="btn btn-xs btn-danger waves-effect waves-light">
                                                    <i class="mdi mdi-trash-can-outline"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php
                                        $no++;
                                        ?>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-5"></div>
                            <div class="col-sm-12 col-md-7"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade bd-example-modal-xl" id="md_tambah" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{route('roles.store')}}">
                @csrf
                <div class="modal-header bg-success">
                    <h6 class="modal-title m-0 text-white" id="myExtraLargeModalLabel">Add Role</h6>
                    <button type="button" class="close " data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="la la-times text-white"></i></span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12 col-lg-12">
                        <div class="form-group">
                            <label for="name">Nama</label>
                            <input type="text" name="name" class="form-control" placeholder="name">
                        </div>
                        @foreach($permissions as $data)
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="permissions[]" value="{{$data->id}}" id="{{$data->id}}" data-parsley-multiple="groups" data-parsley-mincheck="2">
                                <label class="custom-control-label" for="{{$data->id}}">{{$data->name}}</label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                </div>
            </form>
        </div>
        <!--end modal-content-->
    </div>
    <!--end modal-dialog-->
</div>
<div class="modal fade bd-example-modal-xl" id="md_tambah_permission" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{route('permission.store')}}">
                @csrf
                <div class="modal-header bg-success">
                    <h6 class="modal-title m-0 text-white" id="myExtraLargeModalLabel">Add Permission</h6>
                    <button type="button" class="close " data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="la la-times text-white"></i></span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12 col-lg-12">
                        <div class="form-group">
                            <label for="name">Nama</label>
                            <input type="text" name="name" class="form-control" placeholder="name">
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                </div>
            </form>
        </div>
        <!--end modal-content-->
    </div>
    <!--end modal-dialog-->
</div>
<div class="modal fade bd-example-modal-xl" id="md_edit" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" action="{{route('users.update')}}">
                @csrf
                <input type="hidden" name="id" id="id">
                <div class="modal-header bg-success">
                    <h6 class="modal-title m-0 text-white" id="myExtraLargeModalLabel">Edit User</h6>
                    <button type="button" class="close " data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="la la-times text-white"></i></span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12 col-lg-4">
                        <div class="form-group">
                            <label for="name">Nama</label>
                            <input type="text" name="name" class="form-control" id="name" placeholder="name">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" class="form-control" id="email" placeholder="email">
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" name="phone" class="form-control" id="phone" placeholder="phone">
                        </div>

                        <div class="form-group">
                            <label for="exampleInputPassword1">Password</label>
                            <input type="password" name="password" class="form-control" id="password" placeholder="Password">
                        </div>

                        <div class="form-group">
                            <label for="password-confirm">Confirm Password</label>
                            <input type="password" name="password" class="form-control" id="password2" placeholder="Password">
                        </div>
                        <select class="form-control select2" name="role">
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                </div>
            </form>
        </div>
        <!--end modal-content-->
    </div>
    <!--end modal-dialog-->
</div>

@endsection
@section('pagescript')
<script>
    $(document).ready(function() {
        $('#tb_kamar').DataTable({});
        $('#tb_permission').DataTable({});
    });

    function deletes(e) {
        e.preventDefault();
        var url = e.currentTarget.getAttribute('href');
        $.confirm({
            title: 'Hapus data ini?',
            content: 'Aksi ini tidak dapat diurungkan',
            buttons: {
                confirm: {
                    text: 'Ya',
                    btnClass: 'btn-red',
                    keys: ['enter'],
                    action: function() {
                        window.location.href = url;
                    },
                },
                cancel: {
                    text: 'Batal',
                    action: function() {}
                }
            }
        });
    };
    $('.edit').on('click', function() {
        var id = $(this).data('id');
        var address = "{{route('users.edit',':id')}}";
        $.get(address, {
                'id': id
            },
            function(data) {
                $('#id').val(data.id);
                $('#name').val(data.name);
                $('#email').val(data.email);
                $('#phone').val(data.phone);
                $('#password').val(data.password);
                $('#password2').val(data.password);
                $('#md_edit').modal('show');
            });
    })
</script>
@stop