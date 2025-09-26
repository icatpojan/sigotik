<aside :class="sidebarToggle ? 'translate-x-0 lg:w-[90px]' : '-translate-x-full'" class="sidebar fixed left-0 top-0 z-9999 flex h-screen w-[290px] flex-col overflow-y-hidden border-r border-gray-200 bg-white px-5 dark:border-gray-800 dark:bg-black lg:static lg:translate-x-0 transition-all duration-300 ease-in-out">
    <!-- SIDEBAR HEADER -->
    <div :class="sidebarToggle ? 'justify-center' : 'justify-center'" class="justify-center flex items-center gap-2 pt-8 sidebar-header pb-7">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <span class="logo transition-all duration-300 ease-in-out" :class="sidebarToggle ? 'hidden' : ''">
                <img src="{{ asset('images/logo-kkp.png') }}" alt="Logo KKP" class="h-8 w-auto" />
            </span>
            <img class="logo-icon transition-all duration-300 ease-in-out" :class="sidebarToggle ? 'lg:block' : 'hidden'" src="{{ asset('images/logo-kkp.png') }}" alt="Logo" />
            <span class="text-xl font-bold text-gray-800 dark:text-white transition-all duration-300 ease-in-out" :class="sidebarToggle ? 'lg:hidden' : ''">SIGOTIK</span>
        </a>
    </div>
    <!-- SIDEBAR HEADER -->

    <div class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar">
        <!-- Sidebar Menu -->
        <nav x-data="{selected: $persist('Dashboard')}">
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
                        <a href="{{ route('dashboard') }}" class="menu-item group menu-item-active">
                            <svg class="menu-item-icon-active" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 3h7v7H3zM14 3h7v7h-7zM14 14h7v7h-7zM3 14h7v7H3z" />
                            </svg>
                            <span class="menu-item-text transition-all duration-300 ease-in-out" :class="sidebarToggle ? 'lg:hidden' : ''">Dashboard</span>
                        </a>
                    </li>

                    <!-- Config -->
                    <li>
                        <a href="#" @click.prevent="selected = (selected === 'Config' ? '':'Config')" class="menu-item group" :class="(selected === 'Config') ? 'menu-item-active' : 'menu-item-inactive'">
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
                                <li><a href="{{ route('users.index') }}" class="menu-dropdown-item group menu-dropdown-item-inactive">User</a></li>
                                <li><a href="{{ route('groups.index') }}" class="menu-dropdown-item group menu-dropdown-item-inactive">Group</a></li>
                                <li><a href="{{ route('menus.index') }}" class="menu-dropdown-item group menu-dropdown-item-inactive">Menu</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">Release</a></li>
                            </ul>
                        </div>
                    </li>

                    <!-- Master Data -->
                    <li>
                        <a href="#" @click.prevent="selected = (selected === 'Master Data' ? '':'Master Data')" class="menu-item group" :class="(selected === 'Master Data') ? 'menu-item-active' : 'menu-item-inactive'">
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
                                <li><a href="{{ route('kapals.index') }}" class="menu-dropdown-item group menu-dropdown-item-inactive">Kapal</a></li>
                                <li><a href="{{ route('upts.index') }}" class="menu-dropdown-item group menu-dropdown-item-inactive">UPT</a></li>
                            </ul>
                        </div>
                    </li>

                    <!-- Monitoring BBM -->
                    <li>
                        <a href="#" @click.prevent="selected = (selected === 'Monitoring BBM' ? '':'Monitoring BBM')" class="menu-item group" :class="(selected === 'Monitoring BBM') ? 'menu-item-active' : 'menu-item-inactive'">
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
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">BA Sebelum Pengisian</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">BA Sebelum Pelayaran</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">BA Sesudah Pelayaran</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">BA Penggunaan BBM</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">BA Pemeriksaan Sarana Pengisian</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">BA Penerimaan BBM</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">BA Akhir Bulan</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">BA Penitipan BBM</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">BA Pengembalian BBM</a></li>
                            </ul>
                        </div>
                    </li>

                    <!-- Monitoring PINJAMAN -->
                    <li>
                        <a href="#" @click.prevent="selected = (selected === 'Monitoring PINJAMAN' ? '':'Monitoring PINJAMAN')" class="menu-item group" :class="(selected === 'Monitoring PINJAMAN') ? 'menu-item-active' : 'menu-item-inactive'">
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
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">BA Peminjaman BBM</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">BA Penerimaan Pinjaman BBM</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">BA Pengembalian Pinjaman BBM</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">BA Penerimaan Pengembalian BBM</a></li>
                            </ul>
                        </div>
                    </li>

                    <!-- Monitoring HIBAH -->
                    <li>
                        <a href="#" @click.prevent="selected = (selected === 'Monitoring HIBAH' ? '':'Monitoring HIBAH')" class="menu-item group" :class="(selected === 'Monitoring HIBAH') ? 'menu-item-active' : 'menu-item-inactive'">
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
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">BA Pemberi Hibah BBM Kapal Pengawas</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">BA Penerima Hibah BBM Kapal Pengawas</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">BA Pemberi Hibah BBM Dengan Instansi Lain</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">BA Penerima Hibah BBM Dengan Instansi Lain</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">BA Penerimaan Hibah BBM</a></li>
                            </ul>
                        </div>
                    </li>

                    <!-- Anggaran dan Realisasi -->
                    <li>
                        <a href="#" @click.prevent="selected = (selected === 'Anggaran dan Realisasi' ? '':'Anggaran dan Realisasi')" class="menu-item group" :class="(selected === 'Anggaran dan Realisasi') ? 'menu-item-active' : 'menu-item-inactive'">
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
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">Entri Anggaran</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">Perubahan Anggaran</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">Approval Anggaran</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">Entry Realisasi</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">Approval Realisasi</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">Pembatalan Realisasi</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">Tanggal SPPD</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">Entry Anggaran Internal</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">Approval Anggaran Internal</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">Pembatalan Anggaran Internal</a></li>
                            </ul>
                        </div>
                    </li>

                    <!-- Laporan BBM -->
                    <li>
                        <a href="#" @click.prevent="selected = (selected === 'Laporan BBM' ? '':'Laporan BBM')" class="menu-item group" :class="(selected === 'Laporan BBM') ? 'menu-item-active' : 'menu-item-inactive'">
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
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">LAP Total Penerimaan & Penggunaan BBM</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">LAP Detail Penggunaan & Penerimaan BBM</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">History Penerimaan & Penggunaan BBM</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">Laporan BBM Akhir Bulan</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">Laporan Penerimaan BBM</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">Laporan Penitipan BBM</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">Laporan Pengembalian BBM</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">Laporan Peminjaman</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">Laporan Pengembalian Pinjaman</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">Laporan Pinjaman Belum di Kembalikan</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">Laporan Hibah Antar Kapal Pengawas</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">Laporan Pemberi Hibah BBM Instansi Lain</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">Laporan Penerima Hibah BBM Instansi Lain</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">Laporan Penerimaan Hibah BBM</a></li>
                            </ul>
                        </div>
                    </li>

                    <!-- Laporan Anggaran -->
                    <li>
                        <a href="#" @click.prevent="selected = (selected === 'Laporan Anggaran' ? '':'Laporan Anggaran')" class="menu-item group" :class="(selected === 'Laporan Anggaran') ? 'menu-item-active' : 'menu-item-inactive'">
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
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">Laporan Anggaran</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">Riwayat Anggaran & Realisasi ALL</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">Laporan Realisasi per Periode</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">Laporan Transaksi Realisasi UPT</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">Laporan Transaksi Perubahan Anggaran Internal UPT</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">Laporan Berita Acara Pembayaran Tagihan</a></li>
                                <li><a href="#" class="menu-dropdown-item group menu-dropdown-item-inactive">Laporan Verifikasi Tagihan</a></li>
                            </ul>
                        </div>
                    </li>

                    <!-- Portal Berita -->
                    <li>
                        <a href="#" class="menu-item group menu-item-inactive">
                            <svg class="menu-item-icon-inactive" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" xmlns="http://www.w3.org/2000/svg">
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
