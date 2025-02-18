<?php




ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_reporting', E_ALL | E_STRICT);
ini_set('error_log', 'temp_file.log');




// Function to limit requests to 5 per second
function limit_requests_per_second($maxRequests, $timeFrame)
{
    $tokenBucketFile = 'tokenbucket.txt';

    // Check if the token bucket file exists
    if (!file_exists($tokenBucketFile)) {
        file_put_contents($tokenBucketFile, json_encode(['tokens' => $maxRequests, 'lastRefillTime' => time()]));
    }

    // Read the current token bucket state from the file
    $bucketData = json_decode(file_get_contents($tokenBucketFile), true);
    $tokens = $bucketData['tokens'];
    $lastRefillTime = $bucketData['lastRefillTime'];

    // Calculate the time elapsed since the last refill
    $currentTime = time();
    $timeElapsed = $currentTime - $lastRefillTime;

    // Refill the token bucket based on the time elapsed
    $refillAmount = $timeElapsed * ($maxRequests / $timeFrame);
    $tokens = min($maxRequests, $tokens + $refillAmount);

    // Check if there are enough tokens for the current request
    while ($tokens < 1) {
        // Not enough tokens, wait for a short time and check again
        usleep(100000); // Sleep for 100 milliseconds (adjust as needed)

        // Read the token bucket state again
        $bucketData = json_decode(file_get_contents($tokenBucketFile), true);
        $tokens = $bucketData['tokens'];
    }

    // Consume one token and update the token bucket
    $tokens -= 1;
    $bucketData['tokens'] = $tokens;
    $bucketData['lastRefillTime'] = $currentTime;
    file_put_contents($tokenBucketFile, json_encode($bucketData));

    // Allow the request to proceed
    // Your PHP file logic goes here
    // ...


    call();

}

// Call the function to limit requests to 5 per second
limit_requests_per_second(20, 1); // 5 requests per second




function get_resolution($resolution)
{


    $resolution = explode("x", $resolution);


    $resolution_width = (int) $resolution[0];
    $resolution_height = (int) $resolution[1];

    if ($resolution_width < $resolution_height) {
        $old_resolution_width = $resolution_width;
        $resolution_width = $resolution_height;

        $resolution_height = $old_resolution_width;

    }



    $original_resolution = "";
    $original_quality = "";


    if ($resolution_height <= 144) {
        $original_resolution = "144p";
        $original_quality = "SD";

    } else if ($resolution_height > 144 && $resolution_height <= 180) {
        $original_resolution = "180p";
        $original_quality = "SD";
    } else if ($resolution_height > 180 && $resolution_height <= 222) {
        $original_resolution = "222p";
        $original_quality = "SD";
    } else if ($resolution_height > 222 && $resolution_height <= 240) {
        $original_resolution = "240p";
        $original_quality = "SD";
    } else if ($resolution_height > 240 && $resolution_height <= 288) {
        $original_resolution = "288p";
        $original_quality = "SD";
    } else if ($resolution_height > 288 && $resolution_height <= 272) {
        $original_resolution = "272p";
        $original_quality = "SD";
    } else if ($resolution_height > 272 && $resolution_height <= 320) {
        $original_resolution = "320p";
        $original_quality = "SD";
    } else if ($resolution_height > 320 && $resolution_height <= 360) {
        $original_resolution = "360p";
        $original_quality = "SD";
    } else if ($resolution_height > 360 && $resolution_height <= 480) {
        $original_resolution = "480p";
        $original_quality = "SD";
    } else if ($resolution_height > 480 && $resolution_height <= 720) {
        $original_resolution = "720p";
        $original_quality = "HD";

    } else if ($resolution_height > 720 && $resolution_height <= 1080) {
        $original_resolution = "1080p";
        $original_quality = "FHD";
    } else if ($resolution_height > 1080 && $resolution_height <= 2048) {
        $original_resolution = "2k";
        $original_quality = "QHD";

    } else if ($resolution_height > 2048 && $resolution_height <= 2160) {
        $original_resolution = "4k";
        $original_quality = "UHD";

    } else if ($resolution_height > 2160 && $resolution_height <= 4320) {
        $original_resolution = "8k";
        $original_quality = "8K UHD";

    } else if ($resolution_height > 4320) {
        $original_resolution = "10k";
        $original_quality = "10K UHD";

    } else {

        $original_resolution = "TBD";
        $original_quality = "TBD";


    }

    return array("resolution" => $original_resolution, "quality" => $original_quality);

}




function get_remote_file_info($url) {
    $maxRetries = 10;
    $retryDelay = 500 * 1000; // 100ms in microseconds
    $fileSize = -1;
    $httpResponseCode = 0;

    for ($i = 0; $i < $maxRetries; $i++) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects

        curl_exec($ch);
        $fileSize = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        $httpResponseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (($fileSize > 0 || $httpResponseCode != 200)) {
            break; // Exit loop if file size is obtained or error code is not 200
        }

        usleep($retryDelay); // Delay 100ms before retry
    }

    return array(
        'statusCode' => $httpResponseCode,
        'fileExists' => ($httpResponseCode == 200),
        'fileSize' => (int) $fileSize
    );
}



function call()
{
    if (isset($_GET)) {



        if (isset($_GET['url']) && !empty($_GET['url'])) {
            $URL = $_GET['url'];




            if (filter_var($URL, FILTER_VALIDATE_URL)) {

                $parsed_url = parse_url($URL);



                function isValidTwitterTweetURL($url)
                {

                    // Regular expression pattern to match Twitter tweet URLs

                    $pattern = '/^https?:\/\/twitter\.com\/(?:#!\/)?(\w+)\/status\/(\d+)(?:\?.*)?$/i';
                    $pattern2 = '/^http?:\/\/twitter\.com\/(?:#!\/)?(\w+)\/status\/(\d+)(?:\?.*)?$/i';
                    $pattern3 = '/^https?:\/\/x\.com\/(?:#!\/)?(\w+)\/status\/(\d+)(?:\?.*)?$/i';
                    $pattern4 = '/^http?:\/\/x\.com\/(?:#!\/)?(\w+)\/status\/(\d+)(?:\?.*)?$/i';


                    // Perform the regex match
                    if (preg_match($pattern, $url, $matches) || preg_match($pattern2, $url, $matches) || preg_match($pattern3, $url, $matches) || preg_match($pattern4, $url, $matches)) {
                        // The URL is valid, and $matches[1] contains the username and $matches[2] contains the tweet ID.
                        return true;
                    }

                    return false;
                }


                if ((($parsed_url['scheme'] == 'http' || $parsed_url['scheme'] == 'https') && ($parsed_url['host'] == 'twitter.com') || $parsed_url['host'] == 'x.com') && isValidTwitterTweetURL($URL)) {



                    $response_code = get_remote_file_info($URL, false)["statusCode"];



                    // Use condition to check the existence of URL
                    if ($response_code == 200 || $response_code = 302) {



                        try {


                            // Remove any query strings from the URL
                            $url = preg_replace('/\?.*/', '', $URL);

                            // Extract the numeric tweet ID from the URL
                            preg_match('/\/status\/(\d+)/', $url, $matches);
                            $tweet_id = $matches[1];


                            $error = error_get_last();
                            if ($error != null) {
                                error_log($URL . "\n", 3, 'temp_file.log');
                            } else {
                                error_log($URL . "\n", 3, 'app_urls.log');
                            }


                            // Array of random user agents
                            $userAgents = [
                                'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.1 Safari/537.36',
                                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.1 Safari/537.36',
                                'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:97.0) Gecko/20100101 Firefox/97.0',
                                // Add more user agents as needed
                            ];

                            // Randomly select a user agent
                            $randomUserAgent = $userAgents[array_rand($userAgents)];




                            $curl = curl_init();



                            // Set cURL options
                            curl_setopt_array($curl, [
                                CURLOPT_URL => "https://api.fxtwitter.com/status/" . $tweet_id,
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING => "",
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 30,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => "GET",
                                CURLOPT_HTTPHEADER => [
                                    "Content-Type: application/json",
                                    "User-Agent: " . $randomUserAgent, // Include the randomly selected user agent
                                ],
                            ]);






                            $response = curl_exec($curl);
                            $err = curl_error($curl);




                            if ($err) {
                                header('Content-Type:application/json');

                                $error = array("error" => true, "message" => "Server probelm try again");
                                http_response_code(401);
                                die(json_encode($error));

                            } else {


                                $data = json_decode($response);



                                if ($data->code == 404) {
                                    header('Content-Type:application/json');
                                    $error = array("error" => true, "message" => "Tweet Not Founded");
                                    http_response_code(401);
                                    die(json_encode($error));
                                }

                                if (isset($data->errors)) {

                                    header('Content-Type:application/json');
                                    $error = array("error" => true, "message" => "Oops try again");
                                    http_response_code(401);
                                    die(json_encode($error));


                                }
                            }





                            $response = $data->tweet;
                            $author = $response->author;

                            if (isset($response->quote)) {
                                $medias = $response->quote->media->all;


                            } else {
                                $medias = $response->media->all;


                            }



                            $name = $author->name;
                            $full_text = $response->text;



                            $screen_name = $author->screen_name;
                            $profile_image_url_https = $author->avatar_url;



                            if (!isset($medias) || sizeof($medias) == 0) {

                                header('Content-Type:application/json');
                                $error = array("error" => true, "message" => "No Video Or GIF Founded");
                                http_response_code(401);
                                die(json_encode($error));

                            }


                            $final_video_items = array();





                            foreach ($medias as $key => $media) {




                                if ($media->type === 'video' || $media->type === 'animated_gif') {







                                    $media_meta = array("thumbnail" => $media->thumbnail_url, "media_type" => $media->type, "name" => $name, "full_text" => $full_text, "screen_name" => $screen_name, "profile_image_url_https" => $profile_image_url_https);



                                    $original_variants = $media->variants;



                                    $variants = array();


                                    foreach ($original_variants as $key => $variant) {




                                        if (isset($variant->bitrate)) {




                                            if ($media->type === "video") {


                                                $parse_variant_url = parse_url($variant->url);

                                                $variant_item = explode("/", $parse_variant_url["path"]);

                                                $resolution = $variant_item[count($variant_item) - 2];

                                                $screen_size = $resolution;


                                                $resolution_data = get_resolution($resolution);
                                                $resolution = $resolution_data["resolution"];

                                                $quality = $resolution_data["quality"];


                                                $size = get_remote_file_info($variant->url)["fileSize"];


                                                $media_data = array("bitrate" => $variant->bitrate, "content_type" => $variant->content_type, "url" => $variant->url, "resolution" => $resolution, "file_size" => $size, "quality" => $quality, "screen_size" => $screen_size);


                                            } else {
                                                $size = get_remote_file_info($variant->url)["fileSize"];



                                                $media_data = array(
                                                    "bitrate" => $variant->bitrate,
                                                    "content_type" => $variant->content_type,
                                                    "url" => $variant->url,
                                                    "file_size" => $size

                                                );

                                            }

                                            array_push($variants, $media_data);
                                        }
                                    }




                                    usort($variants, function ($a, $b) { //Sort the array using a user defined function

                                        return $a["bitrate"] > $b["bitrate"] ? -1 : 1; //Compare the scores
                                    });

                                    $media_meta['variants'] = $variants;


                                    array_push($final_video_items, $media_meta);


                                }

                            }




                            if (!empty($final_video_items)) {
                                header('Content-Type:application/json');
                                echo json_encode($final_video_items);
                            } else {


                                header('Content-Type:application/json');
                                http_response_code(401);

                                die(json_encode(array('error' => true, 'message' => 'No Video Or GIF Founded')));
                            }





                            exit;









                            if (
                                (isset($data->threaded_conversation_with_injections_v2->instructions[0]->entries[$entryIndex]->content->itemContent->tweet_results->result->legacy->is_quote_status)
                                    && $data->threaded_conversation_with_injections_v2->instructions[0]->entries[$entryIndex]->content->itemContent->tweet_results->result->legacy->is_quote_status
                                )
                                || (isset($data->threaded_conversation_with_injections_v2->instructions[0]->entries[$entryIndex]->content->itemContent->tweet_results->result->tweet->legacy->is_quote_status) && $data->threaded_conversation_with_injections_v2->instructions[0]->entries[$entryIndex]->content->itemContent->tweet_results->result->tweet->legacy->is_quote_status)

                            ) {






                                if (isset($data->threaded_conversation_with_injections_v2->instructions[0]->entries[$entryIndex]->content->itemContent->tweet_results->result->tweet->legacy->extended_entities)) {

                                    $response = $data->threaded_conversation_with_injections_v2->instructions[0]->entries[$entryIndex]->content->itemContent->tweet_results->result->tweet->legacy;
                                    $users = $data->threaded_conversation_with_injections_v2->instructions[0]->entries[$entryIndex]->content->itemContent->tweet_results->result->tweet->result->core->user_results->result->legacy;




                                    $quoted_response = $response;



                                } else if (isset($data->threaded_conversation_with_injections_v2->instructions[0]->entries[$entryIndex]->content->itemContent->tweet_results->result->legacy->extended_entities)) {
                                    $response = $data->threaded_conversation_with_injections_v2->instructions[0]->entries[$entryIndex]->content->itemContent->tweet_results->result->legacy;

                                    $users = $data->threaded_conversation_with_injections_v2->instructions[0]->entries[$entryIndex]->content->itemContent->tweet_results->result->core->user_results->result->legacy;

                                    $quoted_response = $response;

                                } else if (
                                    isset(
                                    $data->threaded_conversation_with_injections_v2->instructions[0]->entries[$entryIndex]->content->itemContent->tweet_results->result->
                                        quoted_status_result->result->tweet->legacy->extended_entities
                                )
                                ) {

                                    $response = $data->threaded_conversation_with_injections_v2->instructions[0]->entries[$entryIndex]->content->itemContent->tweet_results->result->
                                        quoted_status_result->result->tweet->legacy;
                                    $users = $data->threaded_conversation_with_injections_v2->instructions[0]->entries[$entryIndex]->content->itemContent->tweet_results->result->tweet->result->
                                        quoted_status_result->result->tweet->core->user_results->result->legacy;



                                    $quoted_response = $response;



                                } else if (
                                    isset(
                                    $data->threaded_conversation_with_injections_v2->instructions[0]->entries[$entryIndex]->content->itemContent->tweet_results->result->
                                        quoted_status_result->result->legacy->extended_entities
                                )
                                ) {

                                    $response = $data->threaded_conversation_with_injections_v2->instructions[0]->entries[$entryIndex]->content->itemContent->tweet_results->result->
                                        quoted_status_result->result->legacy;

                                    $users = $data->threaded_conversation_with_injections_v2->instructions[0]->entries[$entryIndex]->content->itemContent->tweet_results->result->
                                        quoted_status_result->result->core->user_results->result->legacy;



                                    $quoted_response = $response;

                                }




                                $response = json_encode($response);


                                if (!isset($quoted_response->extended_entities)) {
                                    $error = array("error" => true, "message" => "Quoted Status: No video or gif found in the given URL");
                                    http_response_code(401);
                                    die(json_encode($error));

                                } else {
                                    $medias = json_decode(json_encode($quoted_response), true)['extended_entities']['media'];

                                }




                            } else if ((isset($data->threaded_conversation_with_injections_v2->instructions[0]->entries[$entryIndex]->content->itemContent->tweet_results->result->tweet->card) && $data->threaded_conversation_with_injections_v2->instructions[0]->entries[$entryIndex]->content->itemContent->tweet_results->result->tweet->card) || isset($data->threaded_conversation_with_injections_v2->instructions[0]->entries[$entryIndex]->content->itemContent->tweet_results->result->card) && $data->threaded_conversation_with_injections_v2->instructions[0]->entries[$entryIndex]->content->itemContent->tweet_results->result->card) {






                                if (isset($data->threaded_conversation_with_injections_v2->instructions[0]->entries[$entryIndex]->content->itemContent->tweet_results->result->tweet->card) && $data->threaded_conversation_with_injections_v2->instructions[0]->entries[$entryIndex]->content->itemContent->tweet_results->result->tweet->card) {


                                    $users = $data->threaded_conversation_with_injections_v2->instructions[0]->entries[$entryIndex]->content->itemContent->tweet_results->result->tweet->core->user_results->result->legacy;


                                    $response = json_encode($data->threaded_conversation_with_injections_v2->instructions[0]->entries[$entryIndex]->content->itemContent->tweet_results->result->tweet->legacy);




                                    $binding_values = $data->threaded_conversation_with_injections_v2->instructions[0]->entries[$entryIndex]->content->itemContent->tweet_results->result->tweet->card->legacy->binding_values;


                                }


                                if (isset($data->threaded_conversation_with_injections_v2->instructions[0]->entries[$entryIndex]->content->itemContent->tweet_results->result->card) && $data->threaded_conversation_with_injections_v2->instructions[0]->entries[$entryIndex]->content->itemContent->tweet_results->result->card) {


                                    $users = $data->threaded_conversation_with_injections_v2->instructions[0]->entries[$entryIndex]->content->itemContent->tweet_results->result->core->user_results->result->legacy;


                                    $response = json_encode($data->threaded_conversation_with_injections_v2->instructions[0]->entries[$entryIndex]->content->itemContent->tweet_results->result->legacy);



                                    $binding_values = $data->threaded_conversation_with_injections_v2->instructions[0]->entries[$entryIndex]->content->itemContent->tweet_results->result->card->legacy->binding_values;


                                }




                                $medias = array();

                                foreach ($binding_values as $bv) {



                                    if ($bv->key == "unified_card") {



                                        $data = json_decode(json_encode($bv->value, true));



                                        if ($data && isset($data->string_value)) {


                                            if (json_decode($data->string_value)->type === "video_app") {

                                                $media_id = json_decode($data->string_value)->destination_objects->app_store_with_docked_media_1->data->media_id;
                                                header('Content-Type:application/json');


                                                array_push($medias, json_decode(json_encode(json_decode($data->string_value)->media_entities->$media_id), true));


                                            } else {

                                                $media_id = json_decode($data->string_value)->destination_objects->browser_with_docked_media_1->data->media_id;
                                                header('Content-Type:application/json');


                                                array_push($medias, json_decode(json_encode(json_decode($data->string_value)->media_entities->$media_id), true));

                                            }





                                        }



                                    }

                                }





                            } else {





                                if (isset($data->threaded_conversation_with_injections_v2->instructions[0]->entries[$entryIndex]->content->itemContent->tweet_results->result->tweet->legacy)) {
                                    $response = $data->threaded_conversation_with_injections_v2->instructions[0]->entries[$entryIndex]->content->itemContent->tweet_results->result->tweet->legacy;
                                    $users = $data->threaded_conversation_with_injections_v2->instructions[0]->entries[$entryIndex]->content->itemContent->tweet_results->result->tweet->core->user_results->result->legacy;


                                    $response = json_encode($response);

                                    $ee = $data->threaded_conversation_with_injections_v2->instructions[0]->entries[$entryIndex]->content->itemContent->tweet_results->result->tweet->legacy;



                                } else {

                                    $response = $data->threaded_conversation_with_injections_v2->instructions[0]->entries[$entryIndex]->content->itemContent->tweet_results->result->legacy;
                                    $users = $data->threaded_conversation_with_injections_v2->instructions[0]->entries[$entryIndex]->content->itemContent->tweet_results->result->core->user_results->result->legacy;


                                    $response = json_encode($response);

                                    $ee = $data->threaded_conversation_with_injections_v2->instructions[0]->entries[$entryIndex]->content->itemContent->tweet_results->result->legacy;


                                }


                                if (!isset($ee->extended_entities)) {
                                    $error = array("error" => true, "message" => "No video or gif found in the given URL");
                                    http_response_code(401);
                                    die(json_encode($error));

                                } else {

                                    $medias = json_decode($response, true)['extended_entities']['media'];
                                }




                            }







                        } catch (Exception $e) {

                            $error = array("error" => true, "message" => "Server error");
                            http_response_code(401);
                            die(json_encode($error));
                        }







                        if (isset($medias) && sizeof($medias) > 0) {

                            $name = $users->name;
                            $full_text = json_decode($response)->full_text;



                            $screen_name = $users->screen_name;
                            $profile_image_url_https = $users->profile_image_url_https;


                            $final_video_items = array();






                            foreach ($medias as $key => $media) {

                                if ($media['type'] === 'video' || $media['type'] === 'animated_gif') {






                                    $media_meta = array("thumbnail" => $media['media_url_https'], "media_type" => $media['type'], "name" => $name, "full_text" => $full_text, "screen_name" => $screen_name, "profile_image_url_https" => $profile_image_url_https);



                                    $original_variants = $media['video_info']['variants'];





                                    $variants = array();


                                    foreach ($original_variants as $key => $variant) {




                                        if (isset($variant['bitrate'])) {




                                            if ($media['type'] === "video") {


                                                $parse_variant_url = parse_url($variant['url']);

                                                $variant_item = explode("/", $parse_variant_url["path"]);

                                                $resolution = $variant_item[count($variant_item) - 2];

                                                $screen_size = $resolution;


                                                $resolution_data = get_resolution($resolution);
                                                $resolution = $resolution_data["resolution"];

                                                $quality = $resolution_data["quality"];


                                                $size = get_remote_file_info($variant['url'])["fileSize"];


                                                $media_data = array("bitrate" => $variant['bitrate'], "content_type" => $variant['content_type'], "url" => $variant['url'], "resolution" => $resolution, "file_size" => $size, "quality" => $quality, "screen_size" => $screen_size);


                                            } else {
                                                $size = get_remote_file_info($variant['url'])["fileSize"];



                                                $media_data = array(
                                                    "bitrate" => $variant['bitrate'],
                                                    "content_type" => $variant['content_type'],
                                                    "url" => $variant['url'],
                                                    "file_size" => $size

                                                );

                                            }

                                            array_push($variants, $media_data);
                                        }
                                    }




                                    usort($variants, function ($a, $b) { //Sort the array using a user defined function

                                        return $a["bitrate"] > $b["bitrate"] ? -1 : 1; //Compare the scores
                                    });

                                    $media_meta['variants'] = $variants;


                                    array_push($final_video_items, $media_meta);


                                }

                            }





                            if (!empty($final_video_items)) {
                                header('Content-Type:application/json');
                                echo json_encode($final_video_items);
                            } else {


                                header('Content-Type:application/json');
                                http_response_code(401);

                                die(json_encode(array('error' => true, 'message' => 'No Video Or GIF Founded')));
                            }


                        } else {

                            header('Content-Type:application/json');
                            http_response_code(401);

                            die(json_encode(array('error' => true, 'message' => ' No video or gif found in the given URL')));
                        }








                    } else {

                        header('Content-Type:application/json');
                        http_response_code(401);

                        die(json_encode(array('error' => true, 'message' => 'Broken Tweet URL')));
                    }


                } else {

                    header('Content-Type:application/json');
                    http_response_code(401);

                    die(json_encode(array('error' => true, 'message' => 'Enter Valid Tweet URL')));
                }

            } else {
                header('Content-Type:application/json');
                http_response_code(401);
                die(json_encode(array('error' => true, 'message' => 'Enter Valid URL')));
            }






        } else {

            header('Content-Type:application/json');
            http_response_code(401);

            die(json_encode(array('error' => true, 'message' => 'No URL Provided')));
        }

    } else {
        header('Content-Type:application/json');
        http_response_code(401);
        die(json_encode(array('error' => true, 'message' => 'Not Implemented')));
    }

}





?>