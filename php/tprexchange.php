<?php

namespace ccxt;

// PLEASE DO NOT EDIT THIS FILE, IT IS GENERATED AND WILL BE OVERWRITTEN:
// https://github.com/ccxt/ccxt/blob/master/CONTRIBUTING.md#how-to-contribute-code

use Exception; // a common import
use \ccxt\ExchangeError;

class tprexchange extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'tprexchange',
            'name' => 'TPR Exchange',
            // 'countries' => array( 'US' ),
            // 'rateLimit' => 500,
            'version' => 'v1',
            'certified' => false,
            'has' => array(
                'loadMarkets' => false,
                'cancelAllOrders' => false,
                'cancelOrder' => true,
                'cancelOrders' => false,
                'CORS' => false,
                'createDepositAddress' => false,
                'createLimitOrder' => false,
                'createMarketOrder' => false,
                'createOrder' => true,
                'deposit' => false,
                'editOrder' => 'emulated',
                'fetchBalance' => false,
                'fetchBidsAsks' => false,
                'fetchClosedOrders' => false,
                'fetchCurrencies' => false,
                'fetchDepositAddress' => false,
                'fetchDeposits' => false,
                'fetchFundingFees' => false,
                'fetchL2OrderBook' => false,
                'fetchLedger' => false,
                'fetchMarkets' => true,
                'fetchMyTrades' => false,
                'fetchOHLCV' => 'emulated',
                'fetchOpenOrders' => false,
                'fetchOrder' => true,
                'fetchOrderBook' => false,
                'fetchOrderBooks' => false,
                'fetchOrders' => true,
                'fetchOrderTrades' => false,
                'fetchStatus' => 'emulated',
                'fetchTicker' => false,
                'fetchTickers' => false,
                'fetchTime' => false,
                'fetchTrades' => false,
                'fetchTradingFee' => false,
                'fetchTradingFees' => false,
                'fetchTradingLimits' => false,
                'fetchTransactions' => false,
                'fetchWithdrawals' => false,
                'privateAPI' => true,
                'publicAPI' => false,
                'signIn' => true,
                'withdraw' => false,
            ),
            'timeframes' => array(
                '1m' => '1',
                '5m' => '5',
                '15m' => '15',
                '1h' => '60',
                '4h' => '240',
                '1d' => '1440',
                '1w' => '10080',
            ),
            'urls' => array(
                'logo' => '',
                'api' => '{hostname}',
                'www' => '',
                'doc' => '',
                'fees' => '',
                'referral' => '',
            ),
            'api' => array(
                'private' => array(
                    'get' => array(
                        'detail/detail/{id}',
                        'order/history',
                        'order/add',
                    ),
                    'post' => array(
                        'uc/api-login',
                    ),
                    'delete' => array(
                        'order/cancel/{id}',
                    ),
                ),
                'feed' => array(
                    'get' => array(
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                ),
            ),
            'requiredCredentials' => array(
                'apiKey' => true,
                'secret' => true,
                'uid' => false,
            ),
            'precisionMode' => SIGNIFICANT_DIGITS,
            'options' => array(
                'createMarketBuyOrderRequiresPrice' => false,
            ),
            'exceptions' => array(
                'exact' => array(
                    'Invalid cost' => '\\ccxt\\InvalidOrder', // array("message":"Invalid cost","_links":array("self":array("href":"/orders","templated":false)))
                    'Invalid order ID' => '\\ccxt\\InvalidOrder', // array("message":"Invalid order ID","_links":array("self":array("href":"/orders/4a151805-d594-4a96-9d64-e3984f2441f7","templated":false)))
                    'Invalid market !' => '\\ccxt\\BadSymbol', // array("message":"Invalid market !","_links":array("self":array("href":"/markets/300/order-book","templated":false)))
                ),
                'broad' => array(
                    'Failed to convert argument' => '\\ccxt\\BadRequest',
                ),
            ),
        ));
    }

    public function fetch_markets($params = array ()) {
        return array(
            array(
                'id' => 'TPR',
                'symbol' => 'TPR/USD',
                'base' => 'TPR',
                'quote' => 'USD',
                'baseId' => 'TPR',
                'quoteId' => 'USD',
                'type' => 'spot',
                'active' => true,
                'precision' => array(
                    'amount' => null,
                    'price' => null,
                ),
                'limits' => array(
                    'amount' => array( 'min' => null, 'max' => null ),
                    'price' => array( 'min' => null, 'max' => null ),
                    'cost' => array( 'min' => null, 'max' => null ),
                ),
                'taker' => '0.005',
                'maker' => '0.0025',
                'info' => 'TPR Market',
            ),
        );
    }

    public function fetch_ohlcv($symbol, $timeframe = '1m', $since = null, $limit = 1000, $params = array ()) {
        return array();
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        // Check existance of authentication token
        // Just use empy one in case of an application is not $signed in yet
        $authToken = '';
        if (is_array($this->options) && array_key_exists('token', $this->options)) {
            $authToken = $this->options['token'];
        }
        // Get URL
        $url = $this->implode_params($this->urls['api'], array( 'hostname' => $this->hostname )) . '/' . $path;
        // Calculate $body and content type depending on $method type => GET or POST
        $keys = is_array($params) ? array_keys($params) : array();
        $keysLength = is_array($keys) ? count($keys) : 0;
        // In case of $body is still not assigned just make it empty string
        if ($body === null) {
            $body = '';
        }
        // Prepare line for hashing
        // This hash sum is checked on backend side to verify API user
        // POST $params should not be added as $body
        $query = $method . ' /' . $path . ' ' . $this->urlencode($params) . ' ' . $authToken . '\n' . $body;
        $signed = $this->hmac($this->encode($query), $this->encode($this->secret));
        $contentType = '';
        if ($method === 'POST') {
            $contentType = 'application/x-www-form-urlencoded';
            if ($keysLength > 0) {
                $body = $this->urlencode($params);
            }
        } else {
            if ($keysLength > 0) {
                $url .= '?' . $this->urlencode($params);
            }
        }
        $headers = array(
            'x-auth-token' => $authToken,
            'x-auth-sign' => $signed,
            'Content-Type' => $contentType,
        );
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function sign_in($params = array ()) {
        $params = array(
            'key' => $this->key,
            'token' => $this->token,
        );
        $response = $this->privatePostUcApiLogin ($params);
        $authToken = $this->safe_string($response, 'message');
        $this->options['token'] = $authToken;
        return $authToken;
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        $request = array(
            'id' => $id,
        );
        $response = $this->privateGetOrdersId (array_merge($request, $params));
        return $this->parse_order($response);
    }

    public function parse_order($order, $market = null) {
        return array( );
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $request = array( );
        $query = '';
        $response = $this->privatePostOrders (array_merge($request, $query));
        return $this->parse_order($response);
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $request = array(
            'id' => $id,
        );
        return $this->privateDeleteOrdersId (array_merge($request, $params));
    }

    public function handle_errors($httpCode, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if ($response === null) {
            return; // fallback to default error handler
        }
        if ($httpCode === 200) {
            return;
        }
        // {
        //     "$message" => "Error text in case when HTTP code is not 200",
        //     ...
        // }
        $message = $this->safe_string($response, 'message');
        if ($message !== null) {
            $feedback = $this->id . ' ' . $body;
            $this->throw_exactly_matched_exception($this->exceptions['exact'], $message, $feedback);
            $this->throw_broadly_matched_exception($this->exceptions['broad'], $message, $feedback);
            throw new ExchangeError($feedback); // unknown $message
        }
    }
}
