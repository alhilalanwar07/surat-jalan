<?php

use App\Models\Customer;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $paginate = 10;
    public $search = '';

    public function with(): array
    {
        $query = Customer::query();
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('alamat', 'like', '%' . $this->search . '%')
                    ->orWhere('no_telepon', 'like', '%' . $this->search . '%');
            });
        }

        return [
            'customers' => $query->orderBy('nama')->paginate($this->paginate)
        ];
    }

    // create / edit customer
    public $name, $alamat, $no_telepon, $editingId;

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'no_telepon' => 'nullable|string|max:25',
        ];
    }

    public function store()
    {
        $this->validate($this->rules());

        try {
            if ($this->editingId) {
                $c = Customer::findOrFail($this->editingId);
                $c->update([
                    'nama' => $this->name,
                    'alamat' => $this->alamat,
                    'no_telepon' => $this->no_telepon,
                ]);
                $this->dispatch('updateAlertToast', 'Customer updated');
            } else {
                Customer::create([
                    'nama' => $this->name,
                    'alamat' => $this->alamat,
                    'no_telepon' => $this->no_telepon,
                ]);
                $this->dispatch('updateAlertToast', 'Customer created');
            }

            $this->resetForm();
            $this->resetPage();
            // after saving, reset editing mode back to add
            $this->editingId = null;
        } catch (\Exception $e) {
            $this->dispatch('errorAlertToast', $e->getMessage());
        }
    }

    public function openEdit($id)
    {
        $c = Customer::findOrFail($id);
        $this->editingId = $c->id;
        $this->name = $c->nama;
        $this->alamat = $c->alamat;
        $this->no_telepon = $c->no_telepon;
    }

    public function openCreate()
    {
        $this->editingId = null;
        $this->resetForm();
    }

    // keep resetForm to reset inputs and editing mode

    public function delete($id)
    {
        $c = Customer::find($id);
        if ($c) {
            $c->delete();
            $this->dispatch('deleteAlertToast', 'Customer deleted');
            $this->resetPage();
        } else {
            $this->dispatch('errorAlertToast', 'Customer not found');
        }
    }

    public function resetForm()
    {
        $this->name = null;
        $this->alamat = null;
        $this->no_telepon = null;
        $this->editingId = null;
    }
};
?>

<div>

    <div class="row">
        <!-- Card: Form (Tambah / Ubah) -->
        <div class="col-md-12 mb-3">
            <div class="card card-round">
                <div class="card-header">
                    <div class="card-head-row">
                        <div class="card-title">@if($editingId) Ubah Customer @else Tambah Customer @endif</div>
                    </div>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="store()">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <input type="text" class="form-control @error('name') is-invalid @enderror" placeholder="Nama" wire:model="name">
                                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4 mb-2">
                                <input type="text" class="form-control @error('no_telepon') is-invalid @enderror" placeholder="No Telepon" wire:model="no_telepon">
                                @error('no_telepon') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4 mb-2">
                                <input type="text" class="form-control @error('alamat') is-invalid @enderror" placeholder="Alamat" wire:model="alamat">
                                @error('alamat') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                            <button type="button" wire:click="resetForm" class="btn btn-secondary btn-sm">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Card: Table / List -->
        <div class="col-md-12">
            <div class="card card-round">
                <div class="card-header">
                    <div class="card-head-row">
                        <div class="card-title">Daftar Customers</div>
                        <div class="card-tools">
                            {{-- Optionally add controls here in the future --}}
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3 justify-content-between">
                        <div class="col-md-6">
                            <input type="text" class="form-control" placeholder="Cari nama, alamat atau telepon..." wire:model.live="search">
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" wire:model.live="paginate">
                                <option value="5">5 </option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                        </div>
                    </div>

                    <div class="table table-responsive">
                        <table class="table table-hover table-borderless">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>Alamat</th>
                                    <th>No Telepon</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = ($customers->currentPage() - 1) * $customers->perPage() + 1; @endphp
                                @foreach($customers as $customer)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $customer->nama }}</td>
                                    <td>{{ $customer->alamat }}</td>
                                    <td>{{ $customer->no_telepon }}</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-primary m-1" wire:click="openEdit({{ $customer->id }})">Edit</a>
                                        <button type="button" class="btn btn-sm btn-danger m-1" wire:click="delete({{ $customer->id }})">Delete</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $customers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <livewire:_alert />

    <!-- Toast container -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080">
        <div id="liveToastContainer"></div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function showToast(message, type = 'info') {
            var container = document.getElementById('liveToastContainer');
            if (!container) return;
            var toastId = 'toast-' + Date.now();
            var toastEl = document.createElement('div');
            toastEl.className = 'toast align-items-center text-bg-' + (type === 'error' ? 'danger' : (type === 'success' ? 'success' : 'secondary')) + ' border-0 mb-2';
            toastEl.setAttribute('role', 'alert');
            toastEl.setAttribute('aria-live', 'assertive');
            toastEl.setAttribute('aria-atomic', 'true');
            toastEl.id = toastId;
            toastEl.innerHTML = `
      <div class="d-flex">
        <div class="toast-body">${message}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    `;
            container.appendChild(toastEl);
            var bsToast = new bootstrap.Toast(toastEl, {
                delay: 4000
            });
            bsToast.show();
            toastEl.addEventListener('hidden.bs.toast', function() {
                toastEl.remove();
            });
        }

        if (window.Livewire) {
            Livewire.on('updateAlertToast', function(message) {
                if (message) showToast(message, 'success');
            });
            Livewire.on('deleteAlertToast', function(message) {
                if (message) showToast(message, 'success');
            });
            Livewire.on('errorAlertToast', function(message) {
                if (message) showToast(message, 'error');
            });
        }
    });
</script>