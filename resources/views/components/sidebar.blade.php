@php
use App\Helpers\SidebarHelper;
use App\Helpers\RoleHelper;
$currentRoute = request()->route()->getName();
$activeGroup = SidebarHelper::getActiveGroup($currentRoute);
@endphp

<style>
    /* Custom sidebar styles for blue background with white text */
    .sidebar .menu-item-text {
        color: white !important;
    }

    .sidebar .menu-item-inactive {
        color: white !important;
    }

    .sidebar .menu-item-inactive:hover {
        background-color: rgba(255, 255, 255, 0.1) !important;
        color: white !important;
    }

    /* Remove broad menu-item-active selector */

    .sidebar .menu-item-icon-inactive {
        stroke: white !important;
    }

    .sidebar .menu-item-icon-active {
        stroke: white !important;
    }

    .sidebar .menu-item-arrow-inactive {
        stroke: white !important;
    }

    .sidebar .menu-item-arrow-active {
        stroke: white !important;
    }

    .sidebar .menu-dropdown-item {
        color: white !important;
    }

    .sidebar .menu-dropdown-item:hover {
        background-color: rgba(255, 255, 255, 0.1) !important;
        color: white !important;
    }

    /* Remove broad dropdown active selector */

    /* Only keep specific active states */

    /* Remove broad override selectors */

    /* Custom manual class for active items */
    .sidebar-active-blue {
        background-color: #354f96 !important;
        color: white !important;
    }

    /* Force only specific active states - remove broad selectors */

</style>

<aside :class="sidebarToggle ? 'translate-x-0 lg:w-[90px]' : '-translate-x-full'" class="sidebar fixed left-0 top-0 z-9999 flex h-screen w-[290px] flex-col overflow-y-hidden border-r border-blue-800 px-5 dark:border-blue-600 lg:static lg:translate-x-0 transition-all duration-300 ease-in-out" style="background-color: #1e3a8a;">
    <!-- SIDEBAR HEADER -->
    <div class="flex items-center justify-center gap-2 pt-8 sidebar-header pb-7" style="justify-content: center !important;">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <span class="logo transition-all duration-300 ease-in-out" :class="sidebarToggle ? 'hidden' : ''">
                <img src="{{ asset('images/logo-kkp.png') }}" alt="Logo KKP" class="h-8 w-auto" />
            </span>
            <img class="logo-icon transition-all duration-300 ease-in-out" :class="sidebarToggle ? 'lg:block' : 'hidden'" src="{{ asset('images/logo-kkp.png') }}" alt="Logo" />
            <span class="text-xl font-bold text-white transition-all duration-300 ease-in-out" :class="sidebarToggle ? 'lg:hidden' : ''">SIGOTIK</span>
        </a>
    </div>
    <!-- SIDEBAR HEADER -->

    <div class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar">
        <!-- Sidebar Menu -->
        <nav x-data="{selected: $persist('{{ $activeGroup ?: 'Dashboard' }}')}" x-init="selected = '{{ $activeGroup ?: 'Dashboard' }}'">
            <!-- Menu Group -->
            <div>
                {{-- <h3 class="mb-4 text-xs uppercase leading-[20px] text-gray-400">
                    <span class="menu-group-title transition-all duration-300 ease-in-out" :class="sidebarToggle ? 'lg:hidden' : ''">MASTER</span>
                    <svg :class="sidebarToggle ? 'lg:block hidden' : 'hidden'" class="mx-auto fill-current menu-group-icon transition-all duration-300 ease-in-out" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M5.99915 10.2451C6.96564 10.2451 7.74915 11.0286 7.74915 11.9951V12.0051C7.74915 12.9716 6.96564 13.7551 5.99915 13.7551C5.03265 13.7551 4.24915 12.9716 4.24915 12.0051V11.9951C4.24915 11.0286 5.03265 10.2451 5.99915 10.2451ZM17.9991 10.2451C18.9656 10.2451 19.7491 11.0286 19.7491 11.9951V12.0051C19.7491 12.9716 18.9656 13.7551 17.9991 13.7551C17.0326 13.7551 16.2491 12.9716 16.2491 12.0051V11.9951C16.2491 11.0286 17.0326 10.2451 17.9991 10.2451ZM13.7491 11.9951C13.7491 11.0286 12.9656 10.2451 11.9991 10.2451C11.0326 10.2451 10.2491 11.0286 10.2491 11.9951V12.0051C10.2491 12.9716 11.0326 13.7551 11.9991 13.7551C12.9656 13.7551 13.7491 12.9716 13.7491 12.0051V11.9951Z" fill="" />
                    </svg>
                </h3> --}}

                <ul class="flex flex-col gap-4 mb-6">
                    <!-- Dashboard -->
                    <li>
                        <a href="{{ route('dashboard') }}" class="menu-item group {{ SidebarHelper::getActiveClass('dashboard', $currentRoute) }}" style="{{ $currentRoute === 'dashboard' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">
                            <svg class="{{ SidebarHelper::getIconActiveClass('dashboard', $currentRoute) }}" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 3h7v7H3zM14 3h7v7h-7zM14 14h7v7h-7zM3 14h7v7H3z" />
                            </svg>
                            <span class="menu-item-text transition-all duration-300 ease-in-out" :class="sidebarToggle ? 'lg:hidden' : ''">Dashboard</span>
                        </a>
                    </li>

                    <!-- Config -->
                    @if(RoleHelper::hasAnySubmenuAccess(['users.index', 'groups.index', 'menus.index', 'release.index']))
                    <li>
                        <a href="#" @click.prevent="selected = (selected === 'Config' ? '':'Config')" class="menu-item group {{ SidebarHelper::getMenuGroupClass('Config', $currentRoute) }}">
                            <svg :class="(selected === 'Config') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="3" />
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1 1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" />
                            </svg>
                            <span class="menu-item-text transition-all duration-300 ease-in-out" :class="sidebarToggle ? 'lg:hidden' : ''">Config</span>
                            <svg class="menu-item-arrow absolute right-2.5 stroke-current transition-all duration-300 ease-in-out" :class="[(selected === 'Config') ? 'top-1/3 -translate-y-1/3 menu-item-arrow-active' : 'mt-3 menu-item-arrow-inactive top-1/2 -translate-y-1/2', sidebarToggle ? 'lg:hidden' : '' ]" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.79175 7.39584L10.0001 12.6042L15.2084 7.39585" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                        <div class="overflow-hidden transform translate transition-all duration-300 ease-in-out" :class="(selected === 'Config') ? 'block' :'hidden'">
                            <ul :class="sidebarToggle ? 'lg:hidden' : 'flex'" class="flex flex-col gap-1 mt-2 menu-dropdown pl-9 transition-all duration-300 ease-in-out">
                                @if(RoleHelper::hasPermission('users.index'))
                                <li><a href="{{ route('users.index') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('users.index', $currentRoute) }}" style="{{ $currentRoute === 'users.index' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">User</a></li>
                                @endif
                                @if(RoleHelper::hasPermission('groups.index'))
                                <li><a href="{{ route('groups.index') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('groups.index', $currentRoute) }}" style="{{ $currentRoute === 'groups.index' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">Group</a></li>
                                @endif
                                @if(RoleHelper::hasPermission('menus.index'))
                                <li><a href="{{ route('menus.index') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('menus.index', $currentRoute) }}" style="{{ $currentRoute === 'menus.index' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">Menu</a></li>
                                @endif
                                @if(RoleHelper::hasPermission('release.index'))
                                <li><a href="{{ route('release.index') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('release.index', $currentRoute) }}" style="{{ $currentRoute === 'release.index' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">Release</a></li>
                                @endif
                            </ul>
                        </div>
                    </li>
                    @endif

                    <!-- Master Data -->
                    @if(RoleHelper::hasAnySubmenuAccess(['kapals.index', 'upts.index']))
                    <li>
                        <a href="#" @click.prevent="selected = (selected === 'Master Data' ? '':'Master Data')" class="menu-item group {{ SidebarHelper::getMenuGroupClass('Master Data', $currentRoute) }}">
                            <svg :class="(selected === 'Master Data') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                <polyline points="14,2 14,8 20,8" />
                                <line x1="16" y1="13" x2="8" y2="13" />
                                <line x1="16" y1="17" x2="8" y2="17" />
                                <polyline points="10,9 9,9 8,9" />
                            </svg>
                            <span class="menu-item-text transition-all duration-300 ease-in-out" :class="sidebarToggle ? 'lg:hidden' : ''">Master Data</span>
                            <svg class="menu-item-arrow absolute right-2.5 stroke-current transition-all duration-300 ease-in-out" :class="[(selected === 'Master Data') ? 'top-1/3 -translate-y-1/3 menu-item-arrow-active' : 'mt-3 menu-item-arrow-inactive top-1/2 -translate-y-1/2', sidebarToggle ? 'lg:hidden' : '' ]" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.79175 7.39584L10.0001 12.6042L15.2084 7.39585" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                        <div class="overflow-hidden transform translate transition-all duration-300 ease-in-out" :class="(selected === 'Master Data') ? 'block' :'hidden'">
                            <ul :class="sidebarToggle ? 'lg:hidden' : 'flex'" class="flex flex-col gap-1 mt-2 menu-dropdown pl-9 transition-all duration-300 ease-in-out">
                                @if(RoleHelper::hasPermission('kapals.index'))
                                <li><a href="{{ route('kapals.index') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('kapals.index', $currentRoute) }}" style="{{ $currentRoute === 'kapals.index' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">Kapal</a></li>
                                @endif
                                @if(RoleHelper::hasPermission('upts.index'))
                                <li><a href="{{ route('upts.index') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('upts.index', $currentRoute) }}" style="{{ $currentRoute === 'upts.index' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">UPT</a></li>
                                @endif
                            </ul>
                        </div>
                    </li>
                    @endif

                    <!-- Monitoring BBM -->
                    @if(RoleHelper::hasAnySubmenuAccess(['ba-sebelum-pengisian.index', 'ba-sebelum-pelayaran.index', 'ba-sesudah-pelayaran.index', 'ba-pemeriksaan-sarana-pengisian.index', 'ba-penggunaan-bbm.index', 'ba-akhir-bulan.index', 'ba-penerimaan-bbm.index', 'ba-penitipan-bbm.index', 'ba-pengembalian-bbm.index']))
                    <li>
                        <a href="#" @click.prevent="selected = (selected === 'Monitoring BBM' ? '':'Monitoring BBM')" class="menu-item group {{ SidebarHelper::getMenuGroupClass('Monitoring BBM', $currentRoute) }}">
                            <svg :class="(selected === 'Monitoring BBM') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" xmlns="http://www.w3.org/2000/svg">
                                <rect x="2" y="3" width="20" height="14" rx="2" ry="2" />
                                <line x1="8" y1="21" x2="16" y2="21" />
                                <line x1="12" y1="17" x2="12" y2="21" />
                            </svg>
                            <span class="menu-item-text transition-all duration-300 ease-in-out" :class="sidebarToggle ? 'lg:hidden' : ''">Monitoring BBM</span>
                            <svg class="menu-item-arrow absolute right-2.5 stroke-current transition-all duration-300 ease-in-out" :class="[(selected === 'Monitoring BBM') ? 'top-1/3 -translate-y-1/3 menu-item-arrow-active' : 'mt-3 menu-item-arrow-inactive top-1/2 -translate-y-1/2', sidebarToggle ? 'lg:hidden' : '' ]" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.79175 7.39584L10.0001 12.6042L15.2084 7.39585" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                        <div class="overflow-hidden transform translate transition-all duration-300 ease-in-out" :class="(selected === 'Monitoring BBM') ? 'block' :'hidden'">
                            <ul :class="sidebarToggle ? 'lg:hidden' : 'flex'" class="flex flex-col gap-1 mt-2 menu-dropdown pl-9 transition-all duration-300 ease-in-out">
                                @if(RoleHelper::hasPermission('ba-sebelum-pengisian.index'))
                                <li><a href="{{ route('ba-sebelum-pengisian.index') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('ba-sebelum-pengisian.index', $currentRoute) }}" style="{{ $currentRoute === 'ba-sebelum-pengisian.index' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">BA Sebelum Pengisian</a></li>
                                @endif
                                @if(RoleHelper::hasPermission('ba-sebelum-pelayaran.index'))
                                <li><a href="{{ route('ba-sebelum-pelayaran.index') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('ba-sebelum-pelayaran.index', $currentRoute) }}" style="{{ $currentRoute === 'ba-sebelum-pelayaran.index' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">BA Sebelum Pelayaran</a></li>
                                @endif
                                @if(RoleHelper::hasPermission('ba-sesudah-pelayaran.index'))
                                <li><a href="{{ route('ba-sesudah-pelayaran.index') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('ba-sesudah-pelayaran.index', $currentRoute) }}" style="{{ $currentRoute === 'ba-sesudah-pelayaran.index' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">BA Sesudah Pelayaran</a></li>
                                @endif
                                @if(RoleHelper::hasPermission('ba-pemeriksaan-sarana-pengisian.index'))
                                <li><a href="{{ route('ba-pemeriksaan-sarana-pengisian.index') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('ba-pemeriksaan-sarana-pengisian.index', $currentRoute) }}" style="{{ $currentRoute === 'ba-pemeriksaan-sarana-pengisian.index' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">BA Pemeriksaan Sarana Pengisian</a></li>
                                @endif
                                @if(RoleHelper::hasPermission('ba-penggunaan-bbm.index'))
                                <li><a href="{{ route('ba-penggunaan-bbm.index') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('ba-penggunaan-bbm.index', $currentRoute) }}" style="{{ $currentRoute === 'ba-penggunaan-bbm.index' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">BA Penggunaan BBM</a></li>
                                @endif
                                @if(RoleHelper::hasPermission('ba-akhir-bulan.index'))
                                <li><a href="{{ route('ba-akhir-bulan.index') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('ba-akhir-bulan.index', $currentRoute) }}" style="{{ $currentRoute === 'ba-akhir-bulan.index' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">BA Akhir Bulan</a></li>
                                @endif
                                @if(RoleHelper::hasPermission('ba-penerimaan-bbm.index'))
                                <li><a href="{{ route('ba-penerimaan-bbm.index') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('ba-penerimaan-bbm.index', $currentRoute) }}" style="{{ $currentRoute === 'ba-penerimaan-bbm.index' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">BA Penerimaan BBM</a></li>
                                @endif
                                @if(RoleHelper::hasPermission('ba-penitipan-bbm.index'))
                                <li><a href="{{ route('ba-penitipan-bbm.index') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('ba-penitipan-bbm.index', $currentRoute) }}" style="{{ $currentRoute === 'ba-penitipan-bbm.index' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">BA Penitipan BBM</a></li>
                                @endif
                                @if(RoleHelper::hasPermission('ba-pengembalian-bbm.index'))
                                <li><a href="{{ route('ba-pengembalian-bbm.index') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('ba-pengembalian-bbm.index', $currentRoute) }}" style="{{ $currentRoute === 'ba-pengembalian-bbm.index' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">BA Pengembalian BBM</a></li>
                                @endif
                            </ul>
                        </div>
                    </li>
                    @endif

                    <!-- Monitoring PINJAMAN -->
                    <li>
                        <a href="#" @click.prevent="selected = (selected === 'Monitoring PINJAMAN' ? '':'Monitoring PINJAMAN')" class="menu-item group {{ SidebarHelper::getMenuGroupClass('Monitoring PINJAMAN', $currentRoute) }}">
                            <svg :class="(selected === 'Monitoring PINJAMAN') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" xmlns="http://www.w3.org/2000/svg">
                                <rect x="2" y="3" width="20" height="14" rx="2" ry="2" />
                                <line x1="8" y1="21" x2="16" y2="21" />
                                <line x1="12" y1="17" x2="12" y2="21" />
                            </svg>
                            <span class="menu-item-text transition-all duration-300 ease-in-out" :class="sidebarToggle ? 'lg:hidden' : ''">Monitoring PINJAMAN</span>
                            <svg class="menu-item-arrow absolute right-2.5 stroke-current transition-all duration-300 ease-in-out" :class="[(selected === 'Monitoring PINJAMAN') ? 'top-1/3 -translate-y-1/3 menu-item-arrow-active' : 'mt-3 menu-item-arrow-inactive top-1/2 -translate-y-1/2', sidebarToggle ? 'lg:hidden' : '' ]" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.79175 7.39584L10.0001 12.6042L15.2084 7.39585" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                        <div class="overflow-hidden transform translate transition-all duration-300 ease-in-out" :class="(selected === 'Monitoring PINJAMAN') ? 'block' :'hidden'">
                            <ul :class="sidebarToggle ? 'lg:hidden' : 'flex'" class="flex flex-col gap-1 mt-2 menu-dropdown pl-9 transition-all duration-300 ease-in-out">
                                <li><a href="{{ route('ba-peminjaman-bbm.index') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('ba-peminjaman-bbm.index', $currentRoute) }}" style="{{ $currentRoute === 'ba-peminjaman-bbm.index' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">BA Peminjaman BBM</a></li>
                                <li><a href="{{ route('ba-penerimaan-pinjaman-bbm.index') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('ba-penerimaan-pinjaman-bbm.index', $currentRoute) }}" style="{{ $currentRoute === 'ba-penerimaan-pinjaman-bbm.index' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">BA Penerimaan Pinjaman BBM</a></li>
                                <li><a href="{{ route('ba-pengembalian-pinjaman-bbm.index') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('ba-pengembalian-pinjaman-bbm.index', $currentRoute) }}" style="{{ $currentRoute === 'ba-pengembalian-pinjaman-bbm.index' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">BA Pengembalian Pinjaman BBM</a></li>
                                <li><a href="{{ route('ba-penerimaan-pengembalian-pinjaman-bbm.index') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('ba-penerimaan-pengembalian-pinjaman-bbm.index', $currentRoute) }}" style="{{ $currentRoute === 'ba-penerimaan-pengembalian-pinjaman-bbm.index' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">BA Penerimaan Pengembalian BBM</a></li>
                            </ul>
                        </div>
                    </li>

                    <!-- Monitoring HIBAH -->
                    <li>
                        <a href="#" @click.prevent="selected = (selected === 'Monitoring HIBAH' ? '':'Monitoring HIBAH')" class="menu-item group {{ SidebarHelper::getMenuGroupClass('Monitoring HIBAH', $currentRoute) }}">
                            <svg :class="(selected === 'Monitoring HIBAH') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" xmlns="http://www.w3.org/2000/svg">
                                <rect x="2" y="3" width="20" height="14" rx="2" ry="2" />
                                <line x1="8" y1="21" x2="16" y2="21" />
                                <line x1="12" y1="17" x2="12" y2="21" />
                            </svg>
                            <span class="menu-item-text transition-all duration-300 ease-in-out" :class="sidebarToggle ? 'lg:hidden' : ''">Monitoring HIBAH</span>
                            <svg class="menu-item-arrow absolute right-2.5 stroke-current transition-all duration-300 ease-in-out" :class="[(selected === 'Monitoring HIBAH') ? 'top-1/3 -translate-y-1/3 menu-item-arrow-active' : 'mt-3 menu-item-arrow-inactive top-1/2 -translate-y-1/2', sidebarToggle ? 'lg:hidden' : '' ]" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.79175 7.39584L10.0001 12.6042L15.2084 7.39585" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                        <div class="overflow-hidden transform translate transition-all duration-300 ease-in-out" :class="(selected === 'Monitoring HIBAH') ? 'block' :'hidden'">
                            <ul :class="sidebarToggle ? 'lg:hidden' : 'flex'" class="flex flex-col gap-1 mt-2 menu-dropdown pl-9 transition-all duration-300 ease-in-out">
                                <li><a href="{{ route('ba-pemberi-hibah-bbm-kapal-pengawas.index') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('ba-pemberi-hibah-bbm-kapal-pengawas.index', $currentRoute) }}" style="{{ $currentRoute === 'ba-pemberi-hibah-bbm-kapal-pengawas.index' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">BA Pemberi Hibah BBM Kapal Pengawas</a></li>
                                <li><a href="{{ route('ba-penerima-hibah-bbm-kapal-pengawas.index') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('ba-penerima-hibah-bbm-kapal-pengawas.index', $currentRoute) }}" style="{{ $currentRoute === 'ba-penerima-hibah-bbm-kapal-pengawas.index' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">BA Penerima Hibah BBM Kapal Pengawas</a></li>
                                <li><a href="{{ route('ba-pemberi-hibah-bbm-dengan-instansi-lain.index') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('ba-pemberi-hibah-bbm-dengan-instansi-lain.index', $currentRoute) }}" style="{{ $currentRoute === 'ba-pemberi-hibah-bbm-dengan-instansi-lain.index' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">BA Pemberi Hibah BBM Dengan Instansi Lain</a></li>
                                <li><a href="{{ route('ba-penerima-hibah-bbm-dengan-instansi-lain.index') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('ba-penerima-hibah-bbm-dengan-instansi-lain.index', $currentRoute) }}" style="{{ $currentRoute === 'ba-penerima-hibah-bbm-dengan-instansi-lain.index' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">BA Penerima Hibah BBM Dengan Instansi Lain</a></li>
                                <li><a href="{{ route('ba-penerimaan-hibah-bbm.index') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('ba-penerimaan-hibah-bbm.index', $currentRoute) }}" style="{{ $currentRoute === 'ba-penerimaan-hibah-bbm.index' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">BA Penerimaan Hibah BBM</a></li>
                            </ul>
                        </div>
                    </li>

                    <!-- Anggaran dan Realisasi -->
                    <li>
                        <a href="#" @click.prevent="selected = (selected === 'Anggaran dan Realisasi' ? '':'Anggaran dan Realisasi')" class="menu-item group {{ SidebarHelper::getMenuGroupClass('Anggaran dan Realisasi', $currentRoute) }}">
                            <svg :class="(selected === 'Anggaran dan Realisasi') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 3v18h18" />
                                <path d="M18.7 8l-5.1 5.2-2.8-2.7L7 14.3" />
                            </svg>
                            <span class="menu-item-text transition-all duration-300 ease-in-out" :class="sidebarToggle ? 'lg:hidden' : ''">Anggaran dan Realisasi</span>
                            <svg class="menu-item-arrow absolute right-2.5 stroke-current transition-all duration-300 ease-in-out" :class="[(selected === 'Anggaran dan Realisasi') ? 'top-1/3 -translate-y-1/3 menu-item-arrow-active' : 'mt-3 menu-item-arrow-inactive top-1/2 -translate-y-1/2', sidebarToggle ? 'lg:hidden' : '' ]" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.79175 7.39584L10.0001 12.6042L15.2084 7.39585" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                        <div class="overflow-hidden transform translate transition-all duration-300 ease-in-out" :class="(selected === 'Anggaran dan Realisasi') ? 'block' :'hidden'">
                            <ul :class="sidebarToggle ? 'lg:hidden' : 'flex'" class="flex flex-col gap-1 mt-2 menu-dropdown pl-9 transition-all duration-300 ease-in-out">
                                @if(RoleHelper::hasPermission('anggaran.entri-anggaran'))
                                <li><a href="{{ route('anggaran.entri-anggaran') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('anggaran.entri-anggaran', $currentRoute) }}" style="{{ $currentRoute === 'anggaran.entri-anggaran' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">Entri Anggaran</a></li>
                                @endif
                                @if(RoleHelper::hasPermission('anggaran.perubahan-anggaran'))
                                <li><a href="{{ route('anggaran.perubahan-anggaran') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('anggaran.perubahan-anggaran', $currentRoute) }}" style="{{ $currentRoute === 'anggaran.perubahan-anggaran' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">Perubahan Anggaran</a></li>
                                @endif
                                @if(RoleHelper::hasPermission('anggaran.approval-anggaran'))
                                <li><a href="{{ route('anggaran.approval-anggaran') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('anggaran.approval-anggaran', $currentRoute) }}" style="{{ $currentRoute === 'anggaran.approval-anggaran' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">Approval Anggaran</a></li>
                                @endif
                                @if(RoleHelper::hasPermission('anggaran.entry-realisasi'))
                                <li><a href="{{ route('anggaran.entry-realisasi') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('anggaran.entry-realisasi', $currentRoute) }}" style="{{ $currentRoute === 'anggaran.entry-realisasi' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">Entry Realisasi</a></li>
                                @endif
                                @if(RoleHelper::hasPermission('anggaran.approval-realisasi'))
                                <li><a href="{{ route('anggaran.approval-realisasi') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('anggaran.approval-realisasi', $currentRoute) }}" style="{{ $currentRoute === 'anggaran.approval-realisasi' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">Approval Realisasi</a></li>
                                @endif
                                @if(RoleHelper::hasPermission('anggaran.pembatalan-realisasi'))
                                <li><a href="{{ route('anggaran.pembatalan-realisasi') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('anggaran.pembatalan-realisasi', $currentRoute) }}" style="{{ $currentRoute === 'anggaran.pembatalan-realisasi' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">Pembatalan Realisasi</a></li>
                                @endif
                                @if(RoleHelper::hasPermission('anggaran.tanggal-sppd'))
                                <li><a href="{{ route('anggaran.tanggal-sppd') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('anggaran.tanggal-sppd', $currentRoute) }}" style="{{ $currentRoute === 'anggaran.tanggal-sppd' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">Tanggal SPPD</a></li>
                                @endif
                                @if(RoleHelper::hasPermission('anggaran.entry-anggaran-internal'))
                                <li><a href="{{ route('anggaran.entry-anggaran-internal') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('anggaran.entry-anggaran-internal', $currentRoute) }}" style="{{ $currentRoute === 'anggaran.entry-anggaran-internal' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">Entry Anggaran Internal</a></li>
                                @endif
                                @if(RoleHelper::hasPermission('anggaran.approval-anggaran-internal'))
                                <li><a href="{{ route('anggaran.approval-anggaran-internal') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('anggaran.approval-anggaran-internal', $currentRoute) }}" style="{{ $currentRoute === 'anggaran.approval-anggaran-internal' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">Approval Anggaran Internal</a></li>
                                @endif
                                @if(RoleHelper::hasPermission('anggaran.pembatalan-anggaran-internal'))
                                <li><a href="{{ route('anggaran.pembatalan-anggaran-internal') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('anggaran.pembatalan-anggaran-internal', $currentRoute) }}" style="{{ $currentRoute === 'anggaran.pembatalan-anggaran-internal' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">Pembatalan Anggaran Internal</a></li>
                                @endif
                            </ul>
                        </div>
                    </li>

                    <!-- Laporan BBM -->
                    <li>
                        <a href="#" @click.prevent="selected = (selected === 'Laporan BBM' ? '':'Laporan BBM')" class="menu-item group {{ SidebarHelper::getMenuGroupClass('Laporan BBM', $currentRoute) }}">
                            <svg :class="(selected === 'Laporan BBM') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                <polyline points="14,2 14,8 20,8" />
                                <line x1="16" y1="13" x2="8" y2="13" />
                                <line x1="16" y1="17" x2="8" y2="17" />
                                <polyline points="10,9 9,9 8,9" />
                            </svg>
                            <span class="menu-item-text transition-all duration-300 ease-in-out" :class="sidebarToggle ? 'lg:hidden' : ''">Laporan BBM</span>
                            <svg class="menu-item-arrow absolute right-2.5 stroke-current transition-all duration-300 ease-in-out" :class="[(selected === 'Laporan BBM') ? 'top-1/3 -translate-y-1/3 menu-item-arrow-active' : 'mt-3 menu-item-arrow-inactive top-1/2 -translate-y-1/2', sidebarToggle ? 'lg:hidden' : '' ]" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.79175 7.39584L10.0001 12.6042L15.2084 7.39585" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                        <div class="overflow-hidden transform translate transition-all duration-300 ease-in-out" :class="(selected === 'Laporan BBM') ? 'block' :'hidden'">
                            <ul :class="sidebarToggle ? 'lg:hidden' : 'flex'" class="flex flex-col gap-1 mt-2 menu-dropdown pl-9 transition-all duration-300 ease-in-out">
                                <li><a href="{{ route('laporan-bbm.total-penerimaan-penggunaan') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('laporan-bbm.total-penerimaan-penggunaan', $currentRoute) }}" style="{{ $currentRoute === 'laporan-bbm.total-penerimaan-penggunaan' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">LAP Total Penerimaan & Penggunaan BBM</a></li>
                                <li><a href="{{ route('laporan-bbm.detail-penggunaan-penerimaan') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('laporan-bbm.detail-penggunaan-penerimaan', $currentRoute) }}" style="{{ $currentRoute === 'laporan-bbm.detail-penggunaan-penerimaan' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">LAP Detail Penggunaan & Penerimaan BBM</a></li>
                                <li><a href="{{ route('laporan-bbm.history-penerimaan-penggunaan') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('laporan-bbm.history-penerimaan-penggunaan', $currentRoute) }}" style="{{ $currentRoute === 'laporan-bbm.history-penerimaan-penggunaan' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">History Penerimaan & Penggunaan BBM</a></li>
                                <li><a href="{{ route('laporan-bbm.akhir-bulan') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('laporan-bbm.akhir-bulan', $currentRoute) }}" style="{{ $currentRoute === 'laporan-bbm.akhir-bulan' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">Laporan BBM Akhir Bulan</a></li>
                                <li><a href="{{ route('laporan-bbm.penerimaan') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('laporan-bbm.penerimaan', $currentRoute) }}" style="{{ $currentRoute === 'laporan-bbm.penerimaan' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">Laporan Penerimaan BBM</a></li>
                                <li><a href="{{ route('laporan-bbm.penitipan') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('laporan-bbm.penitipan', $currentRoute) }}" style="{{ $currentRoute === 'laporan-bbm.penitipan' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">Laporan Penitipan BBM</a></li>
                                <li><a href="{{ route('laporan-bbm.pengembalian') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('laporan-bbm.pengembalian', $currentRoute) }}" style="{{ $currentRoute === 'laporan-bbm.pengembalian' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">Laporan Pengembalian BBM</a></li>
                                <li><a href="{{ route('laporan-bbm.peminjaman') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('laporan-bbm.peminjaman', $currentRoute) }}" style="{{ $currentRoute === 'laporan-bbm.peminjaman' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">Laporan Peminjaman</a></li>
                                <li><a href="{{ route('laporan-bbm.pengembalian-pinjaman') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('laporan-bbm.pengembalian-pinjaman', $currentRoute) }}" style="{{ $currentRoute === 'laporan-bbm.pengembalian-pinjaman' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">Laporan Pengembalian Pinjaman</a></li>
                                <li><a href="{{ route('laporan-bbm.pinjaman-belum-dikembalikan') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('laporan-bbm.pinjaman-belum-dikembalikan', $currentRoute) }}" style="{{ $currentRoute === 'laporan-bbm.pinjaman-belum-dikembalikan' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">Laporan Pinjaman Belum di Kembalikan</a></li>
                                <li><a href="{{ route('laporan-bbm.hibah-antar-kapal-pengawas') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('laporan-bbm.hibah-antar-kapal-pengawas', $currentRoute) }}" style="{{ $currentRoute === 'laporan-bbm.hibah-antar-kapal-pengawas' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">Laporan Hibah Antar Kapal Pengawas</a></li>
                                <li><a href="{{ route('laporan-bbm.pemberi-hibah-instansi-lain') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('laporan-bbm.pemberi-hibah-instansi-lain', $currentRoute) }}" style="{{ $currentRoute === 'laporan-bbm.pemberi-hibah-instansi-lain' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">Laporan Pemberi Hibah BBM Instansi Lain</a></li>
                                <li><a href="{{ route('laporan-bbm.penerima-hibah-instansi-lain') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('laporan-bbm.penerima-hibah-instansi-lain', $currentRoute) }}" style="{{ $currentRoute === 'laporan-bbm.penerima-hibah-instansi-lain' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">Laporan Penerima Hibah BBM Instansi Lain</a></li>
                                <li><a href="{{ route('laporan-bbm.penerimaan-hibah') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('laporan-bbm.penerimaan-hibah', $currentRoute) }}" style="{{ $currentRoute === 'laporan-bbm.penerimaan-hibah' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">Laporan Penerimaan Hibah BBM</a></li>
                            </ul>
                        </div>
                    </li>

                    <!-- Laporan Anggaran -->
                    <li>
                        <a href="#" @click.prevent="selected = (selected === 'Laporan Anggaran' ? '':'Laporan Anggaran')" class="menu-item group {{ SidebarHelper::getMenuGroupClass('Laporan Anggaran', $currentRoute) }}">
                            <svg :class="(selected === 'Laporan Anggaran') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                <polyline points="14,2 14,8 20,8" />
                                <line x1="16" y1="13" x2="8" y2="13" />
                                <line x1="16" y1="17" x2="8" y2="17" />
                                <polyline points="10,9 9,9 8,9" />
                            </svg>
                            <span class="menu-item-text transition-all duration-300 ease-in-out" :class="sidebarToggle ? 'lg:hidden' : ''">Laporan Anggaran</span>
                            <svg class="menu-item-arrow absolute right-2.5 stroke-current transition-all duration-300 ease-in-out" :class="[(selected === 'Laporan Anggaran') ? 'top-1/3 -translate-y-1/3 menu-item-arrow-active' : 'mt-3 menu-item-arrow-inactive top-1/2 -translate-y-1/2', sidebarToggle ? 'lg:hidden' : '' ]" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.79175 7.39584L10.0001 12.6042L15.2084 7.39585" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                        <div class="overflow-hidden transform translate transition-all duration-300 ease-in-out" :class="(selected === 'Laporan Anggaran') ? 'block' :'hidden'">
                            <ul :class="sidebarToggle ? 'lg:hidden' : 'flex'" class="flex flex-col gap-1 mt-2 menu-dropdown pl-9 transition-all duration-300 ease-in-out">
                                <li><a href="{{ route('laporan-anggaran.anggaran') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('laporan-anggaran.anggaran', $currentRoute) }}" style="{{ $currentRoute === 'laporan-anggaran.anggaran' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">Laporan Anggaran</a></li>
                                <li><a href="{{ route('laporan-anggaran.riwayat-all') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('laporan-anggaran.riwayat-all', $currentRoute) }}" style="{{ $currentRoute === 'laporan-anggaran.riwayat-all' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">Riwayat Anggaran & Realisasi ALL</a></li>
                                <li><a href="{{ route('laporan-anggaran.realisasi-periode') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('laporan-anggaran.realisasi-periode', $currentRoute) }}" style="{{ $currentRoute === 'laporan-anggaran.realisasi-periode' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">Laporan Realisasi per Periode</a></li>
                                <li><a href="{{ route('laporan-anggaran.transaksi-realisasi-upt') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('laporan-anggaran.transaksi-realisasi-upt', $currentRoute) }}" style="{{ $currentRoute === 'laporan-anggaran.transaksi-realisasi-upt' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">Laporan Transaksi Realisasi UPT</a></li>
                                <li><a href="{{ route('laporan-anggaran.perubahan-anggaran-internal') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('laporan-anggaran.perubahan-anggaran-internal', $currentRoute) }}" style="{{ $currentRoute === 'laporan-anggaran.perubahan-anggaran-internal' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">Laporan Transaksi Perubahan Anggaran Internal UPT</a></li>
                                <li><a href="{{ route('laporan-anggaran.berita-acara-pembayaran') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('laporan-anggaran.berita-acara-pembayaran', $currentRoute) }}" style="{{ $currentRoute === 'laporan-anggaran.berita-acara-pembayaran' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">Laporan Berita Acara Pembayaran Tagihan</a></li>
                                <li><a href="{{ route('laporan-anggaran.verifikasi-tagihan') }}" class="menu-dropdown-item group {{ SidebarHelper::getDropdownActiveClass('laporan-anggaran.verifikasi-tagihan', $currentRoute) }}" style="{{ $currentRoute === 'laporan-anggaran.verifikasi-tagihan' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">Laporan Verifikasi Tagihan</a></li>
                            </ul>
                        </div>
                    </li>

                    <!-- Portal Berita -->
                    <li>
                        <a href="{{ route('portnews.index') }}" class="menu-item group {{ SidebarHelper::getActiveClass('portnews.index', $currentRoute) }}" style="{{ $currentRoute === 'portnews.index' ? 'background-color: #354f96 !important; color: white !important;' : '' }}">
                            <svg class="{{ SidebarHelper::getIconActiveClass('portnews.index', $currentRoute) }}" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                            </svg>
                            <span class="menu-item-text transition-all duration-300 ease-in-out" :class="sidebarToggle ? 'lg:hidden' : ''">Portal Berita</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
        <!-- Sidebar Menu -->

        <!-- User Info -->

    </div>
</aside>
