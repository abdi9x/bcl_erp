@extends('layouts.app')

@section('content')
<!-- Page-Title -->
<?php

use carbon\carbon;

$data = $data;
?>
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <div class="row">
                <div class="col">
                    <h4 class="page-title">Transaksi Sewa</h4>
                    <span>{{config('app.name')}}</span>
                </div><!--end col-->
                <div class="col-auto align-self-center">
                    <button class="btn btn-outline-dashed btn-success waves-effect waves-light" data-toggle="modal" data-target="#md_sewa" id="bt_sewa">
                        <i class="mdi mdi-check-all"></i> Sewa Kamar
                    </button>
                </div><!--end col-->
            </div><!--end row-->
        </div><!--end page-title-box-->
    </div><!--end col-->
</div><!--end row-->

@if($belum_lunas->count()>0)
<div class="row mb-2">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header bg-danger">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title text-white">Transaksi Belum Lunas</h4>
                    </div>

                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive-sm mb-3">
                    <table class="table table-sm mb-0 table-hover" data-page-length="50" id="tb_pending_tr">
                        <thead class="thead-secondary bg-light">
                            <tr>
                                <th class="" width="25px"><b>No</b></th>
                                <th class=""><b>Tanggal</b></th>
                                <th class=""><b>Nomor</b></th>
                                <th class=" "><b>Tipe</b></th>
                                <th class=" "><b>Catatan</b></th>
                                <th class="text-right"><b>Nominal</b></th>
                                <th class="text-right"><b>Kurang</b></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            foreach ($belum_lunas as $value) {
                            ?>
                                <tr>
                                    <td><?= $no ?></td>
                                    <td><?= $value->tanggal ?></td>
                                    <td><?= $value->doc_id ?></td>
                                    <td><?= $value->identity == 'Sewa Kamar' ? 'Pendapatan Sewa' : 'Pendapatan Lain' ?></td>
                                    <td><?= $value->catatan ?></td>
                                    <td class="text-right">Rp <?= number_format($value->harga, 2) ?></td>
                                    <td class="text-right text-danger font-weight-bold">Rp <?= number_format($value->kurang, 2) ?></td>
                                </tr>
                            <?php
                                $no++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <small class="text-danger">*Untuk melakukan pelunasan silahkan melalui menu Keuangan -> Pemasukan</small>
            </div>
        </div>
    </div>
</div>
@endif
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header bg-dark">
                <div class="row align-self-center">
                    <div class="col align-self-center">
                        <h4 class="card-title text-white">Daftar Transaksi</h4>
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
                    </div>
                    <div class="col-auto align-self-center">
                        <a href="javascript:void(0)" class="btn btn-sm btn-light" id="filter_faktur">
                            <span class="ay-name" id="Day_Name">Today:</span>&nbsp;
                            <span class="" id="Select_date">Jan 11</span>
                            <i data-feather="calendar" class="align-self-center icon-xs ml-1"></i>
                        </a>
                        <form id="f_filter_tgl" action="{{route('transaksi.index')}}" method="POST">
                            @csrf
                            <input type="hidden" name="filter" id="filter">
                        </form>
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
                                            <th class="text-center text-white">Tgl. Trans</th>
                                            <th class="text-white">Kode Trans.</th>
                                            <th class="text-white">No. Kamar</th>
                                            <th class="text-white">Penyewa</th>
                                            <th class="text-white">Jangka Sewa</th>
                                            <th class="text-white">Periode</th>
                                            <th class="text-white text-right">Harga Sewa</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        ?>
                                        @foreach($data as $data)
                                        <tr>
                                            <td class="text-center">{{ $no }}</td>
                                            <td class="text-center">{{ $data->tanggal }}</td>
                                            <td>{{$data->trans_id}}</td>
                                            <td>{{$data->room->room_name}}</td>
                                            <td>{{$data->renter->nama}}</td>
                                            <td>{{$data->lama_sewa.' '.$data->jangka_sewa}}</td>
                                            <td>{{$data->tgl_mulai.' s/d '.$data->tgl_selesai}}</td>
                                            <td class="text-right">{{$data->harga}} </td>
                                        </tr>
                                        <?php $no++; ?>
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
<div class="modal fade" id="md_sewa" tabindex="-1" role="dialog" aria-labelledby="exampleModalDefaultLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h6 class="modal-title m-0 text-white" id="exampleModalDefaultLabel">Sewa Kamar</h6>
                <button type="button" class="close " data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="la la-times"></i></span>
                </button>
            </div>
            <form action="{{route('rooms.sewa')}}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 col-sm-12">
                            <label class="">Penyewa</label>
                            <select class="mb-3 select2" name="renter" required style="width: 100%" data-placeholder="Pilih Penyewa">
                                <option value=""></option>
                                @foreach($renter as $rent)
                                <option value="{{$rent->id}}">{{$rent->nama}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <label class="">No/Nama Kamar</label>
                            <select class="mb-3 select2" id="kamar" name="kamar" required style="width: 100%" data-placeholder="Pilih Kamar">
                                <option value=""></option>
                                @foreach($rooms as $room)
                                <option value="{{$room->id}}" data-room_category="{{$room->room_category}}">{{$room->room_name.' '.$room->category_name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <label class="">Durasi Sewa</label>
                            <select class="mb-3 select2" id="pricelist" name="pricelist" required style="width: 100%" data-placeholder="Pilih Durasi">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4 col-sm-12">
                            <label class="">Tanggal Rencana Masuk</label>
                            <input type="text" id="tgl_masuk" required name="tgl_masuk" class="form-control datePicker">
                        </div>
                        <div class="col-md-8 col-sm-12">
                            <label class="">Catatan</label>
                            <input type="text" id="catatan" name="catatan" class="form-control">
                        </div>
                    </div>
                    <hr class="hr-dashed">
                    <div class="row mt-3">
                        <div class="col-md-4 col-sm-12">
                            <label class="">Tanggal Terima Pembayaran</label>
                            <input type="text" id="tgl_bayar" required name="tgl_bayar" class="form-control datePicker">
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <label class="">Nominal</label>
                            <input type="text" id="nominal" required name="nominal" class="form-control inputmask">
                            <small class="form-text text-muted">*Jika Pembayaran kurang dari harga, maka akan dianggap sebagai DP</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('pagescript')
<script>
    var t = moment('<?= $start ?>', 'YYYY-MM-DD'),
        a = moment('<?= $end ?>', 'YYYY-MM-DD');
    $(document).ready(function() {
        var table_bb = $("#tb_kamar").DataTable({
            order: [
                [1, 'DESC']
            ],
            // "paging": false,
            // "info": false,
            "language": {
                "emptyTable": "Tidak ada data untuk ditampilkan, silakan gunakan filter",
            },
            columnDefs: [{
                targets: 7,
                render: $.fn.dataTable.render.number(',', '.', 2, 'Rp ')
            }],
            rowGroup: {
                dataSrc: [
                    function(row) {
                        return '<i class="fas fa-chevron-down"></i> ' + row[3];
                    }
                ],
                endRender: function(rows, group) {
                    // var numGroups = Math.ceil(rows.count()); //Math.round(rows.count() / 3) + 1;
                    // return group + ' (' + numGroups + ' groups max of 3)';
                    var avg =
                        rows
                        .data()
                        .pluck(7)
                        .reduce((a, b) => a + b.replace(/[(Rp ,)]|(&nbsp;|<([^>]+)>)/g, '') * 1, 0);

                    return (
                        'Total <span class="highlight text-dark">' + $.number(avg, 2) + '</span>'
                    );
                }
            }
        });
        table_bb.on('order.dt search.dt', function() {
            let i = 1;

            table_bb.cells(null, 0, {
                search: 'applied',
                order: 'applied'
            }).every(function(cell) {
                this.data(i++);
            });
        }).draw();
        var buttonCommon = {
            exportOptions: {
                format: {
                    body: function(data, row, column, node) {
                        if (column == 0) {
                            return data;
                        } else {
                            return column == 7 ?
                                data.replace(/[(Rp ,)]|(&nbsp;|<([^>]+)>)/g, '') :
                                data.replace(/(&nbsp;|<([^>]+)>)/ig, "");
                        }
                    }
                }
            }
        };
        var buttons = new $.fn.dataTable.Buttons(table_bb, {
            buttons: [
                $.extend(true, {}, buttonCommon, {
                    extend: 'excelHtml5',
                    filename: function() {
                        return "Transaksi Sewa " + moment().format('YYYY-MM-DD');
                    },
                    title: function() {
                        var data = "{{config('app.name')}} \n Transaksi Sewa";
                        return data.replace(/<br>/g, String.fromCharCode(10));
                    },
                    messageTop: function() {
                        var data = '#Tgl Cetak: ' + moment().format('YYYY-MM-DD, HH:mm') + ' [{{Auth::user()->name}}]';
                        return data.replace(/<br>/g, String.fromCharCode(10));
                    },
                    pageSize: 'A4',
                }),
                $.extend(true, {}, buttonCommon, {
                    extend: 'pdfHtml5',
                    filename: function() {
                        return "Transaksi Sewa " + moment().format('YYYY-MM-DD');
                    },
                    title: "{{config('app.name')}} \n Transaksi Sewa",
                    messageTop: '#Tgl Cetak: ' + moment().format('YYYY-MM-DD, HH:mm') + ' [{{Auth::user()->name}}]',
                    pageSize: 'A4',
                }),
                $.extend(true, {}, buttonCommon, {
                    extend: 'print',
                    title: '<span class="text-center"><h3 class="m-0 p-0">Belova </h3><h4 class="m-0 p-0">Transaksi Sewa</h4></span>',
                    messageTop: '<b>#Tgl Cetak: ' + moment().format('YYYY-MM-DD, HH:mm') + ' [{{Auth::user()->name}}]</b><hr>',
                    pageSize: 'A4',
                })

            ]
        }).container().prependTo($('#button_export'));

        $('.btn_epdf').click(function() {
            $('.buttons-pdf').click();
        });
        $('.btn_exls').click(function() {
            $('.buttons-excel').click();
        });
        $('.btn_eprint').click(function() {
            $('.buttons-print').click();
        });
        table_bb.on('click', 'tbody tr:not(".dtrg-group")', (e) => {
            let classList = e.currentTarget.classList;

            if (classList.contains('selected')) {
                // classList.remove('selected');
            } else {
                table_bb.rows('.selected').nodes().each((row) => row.classList.remove('selected'));
                classList.add('selected');
            }
        });
    });
    $("#filter_faktur").daterangepicker({
        // minDate: periode,
        locale: {
            format: "YYYY-MM-DD",
            "separator": " s/d ",
            "customRangeLabel": "<i class='fas fa-filter'></i> Custom range",
            "firstDay": 1
        },
        autoApply: true,
        startDate: t,
        endDate: a,
        ranges: {
            "Minggu Lalu": [moment().subtract(1, 'weeks').startOf('week'), moment().subtract(1, 'weeks').endOf('week')],
            "Minggu ini": [moment().startOf('week'), moment().endOf('week')],
            "Bulan ini": [moment().startOf("month"), moment().endOf("month")],
            "Bulan lalu": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")],
            "Tahun ini": [moment().startOf('year'), moment().endOf("year")]
        }
    }, e), e(t, a, "");

    function e(t, a, e) {
        $("#filter").val(t.format("YYYY-MM-DD") + "s/d" + a.format("YYYY-MM-DD")).trigger('change');
        var n = "",
            s = "";
        a - t < 100 || "Hari ini" == e ? (n = "Hari ini:", s = t.format("YYYY-MM-DD")) : "Kemarin" == e ? (n = "Kemarin:", s = t.format("YYYY-MM-DD")) : s = t.format("YYYY-MM-DD") + " s/d " + a.format("YYYY-MM-DD"), $("#Select_date").html(s), $("#Day_Name").html(n)
    }
    $('#filter').on('change', function() {
        console.log($(this).val());
        $('#f_filter_tgl').submit();
    });
    $('#kamar').on('select2:select', function() {
        var id = $(this).find(':selected').data('room_category');
        $.ajax({
            url: "{{route('pricelist.get_pl_room', ':id')}}",
            type: "GET",
            data: {
                id: id
            },
            success: function(data) {
                // console.log(data);
                $('#pricelist').empty();
                $('#pricelist').append('<option value=""></option>');
                $.each(data, function(index, value) {
                    $('#pricelist').append('<option data-harga="' + value.price + '" value="' + value.id + '">' + value.jangka_waktu + ' ' + value.jangka_sewa + ' ' + $.number(value.price) + '</option>');
                });
            }
        });
    });
    $('#pricelist').on('select2:select', function() {
        var harga = $(this).find(':selected').data('harga');
        $('#nominal').inputmask({
            min: 0,
            max: parseInt(harga),
            autoUnmask: "true",
            unmaskAsNumber: "true",
            'removeMaskOnSubmit': true,
            alias: 'decimal',
            groupSeparator: ',',
        });
    });
</script>
@stop