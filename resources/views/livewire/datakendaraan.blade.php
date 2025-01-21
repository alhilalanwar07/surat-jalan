<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;
    public $search = '';
    public $perpage = 10;

    public function setPerPage($perpage)
    {
        $this->perpage = $perpage;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function with(): array
    {
        return [
            'kendaraans' => \App\Models\Kendaraan::where('nomor_plat', 'like', '%' . $this->search . '%')->paginate($this->perpage)
        ];
    }
}; ?>

<div>
    <div class="col-md-12">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">Kendaraan</div>
                    <div class="card-tools">
                        <a href="#" class="btn btn-info  btn-sm me-2" data-bs-toggle="modal" data-bs-target="#addUser">
                            <span class="btn-label">
                                <i class="fa fa-plus"></i>
                            </span>
                            Tambah Kendaraan
                        </a>
                        {{-- <a href="#" class="btn btn-label-info btn-round btn-sm">
                            <span class="btn-label">
                                <i class="fa fa-print"></i>
                            </span>
                            Print
                        </a> --}}
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table table-responsive">
                    <table class="table table-hover table-borderless">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nomor Plat</th>
                                <th>Tahun Pembuatan</th>
                                <th>Nama Pemilik</th>
                                <th>Alamat Pemilik</th>
                                <th>No Telp Pemilik</th>
                                <th>Status KIR</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach($kendaraans as $kendaraan)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $kendaraan->nomor_plat }}</td>
                                <td>{{ $kendaraan->tahun_pembuatan }}</td>
                                <td>{{ $kendaraan->nama_pemilik }}</td>
                                <td>{{ $kendaraan->alamat_pemilik }}</td>
                                <td>{{ $kendaraan->no_telepon_pemilik }}</td>
                                <td> <span class="badge badge-{{ $kendaraan->status_kir == 'aktif' ? 'success' : 'danger' }}">{{ $kendaraan->status_kir }}</span> </td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-info">
                                        <i class="fa fa-edit fa-lg"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-danger">
                                        <i class="fa fa-trash fa-lg"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $kendaraans->links() }}
                </div>
            </div>
        </div>
    </div>
    <div wire:ignore class="modal fade" id="addUser" tabindex="-1" aria-labelledby="addUserLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserLabel">Tambah User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                <form wire:submit.prevent="store()">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Name" wire:model="name">
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" placeholder="Email" wire:model="email">
                        @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" placeholder="Password" wire:model="password">
                        @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select @error('role') is-invalid @enderror" id="role" wire:model="role">
                            <option selected>Pilih...</option>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
                        @error('role') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit"  class="btn btn-primary">Simpan</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    <livewire:_alert />
</div>