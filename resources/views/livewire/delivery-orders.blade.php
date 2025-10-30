<?php

use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderItem;
use App\Models\Item;
use App\Models\Purpose;
use App\Services\StockService;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\DB;

new class extends Component {

    public $nomor_sj;
    public $tanggal;
    public $purpose_id;
    public $nomor_po;
    public $nama_sopir;
    public $nomor_kendaraan;
    public $rows = [];

    public function mount()
    {
        $this->tanggal = now()->format('Y-m-d');
        $this->rows = [ ['item_id' => null, 'jumlah' => 1, 'keterangan' => null] ];
        $this->nomor_sj = $this->generateNomor();
    }

    private function generateNomor(): string
    {
        $last = DeliveryOrder::orderBy('id', 'desc')->first();
        $next = $last ? $last->id + 1 : 1;
        return 'SJ-' . now()->format('Ymd') . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public function with(): array
    {
        return [
            'items' => Item::orderBy('nama')->get(),
            'purposes' => Purpose::orderBy('nama')->get(),
            'deliveryOrders' => DeliveryOrder::withCount('items')->orderBy('tanggal', 'desc')->get(),
        ];
    }

    public function addRow()
    {
        $this->rows[] = ['item_id' => null, 'jumlah' => 1, 'keterangan' => null];
    }

    public function removeRow($i)
    {
        array_splice($this->rows, $i, 1);
        if (empty($this->rows)) $this->addRow();
    }

    public function rules()
    {
        return [
            'nomor_sj' => 'required|string|unique:delivery_orders,nomor_sj',
            'tanggal' => 'required|date',
            'purpose_id' => 'required|exists:purposes,id',
            'rows.*.item_id' => 'required|exists:items,id',
            'rows.*.jumlah' => 'required|integer|min:1',
        ];
    }

    public function store()
    {
        $this->validate($this->rules());

        DB::transaction(function() {
            $do = DeliveryOrder::create([
                'nomor_sj' => $this->nomor_sj,
                'tanggal' => $this->tanggal,
                'purpose_id' => $this->purpose_id,
                'nomor_po' => $this->nomor_po,
                'nama_sopir' => $this->nama_sopir,
                'nomor_kendaraan' => $this->nomor_kendaraan,
            ]);

            foreach ($this->rows as $r) {
                DeliveryOrderItem::create([
                    'delivery_order_id' => $do->id,
                    'item_id' => $r['item_id'],
                    'jumlah' => $r['jumlah'],
                    'keterangan' => $r['keterangan'] ?? null,
                ]);
            }
        });

        $this->dispatch('updateAlertToast', 'Delivery Order created (still draft). Confirm to reduce stock.');
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->nomor_sj = $this->generateNomor();
        $this->tanggal = now()->format('Y-m-d');
        $this->purpose_id = null;
        $this->nomor_po = null;
        $this->nama_sopir = null;
        $this->nomor_kendaraan = null;
        $this->rows = [ ['item_id' => null, 'jumlah' => 1, 'keterangan' => null] ];
    }

    public function confirm($id)
    {
        $do = DeliveryOrder::with('items')->findOrFail($id);
        if ($do->confirmed_at) {
            $this->dispatch('errorAlertToast', 'Already confirmed');
            return;
        }

        $stock = new StockService();

        try {
            DB::transaction(function() use ($do, $stock) {
                foreach ($do->items as $line) {
                    // decrease stock; StockService throws on insufficient stock
                    $stock->decrease($line->item_id, (int)$line->jumlah, 'delivery_orders', $do->id, 'Konfirmasi SJ');
                }

                $do->confirmed_at = now();
                $do->save();
            });

            $this->dispatch('updateAlertToast', 'Delivery Order confirmed and stock decreased');
        } catch (\Exception $e) {
            $this->dispatch('errorAlertToast', 'Confirmation failed: ' . $e->getMessage());
        }
    }
};

?>

<div class="col-md-12">
  <div class="card card-round">
    <div class="card-header">
      <div class="card-head-row">
        <div class="card-title">Delivery Orders (Surat Jalan)</div>
      </div>
    </div>
    <div class="card-body">
      <form wire:submit.prevent="store">
        @csrf
        <div class="row mb-2">
          <div class="col-md-3"><input type="text" class="form-control" wire:model="nomor_sj" readonly></div>
          <div class="col-md-2"><input type="date" class="form-control" wire:model="tanggal"></div>
          <div class="col-md-4">
            <select class="form-select" wire:model="purpose_id">
              <option value="">-- Pilih Tujuan --</option>
              @foreach($purposes as $p)
                <option value="{{ $p->id }}">{{ $p->nama }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="mb-3">
          <table class="table">
            <thead><tr><th>Item</th><th>Jumlah</th><th>Keterangan</th><th></th></tr></thead>
            <tbody>
              @foreach($rows as $i => $r)
              <tr>
                <td>
                  <select class="form-select" wire:model="rows.{{ $i }}.item_id">
                    <option value="">-- pilih item --</option>
                    @foreach($items as $it)
                      <option value="{{ $it->id }}">{{ $it->kode }} - {{ $it->nama }}</option>
                    @endforeach
                  </select>
                </td>
                <td><input type="number" class="form-control" wire:model="rows.{{ $i }}.jumlah"></td>
                <td><input type="text" class="form-control" wire:model="rows.{{ $i }}.keterangan"></td>
                <td><button type="button" class="btn btn-sm btn-danger" wire:click.prevent="removeRow({{ $i }})">Hapus</button></td>
              </tr>
              @endforeach
            </tbody>
          </table>
          <button type="button" class="btn btn-sm btn-secondary" wire:click.prevent="addRow">Tambah Baris</button>
        </div>

        <div>
          <button type="submit" class="btn btn-primary">Simpan DO (Draft)</button>
        </div>
      </form>

      <hr>
      <h6>Daftar Delivery Orders</h6>
      <div class="table-responsive">
        <table class="table">
          <thead><tr><th>#</th><th>Nomor SJ</th><th>Tanggal</th><th>Tujuan</th><th>Items</th><th>Confirmed</th><th>Aksi</th></tr></thead>
          <tbody>
            @foreach($deliveryOrders as $do)
            <tr>
              <td>{{ $loop->iteration }}</td>
              <td>{{ $do->nomor_sj }}</td>
              <td>{{ $do->tanggal->format('Y-m-d') }}</td>
              <td>{{ optional($do->purpose)->nama }}</td>
              <td>{{ $do->items_count }}</td>
              <td>{{ $do->confirmed_at ? $do->confirmed_at->format('Y-m-d H:i') : '-' }}</td>
              <td>
                @if(!$do->confirmed_at)
                  <button class="btn btn-sm btn-success" wire:click="confirm({{ $do->id }})">Confirm</button>
                @else
                  <span class="text-muted">--</span>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
