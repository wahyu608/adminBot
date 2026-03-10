<x-filament-panels::page>
     <div class="flex items-center justify-center min-h-[60vh]">
        <div class="text-center max-w-md space-y-4">

            <h2 class="text-2xl font-bold">
                Email Belum Diverifikasi
            </h2>

            <p class="text-gray-500">
                Silakan verifikasi email Anda terlebih dahulu sebelum menggunakan sistem.
                Periksa inbox atau folder spam.
            </p>

            <x-filament::button wire:click="resend">
                Kirim Email Verifikasi
            </x-filament::button>

        </div>
    </div>
</x-filament-panels::page>
