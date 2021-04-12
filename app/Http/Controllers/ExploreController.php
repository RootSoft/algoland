<?php

namespace App\Http\Controllers;

use Algorand;
use App\Utils\NoteParser;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Rootsoft\Algorand\Models\Transactions\TransactionType;

class ExploreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|RedirectResponse
     */
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
}
