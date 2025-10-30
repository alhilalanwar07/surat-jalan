<?php

use App\Models\GoodsInward;
use App\Models\GoodsInwardItem;
use App\Models\Item;
use App\Services\StockService;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\DB;

new class extends Component {

  public $nomor;
  public $tanggal;
  public $note;
    public $rows = [];
    public $attachments = [];

    public function mount()
    {
        $this->rows = [ ['item_id' => null, 'jumlah' => 1, 'harga' => null, 'note' => null] ];
        $this->tanggal = now()->format('Y-m-d');
    }

    public function addRow()
    {
        $this->rows[] = ['item_id' => null, 'jumlah' => 1, 'harga' => null, 'note' => null];
    }

    public function removeRow($index)
    {
        array_splice($this->rows, $index, 1);
        if (empty($this->rows)) $this->addRow();
    }

    public function rules()
    {
        return [
            'tanggal' => 'required|date',
            'rows.*.item_id' => 'required|exists:items,id',
            'rows.*.jumlah' => 'required|integer|min:1',
        ];
    }

    public function store()
    {
        $this->validate($this->rules());

        try {
            DB::transaction(function() {
        $gi = GoodsInward::create([
          'nomor' => $this->nomor,
          'tanggal' => $this->tanggal,
          'note' => $this->note,
        ]);

                // handle attachments
                $paths = [];
                if (!empty($this->attachments)) {
                    foreach ($this->attachments as $file) {
                        $paths[] = $file->store('goods_inwards', 'public');
                    }
                    $gi->attachments = $paths;
                    $gi->save();
                }

                $stockService = new StockService();
                foreach ($this->rows as $r) {
                    $item = GoodsInwardItem::create([
                        'goods_inward_id' => $gi->id,
                        'item_id' => $r['item_id'],
                        'jumlah' => $r['jumlah'],
                        'harga' => $r['harga'] ?? null,
                        'note' => $r['note'] ?? null,
                    ]);

                    // increase stock and record movement with reference
                    $stockService->increase($r['item_id'], (int)$r['jumlah'], 'goods_inwards', $gi->id, 'Penerimaan barang');
                }
            });

            $this->dispatch('updateAlertToast', 'Goods Inward created and stock updated');
            $this->resetForm();
        } catch (\Exception $e) {
            $this->dispatch('errorAlertToast', $e->getMessage());
        }
    }

    public function resetForm()
    {
  $this->nomor = null;
  $this->tanggal = now()->format('Y-m-d');
        $this->note = null;
        $this->rows = [ ['item_id' => null, 'jumlah' => 1, 'harga' => null, 'note' => null] ];
        $this->attachments = [];
    }

    public function with(): array
    {
        return [
            'items' => Item::orderBy('nama')->get(),
        ];
    }
};
?>

<div class="col-md-12">
  <div class="card card-round">
    <div class="card-header">
      <div class="card-head-row">
        <div class="card-title">Penerimaan Barang (Goods Inwards)</div>
      </div>
    </div>
    <div class="card-body">
      <form wire:submit.prevent="store" enctype="multipart/form-data">
        @csrf
        <div class="row mb-2">
          <div class="col-md-3">
            <input type="text" class="form-control" placeholder="Nomor (opsional)" wire:model="nomor">
          </div>
          <div class="col-md-3">
            <input type="date" class="form-control" wire:model="tanggal">
          </div>
          
        </div>

        <div class="mb-3">
          <table class="table">
            <thead>
              <tr>
                <th>Item</th>
                <th>Jumlah</th>
                <th>Harga</th>
                <th>Note</th>
                <th></th>
              </tr>
            </thead>
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
                <td><input type="number" step="0.01" class="form-control" wire:model="rows.{{ $i }}.harga"></td>
                <td><input type="text" class="form-control" wire:model="rows.{{ $i }}.note"></td>
                <td><button type="button" class="btn btn-sm btn-danger" wire:click.prevent="removeRow({{ $i }})">Hapus</button></td>
              </tr>
              @endforeach
            </tbody>
          </table>
          <button type="button" class="btn btn-sm btn-secondary mb-3" wire:click.prevent="addRow">Tambah Baris</button>
        </div>

        <div class="mb-3">
          <label class="form-label">Lampiran</label>
          <input type="file" wire:model="attachments" multiple class="form-control">
        </div>

        <div class="mb-3">
          <textarea class="form-control" rows="3" placeholder="Catatan" wire:model="note"></textarea>
        </div>

        <div>
          <button type="submit" class="btn btn-primary">Simpan Penerimaan</button>
          <button type="button" class="btn btn-secondary" wire:click.prevent="resetForm">Batal</button>
        </div>
      </form>
    </div>
  </div>
</div>
