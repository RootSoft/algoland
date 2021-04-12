<x-app-layout title="Algoland - Create, sell or collect digital items">

    <script>
        function importWallet() {
            return {
                providers: {
                    create: false,
                    import: false,
                    algosigner: false,
                    myalgo: false,
                },
                open(provider) {
                    this.providers[provider] = true;
                },
                close(provider) {
                    this.providers[provider] = false;
                },
                async signInWithAlgoSigner() {
                    // Check if AlgoSigner is installed
                    const installed = await connectWithAlgoSigner();

                    if (!installed)
                        return;

                    // Fetch the first account
                    const accounts = await AlgoSigner.accounts({ ledger: 'TestNet' });
                    const formData = {
                        provider: 'algosigner',
                        address: accounts[0].address,
                    };

                    return axios.post('/signin', formData).then((response) => {
                        window.location.href = "{{ route('wallet.index')}}";
                    }).catch((error) => {
                        console.log(error.response);
                    });
                },
                async signInWithMyAlgo() {
                    try {
                        const accounts = await myAlgoWallet.connect();

                        const formData = {
                            provider: 'myalgo',
                            address: accounts[0].address,
                        };

                        return axios.post('/signin', formData).then((response) => {
                            window.location.href = "{{ route('wallet.index')}}";
                        }).catch((error) => {
                            console.log(error.response);
                        });
                    } catch (err) {
                        console.error(err);
                    }
                }
            }
        }
    </script>

    <div class="max-w-lg mx-auto px-4 pt-8" x-data="importWallet()">
        <h3 class="text-gray-600 font-bold text-center text-2xl">You need an Algorand wallet to continue. Get started now!</h3>

        <div class="grid grid-cols-2 grid-rows-2 grid-cols-2 gap-4 text-center pt-8">
            <div class="rounded border border-gray-400 p-4 flex flex-col text-center justify-center items-center space-y-4 hover:text-primary cursor-pointer" x-on:click="open('create')" >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>

                <span class="text-xs">Create a new Algorand account</span>
            </div>

            <div class="rounded border border-gray-400 p-4 flex flex-col text-center justify-center items-center space-y-4 hover:text-primary cursor-pointer" x-on:click="open('import')">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13l-3 3m0 0l-3-3m3 3V8m0 13a9 9 0 110-18 9 9 0 010 18z" />
                </svg>

                <span class="text-xs">Import an existing account</span>
            </div>

            <div class="rounded border border-gray-400 p-4 text-center items-center hover:text-primary cursor-pointer" x-on:click="signInWithAlgoSigner()">
                <img src="/images/icon_algosigner.jpeg" alt="" class="flex-1 mx-auto">

                <span class="inline-block text-xs">Simply create or import your Algorand account and approve or deny transactions — all from within your browser.</span>
            </div>

            <div class="rounded border border-gray-400 p-4 flex flex-col text-center justify-center items-center space-y-4 hover:text-primary cursor-pointer" x-on:click="signInWithMyAlgo()">
                <img src="https://wallet.myalgo.com/images/MyAlgoBlue.svg" alt="" class="w-24 h-24 mx-auto">

                <span class="inline-block text-xs">Securely sign transactions with My Algo Connect</span>
            </div>
        </div>

        <livewire:create-wallet-modal/>
        <livewire:import-wallet-modal/>

    </div>

</x-app-layout>
