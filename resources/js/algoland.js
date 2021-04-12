window.connectWithAlgoSigner = async function() {
    // Check if AlgoSigner is installed
    if (!isAlgoSignerInstalled()) {
        console.log('AlgoSigner is not installed');
        return false;
    }

    return AlgoSigner.connect().then((d) => {
        console.log('Connected with AlgoSigner')
        return true;
    }).catch((e) => {
        console.log('Unable to connect with AlgoSigner', e);
        return false;
    });
}

window.isAlgoSignerInstalled = function () {
    return typeof AlgoSigner !== 'undefined';
}

window.isAlgoSignedConnected = async function () {
    return AlgoSigner.connect().then((d) => {
        return true;
    }).catch((e) => {
        return false;
    });
}
