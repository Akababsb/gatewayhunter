<?php
// Enable error reporting (for debugging purposes, remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to fetch website data and analyze security features
function fetchWebsiteData($url) {
    $response = [
            'url' => $url,
                    'payment_gateways' => [],
                            'captcha_detected' => false,
                                    'cloudflare_detected' => false,
                                            'payment_security_type' => 'None',
                                                    'cvv_cvc_requirement' => false,
                                                            'status_code' => null
                                                                ];

                                                                    // Validate the URL
                                                                        if (!filter_var($url, FILTER_VALIDATE_URL)) {
                                                                                return ['error' => 'Invalid URL'];
                                                                                    }

                                                                                        // Fetch website content using cURL
                                                                                            $ch = curl_init();
                                                                                                curl_setopt($ch, CURLOPT_URL, $url);
                                                                                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                                                                                        curl_setopt($ch, CURLOPT_HEADER, true);
                                                                                                            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                                                                                                                $result = curl_exec($ch);

                                                                                                                    // Get HTTP status code
                                                                                                                        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                                                                                                            $response['status_code'] = $http_code;

                                                                                                                                if (curl_errno($ch)) {
                                                                                                                                        curl_close($ch);
                                                                                                                                                return ['error' => 'Error fetching the URL: ' . curl_error($ch)];
                                                                                                                                                    }
                                                                                                                                                        curl_close($ch);

                                                                                                                                                            // Check for CAPTCHA
                                                                                                                                                                if (stripos($result, 'captcha') !== false) {
                                                                                                                                                                        $response['captcha_detected'] = true;
                                                                                                                                                                                $response['payment_security_type'] = 'OTP Required';
                                                                                                                                                                                    }

                                                                                                                                                                                        // Check for Cloudflare
                                                                                                                                                                                            if (stripos($result, 'cloudflare') !== false) {
                                                                                                                                                                                                    $response['cloudflare_detected'] = true;
                                                                                                                                                                                                        }

                                                                                                                                                                                                            // Detect Payment Gateways (basic example)
                                                                                                                                                                                                                if (stripos($result, 'upi') !== false) {
                                                                                                                                                                                                                        $response['payment_gateways'][] = 'UPI';
                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                if (stripos($result, 'apple.com') !== false) {
                                                                                                                                                                                                                                        $response['payment_gateways'][] = 'Apple';
                                                                                                                                                                                                                                            }

                                                                                                                                                                                                                                                // Placeholder for CVV/CVC requirement detection
                                                                                                                                                                                                                                                    $response['cvv_cvc_requirement'] = false;

                                                                                                                                                                                                                                                        return $response;
                                                                                                                                                                                                                                                        }

                                                                                                                                                                                                                                                        // Main API logic
                                                                                                                                                                                                                                                        if (isset($_GET['url'])) {
                                                                                                                                                                                                                                                            $url = $_GET['url'];
                                                                                                                                                                                                                                                                $data = fetchWebsiteData($url);
                                                                                                                                                                                                                                                                    header('Content-Type: application/json');
                                                                                                                                                                                                                                                                        echo json_encode($data, JSON_PRETTY_PRINT);
                                                                                                                                                                                                                                                                        } else {
                                                                                                                                                                                                                                                                            header('Content-Type: application/json');
                                                                                                                                                                                                                                                                                echo json_encode(['error' => 'URL parameter is required'], JSON_PRETTY_PRINT);
                                                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                                                                ?>
                                                                                                                                                                                                                                                                                