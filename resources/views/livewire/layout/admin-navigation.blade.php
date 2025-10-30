<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {

    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div>
    <div class="sidebar" data-background-color="white">
        <div class="sidebar-logo">
            <!-- Logo Header -->
            <div class="logo-header" data-background-color="dark">
                <a href="#" class="logo text-white">
                    MyApp
                </a>
                <div class="nav-toggle">
                    <button class="btn btn-toggle toggle-sidebar">
                        <i class="gg-menu-right"></i>
                    </button>
                    <button class="btn btn-toggle sidenav-toggler">
                        <i class="gg-menu-left"></i>
                    </button>
                </div>
                <button class="topbar-toggler more">
                    <i class="gg-more-vertical-alt"></i>
                </button>
            </div>
            <!-- End Logo Header -->
        </div>
        <div class="sidebar-wrapper scrollbar scrollbar-inner">
            <div class="sidebar-content">
                <ul class="nav nav-secondary">
                    <li class="nav-item {{ Route::is('home') ? 'active text-info' : '' }}">
                        <a class="nav-link" href="{{ route('home') }}" >
                            <i class="fas fa-home"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-section">
                        <span class="sidebar-mini-icon">
                            <i class="fa fa-ellipsis-h"></i>
                        </span>
                        <h4 class="text-section">Masters</h4>
                    </li>
                    <li class="nav-item {{ Route::is('purposes.index') ? 'active text-info' : '' }}">
                        <a class="nav-link" href="{{ route('purposes.index') }}" >
                            <i class="fas fa-address-book"></i>
                            <p>Manajemen Tujuan</p>
                        </a>
                    </li>
                    <li class="nav-item {{ Route::is('items.index') ? 'active text-info' : '' }}">
                        <a class="nav-link" href="{{ route('items.index') }}" >
                            <i class="fas fa-boxes"></i>
                            <p>Manajemen Items</p>
                        </a>
                    </li>
                    <li class="nav-section">
                        <span class="sidebar-mini-icon">
                            <i class="fa fa-ellipsis-h"></i>
                        </span>
                        <h4 class="text-section">Proses</h4>
                    </li>
                    <li class="nav-item {{ Route::is('stock.movements.index') ? 'active text-info' : '' }}">
                        <a class="nav-link" href="{{ route('stock.movements.index') }}" >
                            <i class="fas fa-exchange-alt"></i>
                            <p>Pergerakan Stok</p>
                        </a>
                    </li>
                    <li class="nav-item {{ Route::is('goods.inwards.index') ? 'active text-info' : '' }}">
                        <a class="nav-link" href="{{ route('goods.inwards.index') }}" >
                            <i class="fas fa-truck-loading"></i>
                            <p>Barang Masuk</p>
                        </a>
                    </li>
                    <li class="nav-item {{ Route::is('delivery.orders.index') ? 'active text-info' : '' }}">
                        <a class="nav-link" href="{{ route('delivery.orders.index') }}" >
                            <i class="fas fa-truck"></i>
                            <p>Surat Jalan</p>
                        </a>
                    </li>
                    <li class="nav-section">
                        <span class="sidebar-mini-icon">
                            <i class="fa fa-ellipsis-h"></i>
                        </span>
                        <h4 class="text-section">Pengaturan</h4>
                    </li>
                    @if(auth()->user()->role == 'admin')
                    <li class="nav-item {{ Route::is('admin.manajemen-user') ? 'active text-info' : '' }}">
                        <a class="nav-link" href="{{ route('admin.manajemen-user') }}" >
                            <i class="fas fa-users"></i>
                            <p>Manajemen User</p>
                        </a>
                    </li>
                    @endif
                    <li class="nav-item {{ Route::is('profil') ? 'active text-info' : '' }}">
                        <a class="nav-link" href="{{ route('profil') }}" >
                            <i class="fas fa-user"></i>
                            <p>Profil</p>
                        </a>
                    </li>

                    <br>
                    <div class="px-4">
                        <li class="nav-item" style="padding: 0px !important;">
                            <a href="#" wire:click="logout" class=" text-center btn btn-sm btn-danger w-100 btn-block d-flex justify-content-center align-items-center" style="padding: 0px !important;">
                                <i class="fas fa-sign-out-alt fa-lg m-2 p-1"></i> &nbsp;
                                <p style="padding: 0px !important; margin: 5px !important">Keluar</p>
                            </a>
                        </li>
                    </div>
                </ul>
            </div>
        </div>
    </div>
</div>
