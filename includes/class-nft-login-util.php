<?php

class Nft_Login_Util
{

    const ETHEREUM_CHAIN_ID = '0x1';
    const POLYGON_CHAIN_ID = '0x89';

    public function __construct()
    {
    }


    public function chain_id_to_name ($chain_id) {
        if ($chain_id == Nft_Login_Util::ETHEREUM_CHAIN_ID) {
            return 'Ethereum';
        } else if ($chain_id == Nft_Login_Util::POLYGON_CHAIN_ID) {
            return 'Polygon';
        }
    }

    public function chain_id_to_scan_url ($chain_id) {
        if ($chain_id == Nft_Login_Util::ETHEREUM_CHAIN_ID) {
            return 'https://etherscan.io/token/';
        } else if ($chain_id == Nft_Login_Util::POLYGON_CHAIN_ID) {
            return 'https://polygonscan.com/token/';
        }
    }

    public function verify_contract_exists($address, $chain_id)
    {
        if ($chain_id == Nft_Login_Util::ETHEREUM_CHAIN_ID) {
            $node = 'https://cloudflare-eth.com';
        } else if ($chain_id == Nft_Login_Util::POLYGON_CHAIN_ID) {
            $node = 'https://polygon-rpc.com';
        }

        $response = wp_remote_post($node, array(
            'headers' => array('Content-Type' => 'application/json'),
            'body' => json_encode(array('jsonrpc' => '2.0',
                'method' => 'eth_getCode',
                'id' => 1,
                'params' => array($address, "latest")
            )),
            'method' => 'POST',
            'data_format' => 'body',
        ));

        $body = json_decode($response['body']);

        if (isset($body->result) && $body->result != '0x' ) {
            return true;
        }

        return false;
    }
}