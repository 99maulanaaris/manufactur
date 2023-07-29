@extends('layout.app')


@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Master Coa</h1>
        <div class="d-none d-sm-inline-block btn  btn-success shadow-sm" id="btnAdd"><i
                class="fas fa-plus fa-sm text-white-50"></i> Tambah</div>
    </div>

    
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="tableData" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>NO Akun</th>
                            <th>Nama Akun</th>
                            <th>Tipe Akun</th>
                            <th>Tipe Coa</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalInput" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form id="formInput" action="{{ route('master-coa.store') }}" method="post">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="parent_id">Parent Akun</label>
                                <select class="form-control select2" id="parent_id"></select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="no_account">No Akun</label>
                                <input class="form-control" id="no_account"/>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="account_name">Nama Akun</label>
                                <input type="text" class="form-control" name="account_name" id="account_name">
                                </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="account_type">Tipe Akun</label>
                                <input type="text" class="form-control" name="account_type" id="account_type">
                                </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="account_name">Tipe Coa</label>
                                <select name="type_coa" id="type_coa" class="form-control select2">
                                    <option selected disabled>Pilih Type</option>
                                    <option value="0">Debet</option>
                                    <option value="1">Kredit</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
          </div>
        </div>
    </div>      
   

@endsection

@section('script')
    <script>
        $(document).ready(function(){
            let button = $('#btnAdd');
            let modalInput = $('#modalInput');

            $('.select2').select2({
                theme: 'bootstrap'
            });

            let table = $('#tableData').DataTable({
                destroy: true,
                paging:false,
                ordering: true,
                bInfo: true,
                bLengthChange: false,
                serveside:true,
                language: {
                    search: "",
                    searchPlaceholder: "Search",
                    sLengthMenu: "_MENU_items",
                },
                ajax : {
                    url : '{{ route('master-coa.index') }}',
                    method : 'GET',
                    data : function(d){
                        d._token = '{{ csrf_token() }}'
                    }
                },
                columns : [{
                    data : 'DT_RowIndex'
                },{
                    data : function (data){
                        let ref = data.no_account || '-';
                        return ref;
                    }
                },{
                    data : function (data){
                        let ref = data.account_name || '-';
                        return ref;
                    }
                },{
                    data : function (data){
                        let ref = data.account_type || '-';
                        return ref;
                    }
                },{
                    data : function (data){
                        let ref = data.type_coa || '-';
                        if(ref.type_coa == 0){
                            return 'Debet';
                        }else{
                            return 'Kredit';
                        }
                    }
                },{
                    data : function (data){
                        let id = data.id;
                        let html = ` <div class="d-flex justify-content-beatween gap-5">
                                        <div class="btn btn-info mr-2" data-id=${id}>Edit</div>
                                        <div class="btn btn-danger" data-id=${id}>Blokir</div>
                                    </div>`
                        return html;
                    },
                    name : 'Action'
                }]
            });

            button.on('click',function(){
                modalInput.modal('show');
                $.ajax({
                    url : '{{ route('master-coa.accounts') }}',
                    method : 'GET'
                }).then(ress => {
                    let data = ress.data;
                    let select = $('#parent_id');
                    if(data.length > 0){
                        select.empty();
                        select.append('<option selected disabled>Pilih No Akun</option>')
                        data.map((item) => {
                            select.append(`<option ${item.id}> ${item.no_account} - ${item.account_name}</option>`)
                        })
                    }else{
                        select.empty();
                        select.append('<option selected disabled>Pilih No Akun</option>')
                    }
                })
            })

            $('#formInput').on('submit',function(e){
                e.preventDefault();
                let url = $(this).attr('action');
                $.ajax({
                    url : url,
                    method : 'POST',
                    data : {
                        _token :`{{ csrf_token() }}`,
                        parent_id : $('#parent_id').val(),
                        account_name : $('#account_name').val(),
                        account_type : $('#account_type').val(),
                        no_account : $('#no_account').val(),
                        type_coa : $('#type_coa').val()
                    }
                }).then(ress => {
                    if(ress.status == 200){
                        alertToast('success',ress.msg);
                        modalInput.modal('hide');
                        getAccount();
                    }else{
                        alertToast('error',ress.msg);
                    }
                })
            })

            modalInput.on('hide.bs.modal',function(e){
                $(this).find('#parent_id').val(''),
                $(this).find('#account_name').val(''),
                $(this).find('#account_type').val(''),
                $(this).find('#no_account').val(''),
                $(this).find('#type_coa option:first').prop('selected',true)
            })

            const getAccount = () => {
                $.ajax({
                    url : '{{ route('master-coa.accounts') }}',
                    method : 'GET'
                }).then(ress => {
                    let data = ress.data;
                    console.log(ress.data);
                    getTable(data);
                })
            }

            const getTable = async (data) => {
               table.rows().remove().draw();
               data.map((item,index) => {
                    let status = '';
                    
                    switch (item.type_coa) {
                        case 0:
                            status = 'Debit';
                            break;
                    
                        default:
                            status = 'Kredit';
                            break;
                    }

                    let rowAdd = table.row.add([
                        item.DT_RowIndex,
                        item.no_account,
                        item.account_name,
                        item.account_type,
                        status,
                        ` <div class="d-flex justify-content-beatween gap-5">
                            <div class="btn btn-info mr-2" data-id=${item.id}>Edit</div>
                            <div class="btn btn-danger" data-id=${item.id}>Blokir</div>
                        </div>`
                    ]).draw(true).node();
               })
            }

            const alertToast = (icon, text) => {
                const Toast = Swal.mixin({
                    toast:true,
                    position:'top-end',
                    showConfirmButton: false,
                                timer: 3000,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                })
                Toast.fire({
                        icon: icon,
                        text: text
                });
            }

        })
    </script>
@endsection