<x-app-layout title="Algoland - Create a new collectible">

    <script>
        function assetForm() {
            return {
                form: {
                    transaction: '',
                    name: '',
                    description: '',
                    collectible: null,
                    minting: false,
                },

                error: {
                    hasError: false,
                    errorMessage: '',
                },

                /**
                 * Create a new NFT (Non-Fungible Token).
                 * First we request the transaction fields from the API.
                 * Once we got the fields, request AlgoSigner to sign the tx.
                 * Send the tx back to the server to mint the token.
                 */
                async createNFT(event) {
                    const provider = @json(Session::get('provider'));

                    console.log(provider);
                    this.error.hasError = false;
                    this.error.errorMessage = '';

                    axios.interceptors.request.use(request => {
                        console.log('Starting Request', JSON.stringify(request, null, 2))
                        return request
                    })

                    axios.interceptors.response.use(response => {
                        console.log('Response:', JSON.stringify(response, null, 2))
                        return response
                    })

                    try {
                        const signingManager = new SigningManager();
                        signingManager.addProvider(new AlgolandProvider());
                        signingManager.addProvider(new AlgoSignerProvider());
                        signingManager.addProvider(new MyAlgoProvider());

                        // Sign the transaction
                        const response = await signingManager.getProvider(provider).sign(this.form);

                        this.form.minting = false;
                        window.location.href = '{{ route('collection.index') }}';
                    } catch (error) {
                        this.form.minting = false;
                        this.error.hasError = true;
                        this.error.errorMessage = error;

                        if (error.response) {
                            // Request made and server responded
                            console.log(error.response.data);
                            console.log(error.response.status);
                            console.log(error.response.headers);
                        } else if (error.request) {
                            // The request was made but no response was received
                            console.log(error.request);
                        } else {
                            // Something happened in setting up the request that triggered an Error
                            console.log('Error', error);
                        }
                    }
                },
            }
        }
    </script>

    <div class="max-w-3xl mx-auto px-4 py-10" x-data="assetForm()">
        <form action="{{ route('asa.create') }}" method="POST" @submit.prevent="createNFT">
            @csrf
            <h2 class="text-2xl font-bold text-black font-sans">Create a new collectible</h2>

            <!-- Upload Form -->
            <div class="border-dotted border-2 border-primary-dark text-center mt-6 p-8 space-y-4 relative">
                <input class="absolute inset-0 z-50 m-0 p-0 w-full h-full outline-none opacity-0" type="file"
                       x-on:change="form.collectible = $event.target.files[0]; console.log(form.collectible);"
                       x-on:dragover="$el.classList.add('active')"
                       x-on:dragleave="$el.classList.remove('active')"
                       x-on:drop="$el.classList.remove('active')"
                >

                <template x-if="form.collectible === null">
                    <div>
                        <div class="font-bold">JPEG, PNG, or GIF. Max 10mb.</div>

                        <!-- Draw the upload button -->
                        <div class="btn inline-block mt-4">
                            Upload
                        </div>
                    </div>

                </template>

                <template x-if="form.collectible !== null">
                    <div class="flex flex-row items-center space-x-2 justify-center">
                        <span class="font-medium text-gray-900" x-text="form.collectible.name">Uploading</span>
                        <span class="text-xs self-end text-gray-500" x-text="form.collectible.size">...</span>
                    </div>
                </template>

            </div>

            <!-- Form fields -->
            <div class="my-5 space-y-2">
                <h3 class="font-bold">Name</h3>
                <input type="text"
                       name="name"
                       placeholder="e.g. Tweet of Elon Musk"
                       class="input w-3/4 max-h-80"
                       x-model="form.name">
            </div>

            <div class="my-5 space-y-2">
                <h3 class="font-bold">Description</h3>
                <textarea class="input w-3/4 h-32 max-h-80"
                          name="description"
                          placeholder="e.g. Tesla's can now be bought with Algorand"
                          x-model="form.description"></textarea>
            </div>

            <!-- Create item button -->
            <button class="btn mt-6 inline-block focus:outline-none">Create item</button>

        </form>

        <!-- Loading modal -->
        <div class="absolute top-0 left-0 flex items-center justify-center w-full h-full" style="background-color: rgba(0,0,0,.5);" x-show="form.minting" x-cloak>

            <div class="h-auto p-4 mx-2 text-left bg-white rounded shadow-xl md:max-w-xl md:p-6 lg:p-8 md:mx-0">
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">
                        Forging your NFT
                    </h3>

                    <div class="mt-2">
                        <p class="text-sm leading-5 text-gray-500">
                            Our miners are working hard forging your token. Please standby...
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error modal -->
        <div class="absolute top-0 left-0 flex items-center justify-center w-full h-full" style="background-color: rgba(0,0,0,.5);" x-show="error.hasError" x-cloak>

            <div class="h-auto p-4 mx-2 text-left bg-white rounded shadow-xl md:max-w-xl md:p-6 lg:p-8 md:mx-0" @click.away="error.hasError = false">
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg font-medium leading-6 text-gray-900" >
                        An error occured when minting your NFT.
                    </h3>

                    <div class="mt-2">
                        <p class="text-sm leading-5 text-gray-500" x-text="error.errorMessage">
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>

</x-app-layout>
