class SigningManager {
    constructor() {
        this._providers = [];
    }

    addProvider(provider) {
        this._providers = [...this._providers, provider];
    }

    getProvider(name) {
        return this._providers.find(provider => provider._name === name);
    }
}

class Provider {
    constructor(name, handler) {
        this._name = name;
        this._handler = handler;
    }

    sign(formData) {
        this._handler(formData);
    }

    /**
     * Request the asset create transaction to be signed by AlgoSigner
     */
    getTransactionFields(form) {
        const formData = new FormData();
        formData.append('name', form.name);
        formData.append('description', form.description);
        formData.append('collectible', form.collectible);
        formData.append('provider', this._name);

        return axios.post('/asa/transaction/fields', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        }).then((response) => {
            return response.data;
        });
    }

    /**
     * Upload the NFT and broadcast the signed transaction on the network.
     */
    sendSignedTransaction(form, signedTransaction, cid) {
        const formData = new FormData();
        formData.append('name', form.name);
        formData.append('description', form.description);
        formData.append('collectible', form.collectible);
        formData.append('cid', cid);
        formData.append('transaction', signedTransaction);

        return axios.post('/asa/transaction', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        }).then((response) => {
            return response.data;
        });
    }
}

class AlgolandProvider extends Provider {
    constructor() {
        super('algoland', () => {});
    }

    async sign(form) {
        form.minting = true;

        return await this.sendSignedTransaction(form);
    }
}

class AlgoSignerProvider extends Provider {
    constructor() {
        super('algosigner', () => {});
    }

    async sign(form) {
        console.log('signing with algosigner');

        // Get the transaction fields for AlgoSigner
        const transaction = await this.getTransactionFields(form);
        console.log(transaction);

        // Sign the transaction
        const signedTx = await AlgoSigner.connect()
            .then((result) => {
                return AlgoSigner.sign(transaction.fields);
            });

        form.minting = true;

        return await this.sendSignedTransaction(form, signedTx.blob, transaction.cid);
    }
}

class MyAlgoProvider extends Provider {

    constructor() {
        super('myalgo', () => {});
    }

    async sign(form) {
        console.log('signing with my algo connect');

        // Get the transaction fields for MyAlgo
        const transaction = await this.getTransactionFields(form);
        console.log(transaction);

        // Connect to myAlgo
        const accounts = await myAlgoWallet.connect();

        // Sign the transaction
        const signedTx = await myAlgoWallet.signTransaction(transaction.fields);
        const blob = base64.bytesToBase64(signedTx.blob);

        console.log(blob);
        form.minting = true;

        return await this.sendSignedTransaction(form, blob, transaction.cid);
    }
}


export { SigningManager, AlgolandProvider, AlgoSignerProvider, MyAlgoProvider };
