@extends('layouts.app')

@section('title', 'Manajemen Atribut Hero - MetaScout Admin')
@section('hide_navbar', true)
@section('hide_footer', true)

@section('content')
<div class="space-y-6">

    <!-- 1. ADMIN TOP NAVIGATION BAR -->
    <header class="bg-white border border-[#F3E8E8] rounded-full px-4 lg:px-5 py-2 flex items-center justify-between shadow-sm flex-nowrap gap-3 w-full">
        <!-- Brand Logo -->
        <a href="{{ route('home') }}" class="flex items-center gap-2.5 flex-shrink-0">
            <span class="w-3 h-3 rounded-full bg-[#700B1A] inline-block shadow-[0_0_8px_rgba(112,11,26,0.5)]"></span>
            <span class="font-black text-sm tracking-widest text-[#18181B] uppercase">METASCOUT</span>
        </a>

        <!-- Admin Links -->
        <nav class="flex items-center gap-1.5 sm:gap-2 flex-nowrap text-xs font-bold uppercase tracking-wider flex-shrink-0">
            <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-[#700B1A] hover:bg-[#FCECEE] px-3.5 py-1.5 rounded-full transition whitespace-nowrap">
                DASHBOARD
            </a>
            <a href="{{ route('matches') }}" class="text-gray-600 hover:text-[#700B1A] hover:bg-[#FCECEE] px-3.5 py-1.5 rounded-full transition whitespace-nowrap">
                DATASET MATCH
            </a>
            <a href="{{ route('admin.heroes') }}" class="bg-[#700B1A] text-white px-4 py-1.5 rounded-full transition shadow-sm whitespace-nowrap">
                MANAJEMEN HERO
            </a>
        </nav>

        <!-- Superadmin Info & Logout -->
        <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
            <div class="flex items-center gap-1.5 text-[11px] font-extrabold text-[#700B1A] uppercase tracking-wider">
                <span class="w-2 h-2 rounded-full bg-[#700B1A]"></span>
                <span>SUPERADMIN</span>
            </div>
            <a href="{{ route('admin.logout') }}" class="bg-[#FAF8F8] hover:bg-[#FCECEE] text-gray-800 hover:text-[#700B1A] border border-[#E5E7EB] text-[11px] font-extrabold uppercase px-3.5 py-1.5 rounded-full transition">
                LOGOUT
            </a>
            <div class="w-7 h-7 rounded-full bg-[#700B1A] text-white flex items-center justify-center font-black text-xs shadow-sm">
                A
            </div>
        </div>
    </header>

    <!-- 2. FLASH NOTIFICATIONS -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl flex items-center justify-between text-xs font-bold shadow-sm">
            <div class="flex items-center gap-2">
                <span class="text-emerald-600 text-base">✓</span>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl flex items-center justify-between text-xs font-bold shadow-sm">
            <div class="flex items-center gap-2">
                <span class="text-rose-600 text-base">⚠</span>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900 font-bold">&times;</button>
        </div>
    @endif

    <!-- 3. BREADCRUMBS & STATUS -->
    <div class="flex items-center justify-between flex-wrap gap-2 pt-1">
        <div class="flex items-center gap-2 text-xs font-bold text-gray-500 uppercase tracking-wider">
            <span>ADMIN</span>
            <span>/</span>
            <span class="text-[#700B1A] font-extrabold">MANAJEMEN ATRIBUT HERO (RU)</span>
        </div>

        <div class="flex items-center gap-2 text-[11px] font-extrabold text-[#700B1A] bg-[#FCECEE] border border-[#F8B4BD] rounded-full px-3.5 py-1 shadow-sm">
            <span class="w-2 h-2 rounded-full bg-[#700B1A]"></span>
            <span>DATA HERO: LIQUIPEDIA API SYNCHRONIZED</span>
        </div>
    </div>

    <!-- 4. PAGE HEADING -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-[#18181B] tracking-tight uppercase">
                ATRIBUT HERO (READ & UPDATE)
            </h1>
            <p class="text-xs text-gray-500 font-medium mt-1">
                Katalog data hero ditarik otomatis dari Liquipedia API. Anda dapat membaca daftar hero dan memperbarui atribut (Role, Lane, Specialty) bila terjadi perubahan patch meta.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="bg-[#FAF8F8] border border-[#F3E8E8] text-gray-600 text-xs font-bold px-3 py-1.5 rounded-full">
                Total: <strong class="text-[#700B1A]">{{ $heroes->count() }}</strong> Hero Terdaftar
            </span>
        </div>
    </div>

    <!-- 5. TABLE SECTION (HEROES LIST RU) -->
    <div class="card-custom p-6 space-y-4">
        <!-- Search & Filter Controls -->
        <div class="space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <input type="text" 
                           id="heroTableSearch" 
                           oninput="filterHeroTable()" 
                           placeholder="Cari hero berdasarkan nama, role, atau lane..." 
                           class="w-full bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl py-2 pl-8 pr-3 text-xs font-medium text-[#18181B] placeholder-gray-400 focus:outline-none focus:border-[#700B1A]">
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-extrabold uppercase text-gray-400">URUTKAN:</span>
                    <select id="adminHeroSortSelect" onchange="sortAdminHeroTable(this.value)" class="bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl py-1.5 px-3 text-xs font-bold text-[#18181B] focus:outline-none cursor-pointer">
                        <option value="name">Berdasarkan Nama</option>
                        <option value="role">Berdasarkan Role</option>
                        <option value="lane">Berdasarkan Lane</option>
                    </select>
                </div>
            </div>

            <!-- Role Filters -->
            <div class="flex items-center gap-2 flex-wrap text-xs">
                <span class="text-[10px] font-extrabold uppercase text-gray-400 w-12">ROLE:</span>
                <div class="flex items-center gap-1 flex-wrap" id="adminRoleFilterContainer">
                    <button type="button" onclick="setAdminRoleFilter('all', this)" class="admin-role-btn px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#700B1A] text-white cursor-pointer">SEMUA</button>
                    <button type="button" onclick="setAdminRoleFilter('tank', this)" class="admin-role-btn px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] hover:text-[#700B1A] cursor-pointer">TANK</button>
                    <button type="button" onclick="setAdminRoleFilter('fighter', this)" class="admin-role-btn px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] hover:text-[#700B1A] cursor-pointer">FIGHTER</button>
                    <button type="button" onclick="setAdminRoleFilter('assassin', this)" class="admin-role-btn px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] hover:text-[#700B1A] cursor-pointer">ASSASSIN</button>
                    <button type="button" onclick="setAdminRoleFilter('mage', this)" class="admin-role-btn px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] hover:text-[#700B1A] cursor-pointer">MAGE</button>
                    <button type="button" onclick="setAdminRoleFilter('marksman', this)" class="admin-role-btn px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] hover:text-[#700B1A] cursor-pointer">MARKSMAN</button>
                    <button type="button" onclick="setAdminRoleFilter('support', this)" class="admin-role-btn px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] hover:text-[#700B1A] cursor-pointer">SUPPORT</button>
                </div>
            </div>

            <!-- Lane Filters -->
            <div class="flex items-center gap-2 flex-wrap text-xs">
                <span class="text-[10px] font-extrabold uppercase text-gray-400 w-12">LANE:</span>
                <div class="flex items-center gap-1 flex-wrap" id="adminLaneFilterContainer">
                    <button type="button" onclick="setAdminLaneFilter('all', this)" class="admin-lane-btn px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#700B1A] text-white cursor-pointer">SEMUA</button>
                    <button type="button" onclick="setAdminLaneFilter('exp', this)" class="admin-lane-btn px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] hover:text-[#700B1A] cursor-pointer">EXP LANE</button>
                    <button type="button" onclick="setAdminLaneFilter('jungle', this)" class="admin-lane-btn px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] hover:text-[#700B1A] cursor-pointer">JUNGLE</button>
                    <button type="button" onclick="setAdminLaneFilter('mid', this)" class="admin-lane-btn px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] hover:text-[#700B1A] cursor-pointer">MID LANE</button>
                    <button type="button" onclick="setAdminLaneFilter('gold', this)" class="admin-lane-btn px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] hover:text-[#700B1A] cursor-pointer">GOLD LANE</button>
                    <button type="button" onclick="setAdminLaneFilter('roam', this)" class="admin-lane-btn px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] hover:text-[#700B1A] cursor-pointer">ROAM</button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto rounded-xl border border-[#F3E8E8]">
            <table class="w-full text-left text-xs border-collapse" id="adminHeroTable">
                <thead>
                    <tr class="bg-[#FAF8F8] border-b border-[#F3E8E8] text-gray-500 font-extrabold uppercase text-[10px] tracking-wider select-none">
                        <th class="py-3 px-3 w-10 text-center">#</th>
                        <th class="py-3 px-3">HERO</th>
                        <th class="py-3 px-3">ROLE / CLASS</th>
                        <th class="py-3 px-3">REKOMENDASI LANE</th>
                        <th class="py-3 px-3">SPECIALTY</th>
                        <th class="py-3 px-3 text-center w-28">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F3E8E8] font-medium" id="heroTableBody">
                    @forelse($heroes as $idx => $h)
                        @php
                            $portraitUrl = $h->portrait ?: 'https://ui-avatars.com/api/?name=' . urlencode($h->hero_name) . '&background=700B1A&color=fff';
                            $laningStr = is_array($h->laning) ? implode(', ', $h->laning) : ($h->laning ?: 'Flex');
                            $specialtyStr = is_array($h->specialty) ? implode(', ', $h->specialty) : ($h->specialty ?: 'General');
                        @endphp
                        <tr class="hover:bg-[#FAF8F8]/80 transition hero-row"
                            data-id="{{ $h->id }}"
                            data-name="{{ strtolower($h->hero_name) }}"
                            data-role="{{ strtolower($h->class) }}"
                            data-lane="{{ strtolower($laningStr) }}">
                            
                            <td class="py-2.5 px-3 text-center text-gray-400 font-bold row-num-cell">
                                {{ sprintf('%02d', $idx + 1) }}
                            </td>
                            
                            <!-- Hero Info -->
                            <td class="py-2.5 px-3">
                                <div class="flex items-center gap-2.5">
                                    <img src="{{ $portraitUrl }}" 
                                         alt="{{ $h->hero_name }}" 
                                         class="w-8 h-8 rounded-full object-cover bg-gray-100 border border-[#F3E8E8] shadow-2xs flex-shrink-0"
                                         onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($h->hero_name) }}&background=700B1A&color=fff';">
                                    <div>
                                        <div class="font-extrabold text-[#18181B] flex items-center gap-1.5 leading-tight">
                                            <span>{{ $h->hero_name }}</span>
                                        </div>
                                        <div class="text-[9.5px] text-gray-400 font-mono">{{ $h->hero_id }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Role / Class -->
                            <td class="py-2.5 px-3">
                                <span class="bg-[#FAF8F8] border border-[#F3E8E8] text-gray-800 text-[11px] font-bold px-2 py-0.5 rounded">
                                    {{ $h->class ?: 'Hero' }}
                                </span>
                            </td>

                            <!-- Rekomendasi Lane -->
                            <td class="py-2.5 px-3">
                                <span class="text-xs font-semibold text-gray-700">
                                    {{ $laningStr }}
                                </span>
                            </td>

                            <!-- Specialty -->
                            <td class="py-2.5 px-3">
                                <span class="text-xs text-gray-600">
                                    {{ $specialtyStr }}
                                </span>
                            </td>

                            <!-- Aksi (Edit Atribut) -->
                            <td class="py-2.5 px-3 text-center">
                                <button type="button" 
                                        onclick="openEditHeroModal({{ json_encode($h) }})" 
                                        class="bg-[#700B1A] hover:bg-[#550713] text-white text-[11px] font-extrabold px-3 py-1.5 rounded-lg inline-flex items-center gap-1 transition shadow-2xs cursor-pointer">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                    <span>Edit</span>
                                </button>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400 font-bold">
                                Tidak ada data hero di database.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between pt-4 border-t border-[#F3E8E8] flex-wrap gap-3 text-xs">
            <div class="flex items-center gap-2">
                <span class="text-gray-500 font-medium">Tampilkan:</span>
                <select id="adminHeroPerPageSelect" onchange="changeAdminHeroPerPage(this.value)" class="bg-[#FAF8F8] border border-[#F3E8E8] rounded-lg px-2.5 py-1 text-xs font-bold text-[#18181B] focus:outline-none focus:border-[#700B1A] cursor-pointer">
                    <option value="10">10</option>
                    <option value="25" selected>25</option>
                    <option value="50">50</option>
                    <option value="all">Semua</option>
                </select>
                <span class="text-gray-300">|</span>
                <div class="text-gray-600 font-semibold" id="adminHeroPaginationInfo">
                    Menampilkan <span class="font-extrabold text-[#700B1A]" id="adminHeroPageStart">1</span> - <span class="font-extrabold text-[#700B1A]" id="adminHeroPageEnd">25</span> dari <span class="font-extrabold text-[#18181B]" id="adminHeroTotalRows">0</span> hero terdaftar
                </div>
            </div>

            <!-- Page Buttons (Prev, 1, 2, 3..., Next) -->
            <div class="flex items-center gap-1.5" id="adminHeroPaginationButtons">
                <!-- Dynamically generated via JS -->
            </div>
        </div>
    </div>

    <!-- 6. FOOTER -->
    <div class="border-t border-[#F3E8E8] pt-4 flex items-center justify-between text-[11px] font-bold text-gray-400 flex-wrap gap-2">
        <div>METASCOUT : LAND OF DAWN • ADMIN PANEL</div>
        <div>ALL TIMESTAMPS IN UTC+7</div>
    </div>

</div>

<!-- ========================================================================= -->
<!-- 7. MODAL EDIT ATRIBUT HERO                                                -->
<!-- ========================================================================= -->
<div id="editHeroModal" 
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs hidden"
     onclick="closeEditModalOnBackdrop(event)">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 border border-[#E5E7EB] space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-[#700B1A]"></span>
                <h3 class="text-sm font-black uppercase tracking-wider text-[#18181B]">
                    EDIT ATRIBUT HERO
                </h3>
            </div>
            <button type="button" onclick="closeEditHeroModal()" class="text-gray-400 hover:text-gray-600 font-bold text-base">&times;</button>
        </div>

        <form id="editHeroForm" method="POST" action="" class="space-y-4">
            @csrf

            <!-- Hero Header Info -->
            <div class="flex items-center gap-3 p-3 bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl">
                <img id="modalHeroPortrait" src="" alt="Hero" class="w-12 h-12 rounded-full object-cover border border-gray-200 shadow-2xs">
                <div>
                    <h4 id="modalHeroName" class="font-black text-base text-[#18181B]">Hero Name</h4>
                    <span id="modalHeroId" class="text-xs font-mono text-gray-400">h000</span>
                </div>
            </div>

            <!-- Role / Class -->
            <div>
                <label class="block text-[10px] font-black uppercase tracking-wider text-gray-500 mb-1">
                    ROLE / CLASS (e.g. Assassin, Mage, Marksman, Fighter, Tank, Support)
                </label>
                <input type="text" 
                       id="modalInputClass" 
                       name="class" 
                       required
                       class="w-full bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl py-2 px-3 text-xs font-semibold text-[#18181B] focus:outline-none focus:border-[#700B1A]">
            </div>

            <!-- Rekomendasi Laning -->
            <div>
                <label class="block text-[10px] font-black uppercase tracking-wider text-gray-500 mb-1">
                    REKOMENDASI LANING (e.g. Gold Lane, Mid Lane, Exp Lane, Jungle, Roam)
                </label>
                <input type="text" 
                       id="modalInputLaning" 
                       name="laning" 
                       required
                       class="w-full bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl py-2 px-3 text-xs font-semibold text-[#18181B] focus:outline-none focus:border-[#700B1A]">
            </div>

            <!-- Specialty -->
            <div>
                <label class="block text-[10px] font-black uppercase tracking-wider text-gray-500 mb-1">
                    SPECIALTY (e.g. Burst, Damage, Finisher, Crowd Control, Guard, Poke)
                </label>
                <input type="text" 
                       id="modalInputSpecialty" 
                       name="specialty" 
                       class="w-full bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl py-2 px-3 text-xs font-semibold text-[#18181B] focus:outline-none focus:border-[#700B1A]">
            </div>

            <!-- Portrait URL -->
            <div>
                <label class="block text-[10px] font-black uppercase tracking-wider text-gray-500 mb-1">
                    PORTRAIT URL (GAMBAR AVATAR HERO)
                </label>
                <input type="text" 
                       id="modalInputPortrait" 
                       name="portrait" 
                       class="w-full bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl py-2 px-3 text-xs font-semibold text-[#18181B] focus:outline-none focus:border-[#700B1A]">
            </div>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                <button type="button" 
                        onclick="closeEditHeroModal()" 
                        class="px-4 py-2 rounded-full text-xs font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" 
                        class="px-5 py-2 rounded-full text-xs font-bold text-white bg-[#700B1A] hover:bg-[#550713] transition shadow-sm flex items-center gap-1.5 cursor-pointer">
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentAdminHeroPage = 1;
    let adminHeroPerPage = 25;
    let activeRoleFilter = 'all';
    let activeLaneFilter = 'all';

    function openEditHeroModal(hero) {
        document.getElementById('editHeroForm').action = `/admin/heroes/${hero.id}/update`;
        document.getElementById('modalHeroName').textContent = hero.hero_name;
        document.getElementById('modalHeroId').textContent = hero.hero_id || '';
        document.getElementById('modalHeroPortrait').src = hero.portrait || `https://ui-avatars.com/api/?name=${encodeURIComponent(hero.hero_name)}&background=700B1A&color=fff`;

        document.getElementById('modalInputClass').value = hero.class || '';
        document.getElementById('modalInputLaning').value = Array.isArray(hero.laning) ? hero.laning.join(', ') : (hero.laning || '');
        document.getElementById('modalInputSpecialty').value = Array.isArray(hero.specialty) ? hero.specialty.join(', ') : (hero.specialty || '');
        document.getElementById('modalInputPortrait').value = hero.portrait || '';

        document.getElementById('editHeroModal').classList.remove('hidden');
    }

    function closeEditHeroModal() {
        document.getElementById('editHeroModal').classList.add('hidden');
    }

    function closeEditModalOnBackdrop(e) {
        if (e.target.id === 'editHeroModal') {
            closeEditHeroModal();
        }
    }

    function setAdminRoleFilter(role, btn) {
        activeRoleFilter = role;
        document.querySelectorAll('.admin-role-btn').forEach(b => {
            b.className = 'admin-role-btn px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] hover:text-[#700B1A] cursor-pointer';
        });
        btn.className = 'admin-role-btn px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#700B1A] text-white cursor-pointer';
        filterHeroTable();
    }

    function setAdminLaneFilter(lane, btn) {
        activeLaneFilter = lane;
        document.querySelectorAll('.admin-lane-btn').forEach(b => {
            b.className = 'admin-lane-btn px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] hover:text-[#700B1A] cursor-pointer';
        });
        btn.className = 'admin-lane-btn px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#700B1A] text-white cursor-pointer';
        filterHeroTable();
    }

    function filterHeroTable() {
        const q = (document.getElementById('heroTableSearch').value || '').toLowerCase().trim();
        const rows = document.querySelectorAll('.hero-row');

        rows.forEach(r => {
            const name = r.dataset.name || '';
            const role = r.dataset.role || '';
            const lane = r.dataset.lane || '';

            const matchQuery = !q || name.includes(q) || role.includes(q) || lane.includes(q);
            const matchRole = activeRoleFilter === 'all' || role.includes(activeRoleFilter);
            const matchLane = activeLaneFilter === 'all' || lane.includes(activeLaneFilter);

            r.dataset.matched = (matchQuery && matchRole && matchLane) ? 'true' : 'false';
        });

        currentAdminHeroPage = 1;
        updateAdminHeroPagination();
    }

    function changeAdminHeroPerPage(val) {
        adminHeroPerPage = val === 'all' ? 999999 : parseInt(val);
        currentAdminHeroPage = 1;
        updateAdminHeroPagination();
    }

    function goToAdminHeroPage(page) {
        currentAdminHeroPage = page;
        updateAdminHeroPagination();
        const table = document.getElementById('adminHeroTable');
        if (table) {
            table.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    function sortAdminHeroTable(sortBy) {
        const tbody = document.getElementById('heroTableBody');
        const rows = Array.from(tbody.querySelectorAll('.hero-row'));

        rows.sort((a, b) => {
            if (sortBy === 'role') {
                return (a.dataset.role || '').localeCompare(b.dataset.role || '');
            } else if (sortBy === 'lane') {
                return (a.dataset.lane || '').localeCompare(b.dataset.lane || '');
            } else {
                return (a.dataset.name || '').localeCompare(b.dataset.name || '');
            }
        });

        rows.forEach(r => tbody.appendChild(r));
        updateAdminHeroPagination();
    }

    function updateAdminHeroPagination() {
        const tbody = document.getElementById('heroTableBody');
        const allRows = Array.from(tbody.querySelectorAll('.hero-row'));
        const matchedRows = allRows.filter(r => r.dataset.matched !== 'false');
        const totalMatched = matchedRows.length;
        const totalPages = Math.ceil(totalMatched / adminHeroPerPage) || 1;

        if (currentAdminHeroPage > totalPages) currentAdminHeroPage = totalPages;
        if (currentAdminHeroPage < 1) currentAdminHeroPage = 1;

        const startIdx = (currentAdminHeroPage - 1) * adminHeroPerPage;
        const endIdx = startIdx + adminHeroPerPage;

        allRows.forEach(r => r.style.display = 'none');
        matchedRows.slice(startIdx, endIdx).forEach(r => r.style.display = '');

        // Update info text
        const startElem = document.getElementById('adminHeroPageStart');
        const endElem = document.getElementById('adminHeroPageEnd');
        const totalElem = document.getElementById('adminHeroTotalRows');
        if (startElem) startElem.textContent = totalMatched > 0 ? (startIdx + 1) : 0;
        if (endElem) endElem.textContent = Math.min(endIdx, totalMatched);
        if (totalElem) totalElem.textContent = totalMatched;

        // Render buttons
        const container = document.getElementById('adminHeroPaginationButtons');
        if (!container) return;
        container.innerHTML = '';

        if (totalPages <= 1) return;

        // Prev
        const prevBtn = document.createElement('button');
        prevBtn.type = 'button';
        prevBtn.disabled = currentAdminHeroPage === 1;
        prevBtn.className = `px-3 py-1 rounded-full text-xs font-bold border transition ${
            currentAdminHeroPage === 1 
                ? 'bg-gray-100 text-gray-300 border-gray-200 cursor-not-allowed' 
                : 'bg-white hover:bg-[#FAF8F8] text-gray-700 border-[#F3E8E8] hover:text-[#700B1A] cursor-pointer shadow-2xs'
        }`;
        prevBtn.innerHTML = '&larr; Prev';
        prevBtn.onclick = () => goToAdminHeroPage(currentAdminHeroPage - 1);
        container.appendChild(prevBtn);

        // Page pills
        for (let p = 1; p <= totalPages; p++) {
            if (p === 1 || p === totalPages || (p >= currentAdminHeroPage - 1 && p <= currentAdminHeroPage + 1)) {
                const pageBtn = document.createElement('button');
                pageBtn.type = 'button';
                pageBtn.className = `w-7 h-7 rounded-full text-xs font-extrabold flex items-center justify-center transition ${
                    p === currentAdminHeroPage 
                        ? 'bg-[#700B1A] text-white shadow-sm' 
                        : 'bg-white hover:bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:text-[#700B1A] cursor-pointer'
                }`;
                pageBtn.textContent = p;
                pageBtn.onclick = () => goToAdminHeroPage(p);
                container.appendChild(pageBtn);
            } else if (p === currentAdminHeroPage - 2 || p === currentAdminHeroPage + 2) {
                const dots = document.createElement('span');
                dots.className = 'text-gray-400 px-1 font-bold text-xs';
                dots.textContent = '...';
                container.appendChild(dots);
            }
        }

        // Next
        const nextBtn = document.createElement('button');
        nextBtn.type = 'button';
        nextBtn.disabled = currentAdminHeroPage === totalPages;
        nextBtn.className = `px-3 py-1 rounded-full text-xs font-bold border transition ${
            currentAdminHeroPage === totalPages 
                ? 'bg-gray-100 text-gray-300 border-gray-200 cursor-not-allowed' 
                : 'bg-white hover:bg-[#FAF8F8] text-gray-700 border-[#F3E8E8] hover:text-[#700B1A] cursor-pointer shadow-2xs'
        }`;
        nextBtn.innerHTML = 'Next &rarr;';
        nextBtn.onclick = () => goToAdminHeroPage(currentAdminHeroPage + 1);
        container.appendChild(nextBtn);
    }

    document.addEventListener('DOMContentLoaded', function() {
        filterHeroTable();
    });
</script>
@endsection
