{{-- resources/views/components/navbar.blade.php --}}
<header class="bg-white border border-[#F3E8E8] rounded-full px-5 py-2.5 flex items-center justify-between shadow-sm flex-wrap gap-3">
    <!-- Brand Logo & Liquipedia Badge -->
    <div class="flex items-center gap-3">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5">
            <span class="w-3 h-3 rounded-full bg-[#700B1A] inline-block shadow-[0_0_8px_rgba(112,11,26,0.5)]"></span>
            <span class="font-black text-sm tracking-widest text-[#18181B] uppercase">METASCOUT</span>
        </a>

        <span class="text-gray-300 font-light select-none">|</span>

        <a href="https://liquipedia.net/mobilelegends" target="_blank" rel="noopener noreferrer" 
           class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-[#F5F8FC] hover:bg-[#EBF2FA] border border-[#D0E1F5] transition group shadow-2xs"
           title="Data Pertandingan Resmi Bersumber dari Liquipedia API">
            <img src="/images/liquipedia_logo.svg" alt="Liquipedia Logo" class="w-4 h-4 object-contain">
            <span class="font-black text-[11px] text-[#0A285F] group-hover:text-[#0072CE] tracking-wider uppercase">LIQUIPEDIA</span>
        </a>
    </div>

    <!-- Navigation Links: Tepat 5 Halaman Publik Sesuai Permintaan -->
    <nav class="flex items-center gap-1.5 sm:gap-2 flex-wrap text-xs font-bold uppercase tracking-wider">
        <a href="{{ route('home') }}" 
           class="{{ request()->routeIs('home') ? 'bg-[#700B1A] text-white shadow-sm' : 'text-gray-600 hover:text-[#700B1A] hover:bg-[#FCECEE]' }} px-4 py-1.5 rounded-full transition">
            HOMEPAGE
        </a>
        <a href="{{ route('draft.recommendation') }}" 
           class="{{ (request()->routeIs('draft.recommendation') || request()->routeIs('draft.analyzer')) ? 'bg-[#700B1A] text-white shadow-sm' : 'text-gray-600 hover:text-[#700B1A] hover:bg-[#FCECEE]' }} px-3.5 py-1.5 rounded-full transition">
            REKOM DRAFT
        </a>
        <a href="{{ route('counter.picks') }}" 
           class="{{ request()->routeIs('counter.picks') ? 'bg-[#700B1A] text-white shadow-sm' : 'text-gray-600 hover:text-[#700B1A] hover:bg-[#FCECEE]' }} px-3.5 py-1.5 rounded-full transition">
            COUNTER PICK
        </a>
        <a href="{{ route('heroes') }}" 
           class="{{ request()->routeIs('heroes') ? 'bg-[#700B1A] text-white shadow-sm' : 'text-gray-600 hover:text-[#700B1A] hover:bg-[#FCECEE]' }} px-3.5 py-1.5 rounded-full transition">
            LIST HERO
        </a>
        <a href="{{ route('hero.statistics') }}" 
           class="{{ request()->routeIs('hero.statistics') ? 'bg-[#700B1A] text-white shadow-sm' : 'text-gray-600 hover:text-[#700B1A] hover:bg-[#FCECEE]' }} px-3.5 py-1.5 rounded-full transition">
            STATISTIK HERO
        </a>
    </nav>

    <!-- Admin Login / Console Action -->
    <div>
        @if(session('admin_logged_in'))
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.dashboard') }}" class="bg-[#FAF8F8] hover:bg-[#FCECEE] text-[#700B1A] border border-[#F8B4BD] text-[11px] font-extrabold uppercase tracking-wider px-3.5 py-1.5 rounded-full flex items-center gap-1.5 transition">
                    <span class="w-1.5 h-1.5 bg-[#700B1A] rounded-full"></span>
                    <span>ADMIN CONSOLE</span>
                </a>
                <a href="{{ route('admin.logout') }}" class="bg-[#FAF8F8] hover:bg-[#FCECEE] text-gray-700 hover:text-[#700B1A] border border-[#E5E7EB] text-[11px] font-extrabold uppercase tracking-wider px-3 py-1.5 rounded-full transition">
                    LOGOUT
                </a>
            </div>
        @else
            <a href="{{ route('login') }}" class="bg-[#FAF8F8] hover:bg-[#FCECEE] text-gray-800 hover:text-[#700B1A] border border-[#E5E7EB] text-[11px] font-extrabold uppercase tracking-wider px-3.5 py-1.5 rounded-full flex items-center gap-1.5 transition">
                <span class="w-1.5 h-1.5 bg-gray-600 rounded-sm"></span>
                <span>LOGIN ADMIN</span>
            </a>
        @endif
    </div>
</header>