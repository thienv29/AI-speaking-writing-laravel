<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SpeechController extends Controller
{
    // Use service name from docker-compose network (not container name)
    private const TTS_SERVICE_URL = 'http://ai-speech-service:8000/tts';
    private const STT_SERVICE_URL = 'http://ai-speech-service:8000/stt';

    /**
     * Proxy TTS request to Python service
     */
    public function textToSpeech(Request $request)
    {
        try {
            $request->validate([
                'text' => 'required|string|max:500',
                'lang' => 'nullable|string|max:10'
            ]);

            $text = $request->input('text');
            $lang = $request->input('lang', 'en');

            Log::info('TTS Proxy: Forwarding request', ['text' => substr($text, 0, 50), 'lang' => $lang]);

            // Forward request to Python TTS service (using internal Docker network)
            $response = Http::timeout(15)->post(self::TTS_SERVICE_URL, [
                'text' => $text,
                'lang' => $lang
            ]);

            if (!$response->successful()) {
                Log::error('TTS Proxy: Service error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return response()->json([
                    'error' => 'TTS service error',
                    'message' => 'Cannot generate audio'
                ], $response->status());
            }

            // Return audio file with correct headers
            return response($response->body(), 200, [
                'Content-Type' => 'audio/mpeg',
                'Content-Disposition' => 'inline; filename="tts.mp3"'
            ]);

        } catch (\Throwable $e) {
            Log::error('TTS Proxy error', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => 'TTS proxy error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Proxy STT request to Python service
     */
    public function speechToText(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:mp3,wav,m4a,ogg,webm|max:10240'
            ]);

            $file = $request->file('file');
            
            Log::info('STT Proxy: Forwarding request', [
                'filename' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime' => $file->getMimeType()
            ]);

            // Forward request to Python STT service (using internal Docker network)
            $response = Http::timeout(30)
                ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
                ->post(self::STT_SERVICE_URL);

            if (!$response->successful()) {
                Log::error('STT Proxy: Service error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return response()->json([
                    'error' => 'STT service error',
                    'message' => 'Cannot transcribe audio'
                ], $response->status());
            }

            return response()->json($response->json());

        } catch (\Throwable $e) {
            Log::error('STT Proxy error', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => 'STT proxy error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

