@extends('layouts.dashboard_template')


@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{ $page_title ?? "Page Title" }}
        <small>{{ $page_description ?? '' }}</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">{{ $page_title }}</li>
    </ol>
</section>

<section class="content container-fluid">
    @include('partials.flash_message')

    <section class="content">
        <div class="row">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <a href="{{ route('setting.role.create') }}">
                        <button type="button" class="btn btn-primary btn-sm" title="Tambah Data"><i class="fa fa-plus"></i> Tambah</button>
                    </a>
                </div>
                <div class="box-body">
                    @include( 'flash::message' )
                    <table class="table table-striped table-bordered" id="user-table">
                        <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Slug</th>
                            <th>Aksi</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>

</section>
<!-- /.content -->
@endsection
@include('partials.asset_datatables')
@push('scripts')
<script type="text/javascript">
    $(document).ready(function () {
        var data = $('#user-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{!! route( 'setting.role.getdata' ) !!}",
            columns: [
                {data: 'name', name: 'name'},
                {data: 'slug', name: 'slug'},
                {data: 'aksi', name: 'aksi', class: 'text-center', searchable: false, orderable: false}
            ]
        });
    });
</script>
@include('forms.datatable-vertical')
@include('forms.delete-modal')
@endpush
