<?php

namespace Classiebit\Eventmie\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiController extends Controller
{
    public function generate(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|min:5',
        ]);

        $apiKey = setting('ai.openai_api_key');
        if (empty($apiKey)) {
            return response()->json([
                'status' => false,
                'error' => 'OpenAI API key is not configured. Set it in Admin → Settings → AI.',
            ], 422);
        }

        $prompt = (string) $request->input('prompt');

        // Ask the model to return strict JSON for easier parsing
        $sys = 'You are a senior copywriter for event landing pages. '
            .'Write compelling, concise copy. '
            .'Return STRICT JSON with keys: '
            .'title, excerpt, description_html, faq_html, meta_title, meta_description, meta_keywords. '
            .'Do not include any text outside JSON.';

        $user = "Create event page copy based on this brief:\n".$prompt.
            "\n\nConstraints:\n- Excerpt max 160 chars\n- Meta title max 60 chars\n- Meta description max 155 chars\n- description_html and faq_html must be valid HTML";

        try {
            $response = Http::withToken($apiKey)
                ->timeout(20)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => $sys],
                        ['role' => 'user', 'content' => $user],
                    ],
                    'temperature' => 0.7,
                ]);

            if (!$response->successful()) {
                return response()->json([
                    'status' => false,
                    'error' => 'AI service error',
                    'details' => $response->json(),
                ], 502);
            }

            $content = data_get($response->json(), 'choices.0.message.content');
            // Try to decode the JSON content
            $data = json_decode($content, true);
            if (!is_array($data)) {
                // Fallback: attempt to extract JSON block
                if (preg_match('/\{[\s\S]*\}/', (string) $content, $m)) {
                    $data = json_decode($m[0], true);
                }
            }

            if (!is_array($data)) {
                return response()->json([
                    'status' => false,
                    'error' => 'Invalid AI response',
                ], 502);
            }

            return response()->json([
                'status' => true,
                'data' => [
                    'title' => (string) data_get($data, 'title', ''),
                    'excerpt' => (string) data_get($data, 'excerpt', ''),
                    'description' => (string) data_get($data, 'description_html', ''),
                    'faq' => (string) data_get($data, 'faq_html', ''),
                    'meta_title' => (string) data_get($data, 'meta_title', ''),
                    'meta_description' => (string) data_get($data, 'meta_description', ''),
                    'meta_keywords' => (string) data_get($data, 'meta_keywords', ''),
                ],
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'error' => 'AI request failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}


