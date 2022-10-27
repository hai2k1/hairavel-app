<?php

use Illuminate\Support\Facades\Route;


Route::get('appForm/{id}', ['uses' => 'Hairavel\Core\Api\Form@list', 'desc' => 'form list'])->name('api.core .form.list');
Route::get('appFormInfo/{id}', ['uses' => 'Hairavel\Core\Api\Form@info', 'desc' => 'form list'])->name('api.core .form.list');
Route::post('appFormInfo/{id}', ['uses' => 'Hairavel\Core\Api\Form@push', 'desc' => 'Form submit'])->name('api.core .form.push');
