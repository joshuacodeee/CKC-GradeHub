{{-- Global confirmation modal --}}
<div x-data x-show="$store.confirm.open" x-cloak x-transition.opacity @keydown.escape.window="$store.confirm.cancel()"
    class="fixed inset-0 z-[90] flex items-center justify-center
           bg-slate-950/50 p-4 backdrop-blur-sm"
    role="dialog" aria-modal="true">
    <div x-show="$store.confirm.open" x-transition:enter="transition duration-200 ease-out"
        x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100"
        x-transition:leave="transition duration-150 ease-in" x-transition:leave-start="scale-100 opacity-100"
        x-transition:leave-end="scale-95 opacity-0" @click.outside="$store.confirm.cancel()"
        class="w-full max-w-sm overflow-hidden rounded-2xl border
               border-slate-200 bg-white shadow-2xl shadow-slate-950/20
               dark:border-gray-700 dark:bg-gray-800 dark:shadow-black/40">
        {{-- CKC accent --}}
        <div class="h-1 bg-gradient-to-r from-ckc-blue to-ckc-gold"></div>

        <div class="px-6 pb-6 pt-7 text-center">
            {{-- Centered icon --}}
            <div
                class="mx-auto mb-4 flex h-14 w-14 items-center justify-center
                       rounded-2xl bg-ckc-blue/10 text-ckc-blue
                       ring-1 ring-ckc-blue/10
                       dark:bg-ckc-gold/10 dark:text-ckc-gold
                       dark:ring-ckc-gold/10">

            </div>

            {{-- Content --}}
            <h3 class="font-serif text-xl font-bold text-ckc-blue
                       dark:text-white"
                x-text="$store.confirm.title"></h3>

            <p class="mx-auto mt-2 max-w-xs text-sm leading-6
                       text-slate-500 dark:text-gray-400"
                x-text="$store.confirm.message"></p>

            {{-- Actions --}}
            <div class="mt-6 flex items-center justify-center gap-3">
                <button type="button" @click="$store.confirm.cancel()"
                    class="rounded-xl border border-slate-200 bg-white
                           px-4 py-2.5 text-sm font-semibold text-slate-600
                           transition hover:border-slate-300 hover:bg-slate-50
                           focus:outline-none focus:ring-4
                           focus:ring-slate-500/10
                           dark:border-gray-600 dark:bg-gray-800
                           dark:text-gray-300 dark:hover:bg-gray-700">
                    Cancel
                </button>

                <button type="button" @click="$store.confirm.resolve()"
                    class="group inline-flex items-center justify-center gap-2
                           rounded-xl bg-ckc-blue px-4 py-2.5
                           text-sm font-semibold text-white shadow-sm
                           transition hover:bg-ckc-blue-dark hover:shadow-md
                           focus:outline-none focus:ring-4
                           focus:ring-ckc-blue/20
                           dark:bg-ckc-gold dark:text-ckc-blue
                           dark:hover:bg-amber-400">


                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>
