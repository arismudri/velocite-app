<!-- Menghubungkan dengan view template master -->
@extends('master')

<!-- isi bagian judul halaman -->
<!-- cara penulisan isi section yang pendek -->
@section('judul_halaman', 'Halaman Tentang')


<!-- isi bagian konten -->
<!-- cara penulisan isi section yang panjang -->
@section('konten')
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Transaksi</h6>
        </div>
        <div class="card-body">
            <button type="button" class="btn btn-primary mb-4" data-toggle="modal" data-target="#exampleModal">
                Transaksi Baru
            </button>
            <div class="table-responsive">
                <table class="table table-bordered" id="d-tbl" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nominal Transaksi</th>
                            <th>Tanggal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>Nominal Transaksi</th>
                            <th>Tanggal</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        @foreach ($trasactions as $row)
                            <tr>
                                <td>{{ $row->total_harga }}</td>
                                <td>{{ $row->created_at }}</td>
                                <td>
                                    <a href="#" onclick="getDetail('{{ $row->id }}')"
                                        class="btn btn-info btn-circle btn-sm" data-toggle="modal"
                                        data-target="#modalDetail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" id="modal-add" role="document">
            <div class="modal-content">
                <form id="transaction-form">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Buat Transaksi Baru</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="dynamic-form">
                            <button id="add-item" type="button" class="btn btn-md btn-primary">Tambah Barang</button>
                            <div id="item-wrapper" class="mt-3"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Checkout</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-labelledby="modalDetailLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetailLabel">Detail Transaksi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="content-modal-detail">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Checkout</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let countListProduct = 0;
        let listProduct = {!! json_encode($products) !!};
        const FormAdd = $('#transaction-form');

        function removeItem(id) {
            $(`#${id}`).remove();
        }

        function getDetail(id) {
            $.ajax({
                method: "GET",
                url: "{{ url('/api/transaction') }}" + "/" + id,
                processData: false,
                contentType: false,
                success: function(response) {
                    response.data.details.forEach(e => {
                        $("#content-modal-detail").append(`
                        <div class="row mt-3">
                            <div class="col">Harga</div>
                            <div class="col">${e.harga_satuan}</div>
                        </div>
                        <div class="row">
                            <div class="col">Jumlah</div>
                            <div class="col">${e.jumlah}</div>
                        </div>
                        `);
                    })
                    console.log({
                        response
                    });
                },
            });
        }

        $(document).ready(function() {

            $("#transaction-form").submit(function(e) {

                let csrf = $("meta[name=csrf-token]").attr("content");

                let formData = new FormData(FormAdd[0]);

                $.ajax({
                    method: "POST",
                    url: "{{ url('/api/transaction') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $("#modal-add").modal('hide');
                        FormAdd[0].reset();
                    },
                });

                return false;
            });

            $('#add-item').click(function() {


                selectProducts = $(`
                        <select name="data[${countListProduct}][master_barang_id]" class="form-control" required id="select-product-${countListProduct}">
                            <option>Pilih Barang</option>
                        </select>
                `);

                listProduct.forEach(product => {
                    selectProducts.append(`
                        <option value="${product.id}">${product.nama_barang}</option>
                    `);
                });

                $('#item-wrapper').append(`
                <div id="item-${countListProduct}" class="row m-2">
                    <div class="col-4" id="col-select-product-${countListProduct}">
                    </div>
                    <div class="col-3">
                        <input name="data[${countListProduct}][jumlah]" type="number" min="1" required class="form-control" placeholder="Jumlah">
                    </div>
                    <div class="col-3">
                        <input type="text" readonly class="form-control disabled" placeholder="Rp. 0">
                    </div>
                    <div class="col">
                        <a href="#" class="btn btn-danger btn-circle btn-sm" onClick="removeItem('item-${countListProduct}')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>                
                </div>
                `);
                selectProducts.appendTo($(`#col-select-product-${countListProduct}`));
                // .append(selectProducts);
                countListProduct++;

            })
        });
    </script>
@endsection
