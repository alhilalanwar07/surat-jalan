<x-admin-layout>
    @section('title', 'Data Item')
    <div class="page-inner">
        <div class="page-header">
            <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                    <a href="/">
                        <i class="icon-home"></i>
                    </a>
                </li>
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Data Item</a>
                </li>
            </ul>
        </div>
        <div class="row mb-3">
            <div class="col-md-12 d-flex justify-content-end">
                <a href="{{ route('items.export') }}" class="btn btn-sm btn-outline-primary me-2">Export CSV</a>
                <form action="{{ route('items.import') }}" method="post" enctype="multipart/form-data" class="d-inline-block">
                    @csrf
                    <input type="file" name="file" accept=".csv" class="form-control form-control-sm d-inline-block" style="width:auto; display:inline-block;">
                    <button type="submit" class="btn btn-sm btn-outline-success">Import</button>
                </form>
            </div>
        </div>
        <div class="row">
            <livewire:items />
        </div>
    </div>
</x-admin-layout>
