<?php

namespace App\Http\Controllers;

use App\Enums\Currency;
use App\Http\Requests\BankAccountCreateRequest;
use App\Http\Requests\BankAccountUpdateRequest;
use App\Models\BankAccount;
use DOMPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BankAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bank_accounts = BankAccount::get();

        return view('bank_accounts.index', compact('bank_accounts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $currencies = Currency::get();

        return view('bank_accounts.create', compact('currencies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\BankAccountCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BankAccountCreateRequest $request)
    {
        $request['beneficiary_fullname'] = $request->beneficiary_name . '-' . $request->currency;
        BankAccount::create($request->all());

        return redirect(route('bank_accounts.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BankAccount  $bank_account
     * @return \Illuminate\Http\Response
     */
    public function show(BankAccount $bank_account)
    {
        return view('bank_accounts.show', compact('bank_account'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BankAccount  $bank_account
     * @return \Illuminate\Http\Response
     */
    public function edit(BankAccount $bank_account)
    {
        $currencies = Currency::get();

        return view('bank_accounts.edit', compact('bank_account', 'currencies'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\BankAccountUpdateRequest  $request
     * @param  \App\Models\BankAccount  $bank_account
     * @return \Illuminate\Http\Response
     */
    public function update(BankAccountUpdateRequest $request, BankAccount $bank_account)
    {
        $bank_account->beneficiary_fullname = $request->beneficiary_name . '-' . $request->currency;
        $bank_account->update($request->all());

        return redirect(route('bank_accounts.show', [$bank_account->id]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BankAccount  $bank_account
     * @return \Illuminate\Http\Response
     */
    public function destroy(BankAccount $bank_account)
    {
        $bank_account->delete();

        return redirect(route('bank_accounts.index'));
    }

    /**
     * Generate pdf with bank accounts info.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function exportBankAccountInfo(Request $request)
    {
        $bankAccounts = BankAccount::whereIn('id', $request->items)->orderBy('beneficiary_fullname')->get();

        $fileName = 'Bank_accounts.pdf';

        $html = view('pdf_documents.bank-account-info', compact('bankAccounts'))->render();

        $document = DOMPDF::loadHtml($html)->setPaper('a4', 'portrait');

        Storage::put('public/others/' . $fileName, $document->output());
        $url = Storage::url('others/' . $fileName);

        return response()->json(['url' => $url, 'fileName' => $fileName]);
    }
}
