<?php



if (isset($_SERVER['HTTP_USER_AGENT'])) {
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    // Now, you can extract the package name from the User-Agent header
    // The package name is often included in the User-Agent string

    // Example: Extract package name from a typical User-Agent format
    // This may vary depending on your specific app's User-Agent format
    $packageName = '';
    if (preg_match('/^.*\s(\w+\.\w+)\s.*$/', $userAgent, $matches)) {
        $packageName = $matches[1];
    }


    // Use $packageName as needed
    // Your PHP code here
} else {

    // User-Agent header not found, handle the situation accordingly
}




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
    $fileSize = -1;
    $httpResponseCode = 0;

    // Initialize cURL session
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   // Return the transfer as a string
    curl_setopt($ch, CURLOPT_HEADER, true);           // Include the header in the output
    curl_setopt($ch, CURLOPT_NOBODY, true);           // Exclude the body from the output
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);   // Follow any redirects

    // Execute cURL session
    curl_exec($ch);

    // Get file size and HTTP response code
    $fileSize = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
    $httpResponseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Close cURL session
    curl_close($ch);

    // Return an array with the results
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
                                error_log($URL . "\n", 3, 'errors.log');
                            } else {
                                error_log($URL . "\n", 3, 'app_urls.log');
                            }




                            function generateRandomCode($length = 9)
                            {
                                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                                $code = '';

                                for ($i = 0; $i < $length; $i++) {
                                    $code .= $characters[rand(0, strlen($characters) - 1)];
                                }

                                return $code;
                            }

                            $generatedCode = generateRandomCode();


                            $chromeUserAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36';


                            $url = 'https://cdn.syndication.twimg.com/tweet-result?features=&id=' . $tweet_id . '&lang=en&token=' . $generatedCode;  // Replace with your actual URL




                            // Initialize cURL session
                            $ch = curl_init($url);

                            // Set cURL options
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'User-Agent: ' . $chromeUserAgent));

                            // Execute cURL session and get the response
                            $response = curl_exec($ch);



                            // Check for cURL errors
                            if (curl_errno($ch)) {


                                header('Content-Type:application/json');
                                http_response_code(401);

                                die(json_encode(array('error' => true, 'message' => 'Api error')));

                            }

                            // Close cURL session
                            curl_close($ch);

                            // Decode the JSON response
                            $data = json_decode($response, true);

                            // Check for JSON decoding errors
                            if ($data === null) {


                                header('Content-Type:application/json');
                                http_response_code(401);

                                die(json_encode(array('error' => true, 'message' => 'Server error')));
                            } else {





                                if ($data["__typename"] == "Tweet") {

                                    error_log($URL . "\n", 3, 'tombstone.log');


                                    $tweet = $data;



                                    if (isset($tweet["mediaDetails"])) {


                                        $tweet = $data;

                                        $media_details = $tweet["mediaDetails"];

                                    } else {
                                        if (isset($tweet["card"])) {


                                            $binding_values = $tweet["card"]["binding_values"];

                                            $medias = array();



                                            if (isset($binding_values["unified_card"])) {

                                                $value = $binding_values["unified_card"]["string_value"];

                                                $value = json_decode($value);



                                                if (isset($value)) {



                                                    if ($value->type == "image_website") {

                                                        header('Content-Type:application/json');
                                                        http_response_code(401);

                                                        die(json_encode(array('error' => true, 'message' => 'No video or GIF founded')));
                                                    } else if ($value->type === "video_app") {

                                                        $media_id = $value->destination_objects->app_store_with_docked_media_1->data->media_id;




                                                        array_push(
                                                            $medias,

                                                            $value->media_entities->$media_id
                                                        );


                                                    } else {


                                                        $media_id = $value->destination_objects->browser_with_docked_media_1->data->media_id;



                                                        array_push(
                                                            $medias,

                                                            $value->media_entities->$media_id
                                                        );
                                                    }





                                                }




                                            }



                                            $media_details = $medias;





                                            $all_media = $media_details;

                                            $medias_result = array();

                                            foreach ($all_media as $media) {

                                                $user = $tweet["user"];

                                                $name = $user["name"];
                                                $profile_image_url_https = $user["profile_image_url_https"];
                                                $full_text = $tweet["text"];



                                                $media_type = $media->type;
                                                $thumbnail = $media->media_url_https;

                                                if (isset($media->video_info->variants)) {
                                                    $original_variants = $media->video_info->variants;

                                                } else {

                                                    header('Content-Type:application/json');
                                                    http_response_code(401);

                                                    die(json_encode(array('error' => true, 'message' => 'No video or GIF founded')));
                                                }


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
                                                            $size = get_remote_file_info($variant['url'])["fileSize"];



                                                            $media_data = array(
                                                                "bitrate" => $variant->bitrate,
                                                                "content_type" => $variant->content_type,
                                                                "url" => $variant->url,
                                                                "file_size" => $size

                                                            );

                                                        }


                                                        array_push($variants, $media_data);


                                                        usort($variants, function ($a, $b) { //Sort the array using a user defined function

                                                            return $a["bitrate"] > $b["bitrate"] ? -1 : 1; //Compare the scores
                                                        });

                                                    }








                                                }



                                                $media_result = array("thumbnail" => $thumbnail, "media_type" => $media_type, "name" => $name, "full_text" => $full_text, "profile_image_url_https" => $profile_image_url_https, "variants" => $variants);

                                                array_push($medias_result, $media_result);

                                            }


                                            header('Content-Type: application/json');
                                            echo json_encode($medias_result);


                                            exit;


                                        } else {

                                            if (isset($tweet["quoted_tweet"])) {

                                                $tweet = $data["quoted_tweet"];
                                                $media_details = $tweet["mediaDetails"];

                                            } else {

                                                header('Content-Type:application/json');
                                                http_response_code(401);

                                                die(json_encode(array('error' => true, 'message' => 'Nothing founded')));

                                            }




                                        }
                                    }




                                    $all_media = $media_details;

                                    $medias_result = array();

                                    foreach ($all_media as $media) {

                                        $user = $tweet["user"];

                                        $name = $user["name"];
                                        $profile_image_url_https = $user["profile_image_url_https"];
                                        $full_text = $tweet["text"];

                                        $media_type = $media["type"];
                                        $thumbnail = $media["media_url_https"];

                                        if (isset($media["video_info"]["variants"])) {
                                            $original_variants = $media["video_info"]["variants"];

                                        } else {

                                            header('Content-Type:application/json');
                                            http_response_code(401);

                                            die(json_encode(array('error' => true, 'message' => 'No video or GIF founded')));
                                        }


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


                                                usort($variants, function ($a, $b) { //Sort the array using a user defined function

                                                    return $a["bitrate"] > $b["bitrate"] ? -1 : 1; //Compare the scores
                                                });

                                            }



                                        }



                                        $media_result = array("thumbnail" => $thumbnail, "media_type" => $media_type, "name" => $name, "full_text" => $full_text, "profile_image_url_https" => $profile_image_url_https, "variants" => $variants);

                                        array_push($medias_result, $media_result);

                                    }


                                    header('Content-Type: application/json');
                                    echo json_encode($medias_result);



                                } else if ($data["__typename"] == "TweetTombstone") {



                                    error_log("ADULT " . $URL . "\n", 3, 'tombstone.log');



                                    // Specify the URL you want to redirect to
                                    $redirect_url = "https://v2-x-api.25122022.xyz/x-dl-fx-api.php?url=" . $URL;


                                    // Use the header function to send a raw HTTP header with a status code
                                    header("Location: $redirect_url", true, 301);

                                    // Make sure that code execution stops after the header is sent to prevent further processing
                                    exit;


                                } else {

                                    header('Content-Type:application/json');
                                    http_response_code(401);

                                    die(json_encode(array('error' => true, 'message' => 'We will fix this issue within 24hrs stay with us')));

                                }

                            }


                        } catch (Exception $e) {

                            $error = array("error" => true, "message" => "Server error");
                            http_response_code(401);
                            die(json_encode($error));
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
