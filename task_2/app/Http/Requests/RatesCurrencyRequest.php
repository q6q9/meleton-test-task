<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class RatesCurrencyRequest extends FormRequest
{
    /**
     * @var string[]
     */
    private $rightCurrencies;

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
        return [
            "filter.currency" => function ($attribute, $value, $fail) {
                $currencies = explode(",", $this->input("filter.currency"));

                foreach ($currencies as $currency) {
                    $currencyWithoutSpaces = str_replace(' ', '', $currency);
                    $upperCurrency = Str::upper($currencyWithoutSpaces);

                    if ($upperCurrency && !in_array($upperCurrency, $this->getRightCurrencies())) {
                        $fail("{$upperCurrency} is not valid currency");
                    }
                }
            }
        ];
    }

    /**
     * @return array
     */
    public function validated(): array
    {
        $validated = parent::validated();

        if (!empty($validated["filter"]["currency"])) {
            $currencies = explode(",", $this->input("filter.currency"));

            $validated["filter"]["currency"] = collect($currencies)->map(function ($currency) {
                $currencyWithoutSpaces = str_replace(" ", "", $currency);
                return Str::upper($currencyWithoutSpaces);
            })->filter()->toArray();
        }

        return $validated;
    }
}
