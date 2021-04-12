<?php

namespace App\Http\Livewire;

use Algorand;
use App\Http\Controllers\WalletController;
use App\Services\WalletService;
use Livewire\Component;

class CreateWalletModal extends Component
{

    /**
     * Create a new Algorand
     * @param WalletService $walletService
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Rootsoft\Algorand\Exceptions\MnemonicException
     * @throws \Rootsoft\Algorand\Exceptions\WordListException
     */
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
