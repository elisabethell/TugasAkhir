{{-- resources/views/components/footer.blade.php --}}
<footer class="border-t border-[#F3E8E8] pt-6 pb-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-semibold text-gray-500 mt-8">
    <div class="flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-[#700B1A]"></span>
        <span class="font-extrabold text-[#18181B] tracking-wider uppercase">METASCOUT : LAND OF DAWN</span>
        <span class="text-gray-300">•</span>
        <span class="text-gray-400">ALL TIMESTAMPS IN UTC+7</span>
    </div>
    <div class="flex items-center gap-6 uppercase tracking-wider text-[11px]">
        <a href="{{ route('rules') }}" class="hover:text-[#700B1A] transition text-gray-600 font-bold">API DOCUMENTATION</a>
        <span class="text-[#700B1A] font-extrabold">ELLOIS KARINA HANDOYO</span>
    </div>
</footer>
