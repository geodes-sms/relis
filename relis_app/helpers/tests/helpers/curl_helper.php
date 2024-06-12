<?php

class Http_client
{
    private $ci;
    private $cookieFile;

    function __construct()
    {
        $this->ci = get_instance();
        $this->cookieFile = 'relis_app/helpers/tests/helpers/cookies.txt';
    }

    private function http_GET($endpoint)
    {
        return $this->request('GET', $endpoint);
    }

    private function http_POST($endpoint, $data)
    {
        return $this->request('POST', $endpoint, $data);
    }

    private function request($method, $endpoint, $data = [])
    {
        $url = 'http://host.docker.internal:8083/' . $endpoint;

        $error = "err";
        $statusCode = "";
        $header = "";
        $content = "";
        $urlInfo = [];

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 5);
        curl_setopt($curl, CURLOPT_COOKIEJAR, $this->cookieFile);
        curl_setopt($curl, CURLOPT_COOKIEFILE, $this->cookieFile);

        if ($method === 'POST' || $method === 'PUT') {
            if (!empty($data['fileFieldName'])) {
                $filePath = $data['filePath'];
                $post_data[$data['fileFieldName']] = new CURLFile($filePath);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
            } else {
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
            }
        }

        $response = curl_exec($curl);
        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        // Separate the header and content from the response
        $header = substr($response, 0, $headerSize);
        // Get additional information about the request
        $urlInfo = curl_getinfo($curl);
        $statusCode = $urlInfo['http_code'];

        if (curl_errno($curl)) {
            $error = curl_error($curl);
        }

        curl_close($curl);

        return [
            'status_code' => $statusCode,
            'headers' => $header,
            'content' => $response,
            'url' => $urlInfo['url'],
            'error' => $error,
        ];
    }

    function response($controller, $action, $data = [], $http_method = "GET")
    {
        if ($http_method == "GET") {
            return $this->http_GET($controller . '/' . $action);
        } elseif ($http_method == "POST") {
            return $this->http_POST($controller . '/' . $action, $data);
        }
    }

    function getShortUrl($url)
    {
        preg_match('/8083\/(.*?)(\.html)?$/', $url, $matches);
        return $matches[1];
    }

    //get session id
    function getCookieValue($cookieName)
    {
        $cookies = file_get_contents($this->cookieFile);

        if (preg_match("/{$cookieName}\s+(.*?)\s+/", $cookies, $match)) {
            return $match[1];
        }
        return "deleted"; // Cookie not found
    }

    public function unsetCookie($cookieName)
    {
        // Read the content of the cookie file
        $cookieContent = file_get_contents($this->cookieFile);
        // Remove cookie entry
        $cookieContent = preg_replace('/\b' . $cookieName . '\b.*\R?/', '', $cookieContent);
        // Write the updated content back to the cookie file
        file_put_contents($this->cookieFile, $cookieContent);
    }

    function readUserdata($elementName)
    {
        $session_id = $this->getCookieValue("relis_session");
        $file = 'cside/sessions/relis_session' . $session_id;

        if (file_exists($file)) {
            $serializedData = file_get_contents($file);
            $parts = explode(';', $serializedData);

            foreach ($parts as $part) {
                $keyValue = explode('|', $part, 2);

                if (count($keyValue) === 2) {
                    list($key, $value) = $keyValue;

                    // Remove any surrounding quotes if they exist
                    $key = preg_replace('/s:\d+:"(.*?)"/', '$1', $key);
                    $value = preg_replace('/s:\d+:"(.*?)"/', '$1', $value);

                    if ($key === $elementName) {
                        // Convert numeric strings to integers or leave as strings
                        $value = is_numeric($value) ? (int) $value : $value;
                        return (string) $value;
                    }
                }
            }
        }
        return 'Null';
    }

    function addUserData($key, $value, $sessionName)
    {
        $sessionId = $this->getCookieValue($sessionName);

        //file path
        $filePath = 'cside/sessions/relis_session' . $sessionId;
        $dataToAdd = $key . '|s:' . strlen($value) . ':"' . $value . '";';

        // Write the new data back to the file (append mode)
        file_put_contents($filePath, $dataToAdd, FILE_APPEND);
    }

    //get session serialized data
    function getSessionData($session_id)
    {
        $file = 'cside/sessions/relis_session' . $session_id;

        if (file_exists($file)) {
            $session_file_contents = file_get_contents($file);
            return $session_file_contents;
        }
        return null;
    }

    // function getUserdata($session_id)
    // {
    //     $file = 'cside/sessions/relis_session' . $session_id;

    //     if (file_exists($file)) {
    //         $session_file_contents = file_get_contents($file);
    //         session_decode($session_file_contents);
    //     } else {
    //         return null;
    //     }
    // }
}

function http_code()
{
    return array(
        100 => '100 Continue',
        101 => '101 Switching Protocols',
        102 => '102 Processing',
        103 => '103 Early Hints',
        200 => '200 OK',
        201 => '201 Created',
        202 => '202 Accepted',
        203 => '203 Non-Authoritative Information',
        204 => '204 No Content',
        205 => '205 Reset Content',
        206 => '206 Partial Content',
        207 => '207 Multi-Status',
        208 => '208 Already Reported',
        226 => '226 IM Used',
        300 => '300 Multiple Choices',
        301 => '301 Moved Permanently',
        302 => '302 Found',
        303 => '303 See Other',
        304 => '304 Not Modified',
        305 => '305 Use Proxy',
        307 => '307 Temporary Redirect',
        308 => '308 Permanent Redirect',
        400 => '<span style="color: #C00;">400 Bad Request</span>',
        401 => '<span style="color: #C00;">401 Unauthorized</span>',
        402 => '<span style="color: #C00;">402 Payment Required</span>',
        403 => '<span style="color: #C00;">403 Forbidden</span>',
        404 => '<span style="color: #C00;">404 Not Found</span>',
        405 => '<span style="color: #C00;">405 Method Not Allowed</span>',
        406 => '<span style="color: #C00;">406 Not Acceptable</span>',
        407 => '<span style="color: #C00;">407 Proxy Authentication Required</span>',
        408 => '<span style="color: #C00;">408 Request Timeout</span>',
        409 => '<span style="color: #C00;">409 Conflict</span>',
        410 => '<span style="color: #C00;">410 Gone</span>',
        411 => '<span style="color: #C00;">411 Length Required</span>',
        412 => '<span style="color: #C00;">412 Precondition Failed</span>',
        413 => '<span style="color: #C00;">413 Payload Too Large</span>',
        414 => '<span style="color: #C00;">414 URI Too Long</span>',
        415 => '<span style="color: #C00;">415 Unsupported Media Type</span>',
        416 => '<span style="color: #C00;">416 Range Not Satisfiable</span>',
        417 => '<span style="color: #C00;">417 Expectation Failed</span>',
        418 => '<span style="color: #C00;">418 I\'m a Teapot</span>',
        421 => '<span style="color: #C00;">421 Misdirected Request</span>',
        422 => '<span style="color: #C00;">422 Unprocessable Entity</span>',
        423 => '<span style="color: #C00;">423 Locked</span>',
        424 => '<span style="color: #C00;">424 Failed Dependency</span>',
        425 => '<span style="color: #C00;">425 Too Early</span>',
        426 => '<span style="color: #C00;">426 Upgrade Required</span>',
        428 => '<span style="color: #C00;">428 Precondition Required</span>',
        429 => '<span style="color: #C00;">429 Too Many Requests</span>',
        431 => '<span style="color: #C00;">431 Request Header Fields Too Large</span>',
        451 => '<span style="color: #C00;">451 Unavailable For Legal Reasons</span>',
        500 => '<span style="color: #C00;">500 Internal Server Error</span>',
        501 => '<span style="color: #C00;">501 Not Implemented</span>',
        502 => '<span style="color: #C00;">502 Bad Gateway</span>',
        503 => '<span style="color: #C00;">503 Service Unavailable</span>',
        504 => '<span style="color: #C00;">504 Gateway Timeout</span>',
        505 => '<span style="color: #C00;">505 HTTP Version Not Supported</span>',
        506 => '<span style="color: #C00;">506 Variant Also Negotiates</span>',
        507 => '<span style="color: #C00;">507 Insufficient Storage</span>',
        508 => '<span style="color: #C00;">508 Loop Detected</span>',
        510 => '<span style="color: #C00;">510 Not Extended</span>',
        511 => '<span style="color: #C00;">511 Network Authentication Required</span>'
    );
}

//delete created test session files
function deleteSessionFiles()
{
    $http_client = new Http_client();
    $sessionDir = "cside/sessions";
    $sessionFiles = glob($sessionDir . '/relis_session*');

    //logout first
    $http_client->response("user", "discon");

    if ($sessionFiles !== false) {
        foreach ($sessionFiles as $file) {
            if (is_file($file)) {
                unlink($file); // Delete the file
            }
        }
    }
}

// function findUrlWithWord($document, $keywords)
// {
//     $htmlContent = file_get_contents($document);

//     // Define the regular expression pattern to match URLs containing specified keywords
//     $pattern = '/\b(?:https?:\/\/|www\.)[^\s"\'<>]+(?:' . implode('|', $keywords) . ')[^\s"\'<>]*\b/';

//     // Find all matches
//     preg_match_all($pattern, $htmlContent, $matches);

//     // Return the array of matching URLs
//     return $matches[0];
// }

//convert html body to php array
function html_to_array($html, $tag)
{
    $pattern = '/(<' . $tag . '.*?>.*?<\/' . $tag . '>)/s';
    preg_match_all($pattern, $html, $matches);
    $html = $matches[1];
    $content = "";

    foreach ($html as $value) {
        $content = $content . " " . $value;
    }

    $dom = new DOMDocument();
    $dom->loadHTML($content);
    return element_to_array($dom->documentElement);
}

function element_to_array($element)
{
    $result = [
        'tag' => $element->tagName,
        'attributes' => [],
        'content' => '',
        'children' => [],
    ];

    foreach ($element->attributes as $attribute) {
        $result['attributes'][$attribute->name] = $attribute->value;
    }

    foreach ($element->childNodes as $subElement) {
        if ($subElement->nodeType == XML_TEXT_NODE) {
            $result['content'] = $subElement->wholeText;
        } else {
            $result['children'][] = element_to_array($subElement);
        }
    }

    return $result;
}
