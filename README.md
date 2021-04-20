## Introduction
Laravel is a web application framework with an expressive, elegant syntax designed to make developing web apps easier and faster through built-in features. Laravel strives to provide an amazing developer experience, while providing powerful features such as thorough dependency injection, an expressive database abstraction layer, queues and scheduled jobs, unit and integration testing, and more.

In our solution, we will be using the TALL stack which is a full-stack development solution which combines a set of tools as a way of easily and quickly building applications. The TALL stack consists of the following:

* [Tailwind CSS](https://tailwindcss.com/) - A utility-first CSS framework
* [AlpineJS](https://github.com/alpinejs/alpine) - A minimal frontend development framework for adding JavaScript behavior to HTML markups
* [Laravel](https://laravel.com/docs/8.x)
* [Livewire](https://laravel-livewire.com/) - Building dynamic interfaces simple, without leaving the comfort of Laravel.

Note that this guide also applies to anyone who wishes to uses another front-end framework like [Vue](https://vuejs.org/) & [React](https://reactjs.org/) while using any PHP framework as your back-end. [Inertia.js](https://inertiajs.com) allows you to create fully client-side rendered, single-page apps, without much of the complexity that comes with modern SPAs. It does this by leveraging existing server-side frameworks, like Laravel and Rails.

Before we get started, I highly recommend you to read [this excellent article](https://developer.algorand.org/articles/building-nfts-on-algorand/) by Jason Weathersby on the different approaches NFTs can be implemented on the Algorand blockchain. We will be focusing on the primary method to build and deploy an NFT which is by using the ASA (Algorand Standard Assets), a layer 1 primitive that allows an NFT to be created in seconds.

![Create collectible](https://i.imgur.com/068JQ8C.png)

## Requirements
1. PHPStorm (or another PHP-supported IDE)
2. PHP 7.4 (>=)
3. [Docker Desktop](https://docs.docker.com/desktop/) (when using Laravel Sail)
4. [IPFS Desktop](https://docs.ipfs.io/install/ipfs-desktop/)
4. (optional) A [PureStake](../../tutorials/getting-started-purestake-api-service/) Account, and the corresponding API key OR a [locally hosted node](../../docs/run-a-node/setup/install/)

## Setting up our development environment
If it's your first time working with Laravel, I recommend you to go through the [Getting Started](https://laravel.com/docs/8.x/installation) section of the Laravel documentation to learn more about the different features and installation methods Laravel has to offer.

Laravel offers a couple of local development environments like [Laravel Sail](https://laravel.com/docs/8.x/sail), [Homestead](https://laravel.com/docs/8.x/homestead), or [Valet](https://laravel.com/docs/8.x/valet) that provides you a wonderful development environment without requiring you to install PHP, a web server, and any other server software on your local machine.
We are using the latest version of Laravel (at the time of writing 8.0), so we will be using the recommend approach, which is **Laravel Sail**.

!!!Sail
Laravel Sail is a light-weight command-line interface for interacting with Laravel's default Docker development environment and provides a great starting point for building Laravel applications using PHP, MySQL, and Redis without requiring prior Docker experience. **Make sure Docker is installed before using Laravel Sail.**

Let's get started by cloning the project from [Github](https://github.com/RootSoft/algorand-php).
Once the project has been cloned, open it with your preferred IDE and open **3 new command prompts.**

First we need to install all of our dependencies. None of the application's Composer dependencies, including Sail, will be installed after you clone the application's repository to your local computer. You may install the application's dependencies by navigating to the application's directory and executing the following command.

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/opt \
    -w /opt \
    laravelsail/php80-composer:latest \
    composer install --ignore-platform-reqs
```

This command uses a small Docker container containing PHP and Composer to install the application's dependencies:

In the first terminal window, use the ```sail up``` command to start Laravel Sail. This will execute your application within a Docker container and is isolated from your local computer.

```bash
./vendor/bin/sail up
```

![Terminal 1](https://i.imgur.com/WIWp3mC.png)

In our second terminal, we will use the ```npm run watch``` command. The npm run watch command will continue running in your terminal and watch all relevant CSS and JavaScript files for changes. Webpack will automatically recompile your assets when it detects a change to one of these files:

```bash
npm install
npm run watch
```

![Terminal 2](https://i.imgur.com/aqXfDWJ.png)

A new browser tab will open and you will be able to use the application. If you receive an error, make sure your dependencies are up to date (see the next step).

!!!BrowserSync
[BrowserSync](https://browsersync.io/) is an automation tool that makes web development faster. It makes tweaking and testing faster by synchronizing file changes and interactions across many devices.

Our last terminal is used to perform [Composer](https://getcomposer.org/) & [Artisan](https://laravel.com/docs/8.x/artisan) console commands.

Let's do that and update our dependencies:

```bash
sail composer update
```

![Terminal 3](https://i.imgur.com/jVOG9ko.png)

## Installation

If you wish to start from scratch, you will have to install the [Algorand-PHP SDK](https://github.com/RootSoft/algorand-php) in your project. We can do this by using the following command:

```bash
sail composer require rootsoft/algorand-php
```

This will install the sdk and all required dependencies in your project.
For Laravel developers, I highly recommend to publish the configuration file so we can use the Algorand facade. Facades provide a "static" interface to classes that are available in the application's service container.

```bash
sail artisan vendor:publish --provider="Rootsoft\Algorand\AlgorandServiceProvider" --tag="config"
```

This will create an ```algorand.php``` file in your ```config``` directory. Open the file and update endpoints for Algod and Indexer to your preferred service.

!!!note
In this solution we are using Rand Labs [AlgoExplorer's API](https://algoexplorer.io/api-dev/v2). At the time of writing, the note-prefix is disabled in PureStake's API. You can also host your own node or use the [sandbox](https://developer.algorand.org/articles/introducing-sandbox-20/) for development & testing on a private network.

Once that's been done, you can use the ```Algorand``` facade to easily perform Algorand related operations.

```php
<?php
Algorand::sendPayment($account, $recipient, Algo::toMicroAlgos(10), 'Hi');
```

## Account management

Before we can get started creating our Non-Fungible Token, we need to have an Algorand account in order to approve, authorize and sign transactions.

Accounts are entities on the Algorand blockchain associated with specific onchain data, like a balance. An Algorand Address is the identifier for an Algorand account. Creating an account means creating an Algorand cryptocurrency address that is managed by your mobile wallet, hence an account can be seen as your wallet.

Creating a new account or importing an existing account manages the account on the server, just like a custodial wallet. A custodial wallet is defined as a wallet in which the private keys are held by a third party (in this case, our application), meaning, we have a full control over your funds while you only have to give permission to send or receive payments. We can reverse these roles and give the dApp user full control over all transactions using [AlgoSigner](https://chrome.google.com/webstore/detail/algosigner/kmmolakhbgdlpkjkcjkebenjheonagdm) and [MyAlgo Connect](https://wallet.myalgo.com/access).

![wallet](https://i.imgur.com/RBZGLhh.png)

### Create a new account
Creating a new account is really easy using the Algorand-PHP sdk. Since we published the ```algorand.php``` configuration file and have access to our Facade, we can create a new account using ```Algorand::accountManager()->createNewAccount();```.

```php
<?php
class CreateWalletModal extends Component
{
    public function createWallet(WalletService $walletService) {
        // Create a new account
        $account = Algorand::accountManager()->createNewAccount();

        // Store information in encrypted session
        $walletService->storeAccount('algoland', $account->getPublicAddress(), $account->getSeedPhrase()->words);

        // Navigate back
        return redirect()->route('wallet.index');
    }

    public function render()
    {
        return view('livewire.create-wallet-modal');
    }
}
```
```html
<div class="text-center">
    <button class="btn mx-auto text-center" wire:click="createWallet()">Create account</button>
</div>
```

In our solution, we use a [Livewire component](https://laravel-livewire.com/docs/2.x/making-components) to show a dialog with a button wired to the ```createWallet()``` method. There we create our new account using the Algorand Facade and store the account information in a [Session](https://laravel.com/docs/8.x/session). Once the account is created and our session is prepared, we redirect the user to the index page of our wallet.

!!!Caution
In a production environment, you would **never store your seed or seedphrase in a Session** and expose it to the front-end. In this solution we are doing this for ease of use and demonstration purposes. Use a dedicated secure file management system.

### Import an existing account
We can also import an existing account by entering our 25-word passphrase into our application using the ```restoreAccount()``` method.

![Import account](https://i.imgur.com/QNjuAzO.png)


```php
<?php
class ImportWalletModal extends Component
{

    public $seedphrase;

    public function importWallet(WalletService $walletService)
    {
        try {
            // Restore account from seedphrase
            $account = Algorand::accountManager()->restoreAccount($this->seedphrase);

            // Store information in encrypted session
            $walletService->storeAccount('algoland', $account->getPublicAddress(), $account->getSeedPhrase()->words);

            // Navigate back
            redirect()->route('wallet.index');
        } catch (Exception $ex) {
            session()->flash('errorMessage', $ex->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.import-wallet-modal');
    }
}
```
```HTML
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
```


Once again, we are using a Livewire component and [two-way binding](https://stackoverflow.com/questions/13504906/what-is-two-way-binding) to bind our HTML input to the php variable ```$seedphrase```. Once we click the **Import account** button, the ```restoreAccount()``` method restores your account and gets stored in the session off the application.

### Connect with AlgoSigner

[AlgoSigner](https://chrome.google.com/webstore/detail/algosigner/kmmolakhbgdlpkjkcjkebenjheonagdm) is a blockchain wallet that makes it easy to use Algorand-based applications on the web, while you are still in control of your keys. Simply create or import your Algorand account, visit a compatible dApp, and approve or deny transactions — all from within your browser.

When installing the AlgoSigner extension from the Chrome Web Store, the Javascript is automatically injected in every webpage you visit, so it's not required to install any SDK or package into your application.


![AlgoSigner Connect](https://i.imgur.com/gmrML2z.png)

```js
async signInWithAlgoSigner() {
    // Check if AlgoSigner is installed
    if (!isAlgoSignerInstalled()) {
        console.log('AlgoSigner is not installed');
        return false;
    }
    
    // Check if AlgoSigner is connected
    const connected = await AlgoSigner.connect();

    if (!connected)
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
```
```php
<?php
public function signIn(ConnectWalletRequest $request) {
    // Store the account in the session
    $this->walletService->storeAccount($request->provider, $request->address);

    // Redirect to the installation page
    return redirect()->route('wallet.index');
}
```

In ```install-wallet.blade.php```, we check if AlgoSigner extension is available and installed in the browser. If it's installed we use the ```connect()``` method which will show a dialog in order to give permissions for our dApp. Once permission has been giving, we fetch the first address in our accounts array and send an HTTP Post request to the ```signin``` endpoint of our server. This information is then stored in the Session for our user. Note that we provide a ```provider``` variable which is used to determine on how we signed in (algoland/our application, algosigner or myalgo).

### Connect with MyAlgo Connect
[MyAlgo Connect](../..articles/introducing-myalgo-connect/) allows WebApp users to review and sign Algorand transactions using accounts secured within their [MyAlgo Wallet](https://myalgo.com/). This enables Algorand applications to use MyAlgo Wallet to interact with the Algorand blockchain and users to access the applications in a private and secure manner.

The main novelty of MyAlgo Connect lies in the fact that all the process is managed in the user’s browser without the need for any backend service nor downloads, extensions or browser plugins.

!!!installation
For MyAlgo connect, you need to install the SDK, which can be done through NPM
``` npm install @randlabs/myalgo-connect```

![MyAlgo Connect](https://i.imgur.com/JDkCWNh.png)
```js
async signInWithMyAlgo() {
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
}
```

### Funding our account

In order to register our new account on the Algorand ledger, we need to have a minimum balance of atleast 0.1 Algo.
Therefore, a transaction that sends funds to a new account (i.e. an account with a 0 balance) must send a minimum of 100,000 microAlgos for the transaction to be valid. Similarly, any sending account must make sure that the amount of algos that they send will keep their remaining balance greater than or equal to 100,000 microAlgos.

In order to create our account onchain, we can use the [Algorand dispenser](https://bank.testnet.algorand.network/) on TestNet to fund our newly created account with some Algo. Just enter your address (using ```$account->getPublicAddress()```) in the target address field and click Dispense. After a couple of seconds, you will see your transaction on [AlgoExplorer](https://testnet.algoexplorer.io/transactions).

### Account information

![account information](https://i.imgur.com/DayYssU.png)

Now that we have our account connected to our application, we can use the public methods of the Algorand-php SDK to fetch all information about a given Algorand address.

``` Algorand::accountManager()->getAccountInformation($address);``` let us fetch all public information about an account including the balance, pending rewards, created assets & applications, and so much more. In our index method ,we pass all relevant information back to our view so it can easily be displayed on our webpage.

```php
<?php
public function index()
{
    // Check if we have a current provider
    if (!request()->session()->has('provider')) {
        return redirect()->route('wallet.install');
    }

    $address = Session::get('address');
    $accountInformation = Algorand::accountManager()->getAccountInformation($address);

    return view('pages.wallet.index-wallet', [
        'provider' => Session::get('provider'),
        'address' => $address,
        'seedphrase' => Session::get('seedphrase'),
        'balance' => $accountInformation->amountWithoutPendingRewards,
    ]);
}
```
```html
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
```

!!!note
There is also a nice utility class, ```Algo``` to easily convert and format your microAlgos to Algos.

## Create a collectible

NFTs are tokens that are unique and cannot be replaced with something else. Because of this, it’s perfect for digital collectibles, art, luxury goods and all sorts of other physical and digital products that can be verified on the blockchain. An example of an NFT could be the Mona Lisa. Even though someone can make a copy of it, there will always only be one Mona Lisa.

![Create collectible](https://i.imgur.com/068JQ8C.png)

Quoting Jason Weathersby article, [Building NFTs on Algorand](../../articles/building-nfts-on-algorand/):

> The primary method a developer or Algorand user can use to build and deploy an NFT is by using the ASA feature. This feature is a layer 1 primitive that allows an NFT or FT to be created in seconds. These assets require no smart contract code. It only takes one transaction on the Algorand blockchain to create an NFT.

> The required parameters are the Creator field which is automatically populated by the sender of the initial creation transaction, the Total number of the asset which represents a unit count for NFTs this should be set to 1, implied Decimals which allow each unit of an FT to be subdivided, and DefaultFrozen which specifies whether the token can be traded by default.


Creating an NFT on the Algorand blockchain is really easy, and you can actually create an NFT with one line of code using the Algorand-php SDK:

```php
Algorand::assetManager()->createNewAsset($account, 'Mona Lisa', 'Mona Lisa', 1,0);
```

This lets us create an Algorand Standard Asset on the Algorand blockchain with the asset name **Mona Lisa** and unit name **Mona Lisa**. The total amount of Mona Lisa's to create is **1** and it cannot be divisible, hence the decimal field is **0**. Now you might think, I just copy this code 5 times and create 5 Mona Lisa's? That’s basically true but what makes this asset unique is that the Algorand network generated **a unique assetId** for your NFT when your transaction was confirmed by the network, which in many collector’s and artists their minds the art holds value because of its unique ID on the blockchain. Similar to holding a physical (1/1) edition of a painting.

Now for our solution, we can only use the one-liner above for our server managed accounts, but since we want to also use AlgoSigner and MyAlgo Connect to sign our transactions we need to look for a different approach.

### IPFS
When smart contracts and NFTs were being created, people quickly realized that it's really expensive to deploy a lot of data to the blockchain and since having creative art means you have to store this information somewhere. We could host the data ourselves on our own server using the excellent [Media Library](https://spatie.be/docs/laravel-medialibrary/v9/introduction) by [Spatie](https://spatie.be/), but this will break the decentralized approach that we are trying to achieve. Another solution would be storing our data off-chain using a decentralized file system like IPFS.

[IPFS](https://ipfs.io/) (Interplanetary File System) is a versioned file system which can store files and track version changes over time, similar to Git. In addition to storing files, IPFS acts as a distributed file system, much like BitTorrent. IPFS uses a content-addressed protocol to transfer content. This is done using a cryptographic hash on a file as the address.

**A content identifier, or CID**, is a label used to point to material in IPFS. It doesn't indicate where the content is stored, but it forms a kind of address based on the content itself. CIDs are short, regardless of the size of their underlying content.

For illustration purposes, here’s how a cat file looks on HTTP and IPFS:

* An HTTP request would look like this: http://1.2.3.4/folder/cat.jpg
* An IPFS request would look like this: /ipfs/Qm8xJy7sj8xKJ/folder/cat.jpg

**Installation**

Let's start by installing [IPFS Desktop](https://docs.ipfs.io/install/ipfs-desktop/). IPFS Desktop bundles an IPFS node, file manager, peer manager, and content explorer into a single, easy to use application.

Open IPFS Desktop and wait until you are connected with the IPFS network.

![IPFS Desktop](https://docs.ipfs.io/assets/img/desktop-status.059adf67.png)

If your IPFS node is running, inspect the ```ipfs.php``` file in the ```config``` folder. This file is published from the [Laravel-IPFS](https://github.com/RootSoft/laravel-ipfs) service provider. In the github sample, the connection with our sail instance is already provided.

!!!note
Addresses using TCP port 4001 are known as "swarm addresses" that your local daemon will listen on for connections from other IPFS peers. Make sure to bind to another port if you are using the Sandbox environment

** IPFS Pinning Service **

When an IPFS node retrieves data from the network it keeps a local cache of that data for future usage, taking up space on that particular IPFS node. IPFS nodes frequently clear this cache out in order to make room for new content. If your node is not online, you might also not able to serve content

But, what happens if you want to make sure that certain content will never be deleted? The act of saving data on an IPFS node is often referred to as “pinning”. An IPFS pinning service is an IPFS node or collection of IPFS nodes dedicated to pinning other peoples’ and apps’ content on IPFS.

IPFS pinning services have IPFS nodes that are always online. Because these nodes are usually cloud hosted, they act as a reliable way of keeping your data available even if your own IPFS node isn’t always online. This allows you and your users to access your content anywhere at any time, regardless of device.

[Pinata](https://pinata.cloud/) is an example of an IPFS Pinning Service and provides secure and verifiable files for your NFTs. Whether you are a platform, creator, or collector, simply upload through Pinata, copy your IPFS CID, and attach to your NFT before minting to have secure and verifiable NFT files.

!!!note
If you want to know more about IPFS, check out [this great article](https://blog.fleek.co/posts/Guide-IPFS).

### Adding metadata

People also also wanted a lightweight way to store attributes about an NFT – and this is where the metadata come into play.
Metadata describes how our NFT looks like and where we can find our content which represents our NFT.

Luckily Algorand provides [several options](https://developer.algorand.org/docs/reference/transactions/) to add metadata to an ASA:

* url - URL where more information about the asset can be retrieved. Max size is 32 bytes.
* metadatahash - 32-byte hash of some metadata that is relevant to your asset
* note - any data up to 1000 bytes

Since our CID alone is bigger then 32 bytes, we will have to use the note field to store our metadata.
The advantage of using the note is that we can also use the Indexer to find collectibles using the ```note-prefix``` for our application, which will come in handy later.

In our application, our metadata is encoded in JSON and looks like this
```json
{
	"application": "algoland",
	"name": "Laravel",
	"description": "The PHP framework for web artisans",
	"image": "https:\/\/ipfs.io\/ipfs\/QmYMkVDNUDk51oQeqdGRhsV9Zdsn532RZt3nLjjbU5SAjq"
}
```

!!!note
If you need to store more then 1000bytes of data, you can both store the metadata and file on IPFS and link the CID of the metadata in the note-field of the ASA. ASA -> Metadata -> File.


### Get the transaction fields

Both AlgoSigner & MyAlgo Connect SDK's have an option to sign a transaction. Our first step would be to fetch the transaction fields from our server. On the Javascript front-end, we use a [strategy pattern](https://en.wikipedia.org/wiki/Strategy_pattern) to fetch the correct transaction fields and sign accordingly.

```php
<?php
public function getTransactionFields(TransactionFieldsRequest $request) {
    $provider = Session::get('provider');
    $address = Session::get('address');

    // Calculate the IPFS CID
    $cid = $this->calculateCID($request);

    // Create the transaction
    $transaction = $this->buildAssetTransaction($request->name, $request->description ?? "", $cid, $address);

    // Transform the transaction for compatibility
    $fields = $this->toProviderFormat($provider, $transaction);

    // Send the transaction to the user, to sign
    return json_encode(['cid' => $cid, 'fields' => $fields]);
}
```
```js
const signingManager = new SigningManager();
signingManager.addProvider(new AlgolandProvider());
signingManager.addProvider(new AlgoSignerProvider());
signingManager.addProvider(new MyAlgoProvider());

// Sign the transaction
const response = await signingManager.getProvider(provider).sign(this.form);
```

We start by calculating the CID of the uploaded file, which can be done with the laravel-ipfs package:

```php
<?php
$collectible = $request->file('collectible');
$fileName = $collectible->getFilename();

// Get the CID of the file
return IPFS::add($collectible->get(), $fileName, ['only-hash' => true])['Hash'];
```

This will return the CID without storing the file on the server. Once we have our CID, we will have to create our metadata, add the data from the front-end (name, description) and build the transaction. Pay attention how the metadata is encoded in the note field.

```php
<?php
public function buildAssetTransaction(string $name, string $description, string $cid, string $address)
{
    // Metadata
    $metadata = [
        'application' => 'algoland',
        'name' => $name,
        'description' => $description,
        'image' => "https://ipfs.io/ipfs/$cid",
    ];

    // Create the transaction
    return TransactionBuilder::assetConfig()
        ->assetName($name)
        ->unitName('NFT')
        ->totalAssetsToCreate(BigInteger::of(1))
        ->decimals(0)
        ->sender(Address::fromAlgorandAddress($address))
        ->note(json_encode($metadata))
        ->useSuggestedParams(Algorand::client())
        ->build();
}
```

At last, we transform the transaction for the correct provider so both AlgoSigner and MyAlgo Connect know how to handle these transaction fields.

```php
<?php
public function toProviderFormat(string $provider, AssetConfigTransaction $transaction)
{
    return [
        'from'=> $transaction->sender->encodedAddress,
        'assetName' => $transaction->assetName,
        'assetUnitName' => $transaction->unitName,
        'assetTotal' => $transaction->total->toInt(),
        'assetDecimals' => $transaction->decimals,
        'type' => $transaction->type,
        'note' => $transaction->note,
        'fee' => $transaction->getFee()->toInt(),
        'flatFee' => $transaction->getFee()->toInt(),
        'firstRound' => $transaction->firstValid->toInt(),
        'lastRound' => $transaction->lastValid->toInt(),
        'genesisID' => $transaction->genesisId,
        'genesisHash' => $transaction->genesisHash,
    ];
}
```

### Sign the transaction

#### AlgoSigner

![Sign transaction with AlgoSigner](https://i.imgur.com/8HkfXjR.png)

```js
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
```

After we got the transaction fields, we pass them to ```AlgoSigner.sign(transaction.fields);``` which will popup a dialog asking to sign the transaction. Once the transaction has been approved, we send the signed transaction back to our server.


#### MyAlgo Connect

![Sign transaction with MyAlgo Connect](https://i.imgur.com/YWd8tZ5.png)

```js
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
```

After we got the transaction fields, we pass them to ```myAlgoWallet.signTransaction(transaction.fields);``` which will popup a dialog asking to sign the transaction. Once the transaction has been approved, we send the signed transaction back to our server. Note that the signTransaction returns the signed transaction as a Uint8Array, so we base64 encode it before sending to the server.


### Mint our collectible

![Minting NFT](https://i.imgur.com/oaQtLO1.png)

```php
<?php
public function store(CreateAssetRequest $request)
{
    $provider = Session::get('provider');
    if ($provider == null)
        return redirect()->route('wallet.install');

    $collectible = $request->file('collectible');
    $fileName = $collectible->getFilename();

    // Add the file to IPFS
    $fileHash = IPFS::add($collectible->get(), $fileName, ['pin' => true])['Hash'];

    // Compare generated CID with uploaded CID
    if ($request->cid != $fileHash) {
        throw new AlgorandException("CID's are not equal.");
    }

    // Decode transaction blob
    $signedTx = Base64::decode($request->transaction);
    
    // Broadcast transaction to network
    $pendingTx = Algorand::sendTransaction($signedTx, true);

    if ($pendingTx->assetIndex == null)
        throw new AlgorandException();
    
    return response()->noContent();
}
```

When we receive the signed transaction, the first thing we do is validating all fields with a [Form Request](https://laravel.com/docs/8.x/validation#form-request-validation) which makes sure all data uploaded is valid. In our case, the request should contain a valid image file of max 10mb, the base64-encoded transaction and the name of the NFT.

If our request is valid, we add our file to IPFS and return the CID. We have to make sure that the returned CID is the same as the one used in the transaction fields, to not have inconsistent data.

After adding our file to IPFS, we decode the base64 signed transaction and broadcast the transaction on the network using ```Algorand::sendTransaction($signedTx, true);```. The ```true``` parameters indicates that we want to [wait until the transaction has been confirmed](https://developer.algorand.org/docs/build-apps/hello_world/#wait-for-confirmation) by the network. When setting the ```waitForConfirmation'``` to ```true```, the ```sendTransaction``` method will return a ```PendingTransaction``` which enables us to act on potential failures and returns us the **generated asset id**. If we didn't encounter any problems, we return a 200 and redirect the user to his collection of NFTs.

## My collection

My collection shows a collection of all non-fungible tokens you've created using our platform, Algoland. It uses the Indexer to query the blockchain, making it really easy to search the ledger in a fluent api and enables application developers to perform rich and efficient queries on accounts, transactions, assets, and so forth.

![My collection](https://i.imgur.com/Zve6Ddi.png)

```php
<?php
public function index()
{
    // Check if we have a current provider
    if (!request()->session()->has('provider')) {
        return redirect()->route('wallet.install');
    }

    $address = Session::get('address');

    // Find all assets
    $accountInformation = Algorand::accountManager()->getAccountInformation($address);
    $holdings = collect($accountInformation->assets);

    // Find all asset config transactions for our application.
    $transactions = Algorand::indexer()
        ->transactions()
        ->whereNotePrefix('{"application":"algoland"')
        ->afterMinRound(13387750)
        ->whereAddress(Address::fromAlgorandAddress($address))
        ->whereTransactionType(TransactionType::ASSET_CONFIG())
        ->search();

    $transactions = collect($transactions->transactions);

    // Filter out all assets for our own application
    $assets = $holdings->keyBy('assetId')->only($transactions->keyBy('createdAssetIndex')->keys())->sort()->reverse();

    $collectibles = collect();
    foreach ($assets as $asset) {
        $transaction = $this->findTransactionForAsset($asset, $transactions);
        if ($transaction == null)
            continue;

        $collectibles->add(NoteParser::parseNoteB64($transaction->note));
    }

    return view('pages.collection.index-collection', [
        'collectibles' => $collectibles,
    ]);
}
```
```html
<div class="max-w-6xl mx-auto px-4 p-8">
    <h3 class="text-gray-600 font-bold text-2xl">My collection</h3>

    <!-- A grid of collectibles -->
    <div class="grid grid-cols-4 gap-8 mt-6">
        @foreach ($collectibles as $collectible)
            <x-collectible-card :collectible="$collectible"/>
        @endforeach
    </div>
</div>
```

We use the ```Algorand::accountManager()->getAccountInformation($address);``` again to fetch all information about our linked account. The ```$accountInformation->assets``` will return all of our Algorand Standard Assets that we own, these included every asset (NFT & FT) that you have in you wallet (also the ones that were not creating by our application). Once we got all of our assets, we use the indexer to find all **asset configuration transactions** that were created by **our account**, after a **min-round** to speed up the indexer where our **note field** starts with the json string ```{"application":"algoland"```.

Once we got all of asset holdings and asset configuration transactions, we filter out all of the assets and only keep the one we are interested in (i.e. the NFT's created by our application). Next, we iterate over all filtered assets, parse the base64-encoded note fields to a list of ```Collectible```s and return all collectibles to our view, which gets displayed in a grid.

## Explore

The Explore page is based on the My Collection page but shows all assets that were created by our application, without filtering through or own assets.
```php
<?php
public function index()
{
    // Find all asset config transactions for our application.
    $transactions = Algorand::indexer()
        ->transactions()
        ->whereNotePrefix('{"application":"algoland"')
        ->afterMinRound(13387750)
        ->whereTransactionType(TransactionType::ASSET_CONFIG())
        ->search();

    $transactions = collect($transactions->transactions)->reverse();

    $collectibles = $transactions->map(function ($transaction) {
        return NoteParser::parseNoteB64($transaction->note);
    });

    return view('pages.explore.index-explore', [
        'collectibles' => $collectibles,
    ]);
}
```
```html
<div class="max-w-6xl mx-auto px-4 p-8">
    <h3 class="text-gray-600 font-bold text-2xl">Explore digital items</h3>

    <!-- A grid of collectibles -->
    <div class="grid grid-cols-4 gap-8 mt-6">
        @foreach ($collectibles as $collectible)
            <x-collectible-card :collectible="$collectible"/>
        @endforeach
    </div>
</div>
```

## What's next
- Use a [IPFS Pinning Service](https://pinata.cloud/) to keep your content online
- Create a decentralized marketplace using [ASC1](https://developer.algorand.org/docs/features/asc1/)
- Managing [NFT royalties](../../solutions/algorealm-nft-royalty-game/)

## Conclusion
Laravel attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as authentication, routing, sessions, and caching. The incredible features of Laravel has made it the [most loved PHP framework](https://insights.stackoverflow.com/survey/2020) of 2020. Apart from this, extensive community support is provided for its users, which makes it approachable and understandable by all its users. Laravel is scalable and helps in software delivery in a fast and cost-effective manner. Bundle Laravel with the raw power of Algorand and create amazing decentralized web applications!
