{{-- resources/views/components/footer.blade.php --}}
<footer class="border-t border-[#F3E8E8] pt-6 pb-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-semibold text-gray-500 mt-8">
    <div class="flex items-center gap-2 flex-wrap">
        <span class="w-2 h-2 rounded-full bg-[#700B1A]"></span>
        <span class="font-extrabold text-[#18181B] tracking-wider uppercase">METASCOUT : LAND OF DAWN</span>
        <span class="text-gray-300">•</span>
        <span class="text-gray-400">ALL TIMESTAMPS IN UTC+7</span>
        <span class="text-gray-300 hidden sm:inline">•</span>
        <a href="https://liquipedia.net/mobilelegends" target="_blank" rel="noopener noreferrer" 
           class="inline-flex items-center gap-1.5 text-[#0A285F] hover:text-[#0072CE] font-bold transition">
            <img src="/images/liquipedia_logo.svg" alt="Liquipedia Logo" class="w-3.5 h-3.5 object-contain">
            <span class="tracking-wider uppercase text-[11px]">DATA BERSUMBER DARI LIQUIPEDIA MLBB</span>
        </a>
    </div>
    <div class="flex items-center gap-6 uppercase tracking-wider text-[11px]">
        <a href="{{ route('rules') }}" class="hover:text-[#700B1A] transition text-gray-600 font-bold">API DOCUMENTATION</a>
        <span class="text-[#700B1A] font-extrabold">ELLOIS KARINA HANDOYO</span>
    </div>
</footer>
