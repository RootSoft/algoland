<?php

namespace App\Http\Controllers;

use Algorand;
use App\Domain\Collectible;
use App\Services\WalletService;
use App\Utils\NoteParser;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use ParagonIE\ConstantTime\Base64;
use Rootsoft\Algorand\Models\Accounts\Address;
use Rootsoft\Algorand\Models\AssetHolding;
use Rootsoft\Algorand\Models\Transactions\AssetConfigTransactionResult;
use Rootsoft\Algorand\Models\Transactions\Transaction;
use Rootsoft\Algorand\Models\Transactions\TransactionType;
use Session;

class CollectionController extends Controller
{

    /**
     * WalletController constructor.
     * @param WalletService $walletService
     */
    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|RedirectResponse
     */
    public function index()
    {
        // Check if we have a current provider
        if (!request()->session()->has('provider')) {
            return redirect()->route('wallet.install');
        }

        $address = Session::get('address');

        // Find all of my asset.
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

    /**
     * @param AssetHolding $asset
     * @param Collection $transactions
     * @return Transaction|null
     */
    public function findTransactionForAsset(AssetHolding $asset, Collection $transactions) {
        return $transactions->first(function (Transaction $transaction) use ($asset) {
            return $transaction->createdAssetIndex != null && $transaction->createdAssetIndex == $asset->assetId;
        });
    }
}
