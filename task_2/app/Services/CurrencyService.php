<?php

namespace App\Services;

use App\Converting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class CurrencyService
{
    /**
     * @var float
     */
    private $commissionPercent;

    /**
     * @var string
     */
    private $urlApi;

    /**
     * @param float $commissionPercent
     */
    public function __construct(float $commissionPercent = 2, string $urlApi = "blockchain.info/ticker")
    {
        $this->commissionPercent = $commissionPercent;
        $this->urlApi = $urlApi;
    }

    /**
     * @return Collection
     */
    public function loadCurrencies(): Collection
    {
        $response = Http::get($this->urlApi);
        return collect($response->json())->map(function ($rates) {
            return [
                //'cause prices equals, i simulate buy and sell prices with commissions
                "buy" => $this->calculate($rates["buy"]),
                "sell" => $rates["sell"] / $this->calculate(1),
                "original" => $rates["last"],
            ];
        });
    }

    /**
     * @param string $from
     * @param string $to
     * @param float $value
     * @return Converting|null
     */
    public function convertBtcWithCommission(string $from, string $to, float $value): ?Converting
    {
        $currencies = $this->loadCurrencies();

        if ($from === "BTC") {
            $converted = $value * $currencies[$to]["buy"];
        } elseif ($to === "BTC") {
            if ($value < 0.01) {
                return null;
            }
            $converted = $value / $currencies[$from]["sell"];
        } else {
            throw new InvalidParameterException("Not exist BTC currency");
        }

        $converted = round($converted, 10);

        return Converting::create([
            "currency_from" => $from,
            "currency_to" => $to,
            "value" => $value,
            "converted_value" => $converted,
            "rate" => $currencies[$from]["original"] ?? $currencies[$to]["original"]
        ]);
    }

    /**
     * @param float $value
     * @return float
     */
    private function calculate(float $value): float
    {
        return $value * (1 - $this->commissionPercent / 100);
    }
}
