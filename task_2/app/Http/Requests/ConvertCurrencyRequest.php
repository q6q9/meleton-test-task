<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class ConvertCurrencyRequest extends FormRequest
{
    /**
     * @var string[]
     */
    private $rightCurrencies;

    /**
     * @var bool
     */
    private $hasBTC;

    /**
     * @return string[]
     */
    public function getRightCurrencies(): array
    {
        if (is_null($this->rightCurrencies)) {
            return [
                "AUD", "BRL", "CAD", "CHF", "CLP", "CNY", "CZK", "DKK", "EUR",
                "GBP", "HKD", "HRK", "HUF", "INR", "ISK", "JPY", "KRW", "NZD",
                "PLN", "RON", "RUB", "SEK", "SGD", "THB", "TRY", "TWD", "USD"
            ];
        }
        return $this->rightCurrencies;
    }

    /**
     * @param array $rightCurrencies
     * @return void
     */
    public function setRightCurrencies(array $rightCurrencies): void
    {
        $this->rightCurrencies = $rightCurrencies;
    }

    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        $validateRightsCurrency = function ($attribute, $value, $fail) {

            $currencyWithoutSpaces = str_replace(' ', '', $value);
            $upperCurrency = Str::upper($currencyWithoutSpaces);

            if (!in_array($upperCurrency, $this->getRightCurrencies())) {
                $fail("{$upperCurrency} is not valid currency");
            }

        };

        $checkIsBTC = function ($currency) {
            $currencyWithoutSpaces = str_replace(' ', '', $currency);
            $upperCurrency = Str::upper($currencyWithoutSpaces);

            return $upperCurrency === "BTC";
        };

        return [
            "currency_from" => ["required", function ($attribute, $value, $fail) use ($validateRightsCurrency, $checkIsBTC) {
                if ($checkIsBTC($value)) {
                    $this->hasBTC = true;
                    return;
                }

                $validateRightsCurrency($attribute, $value, $fail);
            },],

            "currency_to" => ["required", function ($attribute, $value, $fail) use ($validateRightsCurrency, $checkIsBTC) {
                if ($this->hasBTC) {
                    if ($checkIsBTC($value)) {
                        $fail("BTC need only 1 time in 'currency_from' or 'currency_to'");
                    }

                    $validateRightsCurrency($attribute, $value, $fail);
                    return;
                }

                if ($checkIsBTC($value)) {
                    $this->hasBTC = true;
                    return;
                }

                $fail("Not exists BTC in 'currency_from' or 'currency_to'");
            }],

            "value" => ["required", "numeric",],
        ];
    }

    /**
     * @return array
     */
    public function validated(): array
    {
        $validated = parent::validated();

        foreach (["currency_from", "currency_to"] as $keyOfCurrency) {
            $currencyWithoutSpaces = str_replace(' ', '', $validated[$keyOfCurrency]);
            $upperCurrency = Str::upper($currencyWithoutSpaces);

            $validated[$keyOfCurrency] = $upperCurrency;
        }

        return $validated;
    }
}
