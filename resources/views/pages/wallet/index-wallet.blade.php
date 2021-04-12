<x-app-layout title="Algoland - Create, sell or collect digital items">

    <script>
    </script>

    <div class="max-w-lg mx-auto px-4 pt-8">
        <h3 class="text-gray-600 font-bold text-center text-2xl">Welcome to Algoland</h3>

        <div class="mt-6 space-y-4">
            <div>
                <p class="text-gray-600 text-center text-base font-bold">Signed in with</p>
                <p class="text-gray-600 text-center text-sm">{{ $provider }}</p>
            </div>

            <div>
                <p class="text-gray-600 text-center text-base font-bold">Your public address</p>
                <p class="text-gray-600 text-center text-sm">{{ $address }}</p>
            </div>

            <div>
                <p class="text-gray-600 text-center text-base font-bold">Balance</p>
                <p class="text-gray-600 text-center text-sm">{{ \Rootsoft\Algorand\Utils\Algo::fromMicroAlgos($balance) }} Algos</p>
            </div>

            @if($seedphrase)
                <div>
                    <p class="text-gray-600 text-center text-base font-bold">Word list</p>
                    <p class="text-gray-600 text-center text-sm">{{ implode(' ', $seedphrase)}}</p>
                </div>
            @endif
        </div>

        <div class="text-center mt-6">
            <a class="btn" href="{{ route('wallet.logout') }}">Sign out</a>
        </div>

    </div>

</x-app-layout>
