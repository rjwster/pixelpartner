<?php

namespace App\Helpers;

use App\Livewire\Chat;
use App\Models\Message;
use OpenAI as GlobalOpenAI;

class OpenAi {
    static $client;

    public static function client() {
        return self::$client ?? GlobalOpenAI::factory()
            ->withApiKey(config('open-ai.api_key'))
            ->withHttpClient(new \GuzzleHttp\Client(['timeout' => 600]))
            ->make();
    }

    public static function chat(array $messages) {
        $response = self::client()->chat()->create([
            'model' => 'gpt-4o', // gpt-3.5-turbo
            'messages' => $messages,
        ]);

        return $response;
    }

    public static function generateIdea($text, $currentIdeas = [])
    {
        $response = OpenAi::chat([
            [
                'role' => 'system',
                'content' => 'You are a digital desginer in the middle of a brainstorm session. I expect you to help with gathering new ideas for the brainstorm. You need to give an idea that is related to the given subject and that will trigger the human brain to brainstorm further on. You wont give a output that already is in this list: ' . implode(', ', $currentIdeas) . '. You will only give 1 word as output. You always reply in dutch.',
            ],
            ['role' => 'user', 'content' => 'This is the subject that the brainstorm is about: ' . $text . '.'],
        ]);

        $idea = $response['choices'][0]['message']['content'];
        $idea = str_replace('.', '', $idea);

        return $idea;
    }

    public static function generatePrompt($text, $currentIdeas = [])
    {
        $response = OpenAi::chat([
            [
                'role' => 'system',
                'content' => 'You are an experienced prompt writer that can write image prompt in detail. I Expect you to create a detailed and realistic image description for the concept: ' . $text . '. This concept emerged from a brainstorming session. Incorporate and relate these already used words: ' . implode(', ', $currentIdeas) . ' into the description to ensure coherence and thematic connection. You are only required to provide a detailed image description. Please respond in English.',
            ],
            ['role' => 'user', 'content' => 'This is the subject that the brainstorm is about: ' . $text . '.'],
        ]);

        $prompt = $response['choices'][0]['message']['content'];
        $prompt = str_replace('.', '', $prompt);

        return $prompt;
    }

    public static function getImage($prompt)
    {
        $response = OpenAi::client()->images()->create([
            'model' => 'dall-e-3',
            'prompt' => $prompt,
            'size' => '1024x1792',
            'quality' => 'standard',
            'n' => 1,
        ]);

        return [
            'url' => $response['data'][0]['url'],
            'revised_prompt' => $response['data'][0]['revised_prompt'],
        ];
    }

    /***
     * Version 2
     */
    public static function generateIdeaV2($text, $currentIdeas = [])
    {
        $response = OpenAi::chat([
            [
                'role' => 'system',
                'content' => 'You are a digital desginer in the middle of a brainstorm session. I expect you to help with gathering new ideas for the brainstorm. You need to give an idea that is related to the given subject and that will trigger the human brain to brainstorm further on. You wont give a output that already is in this list: ' . implode(', ', $currentIdeas) . '. The length of the idea can only be one word. You will return a list of 5 ideas comma seperated. You always reply in dutch.',
            ],
            ['role' => 'user', 'content' => 'This is the subject that the brainstorm is about: ' . $text . '.'],
        ]);

        $idea = $response['choices'][0]['message']['content'];
        $idea = str_replace('.', '', $idea);
        $ideas = explode(', ', $idea);

        return $ideas;
    }

    public static function generatePrompts($ideas, $currentIdeas = [])
    {
        $prompts = [];
        foreach ($ideas as $idea) {
            $prompts[] = [
                'model' => 'gpt-4o',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an experienced prompt writer that can write image prompt in detail. I expect you to create an realistic image description for the concept: ' . $idea . '. This concept emerged from a brainstorming session. Incorporate and relate these already used words: ' . implode(', ', $currentIdeas) . ' into the description to ensure coherence and thematic connection. You are only required to provide a detailed image description. Make sure the image type will always be a sketch. Please respond in English.',
                    ],
                    ['role' => 'user', 'content' => 'This is the subject that the brainstorm is about: ' . $idea . '.'],
                ],
            ];
        }
        
        $url = 'https://api.openai.com/v1/chat/completions';
        $responses = static::callApi($url, $prompts);

        $imagePrompts = [];
        foreach ($responses as $jsonResponse) {
            $response = json_decode($jsonResponse, true);
            $prompt = $response['choices'][0]['message']['content'];
            $prompt = str_replace('.', '', $prompt);
            $imagePrompts[] = $prompt;
        }

        return $imagePrompts;
    }

    public static function getImages($prompts)
    {
        $url = 'https://api.openai.com/v1/images/generations';

        $dataSets = [];
        foreach ($prompts as $prompt) {
            $dataSets[] = [
                'model' => 'dall-e-3',
                'prompt' => $prompt,
                'n' => 1,
                'size' => '1024x1792',
            ];
        }

        $responses = static::callApi($url, $dataSets);

        $images = [];
        // loop trough responses and get the image url
        foreach ($responses as $jsonResponse) {
            $response = json_decode($jsonResponse, true);
            $images[] = [
                'url' => $response['data'][0]['url'],
                'revised_prompt' => $response['data'][0]['revised_prompt'],
            ];
        }

        return $images;
    }

    private static function callApi($url, $dataSets)
    {
        $apiKey = config('open-ai.api_key');

        $multi_curl = curl_multi_init();
        $curl_handles = [];

        foreach ($dataSets as $data) {
            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey
            ]);
            curl_setopt($ch, CURLOPT_POST, true);

            curl_setopt($ch, CURLOPT_TIMEOUT, 300);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);

            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

            curl_multi_add_handle($multi_curl, $ch);
            $curl_handles[] = $ch;
        }

        $running = null;
        do {
            curl_multi_exec($multi_curl, $running);
            curl_multi_select($multi_curl);
        } while ($running > 0);

        $responses = [];
        foreach ($curl_handles as $ch) {
            if (curl_errno($ch)) {
                $responses[] = 'Error:' . curl_error($ch);
            } else {
                $responses[] = curl_multi_getcontent($ch);
            }
            curl_multi_remove_handle($multi_curl, $ch);
            curl_close($ch);
        }

        curl_multi_close($multi_curl);

        return $responses;
    }
}

