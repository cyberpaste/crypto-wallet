# Info

A small service to get a balance from a connected exchange Binance (https://www.binance.com/).
Main user case of use:
on the page the user sees the current balance of coins from the stock exchange and the "Update" button;
pressing the “Update” button pulls up the current balance from the exchange;
As a balance, a list of coins with a non-zero balance should be displayed 
in a table with the columns “coin ticker” (short coin name) -> residue (1 BTC, 0.5 ETH, 0.0025 XRP);

![preview](https://raw.githubusercontent.com/cyberpaste/crypto-wallet/master/preview.png)

## Install

0) Docker-compose + yii2 standart install
1) run command ``` composer update  ```
2) run command ``` yii migrate ```
3) login admin password admin
4) add data to account /user/edit
5) try service

## Add new market

1) Create migration (tables markets, user_params)
2) Create new api helper
```
namespace common\helpers;

use common\interfaces\ApiInterface;

class DummyApi implements ApiInterface {

    public function addUserParams(array $params) {

    }

    public function validate() {

    }

    public function getBalance() {

    }

}

```
3) Create common\api\DummiApi logic
4) Edit new Api Fields
