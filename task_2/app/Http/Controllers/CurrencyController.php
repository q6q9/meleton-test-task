<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConvertCurrencyRequest;
use App\Http\Requests\RatesCurrencyRequest;
use App\Services\CurrencyService;
use Illuminate\Http\JsonResponse;

class CurrencyController extends Controller
{
    /**
     * @var CurrencyService
     */
    private $currencyService;

    /**
     * @param CurrencyService|null $currencyService
     */
    public function __construct(?CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService ?? new CurrencyService();
    }

    /**
     * @param RatesCurrencyRequest $request
     * @return JsonResponse
     */
    public function rates(RatesCurrencyRequest $request): JsonResponse
    {
        $calculatedCurrencies = $this->currencyService->loadCurrencies();

        $filteredCalculatedCurrencies = $calculatedCurrencies;

        $validated = $request->validated();
        if (!empty($validated["filter"]["currency"])) {
            $filteredCalculatedCurrencies = $calculatedCurrencies->filter(function ($rate, $currency) use ($validated) {
                return in_array($currency, $validated["filter"]["currency"]);
            });
        }

        $sortedFilteredCalculatedCurrencies = $filteredCalculatedCurrencies->sortBy(function ($rate) {
            return ($rate);
        });

        return response()->json($sortedFilteredCalculatedCurrencies);
    }

    /**
     * @param ConvertCurrencyRequest $request
     * @return JsonResponse
     */
    public function convert(ConvertCurrencyRequest $request): JsonResponse
    {
        $validated = collect($request->validated());

        $converting = $this->currencyService->convertBtcWithCommission(
            $validated["currency_from"],
            $validated["currency_to"],
            $validated["value"]
        );

        if (!$converting) {
            abort(422, "Value is very small");
        }

        return response()->json($converting);
    }
}
