<?php

use App\Models\StockMovement;
use App\Models\Item;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $paginate = 10;
    public $item_id = null;

    public function with(): array
    {
        $query = StockMovement::with('item')->orderBy('created_at', 'desc');
        if ($this->item_id) {
            $query->where('item_id', $this->item_id);
        }

        return [
            'movements' => $query->paginate($this->paginate),
            'items' => Item::orderBy('nama')->get(),
        ];
    }
};
?>

<div>
  <div class="card card-round">
    <div class="card-header">
      <div class="card-head-row">
        <div class="card-title">Pergerakan Stok</div>
      </div>
    </div>
    <div class="card-body">
      <div class="row mb-3">
        <div class="col-md-4">
          <select class="form-select" wire:model="item_id">
            <option value="">-- Semua Item --</option>
            @foreach($items as $it)
              <option value="{{ $it->id }}">{{ $it->kode }} - {{ $it->nama }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="table table-responsive">
        <table class="table table-hover table-borderless">
          <thead>
            <tr>
              <th>#</th>
              <th>Waktu</th>
              <th>Item</th>
              <th>Tipe</th>
              <th>Qty</th>
              <th>User</th>
              <th>Note</th>
            </tr>
          </thead>
          <tbody>
            @php $no = ($movements->currentPage() - 1) * $movements->perPage() + 1; @endphp
            @foreach($movements as $m)
            <tr>
              <td>{{ $no++ }}</td>
              <td>{{ $m->created_at->format('Y-m-d H:i') }}</td>
              <td>{{ $m->item->kode ?? '-' }} - {{ $m->item->nama ?? '-' }}</td>
              <td>{{ strtoupper($m->movement_type) }}</td>
              <td>{{ $m->qty }}</td>
              <td>{{ $m->user?->name ?? '-' }}</td>
              <td>{{ $m->note }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="d-flex justify-content-center mt-4">{{ $movements->links() }}</div>
    </div>
  </div>
</div>
