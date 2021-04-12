<?php

namespace App\Http\Livewire;

use Algorand;
use App\Services\WalletService;
use Exception;
use Livewire\Component;

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
