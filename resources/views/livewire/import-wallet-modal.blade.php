<div>
    <div x-show="providers.import" x-cloak wire:ignore.self>
        <div class="fixed inset-0 bg-gray-900 opacity-90"></div>

        <div class="bg-white shadow-md p-4 max-w-3xl max-h-64 m-auto rounded-md fixed inset-0 mx-auto center" @click.away="close('import')">
            <header class="text-center">
                <h3 class="font-bold text-lg">Import an existing account</h3>
            </header>

            <div class="text-center m-8">
                <p>This will override your current account. Make sure you have backed up your seedphrase.</p>
            </div>

            <form wire:submit.prevent="importWallet()" class="text-center">
                <input type="text"
                       class="border p-2 border-gray-400 w-full rounded-lg text-sm  transition duration-150 ease-in-out focus:outline-none "
                       placeholder="Your 25-word passphrase"
                       wire:model="seedphrase"/>

                @if (session()->has('errorMessage'))
                    <div class="text-red-500">
                        {{ session('errorMessage') }}
                    </div>
                @endif

                <button type="submit" class="btn mx-auto text-center mt-4">Import account</button>
            </form>
        </div>
    </div>
</div>



