<?php

namespace App\Http\Controllers;

use Algorand;
use App\Http\Requests\ConnectWalletRequest;
use App\Http\Requests\CreateAssetRequest;
use App\Services\WalletService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Session;

class WalletController extends Controller
{
    private WalletService $walletService;

    /**
     * WalletController constructor.
     * @param WalletService $walletService
     */
    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
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
        $accountInformation = Algorand::accountManager()->getAccountInformation($address);

        return view('pages.wallet.index-wallet', [
            'provider' => Session::get('provider'),
            'address' => $address,
            'seedphrase' => Session::get('seedphrase'),
            'balance' => $accountInformation->amountWithoutPendingRewards,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create()
    {
        return view('pages.wallet.install-wallet');
    }

    public function logout() {
        // Clear the session
        Session::flush();

        // Redirect to the installation page
        return redirect()->route('wallet.install');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateAssetRequest $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function signIn(ConnectWalletRequest $request) {
        // Store the account in the session
        $this->walletService->storeAccount($request->provider, $request->address);

        // Redirect to the installation page
        return redirect()->route('wallet.index');
    }

}
