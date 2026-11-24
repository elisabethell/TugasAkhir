{{-- resources/views/components/navbar.blade.php --}}
<header class="bg-white border border-[#F3E8E8] rounded-full px-3 sm:px-4 xl:px-5 py-2 flex items-center justify-between shadow-sm flex-nowrap gap-2 xl:gap-3 w-full overflow-x-auto [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
    <!-- Brand Logo & Liquipedia Badge -->
    <div class="flex items-center gap-2 sm:gap-2.5 flex-shrink-0">
        <a href="{{ route('home') }}" class="flex items-center gap-2 group whitespace-nowrap">
            <span class="w-2.5 h-2.5 rounded-full bg-[#700B1A] inline-block shadow-[0_0_8px_rgba(112,11,26,0.5)]"></span>
            <span class="font-black text-xs sm:text-sm tracking-wider text-[#18181B] group-hover:text-[#700B1A] transition uppercase">METASCOUT</span>
        </a>

        <div class="h-3.5 w-px bg-gray-200 hidden sm:block"></div>

        <a href="https://liquipedia.net/mobilelegends" target="_blank" rel="noopener noreferrer" 
           class="flex items-center gap-1.5 px-2 py-0.5 sm:py-1 rounded-full bg-[#F5F8FC] hover:bg-[#EBF2FA] border border-[#D0E1F5] transition group shadow-2xs whitespace-nowrap"
           title="Data Pertandingan Resmi Bersumber dari Liquipedia API">
            <img src="/images/liquipedia_logo.svg" alt="Liquipedia Logo" class="w-3.5 h-3.5 object-contain">
            <span class="font-black text-[10px] sm:text-[11px] text-[#0A285F] group-hover:text-[#0072CE] tracking-wider uppercase">LIQUIPEDIA</span>
        </a>
    </div>

    <!-- Navigation Links: Halaman Publik & Rules RBR (Strictly Single-line / Sejajar Tanpa Turun ke Bawah) -->
    <nav class="flex items-center gap-0.5 sm:gap-1 xl:gap-1.5 flex-nowrap text-[10.5px] xl:text-[11px] font-extrabold uppercase tracking-wider flex-shrink-0">
        <a href="{{ route('home') }}" 
           class="{{ request()->routeIs('home') ? 'bg-[#700B1A] text-white shadow-sm' : 'text-gray-600 hover:text-[#700B1A] hover:bg-[#FCECEE]' }} px-2 sm:px-2.5 xl:px-3 py-1.5 rounded-full transition whitespace-nowrap">
            HOMEPAGE
        </a>
        <a href="{{ route('draft.recommendation') }}" 
           class="{{ (request()->routeIs('draft.recommendation') || request()->routeIs('draft.analyzer')) ? 'bg-[#700B1A] text-white shadow-sm' : 'text-gray-600 hover:text-[#700B1A] hover:bg-[#FCECEE]' }} px-2 sm:px-2.5 xl:px-3 py-1.5 rounded-full transition whitespace-nowrap">
            REKOM DRAFT
        </a>
        <a href="{{ route('heroes') }}" 
           class="{{ request()->routeIs('heroes') ? 'bg-[#700B1A] text-white shadow-sm' : 'text-gray-600 hover:text-[#700B1A] hover:bg-[#FCECEE]' }} px-2 sm:px-2.5 xl:px-3 py-1.5 rounded-full transition whitespace-nowrap">
            LIST HERO
        </a>
        <a href="{{ route('hero.statistics') }}" 
           class="{{ request()->routeIs('hero.statistics') ? 'bg-[#700B1A] text-white shadow-sm' : 'text-gray-600 hover:text-[#700B1A] hover:bg-[#FCECEE]' }} px-2 sm:px-2.5 xl:px-3 py-1.5 rounded-full transition whitespace-nowrap">
            STATISTIK HERO
        </a>
        <a href="{{ route('rules') }}" 
           class="{{ request()->routeIs('rules') ? 'bg-[#700B1A] text-white shadow-sm' : 'text-gray-600 hover:text-[#700B1A] hover:bg-[#FCECEE]' }} px-2 sm:px-2.5 xl:px-3 py-1.5 rounded-full transition whitespace-nowrap"
           title="Knowledge Base & Rules Inspector RBR untuk Dosen Penguji">
            RULES RBR
        </a>
    </nav>

    <!-- Admin Login / Console Action (Strictly Sejajar di sisi kanan) -->
    <div class="flex items-center flex-shrink-0">
        @if(session('admin_logged_in'))
            <div class="flex items-center gap-1.5 flex-nowrap whitespace-nowrap">
                <a href="{{ route('admin.dashboard') }}" class="bg-[#FAF8F8] hover:bg-[#FCECEE] text-[#700B1A] border border-[#F8B4BD] text-[10px] sm:text-[11px] font-extrabold uppercase tracking-wider px-2.5 sm:px-3 py-1.5 rounded-full flex items-center gap-1.5 transition whitespace-nowrap">
                    <span class="w-1.5 h-1.5 bg-[#700B1A] rounded-full"></span>
                    <span>ADMIN CONSOLE</span>
                </a>
                <a href="{{ route('admin.logout') }}" class="bg-[#FAF8F8] hover:bg-[#FCECEE] text-gray-700 hover:text-[#700B1A] border border-[#E5E7EB] text-[10px] sm:text-[11px] font-extrabold uppercase tracking-wider px-2 sm:px-2.5 py-1.5 rounded-full transition whitespace-nowrap">
                    LOGOUT
                </a>
            </div>
        @else
            <a href="{{ route('login') }}" class="bg-[#FAF8F8] hover:bg-[#FCECEE] text-gray-800 hover:text-[#700B1A] border border-[#E5E7EB] text-[10px] sm:text-[11px] font-extrabold uppercase tracking-wider px-2.5 sm:px-3.5 py-1.5 rounded-full flex items-center gap-1.5 transition whitespace-nowrap shadow-2xs">
                <span class="w-1.5 h-1.5 bg-gray-500 rounded-xs"></span>
                <span>LOGIN ADMIN</span>
            </a>
        @endif
    </div>
</header>
