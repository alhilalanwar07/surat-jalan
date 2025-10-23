<?php

use App\Models\Item;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $paginate = 10;
    public $search = '';

    public function with(): array
    {
        $query = Item::query();
        if (!empty($this->search)) {
            $query->where('kode', 'like', '%' . $this->search . '%')
                ->orWhere('nama', 'like', '%' . $this->search . '%');
        }

        return [
            'items' => $query->orderBy('nama')->paginate($this->paginate)
        ];
    }

    public $kode;

    public $nama, $satuan, $stok, $editingId;

    public function mount()
    {
        $this->kode = $this->generateNewCode();
    }

    private function generateNewCode(): string
    {
        $lastItem = Item::orderBy('id', 'desc')->first();
        $nextId = $lastItem ? $lastItem->id + 1 : 1;
        return 'kd-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'satuan' => 'nullable|string|max:50',
            'stok' => 'nullable|integer|min:0',
        ];
    }

    public function store()
    {
        $this->validate($this->rules());

        if ($this->editingId) {
            $item = Item::findOrFail($this->editingId);

            $item->update([
                'nama' => $this->nama,
                'satuan' => $this->satuan,
                'stok' => $this->stok ?? 0,
            ]);
            $this->dispatch('updateAlertToast', 'Item updated');
        } else {
            $this->kode = $this->generateNewCode();

            $this->validateOnly('kode', [
                'kode' => 'required|string|max:100|unique:items,kode'
            ]);

            Item::create([
                'kode' => $this->kode,
                'nama' => $this->nama,
                'satuan' => $this->satuan,
                'stok' => $this->stok ?? 0,
            ]);
            $this->dispatch('updateAlertToast', 'Item created');
        }

        $this->resetForm();
        $this->resetPage();
    }

    public function openEdit($id)
    {
        $item = Item::findOrFail($id);
        $this->editingId = $item->id;
        $this->kode = $item->kode;
        $this->nama = $item->nama;
        $this->satuan = $item->satuan;
        $this->stok = $item->stok;
    }

    public function resetForm()
    {
        $this->nama = null;
        $this->satuan = null;
        $this->stok = null;
        $this->editingId = null;
        $this->kode = $this->generateNewCode();
    }

    public function delete($id)
    {
        $item = Item::find($id);
        if ($item) {
            $item->delete();
            $this->dispatch('deleteAlertToast', 'Item deleted');
            $this->resetPage();
        } else {
            $this->dispatch('errorAlertToast', 'Item not found');
        }
    }
};
?>

<div>
    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="card card-round">
                <div class="card-header">
                    <div class="card-head-row">
                        <div class="card-title">@if($editingId) Ubah Item @else Tambah Item @endif</div>
                    </div>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="store()">
                        @csrf
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <input type="text" class="form-control" placeholder="Kode" wire:model="kode" disabled>
                            </div>
                            <div class="col-md-4 mb-2">
                                <input type="text" class="form-control" placeholder="Nama" wire:model="nama">
                            </div>
                            <div class="col-md-2 mb-2">
                                <input type="text" class="form-control" placeholder="Satuan" wire:model="satuan">
                            </div>
                            <div class="col-md-2 mb-2">
                                <input type="number" class="form-control" placeholder="Stok awal" wire:model="stok">
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

        <div class="col-md-12">
            <div class="card card-round">
                <div class="card-header">
                    <div class="card-head-row">
                        <div class="card-title">Daftar Items</div>
                        <div class="card-tools"></div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3 justify-content-between">
                        <div class="col-md-6">
                            <input type="text" class="form-control" placeholder="Cari kode atau nama..." wire:model.debounce.500ms="search">
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" wire:model="paginate">
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
                                    <th>Kode</th>
                                    <th>Nama</th>
                                    <th>Satuan</th>
                                    <th>Stok</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = ($items->currentPage() - 1) * $items->perPage() + 1; @endphp
                                @foreach($items as $item)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $item->kode }}</td>
                                    <td>{{ $item->nama }}</td>
                                    <td>{{ $item->satuan }}</td>
                                    <td>{{ $item->stok }}</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-primary m-1" wire:click="openEdit({{ $item->id }})">Edit</a>
                                        <button type="button" class="btn btn-sm btn-danger m-1" wire:click="delete({{ $item->id }})">Delete</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $items->links() }}
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