<?php

/**
 * @author Rainer Spittel (rainer at silverstripe dot com)
 * @package openlayers
 * @subpackage code
 *
 * Proxy controller delegates HTTP requests to dedicated web servers.
 * To avoid any cross-domain issues within the map application (i.e. requesting
 * XML data from features shown on the map via AJAX calls), we use the
 * proxy controller which delegates requests to the provided URL.
 */
 
class OL3Proxy_Controller extends Controller
{

    private static $allowed_actions = array(
        'dorequest'
    );

    protected static $allowed_host = array('localhost');

    /**
     * This method passes through an HTTP request to another webserver.
     * This proxy is used to avoid any cross domain issues. The proxy
     * uses a white-list of domains to minimize security risks.
     *
     * @param SS_HTTPRequest $data array of parameters
     *
     * $data['u']:         URL (complete request string)
     * $data['no_header']: set to '1' to avoid sending header information
     *                     directly.
     * @return the CURL response
     */
    public function dorequest($data)
    {
        $headers   = array();
        $vars      = $data->requestVars();
        $no_header = false;

        if (!isset($vars['u'])) {
            return "Invalid request: unknown proxy destination.";
        }
        $url = $vars['u'];

        if (isset($vars['no_header']) && $vars['no_header'] == '1') {
            $no_header = true;
        }

        $checkUrl = explode("/", $url);

        if (!in_array($checkUrl[2], $this->config()->get('allowed_host'))) {
            return "Access denied to ($url).";
        }

        // If it's a POST, put the POST data in the body
        if ($data->isPOST()) {
            $session = curl_init($url);
            $postvars = '';
            $vars = $data->getBody();
            if ($vars) {
                $postvars = $vars;
            } else {
                $vars = $data->postVars();
                if ($vars) {
                    foreach ($vars as $k => $v) {
                        $postvars .= $k.'='.$v.'&';
                    }
                }
            }

            $headers[] = 'Content-type: text/xml';
            curl_setopt($session, CURLOPT_HTTPHEADER, $headers);

            curl_setopt($session, CURLOPT_POST, true);
            curl_setopt($session, CURLOPT_POSTFIELDS, $postvars);
        } else {
            unset($vars['url'], $vars['u']);
            $headers[] = 'Content-type: text/xml';
            $session = curl_init($url . '?' . http_build_query($vars));
            curl_setopt($session, CURLOPT_HTTPHEADER, $headers);
        }

        // Don't return HTTP headers. Do return the contents of the call
        curl_setopt($session, CURLOPT_HEADER, false);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

        // Make the call
        $xml = curl_exec($session);

        // The web service returns XML. Set the Content-Type appropriately
        if ($no_header == false) {
            header("Content-Type: text/xml");
        }
        curl_close($session);
        echo $xml;die();
        return $xml;
    }
}
