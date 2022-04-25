=== NFT Login ===
Contributors: Dave Hagler
Donate link: 
Tags: login,authentication,web3,nft
Requires at least: 5.0
Tested up to: 5.9
Stable tag: 1.2.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Use NFT's to register and login to your wordpress site.

== Description ==

NFT Login requires users to verify NFT ownership in order to register and login to your site.

Use this plugin to create exclusive content for any NFT collection. Make a discussion board or e-commerce site just for CryptoPunks
or Bored Ape Yacht Club holders or any other NFT collection. 

Features
* Require NFT verification to register or login to your site
* Lock content on specific posts or pages until the user verifies NFT

== Installation ==

1. Install and activate the plugin
1. Go to NFT Login Plugin settings page in the Wordpress admin dashboard
1. Enter the Token Name and NFT Contract Address 

== Screenshots ==
1. Verify NFT ownership step added to registration and login pages
2. Error if trying to register or login without first verifying NFT
3. Verify button prompts user to connect wallet
4. Verified NFT owner can now login
5. Lock content in the post metadata
6. Locked content displays in list

== Frequently Asked Questions ==

= Which NFT collections can be used? =

Any NFT collection on Ethereum or Polygon. All you need is the public contract address which is easily findable on sites like [Etherscan](https://etherscan.io/) or [OpenSea](https://opensea.io/)

= Do I need to own an NFT from the collection? =

No. All you need is the contract address. WP admin users can login as normal without verifying ownership.

= What is a contract address? =
All NFT collections have a smart contract which created the items in the collection. The contract address is the blockchain address of the smart contract.

= How do I find the contract address for a NFT collection? =

Search for the collection on [OpenSea](https://opensea.io/). Select any item from the collection. For example [CryptoPunk #8666](https://opensea.io/assets/0xb47e3cd837ddf8e4c57f05d70ab865de6e193bbb/8666).
Expand the details section to find the Contract Address for CryptoPunks is 0xb47e3cd837dDF8e4c57F05d70Ab865de6e193BBB.

= How do I lock content =

Check the box under NFT Content Protection in the post/page metadata

= Can you add a new feature to this plugin? =
Yes! Add a support topic or contact me.


== Changelog ==

= 1.0.0 =
Initial release

= 1.1.0 =
Content locking
Configuration option for registration and login

= 1.1.1 =
Fix bug in configuration for registration and login

= 1.2.0 =
Support for Polygon blockchain

= 1.2.1 =
Bug fix missing function

= 1.2.2 =
Php 8 bug fix

= 1.2.3 =
Removed "Verified NFT does not match registered user" check

= 1.2.4 =
Fix compatibility with non ERC721 tokens