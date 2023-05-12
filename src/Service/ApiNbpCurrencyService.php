<?php

namespace App\Service;

//use Symfony\Component\HttpClient\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiNbpCurrencyService
{
    public function __construct(private HttpClientInterface $clientHttp)
    {
    }

    public function getCurrencyByCode($code): ?array
    {
        //http://api.nbp.pl/api/exchangerates/rates/{table}/{code}/
        $url = "http://api.nbp.pl/api/exchangerates/rates/A/$code";
        $response = $this->clientHttp->request('GET', $url);
        $result = $response->toArray();
        $parsedData = [
            'effectiveDate' => $result['rates'][0]['effectiveDate'],
            'list' => []
            ];

        $parsedData['list'][] = ['currency' => $result['currency'], 'code' => $result['code'], 'mid' => $result['rates'][0]['mid'] ];
        return $parsedData;
    }

    public function getCurrencies(): ?array
    {
        $url = "http://api.nbp.pl/api/exchangerates/tables/A";
        $response = $this->clientHttp->request('GET', $url);
        $result = $response->toArray();

        $parsedData = [
            'effectiveDate' => $result[0]['effectiveDate'],
            'list' => []
        ];

        foreach ($result[0]['rates'] as $item) {
            $parsedData['list'][]= ['currency' => $item['currency'], 'code' => $item['code'], 'mid' => $item['mid']];
        }
        return $parsedData;
    }
}