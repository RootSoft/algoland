<div>
    <div x-show="providers.create" x-cloak>
        <div class="fixed inset-0 bg-gray-900 opacity-90" x-cloak></div>

        <div class="bg-white shadow-md p-4 max-w-3xl max-h-60 m-auto rounded-md fixed inset-0 mx-auto center" @click.away="close('create')">
            <header class="text-center">
                <h3 class="font-bold text-lg">Create a new account</h3>
            </header>

            <div class="text-center m-8">
                <p>Are you sure you want to create a new account? This will override your current account. Make sure you have backed up your seedphrase.</p>
            </div>

            <div class="text-center">
                <button class="btn mx-auto text-center" wire:click="createWallet()">Create account</button>
            </div>
        </div>
    </div>
</div>

