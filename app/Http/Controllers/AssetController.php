<?php

namespace App\Http\Controllers;

use Algorand;
use App\AlgorandProvider;
use App\Http\Requests\CreateAssetRequest;
use App\Http\Requests\TransactionFieldsRequest;
use App\Models\Asset;
use Brick\Math\BigInteger;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

use Illuminate\Http\Response;
use IPFS;
use ParagonIE\ConstantTime\Base64;
use Rootsoft\Algorand\Exceptions\AlgorandException;
use Rootsoft\Algorand\Models\Accounts\Address;
use Rootsoft\Algorand\Models\Transactions\Assets\AssetConfigTransaction;
use Rootsoft\Algorand\Models\Transactions\Builders\TransactionBuilder;
use Session;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View|RedirectResponse
     */
    public function create()
    {
        // Check if we have a current provider
        if (!request()->session()->has('provider')) {
            return redirect()->route('wallet.install');
        }

        return view('pages.asa.create-asset');
    }

    /**
     * Get the transaction fields for the given provider.
     *
     * @param TransactionFieldsRequest $request
     * @return array|false|string
     * @throws AlgorandException
     * @throws \SodiumException
     */
    public function getTransactionFields(TransactionFieldsRequest $request) {
        $provider = Session::get('provider');
        $address = Session::get('address');

        $cid = $this->calculateCID($request);

        // Create the transaction
        $transaction = $this->buildAssetTransaction($request->name, $request->description ?? "", $cid, $address);

        // Transform the transaction for AlgoSigner compatibility
        $fields = $this->toProviderFormat($provider, $transaction);

        // Send the transaction to the user, to sign
        return json_encode(['cid' => $cid, 'fields' => $fields]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return Application|ResponseFactory|View|RedirectResponse|Response
     * @throws AlgorandException
     */
    public function store(CreateAssetRequest $request)
    {
        $provider = Session::get('provider');
        if ($provider == null)
            return redirect()->route('wallet.install');

        $collectible = $request->file('collectible');
        $fileName = $collectible->getFilename();

        // Add the file to IPFS
        $fileHash = IPFS::add($collectible->get(), $fileName, ['pin' => true])['Hash'];

        if ($provider == AlgorandProvider::ALGOSIGNER() || $provider == AlgorandProvider::MYALGO()) {
            // Compare generated CID with uploaded CID
            if ($request->cid != $fileHash) {
                throw new AlgorandException("CID's are not equal.");
            }

            // Decode transaction blob
            $signedTx = Base64::decode($request->transaction);
        } else {
            $account = Algorand::accountManager()->restoreAccount(Session::get('seedphrase', []));
            $transaction = $this->buildAssetTransaction($request->name, $request->description ?? "", $fileHash, $account->getPublicAddress());

            $signedTx = $transaction->sign($account);
        }

        // Broadcast transaction to network
        $pendingTx = Algorand::sendTransaction($signedTx, true);

        if ($pendingTx->assetIndex == null)
            throw new AlgorandException();

        // Return 200
        return response()->noContent();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Create a new asset transaction.
     *
     * @param string $name
     * @param string $description
     * @param string $cid
     * @param string $address
     * @return AssetConfigTransaction
     * @throws AlgorandException
     * @throws \SodiumException
     */
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

    public function toProviderFormat(string $provider, AssetConfigTransaction $transaction)
    {
        return [
            'from'=> $transaction->sender->encodedAddress,
            'assetName' => $transaction->assetName,
            'assetUnitName' => $transaction->unitName,
            'assetTotal' => $transaction->total->toInt(),
            'assetDecimals' => $transaction->decimals,
            'type' => $transaction->type,
            'note' => $provider == AlgorandProvider::MYALGO() ? Base64::encode($transaction->note) : $transaction->note,
            'fee' => $transaction->getFee()->toInt(),
            'flatFee' => $transaction->getFee()->toInt(),
            'firstRound' => $transaction->firstValid->toInt(),
            'lastRound' => $transaction->lastValid->toInt(),
            'genesisID' => $transaction->genesisId,
            'genesisHash' => $transaction->genesisHash,
        ];
    }

    /**
     * Calculate the CID of the uploaded file.
     *
     * @param Request $request
     * @param bool $onlyFile
     * @return string|null The CID of the metadata.
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Rootsoft\IPFS\Exceptions\IPFSException
     */
    private function calculateCID(Request $request) {
        if (!$request->has('collectible'))
            return null;

        $collectible = $request->file('collectible');
        $fileName = $collectible->getFilename();

        // Get the CID of the file
        return IPFS::add($collectible->get(), $fileName, ['only-hash' => true])['Hash'];
    }
}
