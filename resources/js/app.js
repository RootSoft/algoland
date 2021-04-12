import '@kingshott/iodine';
import '@ryangjchandler/spruce'
import 'alpinejs';
import MyAlgo from '@randlabs/myalgo-connect';
import * as base64 from "byte-base64";
import { SigningManager, AlgolandProvider, AlgoSignerProvider, MyAlgoProvider } from './signer';

require('./bootstrap');
require('./nav.js');
require('./algoland.js');

window.myAlgoWallet = new MyAlgo();
window.SigningManager = SigningManager;
window.AlgolandProvider = AlgolandProvider;
window.AlgoSignerProvider = AlgoSignerProvider;
window.MyAlgoProvider = MyAlgoProvider;
window.base64 = base64;


