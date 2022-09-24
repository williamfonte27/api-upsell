<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class ExchangeRatesController extends Controller
{
    private $client;
    private $log;
    private $appId;
    private $baseUrl;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->client = new Client();
        $this->appId = env('API_APP_KEY');
        $this->baseUrl = env('API_BASE_URL');

        $this->log = new Logger('exchangeRates');
        $this->log->pushHandler(new StreamHandler(storage_path("logs/exchangeRates.log")));
    }

    /**
     * @OA\Get(
     *     path="/currencies/",
     *     operationId="/currencies/",
     *     tags={"currencies"},
     *     @OA\Response(
     *         response="200",
     *         description="Returns all currencies",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Error: When an internal error has occurred!",
     *     ),
     * )
     */

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function showAllCurrencies()
    {

        try {
            $response = $this->client->request(
                'GET',
                "{$this->baseUrl}currencies.json"
            );

            $currencies = json_decode($response->getBody(), true);

            return response()->json(
                [
                    'error' => false,
                    'currencies' => $currencies
                ]
            );

        } catch (\Exception $e) {

            $this->log->critical("An internal error has occurred! Exception: {$e->getMessage()}");

            return response()->json([
                'error' => true,
                'message' => 'An internal error has occurred!'
            ], 500);

        }

    }

    /**
     * @OA\Get(
     *     path="/rates/{currency}/",
     *     operationId="/rates/currencyX/",
     *     tags={"rates"},
     *     @OA\Parameter(
     *         name="currency",
     *         in="path",
     *         description="The currency parameter in path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns all exchange rates for the reported currency",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Error: When required parameters were not supplied.",
     *     ),
     * )
     */

    /**
     * @param string $currency
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function showExchangesRates(string $currency)
    {

        try {
            $currency = strtoupper($currency);

            $response = $this->client->request(
                'GET',
                "{$this->baseUrl}latest.json?app_id={$this->appId}&base={$currency}"
            );

            $response = json_decode($response->getBody(), true);

            return response()->json(
                [
                    'error' => false,
                    'currency' => $currency,
                    'rates' => $response['rates']
                ]
            );

        } catch (\Exception $e) {

            $this->log->critical("An internal error has occurred! Exception: {$e->getMessage()}");

            return response()->json([
                'error' => true,
                'message' => 'An internal error has occurred!'
            ], 500);

        }

    }

    /**
     * @OA\Get(
     *     path="/rates-between/{currencyX}/{currencyY}/",
     *     operationId="/rates-between/currencyX/currencyY/",
     *     tags={"rates-between"},
     *     @OA\Parameter(
     *         name="currencyX",
     *         in="path",
     *         description="The currencyX parameter in path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="currencyY",
     *         in="path",
     *         description="The currencyY parameter in path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns exchange rate between CurrencyX and CurrencyY",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Error: When required parameters were not supplied.",
     *     ),
     * )
     */

    /**
     * @param string $currencyX
     * @param string $currencyY
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function showExchangesRatesBetween(string $currencyX, string $currencyY)
    {

        try {
            $this->log->info('Start of the rate search process.');

            $currencyX = strtoupper($currencyX);
            $currencyY = strtoupper($currencyY);

            $ratesCurrencyX = $this->client->request(
                'GET',
                "{$this->baseUrl}latest.json?app_id={$this->appId}&base={$currencyX}&symbols={$currencyY}"
            );

            $rateCurrencyX = json_decode($ratesCurrencyX->getBody(), true);

            $this->log->info('End of the rate search process.');

            return response()->json(
                [
                    'error' => false,
                    'currencyX' => $currencyX,
                    'currencyY' => $currencyY,
                    'rates' => $rateCurrencyX['rates']
                ]
            );

        } catch (\Exception $e) {

            $this->log->critical("An internal error has occurred! Exception: {$e->getMessage()}");

            return response()->json([
                'error' => true,
                'message' => 'An internal error has occurred!'
            ], 500);

        }

    }

}
