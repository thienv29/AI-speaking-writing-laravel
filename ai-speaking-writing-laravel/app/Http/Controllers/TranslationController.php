<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TranslationService;

class TranslationController extends Controller
{
    /**
     * Translate English to Vietnamese
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function translate(Request $request)
    {
        $request->validate([
            'text' => 'required|string|max:500'
        ]);
        
        $text = $request->input('text');
        $result = TranslationService::translate($text);
        
        return response()->json([
            'success' => true,
            'text' => $text,
            'translation' => $result['translation'],
            'source' => $result['source']
        ]);
    }
}

