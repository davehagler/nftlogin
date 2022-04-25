var NFTLOGIN = (function () {
    var nftlogin = {};

    var tokenABI =
        [
            {
                "inputs": [{"internalType": "address", "name": "owner", "type": "address"}],
                "name": "balanceOf",
                "outputs": [{"internalType": "uint256", "name": "", "type": "uint256"}],
                "type": "function"
            },
            {
                "inputs": [],
                "name": "totalSupply",
                "outputs": [
                    {
                        "internalType": "uint256",
                        "name": "",
                        "type": "uint256"
                    }
                ],
                "stateMutability": "view",
                "type": "function",
                "constant": true
            },
            {
                "inputs": [
                    {
                        "internalType": "uint256",
                        "name": "tokenId",
                        "type": "uint256"
                    }
                ],
                "name": "ownerOf",
                "outputs": [
                    {
                        "internalType": "address",
                        "name": "",
                        "type": "address"
                    }
                ],
                "stateMutability": "view",
                "type": "function",
                "constant": true
            },
            {
                "inputs":[
                    {
                        "internalType":"address",
                        "name":"owner",
                        "type":"address"
                    },
                    {
                        "internalType":"uint256",
                        "name":"index",
                        "type":"uint256"
                    }
                ],
                "name":"tokenOfOwnerByIndex",
                "outputs":[
                    {
                        "internalType":"uint256",
                        "name":"",
                        "type":"uint256"
                    }
                ],
                "stateMutability":"view",
                "type":"function"
            }
        ]

    var statusElem = document.getElementById('nftlogin_status');

    function set_status(color, message) {
        if (color) {
            statusElem.style.color = color;
        }
        statusElem.innerHTML = message;
    }

    nftlogin.connect_wallet = async function nftlogin_connect_wallet() {
        const Web3Modal = window.Web3Modal.default;
        const WalletConnectProvider = window.WalletConnectProvider.default;

        const providerOptions = {
            /* See Provider Options Section */
            walletconnect: {
                package: WalletConnectProvider, // required
                options: {
                    infuraId: "5901345316dc4c3eaa66ac2c45f8a25f" // required
                }
            }
        };

        const web3Modal = new Web3Modal({
            providerOptions
        });

        try {
            provider = await web3Modal.connect();
            return provider;
        } catch(e) {
            console.log("Unable to get a wallet connection", e);
            set_status('red', 'Unable to get a wallet connection')
            return false;
        }
    }

    nftlogin.connect_and_verify = async function nftlogin_connect_and_verify(addressOfContract, submitForm, chainId, chainName) {

        var connectedProvider = await this.connect_wallet();

        if (!connectedProvider) {
            return;
        }

        if (Web3.givenProvider == null) {
            set_status('red', 'Provider is null. Do you have a crypto wallet installed?');
            return;
        }
        
        var web3 = new Web3(Web3.givenProvider);

        if (web3.currentProvider.chainId !== chainId) {
            set_status('red', 'Wallet is not connected to ' + chainName + ' network');
            return;
        }

        const tokenInst = new web3.eth.Contract(tokenABI, addressOfContract);

        set_status(null,'');

        var statusElem = document.getElementById('nftlogin_status');
        statusElem.innerHTML = '';
        var addressElem = document.getElementById('nftlogin_address');
        addressElem.value = '';
        var tokenIdElem = document.getElementById('nftlogin_token_id');
        tokenIdElem.value = '';

        window.ethereum.request({method: 'eth_requestAccounts'})
            .then((accounts) => {
                tokenInst.methods.balanceOf(accounts[0]).call()
                    .then(tokenBalance => {
                        if (tokenBalance > 0) {
                            addressElem.value = accounts[0];
                            tokenInst.methods.tokenOfOwnerByIndex(accounts[0], 0).call()
                                .then(tokenId => {
                                    tokenIdElem.value = tokenId;
                                    set_status('green', 'Verified owner of token '+tokenId);
                                })
                                .catch(e=> console.log('Error in tokenOfOwnerByIndex ' + e))
                                .finally(()=> {
                                    if(submitForm) {
                                        document.getElementById(submitForm).submit();
                                        return;
                                    }
                                })
                        } else {
                            set_status('red', 'Connected address does not own token');
                        }
                    })
                    .catch(err => {
                        console.log('Error in balanceOf '+ err);
                    });
            });
    }

    return nftlogin;
}());
